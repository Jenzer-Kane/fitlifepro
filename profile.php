<?php
session_start();

include 'database.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

$stmt = $mysqli->prepare("SELECT profile_image, gender FROM registration WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($profile_image_path, $gender);
    $stmt->fetch();
} else {
    $profile_image_path = null;
}

$stmt->close();

// Initialize variables
// $gender = null;
$waist = null;
$neck = null;
$bmiHeight = null;
$bmiWeight = null;
$age = null;
$hip = null;
$thigh = null;

// Function to calculate BMI
function calculateBMI($weight, $height)
{
    if ($height !== null && $height != 0) {
        $heightInMeters = $height / 100; // Convert height to meters
        return $weight / ($heightInMeters * $heightInMeters);
    } else {
        return 0; // Return 0 or handle error logic
    }
}

// Process user input and insert into the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture user input
    $age = $_POST["age"];
    $waist = $_POST["waist"];
    $neck = $_POST["neck"];
    $hip = isset($_POST["hip"]) ? $_POST["hip"] : '';
    $thigh = isset($_POST["thigh"]) ? $_POST["thigh"] : '';
    $activityLevel = $_POST["activityLevel"];
    $bmiWeight = $_POST["bmiWeight"];
    $bmiHeight = $_POST["bmiHeight"];
    $fitnessLevel = $_POST["fitnessLevel"]; // Capture the fitness level input
    $username = $_SESSION['username']; // Assume username is stored in the session

    // Insert user input into the database
    $sql = "INSERT INTO users_info (username, age, waist, neck, hip, thigh, activityLevel, bmiWeight, bmiHeight, fitnessLevel) 
            VALUES ('$username', '$age', '$waist', '$neck', '$hip', '$thigh', '$activityLevel', '$bmiWeight', '$bmiHeight', '$fitnessLevel')";

    if ($mysqli->query($sql) === TRUE) {
        echo '<div class="alert success-alert">Profile updated successfully.</div>';
    } else {
        echo '<div class="alert error-alert">Error: ' . $sql . '<br>' . $mysqli->error . '</div>';
    }
}


// Initialize results arrays
$intakeResults = null; // For caloric and protein intake
$bmiResults = null; // For BMI calculation
$bodyFatResults = null; // For body fat percentage and related calculations
$mealPlan = null; // For meal planning

// Declare $lowerNormalRange and $upperNormalRange outside the if block
$lowerNormalRange = null;
$upperNormalRange = null;

// Function to calculate BMI difference from standard range
function getBMIDifference($bmi)
{
    // Define standard BMI range (adjust as needed)
    $underweightThreshold = 18.5;
    $normalWeightThreshold = 24.9;
    $overweightThreshold = 29.9;

    // Calculate the difference from each threshold
    $underweightDifference = $bmi - $underweightThreshold;
    $normalWeightDifference = abs($bmi - $normalWeightThreshold);
    $overweightDifference = abs($bmi - $overweightThreshold);

    // Return differences as an array
    return [
        'underweight' => $underweightDifference,
        'normalWeight' => $normalWeightDifference,
        'overweight' => $overweightDifference,
    ];
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user input for BMI calculation
    $bmiWeight = floatval($_POST['bmiWeight']);
    $bmiHeight = floatval($_POST['bmiHeight']);

    // Validate input (add more validation as needed)
    if (empty($bmiWeight) || empty($bmiHeight)) {
        echo '<script>
            alert("Please fill in all required fields with valid numeric values.");
        </script>';
        exit();
    }

    // Perform BMI calculation
    $bmi = calculateBMI($bmiWeight, $bmiHeight);

    // Set BMI results
    $bmiResults = [
        'weight' => $bmiWeight,
        'height' => $bmiHeight,
        'bmi' => $bmi,
        'bmiDifference' => getBMIDifference($bmi),
    ];

    // Determine recommended goal based on BMI
    if ($bmi < 18.50) {
        $recommendedGoal = 'weight-gain';
    } elseif ($bmi >= 18.50 && $bmi <= 24.99) {
        $recommendedGoal = 'maintenance';
    } else {
        $recommendedGoal = 'weight-loss';
    }

    // Determine BMI category
    if ($bmi < 18.50) {
        $bmiCategory = "Underweight";
    } elseif ($bmi >= 18.50 && $bmi <= 24.99) {
        $bmiCategory = "Normal";
    } elseif ($bmi >= 25 && $bmi <= 29.99) {
        $bmiCategory = "Overweight";
    } else {
        $bmiCategory = "Obese";
    }

    // Retrieve user input for caloric and protein intake
    $activityLevel = $_POST['activityLevel'];

    // Constants for caloric and protein calculations (adjust as needed)
    $caloriesPerKg = 30; // Adjust based on individual factors
    $proteinRatioBulking = 1.8; // Adjust based on individual factors for weight-gain
    $proteinRatioCutting = 1.2; // Adjust based on individual factors for weight-loss

    // Calculate caloric and protein intake based on the recommended goal
    if ($recommendedGoal === 'weight-gain') {
        // Bulking: aim for 1.8g of protein per kg of body weight and a 500 calorie surplus
        $proteinIntake = $bmiWeight * $proteinRatioBulking;
        $caloricIntake = $bmiWeight * $caloriesPerKg + 1000;
    } elseif ($recommendedGoal === 'weight-loss') {
        // Cutting: aim for 1.2g of protein per kg of body weight and a 500 calorie deficit
        $proteinIntake = $bmiWeight * $proteinRatioCutting;
        $caloricIntake = $bmiWeight * $caloriesPerKg - 1000;
    } else {
        // Maintenance: aim for 1.5g of protein per kg of body weight and maintain current caloric intake
        $proteinIntake = $bmiWeight * 1.5;
        $caloricIntake = $bmiWeight * $caloriesPerKg;
    }

    // Caloric intake based on activity level
    if ($activityLevel === 'active') {
        $caloricIntake += 200; // Add 200 calories for active individuals
    }

    // Store the results for displaying in the HTML later
    $intakeResults = [
        'weight' => $bmiWeight,
        'height' => $bmiHeight,
        'caloricIntake' => $caloricIntake,
        'proteinIntake' => $proteinIntake,
        'goal' => $recommendedGoal,
    ];

    // Assign the goal from intake results
    $goal = $intakeResults['goal'];

    // Retrieve user input for body fat calculation
    $age = intval($_POST['age']);
    $waist = floatval($_POST['waist']);
    $neck = floatval($_POST['neck']);
    $hip = isset($_POST['hip']) ? floatval($_POST['hip']) : 0;
    $thigh = isset($_POST['thigh']) ? floatval($_POST['thigh']) : 0;

    // Establish database connection
    include 'database.php';

}

// Perform body fat calculation
if (isset($gender) && $gender === 'female') {
    $bodyFatPercentage = calculateBodyFatPercentageForWomen($waist, $neck, $hip, $bmiHeight);
} else {
    $bodyFatPercentage = calculateBodyFatPercentageForMen($waist, $neck, $bmiHeight);
}

// Calculate fat mass and lean mass
$fatMass = ($bodyFatPercentage / 100) * $bmiWeight;
$leanMass = $bmiWeight - ($bmiWeight * ($bodyFatPercentage / 100));


// Function to calculate weight from BMI and height
function getWeightFromBMI($bmi, $height)
{
    // Check if height is provided and not zero
    if ($height !== null && $height != 0) {
        // BMI Formula: BMI = weight (kg) / (height (m) * height (m))
        $heightInMeters = $height / 100; // Convert height to meters
        $weight = $bmi * ($heightInMeters * $heightInMeters);

        // Round the weight to two decimal places
        return round($weight, 2);
    } else {
        // Return some default value or handle it as per your application logic
        return 0;
    }
}


// Calculate healthy BMI range
$lowerNormalRange = 18.5 * (($bmiHeight / 100) ** 2);
$upperNormalRange = 24.9 * (($bmiHeight / 100) ** 2);

// Store the body fat results for displaying in the HTML later
$bodyFatResults = [
    'age' => $age,
    'waist' => $waist,
    'neck' => $neck,
    'hip' => $hip,
    'thigh' => $thigh,
    'height' => $bmiHeight,
    'bodyFatPercentage' => $bodyFatPercentage,
    'fatMass' => $fatMass,
    'leanMass' => $leanMass,
    'lowerNormalRange' => $lowerNormalRange,
    'upperNormalRange' => $upperNormalRange,
];


// Function to calculate body fat percentage using Navy Method for men
function calculateBodyFatPercentageForMen($waist, $neck, $height)
{
    $bodyFatPercentage = (495 / (1.0324 - 0.19077 * log10($waist - $neck) + 0.15456 * log10($height))) - 450;

    return round($bodyFatPercentage, 2); // Round to two decimal places
}

// Function to calculate body fat percentage using Navy Method for women
function calculateBodyFatPercentageForWomen($waist, $neck, $hip, $height)
{
    $bodyFatPercentage = (495 / (1.29579 - 0.35004 * log10($waist + $hip - $neck) + 0.221 * log10($height))) - 450;

    return round($bodyFatPercentage, 2); // Round to two decimal places
}

// Function to fetch the meal plan based on the recommended goal from the meat_info table
function getMealPlan($recommendedGoal)
{
    // Include the database connection
    include 'database.php';

    // Initialize variables
    $foodExchangeGroups = [];

    // Determine the food exchange groups based on the recommended goal
    if ($recommendedGoal === 'weight-loss') {
        $foodExchangeGroups = ['Low Fat Meat', 'Fruit', 'Milk', 'Rice', 'Bakery products', 'Noodles and Pasta', 'Others'];
    } elseif ($recommendedGoal === 'weight-gain') {
        $foodExchangeGroups = ['High Fat Meat', 'Fruit', 'Milk', 'Rice', 'Bakery products', 'Noodles and Pasta', 'Others'];
    } elseif ($recommendedGoal === 'maintenance') {
        $foodExchangeGroups = ['Medium Fat Meat', 'Fruit', 'Milk', 'Rice', 'Bakery products', 'Noodles and Pasta', 'Others'];
    }

    $tables = ['meat_info', 'milk_info', 'fruits_info', 'rice_bread_info'];
    $meal_plan = [];

    foreach ($foodExchangeGroups as $foodExchangeGroup) {
        foreach ($tables as $table) {
            $statement = $mysqli->prepare("SELECT filipino_name, english_name, protein_g, fat_g, energy_kcal, household_measure FROM $table WHERE food_exchange_group = ?");
            if ($statement) {
                $statement->bind_param('s', $foodExchangeGroup);
                $statement->execute();
                $result = $statement->get_result();
                $meal_plan = array_merge($meal_plan, $result->fetch_all(MYSQLI_ASSOC));
                $statement->close();
            } else {
                echo "Error preparing statement for table $table: " . $mysqli->error;
            }
        }
    }

    return $meal_plan;

}

// Fetch the meal plan based on the recommended goal
if (isset($intakeResults['goal'])) {
    $meal_plan = getMealPlan($intakeResults['goal']); // Pass only the recommended goal
}

// Determine the goal name for display purposes
if (isset($intakeResults['goal'])) {
    switch ($intakeResults['goal']) {
        case 'weight-loss':
            $goal_name = 'Weight-Loss';
            break;
        case 'weight-gain':
            $goal_name = 'Weight-Gain';
            break;
        case 'maintenance':
            $goal_name = 'Maintenance';
            break;
        default:
            $goal_name = 'Unknown';
            break;
    }
}

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$timeSlots = ['Breakfast', 'Lunch', 'Snack', 'Snack', 'Snack', 'Dinner'];


function getExercises($recommendedGoal, $exerciseType, $fitnessLevel)
{
    include 'database.php'; // Ensure database connection is available

    // Determine intensity based on fitness level and goal
    $intensity = '';

    // First determine the intensity based on fitnessLevel
    switch ($fitnessLevel) {
        case 'beginner':
            $intensity = 'Low';
            break;
        case 'intermediate':
            $intensity = 'Moderate';
            break;
        case 'advanced':
            $intensity = 'High';
            break;
        default:
            $intensity = 'Moderate'; // Default to Moderate if no fitness level is selected
            break;
    }

    // Refine intensity further based on the goal, if needed
    if ($recommendedGoal === 'weight-loss' && $fitnessLevel !== 'beginner') {
        $intensity = 'Low';
    } elseif ($recommendedGoal === 'maintenance' && $fitnessLevel !== 'advanced') {
        $intensity = 'Moderate';
    } elseif ($recommendedGoal === 'weight-gain' && $fitnessLevel === 'advanced') {
        $intensity = 'High';
    }

    // Advanced fitness level overrides exerciseType, fetch from all exercise types with 'High' intensity
    if ($fitnessLevel === 'advanced') {
        $query = "SELECT name, duration, intensity, category, exercise_type, image_link, target_body_part, description 
                  FROM exercises 
                  WHERE intensity = 'High'"; // No need to filter exerciseType for advanced users
    } else {
        // Regular users fetch based on intensity and exerciseType
        $query = "SELECT name, duration, intensity, category, exercise_type, image_link, target_body_part, description 
                  FROM exercises 
                  WHERE intensity = ? AND exercise_type = ?";
    }

    $statement = $mysqli->prepare($query);

    // Check if the statement was prepared successfully
    if (!$statement) {
        die("Failed to prepare statement: " . $mysqli->error); // Output the error for debugging
    }

    // Bind parameters and execute the query for regular users
    if ($fitnessLevel !== 'advanced') {
        $statement->bind_param('ss', $intensity, $exerciseType);
    }

    $statement->execute();
    $result = $statement->get_result();

    if ($result) {
        $exercises = $result->fetch_all(MYSQLI_ASSOC);
        $exercisePlan = [];
        $categories = ['Strength', 'Cardio', 'Core', 'Back'];

        // Shuffle and select random exercises for each category
        foreach ($categories as $category) {
            $categoryExercises = array_filter($exercises, function ($exercise) use ($category) {
                return $exercise['category'] === $category;
            });
            shuffle($categoryExercises);
            $exercisePlan = array_merge($exercisePlan, $categoryExercises);
        }

        // Shuffle the final exercise plan to mix all categories randomly
        shuffle($exercisePlan);

        // Close the statement
        $statement->close();

        return $exercisePlan;
    } else {
        die("Error retrieving exercises: " . $mysqli->error); // Output the error for debugging
    }
}

$target_body_part = null;
$exerciseType = isset($_POST['exerciseType']) ? $_POST['exerciseType'] : 'Bodyweight';
$fitnessLevel = isset($_POST['fitnessLevel']) ? $_POST['fitnessLevel'] : 'beginner'; // Default to intermediate if not set

if (isset($intakeResults['goal'])) {
    $exercise_plan = getExercises($intakeResults['goal'], $exerciseType, $fitnessLevel);
} else {
    // Handle the case when $intakeResults['goal'] is not set
    $exercise_plan = [];
}


$exercisetimeSlots = ['Any', '-', '-', '-', '-'];

function retrieveSavedUserInfo()
{
    global $mysqli;

    // Check if the username is set in the session
    if (!isset($_SESSION['username'])) {
        return null;
    }

    // Prepare the query to retrieve saved user information based on the username
// Modify the query to include fitnessLevel
    $sql = "SELECT age, waist, neck, gender, hip, thigh, activityLevel, bmiWeight, bmiHeight, fitnessLevel 
        FROM users_info 
        WHERE username = ? 
        ORDER BY created_at DESC LIMIT 1";

    // Prepare the statement
    $stmt = $mysqli->prepare($sql);

    // Check if statement preparation was successful
    if (!$stmt) {
        // If preparation failed, return null
        return null;
    }

    // Bind parameters
    $stmt->bind_param("s", $_SESSION['username']);

    // Execute the query
    $stmt->execute();

    // Check for execution errors
    if ($stmt->errno) {
        // If there's an error, return null
        return null;
    }

    // Bind result variables, including fitnessLevel
    $stmt->bind_result($age, $waist, $neck, $gender, $hip, $thigh, $activityLevel, $bmiWeight, $bmiHeight, $fitnessLevel);


    // Fetch the data
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    // Check if any data is retrieved
    if ($age !== null && $waist !== null && $neck !== null && $activityLevel !== null) {
        // Return the retrieved user information, including fitnessLevel
        return [
            'age' => $age,
            'waist' => $waist,
            'neck' => $neck,
            'hip' => $hip,
            'thigh' => $thigh,
            'activityLevel' => $activityLevel,
            'bmiWeight' => $bmiWeight,
            'bmiHeight' => $bmiHeight,
            'fitnessLevel' => $fitnessLevel // Include fitnessLevel
        ];

    } else {
        // No saved information found
        return getDefaultUserInfo();
    }
}

// Function to return default user information
function getDefaultUserInfo()
{
    // Define default values
    $defaultUserInfo = [
        'age' => 0,
        'waist' => 0,
        'neck' => 0,
        'hip' => 0,
        'thigh' => 0,
        'activityLevel' => '',
        'bmiWeight' => 0,
        'bmiHeight' => 0,
        'fitnessLevel' => 'beginner' // Default to beginner if not set
    ];

    return $defaultUserInfo;
}

// Retrieve saved user information
$savedUserInfo = retrieveSavedUserInfo();

// Check if user information is retrieved successfully
if ($savedUserInfo) {
    // Assign retrieved user information to variables

    $age = $savedUserInfo['age'];
    $waist = $savedUserInfo['waist'];
    $neck = $savedUserInfo['neck'];
    $hip = $savedUserInfo['hip'];
    $thigh = $savedUserInfo['thigh'];
    $bmiWeight = $savedUserInfo['bmiWeight'];
    $bmiHeight = $savedUserInfo['bmiHeight'];
    $activityLevel = $savedUserInfo['activityLevel'];
    $fitnessLevel = $savedUserInfo['fitnessLevel']; // Include fitnessLevel


    // Get the username from the session
    $username = $_SESSION['username'];

    // Fetch user results
    $userResults = fetchUserResults($username);

    // Check if results are available
    if ($userResults) {
        // Store the body fat results for displaying in the HTML later
        $bodyFatResults = [
            'age' => $age,
            'waist' => $waist,
            'neck' => $neck,
            'hip' => $hip,
            'thigh' => $thigh,
            'height' => $bmiHeight,
            'bmi' => $userResults['bmi'],
            'bmiCategory' => $userResults['bmiCategory'],
            'created_at' => $userResults['created_at'],
            'recommendedGoal' => $userResults['recommendedGoal'],
            'bodyFatPercentage' => $userResults['bodyFatPercentage'],
            'fatMass' => $userResults['fatMass'],
            'leanMass' => $userResults['leanMass'],
            'caloricIntake' => $userResults['caloricIntake'],
            'proteinIntake' => $userResults['proteinIntake'],
            'activityLevel' => $activityLevel
        ];
    } else {
        // Handle case where no results are found
        $bodyFatResults = null;
    }
} else {
    // Handle case where user information is not retrieved
    $bodyFatResults = null;
}

// Function to fetch user results from the database
function fetchUserResults($username)
{
    global $mysqli;


    // Prepare and bind, grabs latest row
    $stmt = $mysqli->prepare("SELECT * FROM users_results WHERE username = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $username);


    // Execute the statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if results exist
    if ($result->num_rows > 0) {
        // Results exist, fetch them
        $row = $result->fetch_assoc();

        // Return the results as an associative array
        return [
            'bmi' => $row['bmi'],
            'bmiCategory' => $row['bmiCategory'],
            'bodyFatPercentage' => $row['bodyFatPercentage'],
            'created_at' => $row['created_at'],
            'fatMass' => $row['fatMass'],
            'leanMass' => $row['leanMass'],
            'caloricIntake' => $row['caloricIntake'],
            'proteinIntake' => $row['proteinIntake'],
            'recommendedGoal' => $row['recommendedGoal'],
        ];
    } else {
        // No results found
        return null;
    }

    // Close statement
    $stmt->close();
}

// Check if $bodyFatResults is set and $bodyFatResults['created_at'] is not null
if (isset($intakeResults['created_at']) && $intakeResults['created_at'] !== null) {
    // If intakeResults timestamp is available, use it
    $timestamp = strtotime($intakeResults['created_at']);
    $formattedDate = date("F j, Y   |   g:i A", $timestamp);
    $generatedText = "";
} elseif (isset($bodyFatResults['created_at']) && $bodyFatResults['created_at'] !== null) {
    // If intakeResults timestamp is not available but bodyFatResults timestamp is, use it
    $timestamp = strtotime($bodyFatResults['created_at']);
    $formattedDate = date("F j, Y   |   g:i A", $timestamp);
    $generatedText = "";
} else {
    // If neither timestamp is available
    $formattedDate = "No timestamp available";
    $generatedText = "";
}

// Calculate days ago message if timestamp is available
$daysAgoMessage = '';
if (isset($timestamp)) {
    $currentDate = strtotime("now");
    $diffInDays = floor(($currentDate - $timestamp) / (60 * 60 * 24));

    // Set the "days ago" message
    if ($diffInDays > 0) {
        $daysAgoMessage = "($diffInDays days ago)";
    } else {
        $daysAgoMessage = "(Today)";
    }
}


// Fetch the latest user transaction from transactions table, ordered by created_at descending
$userTransactionsSql = "SELECT * FROM transactions WHERE username = ? ORDER BY created_at DESC LIMIT 1";
$userTransactionsStmt = $mysqli->prepare($userTransactionsSql);
$userTransactionsStmt->bind_param('s', $username);
$userTransactionsStmt->execute();
$userTransactionsResult = $userTransactionsStmt->get_result();

// Fetch user transaction status and plan
$query = "SELECT status, plan FROM transactions WHERE username = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $mysqli->prepare($query);
if (!$stmt) {
    die('Prepare failed: ' . htmlspecialchars($mysqli->error));
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$status = isset($user['status']) ? $user['status'] : null;
$plan = isset($user['plan']) ? $user['plan'] : null;

// Always show the calculator section, regardless of subscription status
$disableButton = true; // Ensure this is false so the calculator section remains enabled

// Only show diet and exercise planning sections if status is approved and active
$showDietPlanningSection = ($status === 'Approved');

// Allow viewing the subscription status if the user had a previous subscription
$showSubscriptionInfoSection = ($status !== 'Disapproved' || $status !== 'Expired' || $status !== null);

// Show a subscribe message if there is no current subscription or if it’s disapproved/expired
$showSubscribeMessage = ($status === null || $status === 'Disapproved' || $status === 'Expired' || $plan === null);


$query = "SELECT * FROM quotes ORDER BY RAND() LIMIT 1";
$result = $mysqli->query($query);

// Check if the query was successful
if ($result && $result->num_rows > 0) {
    $quote = $result->fetch_assoc();
} else {
    $quote = null;
}

// Prepare and execute the SQL query to fetch gender from users_info table
$stmt = $mysqli->prepare("SELECT gender FROM users_info WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($gender);
    $stmt->fetch();
    // Assign the fetched gender to the $savedUserInfo array
    $savedUserInfo['gender'] = $gender;
} else {
    // Handle the case where no gender is found
    $savedUserInfo['gender'] = "not specified"; // Or any default value you prefer
}

// Fetch user information from the database
$userInfoQuery = "SELECT * FROM users_info WHERE username = ?";
$userInfoStmt = $mysqli->prepare($userInfoQuery);
$userInfoStmt->bind_param('i', $username);
$userInfoStmt->execute();
$userInfo = $userInfoStmt->get_result()->fetch_assoc();

// Fetch users results from the database
$userResultsQuery = "SELECT * FROM users_results WHERE username = ? ORDER BY created_at";
$userResultsStmt = $mysqli->prepare($userResultsQuery);
$userResultsStmt->bind_param('s', $username);
$userResultsStmt->execute();
$userResults = $userResultsStmt->get_result()->fetch_assoc();

// Fetch user weight from users_info for the graphs
$userWeightQuery = "SELECT created_at, bmiWeight FROM users_info WHERE username = ? ORDER BY created_at";
$userWeightStmt = $mysqli->prepare($userWeightQuery);
$userWeightStmt->bind_param('s', $username);
$userWeightStmt->execute();
$userWeightResults = $userWeightStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch users_results table for the graphs
$userResultsSql = "SELECT * FROM users_results WHERE username = ? ORDER BY created_at";
$userResultsStmt = $mysqli->prepare($userResultsSql);
$userResultsStmt->bind_param('s', $username);
$userResultsStmt->execute();
$userResultsResult = $userResultsStmt->get_result();

$userResultsData = [];
if ($userResultsResult->num_rows > 0) {
    while ($result = $userResultsResult->fetch_assoc()) {
        $userResultsData[] = $result;
    }
}

// Merge weight data with user results based on closest created_at timestamp
$mergedData = [];
foreach ($userResultsData as $result) {
    $date = $result['created_at'];
    $closestWeight = null;

    // Find the closest weight entry on or before the result date
    foreach ($userWeightResults as $weightEntry) {
        if ($weightEntry['created_at'] <= $date) {
            $closestWeight = $weightEntry['bmiWeight'];
        } else {
            break;
        }
    }

    $mergedData[] = array_merge($result, ['weight' => $closestWeight]);
}

$goal_name = isset($userResults['recommendedGoal']) ? $userResults['recommendedGoal'] : null; // Assuming 'recommendedGoal' column exists in 'users_results'

// Fetch the meal plan based on the recommended goal
$meal_plan = array(); // Initialize as an empty array to avoid undefined variable issues
if (isset($goal_name)) {
    $meal_plan = getMealPlan($goal_name); // Pass only the recommended goal
}

// Fetch the exercise plan based on the recommended goal
$exercise_plan = array(); // Initialize as an empty array to avoid undefined variable issues
$exerciseType = isset($_POST['exerciseType']) ? $_POST['exerciseType'] : 'Bodyweight';

if (isset($goal_name)) {
    $exercise_plan = getExercises($goal_name, $exerciseType, $fitnessLevel, $target_body_part); // Pass all three arguments

}

// Assuming the logged-in user's username is stored in the session
$loggedInUsername = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Query to get the latest created_at timestamp for the logged-in user
$sql = "SELECT created_at FROM users_info WHERE username = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $loggedInUsername);
$stmt->execute();
$result = $stmt->get_result();

$lastCalculationDate = null;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastCalculationDate = $row['created_at'];
}

$stmt->close();

$disabledDays = 5;
$remainingDays = $disabledDays;

if ($lastCalculationDate) {
    // Calculate the number of days since the last calculation
    $remainingDays = $disabledDays - (new DateTime())->diff(new DateTime($lastCalculationDate))->days;
    // Ensure remaining days is not negative and set disable flag
    $remainingDays = max($remainingDays, 0);
    $disableButton = $remainingDays > 0;
}

$disableButton = $remainingDays > 0;

// PROGRESS TRACKING TABLE SECTION ARROW INDICATORS
if (!function_exists('getArrow')) {
    function getArrow($currentValue, $previousValue, $goal)
    {
        $difference = $currentValue - $previousValue;

        if ($goal === 'weight-loss') {
            if ($difference < 0) {
                return "<span style='color: green;'>⬇️ <strong>-" . abs($difference) . "</strong></span>"; // Green for weight loss
            } else {
                return "<span style='color: red;'>⬆️ <strong>+" . $difference . "</strong></span>"; // Red for weight gain
            }
        } elseif ($goal === 'weight-gain') {
            if ($difference > 0) {
                return "<span style='color: green;'>⬆️ <strong>+" . $difference . "</strong></span>"; // Green for weight gain
            } else {
                return "<span style='color: red;'>⬇️ <strong>-" . abs($difference) . "</strong></span>"; // Red for weight loss
            }
        } elseif ($goal === 'maintenance') {
            if ($difference === 0) {
                return "<span style='color: blue;'>↔️ <strong>No Change</strong></span>"; // Blue for no change
            } elseif ($difference > 0) {
                return "<span style='color: red;'>⬆️ <strong>+" . $difference . "</strong></span>"; // Red for increase
            } else {
                return "<span style='color: green;'>⬇️ <strong>-" . abs($difference) . "</strong></span>"; // Green for decrease
            }
        }

        return ''; // Default return if no goal is set
    }
}

// Get today's date
$today = date('Y-m-d');

// Initialize arrays for storing chart data
$foodLogData = [];
$exerciseLogData = [];

// Define an array to keep a consistent order of days starting from today
$days = [];
for ($i = 0; $i < 7; $i++) {
    $days[] = date('l', strtotime("-$i days")); // Get each day name (e.g., Monday)
}

// Fetch food activity log for the last 7 days
$foodQuery = "SELECT date, SUM(calories_consumed) as total_calories, SUM(protein_consumed) as total_protein
              FROM food_activity_log
              WHERE username = ? AND date >= CURDATE() - INTERVAL 6 DAY
              GROUP BY date ORDER BY date DESC";
$stmt = $mysqli->prepare($foodQuery);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $dayName = date('l', strtotime($row['date']));
    $foodLogData[$dayName] = [
        'total_calories' => (int) $row['total_calories'],
        'total_protein' => (int) $row['total_protein']
    ];
}
$stmt->close();

// Fetch exercise activity log for the last 7 days
$exerciseQuery = "SELECT date, COUNT(*) as exercises_completed
                  FROM exercise_activity_log
                  WHERE username = ? AND date >= CURDATE() - INTERVAL 6 DAY
                  GROUP BY date ORDER BY date DESC";
$stmt = $mysqli->prepare($exerciseQuery);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $dayName = date('l', strtotime($row['date']));
    $exerciseLogData[$dayName] = [
        'exercises_completed' => (int) $row['exercises_completed']
    ];
}
$stmt->close();

// Prepare data for the last 7 days (ensures all days are present)
$caloriesData = [];
$proteinData = [];
$exerciseData = [];
foreach ($days as $day) {
    $caloriesData[] = $foodLogData[$day]['total_calories'] ?? 0;
    $proteinData[] = $foodLogData[$day]['total_protein'] ?? 0;
    $exerciseData[] = $exerciseLogData[$day]['exercises_completed'] ?? 0;
}

// Pass data to JavaScript
$labels = json_encode(array_reverse($days)); // Reverse to display from Monday to Sunday
$caloriesData = json_encode(array_reverse($caloriesData));
$proteinData = json_encode(array_reverse($proteinData));
$exerciseData = json_encode(array_reverse($exerciseData));

$mysqli->close();
?>

<!DOCTYPE html>

<head>
    <title>Profile | FITLIFE PRO</title>
    <!-- /SEO Ultimate -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta charset="utf-8">
    <link rel="apple-touch-icon" sizes="57x57" href="./assets/images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="./assets/images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="./assets/images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="./assets/images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="./assets/images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="./assets/images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="./assets/images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="./assets/images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="./assets/images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="./assets/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="./assets/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Latest compiled and minified CSS -->
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/js/bootstrap.min.js">
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- StyleSheet link CSS -->
    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link href="assets/css/mediaqueries.css" rel="stylesheet" type="text/css">
    <link href="assets/css/owl.carousel.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/owl.theme.default.min.css" rel="stylesheet" type="text/css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <style>
        /* General alert styling */
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            font-size: 17px;
            font-weight: 500;
            text-align: left;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            margin: 0 auto;
        }

        /* Success alert */
        .success-alert {
            background-color: #e6f9e6;
            /* Light green background */
            border: 1px solid #28a745;
            /* Green border */
            color: #28a745;
            /* Green text */
        }

        /* Error alert */
        .error-alert {
            background-color: #ffe7e7;
            border: 1px solid #e13a3b;
            color: #e13a3b;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.6);
            position: relative;
            z-index: 2;
        }

        .navbar::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('./assets/images/your-banner-image.jpg') center/cover;
            z-index: -1;
        }

        .navbar-nav {
            margin-left: auto;
        }

        .nav-item {
            margin-right: 15px;
        }

        .navbar-nav .nav-link {
            color: #fff;
        }

        .navbar-nav .nav-link:hover {
            color: #007bff;
        }

        .banner-section {
            position: relative;
            z-index: 1;
            /* Ensure the banner content is above the background image */
        }

        .banner_video {
            position: relative;
            z-index: 1;
            /* Ensure the video icon is above the background image */
        }

        /* Subscription Section */
        .subscription-section {
            background-color: #fafafa;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            background-color: gray;
            color: white;
        }

        .text-center {
            text-align: center;
        }

        .table {
            border: 1px solid #007bff;
            width: 100%;
        }

        .table th,
        .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        /* Status-specific styling */
        .status-approved {
            color: green;
            font-weight: bold;
        }

        .status-expired {
            color: gray;
            font-weight: bold;
        }

        .status-disapproved {
            color: red;
            font-weight: bold;
        }

        /* Plan-specific styling */
        .plan-elite,
        .plan-premium,
        .plan-essential {
            color: #9370DB;
            font-weight: bold;
        }

        /* Calculator Section */
        .calculator-section {
            text-align: center;
            padding: 50px 20px;
            /* Adjusted for smaller screens */
            background-color: #f4f6f9;
        }

        .calculator-form {
            max-width: 70%;
            /* Allow it to fill the width */
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
            margin-bottom: 20px;
            text-align: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }


        /* Optional: If you want to adjust for smaller screens */
        @media (max-width: 768px) {

            .calculator-form,
            .results-form {
                max-width: 90%;
                /* Allow more width on smaller screens */
                padding: 15px;
                /* Further reduced padding */
            }
        }

        .results-form {
            width: 100%;
            /* Allow it to fill the width */
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
            margin-bottom: 20px;
            text-align: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .calculator-form h2,
        .results-form h2 {
            margin-bottom: 20px;
            font-size: 2.5rem;
            /* Adjusted font size for better scaling */
            color: #333;
            font-weight: 600;
        }

        .calculator-form label,
        .results-form label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            font-size: 1rem;
            /* Use rem for better scaling */
            color: #555;
        }

        .calculator-form input,
        .calculator-form select,
        .results-form input,
        .results-form select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .calculator-form button,
        .results-form button {
            background-color: #007bff;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .calculator-form button:hover,
        .results-form button:hover {
            background-color: #0056b3;
        }

        .btn-recalculate[disabled] {
            background-color: #a8d0ff;
            /* Lighter shade */
            color: #fff;
            cursor: not-allowed;
        }

        .disabled-message {
            color: #ff7043;
            font-size: 14px;
            margin-top: 5px;
        }

        .infographic-section {
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.5s ease;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            height: 65%;
            text-align: left;
            margin-top: 30px;
        }

        .infographic-section.active {
            opacity: 1;
            transform: translateX(0);
        }

        .infographic-section h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        .infographic-section p {
            font-size: 17px;
            line-height: 1.6;
            color: #555;
        }

        .infographic-section img {
            display: none;
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
        }

        .calculator-results,
        .result-container {
            margin-top: 30px;
            text-align: left;
        }

        .result-container h2 {
            margin-top: 20px;
            margin-bottom: 30px;
            font-size: 22px;
            color: #333;
        }

        .horizontal-display {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            /* Add horizontal scrolling if needed */
        }

        .diet-planning {
            margin: 0 auto;
        }

        .diet-horizontal-display {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            width: 1870px;
            /* Adjust as needed */
            max-width: 100%;
            /* Adjust as needed */
            text-align: center;
        }

        .mealItem {
            cursor: pointer;
        }

        .mealItem.consumed {
            background-color: green;
            color: white;
        }

        .note {
            font-size: 1.0em;
            text-align: center;
            position: relative;
        }

        .border-mealplan {
            border: 2px solid #d9d9d9;
            margin-right: 20px;
            /* Adjust the margin value as needed */
        }

        .large-counter-text {
            font-size: 1.2em;
            /* Adjust the size as needed */
            font-weight: bold;
        }

        .exercise-planning {
            margin: 0 auto;
        }

        .exercise-horizontal-display {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            width: 1870px;
            /* Adjust as needed */
            max-width: 100%;
            /* Adjust as needed */
            text-align: center;
        }

        .exerciseItem {
            cursor: pointer;
        }

        .exerciseItem.completed {
            background-color: green;
            color: white;
        }

        .blurred {
            filter: blur(5px);
            pointer-events: none;
            /* Disable interaction with blurred exercises */
        }

        .large-counter-text {
            font-size: 1.2em;
            /* Adjust as needed */
            font-weight: bold;
        }

        .quote_image img {
            border-radius: 50%;
            overflow: hidden;
            /* Ensure the image stays within the circular boundary */
            width: 100px;
            /* Set the desired width */
            height: 100px;
            /* Set the desired height */
            object-fit: cover;
            /* Maintain the aspect ratio and cover the container */
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {

            .calculator-form,
            .results-form {
                padding: 20px;
                /* Adjust padding for smaller screens */
            }

            .calculator-form h2,
            .results-form h2 {
                font-size: 2rem;
                /* Smaller font size for mobile */
            }

            .calculator-form label,
            .results-form label {
                font-size: 0.9rem;
                /* Slightly smaller labels */
            }

            .calculator-section {
                padding: 30px 15px;
                /* Adjust overall section padding */
            }

            .btn-calculate,
            .btn-recalculate {
                width: 100%;
                /* Full-width buttons on smaller screens */
            }

            /* Infographic section */
            .infographic-section {
                height: auto;
                /* Ensure it can expand on mobile */
                text-align: center;
                /* Center the content */
            }
        }

        /* Add print styles */
        @media print {
            #resultsSection {
                position: relative;
                left: 0;
                top: 0;
                width: 100%;
            }

            /* Optional: Style adjustments to match on-screen layout */
            .container {
                padding: 20px;
                margin: 0 auto;
            }

            .results-form {
                border: 1px solid #ddd;
                padding: 15px;
                margin-bottom: 20px;
            }

            /* Ensure headings maintain the same size and spacing */
            h2 {
                font-size: 24px;
                margin-bottom: 10px;
            }

            p {
                margin: 5px 0;
            }

            /* Hide the print button */
            button {
                display: none;
            }
        }

        #tooltip {
            display: none;
            position: absolute;
            padding: 5px;
            background-color: rgba(0, 0, 0, 0.75);
            color: white;
            border-radius: 5px;
            font-size: 15px !important;
            /* Add !important to force the change */
            pointer-events: none;
            z-index: 1000;
        }

        .goal-maintenance {
            color: orange;
        }

        .goal-weight-loss {
            color: red;
        }

        .goal-weight-gain {
            color: green;
        }
    </style>

</head>

<body>
    <div class="sub-banner-section about-banner-section">
        <header>
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Logo outside the navbar -->
                    <a class="navbar-brand mb-0" href="./index.php">
                        <figure class="mb-0">
                            <img src="./assets/images/fitlife_pro_logo2.png" alt="" class="img-fluid">
                        </figure>
                    </a>
                    <!-- Navbar -->
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                            <span class="navbar-toggler-icon"></span>
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="./index.php">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./about.php">About</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./services.php">Services</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./collaborators.php">Collaborators</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./indexforum.php">Forums</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link contact_btn" href="./contact.php">Contact</a>
                                </li>
                                <?php
                                //  holds the current page name
                                $currentPage = basename($_SERVER['PHP_SELF']);

                                if (isset($_SESSION['username'])) {

                                    // Check if the current page is the profile.php, set as active
                                    $profileClass = ($currentPage == 'profile.php') ? 'active' : '';
                                    echo '<li class="nav-item ' . $profileClass . '"><a class="nav-link" href="profile.php">' . $_SESSION['username'] . '</a></li>';

                                    echo '<li class="nav-item"><a class="nav-link login_btn" href="logout.php">Logout</a></li>';
                                } else {
                                    // If user is not logged in, show login and register buttons
                                    echo '<li class="nav-item"><a class="nav-link login_btn" href="./login.html">Login</a></li>';
                                    echo '<li class="nav-item"><a class="nav-link login_btn" href="./register.html">Register</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </header>


        <!-- SUB BANNER SECTION -->
        <section class="banner-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="banner-section-content">
                            <!-- Display Profile Image or Example Image -->
                            <div style="color: white; text-align: center;">
                                <?php if ($profile_image_path): ?>
                                    <img src="<?php echo htmlspecialchars($profile_image_path); ?>" alt="Profile Image"
                                        style="max-width: 100px; margin-bottom: 10px;">
                                <?php else: ?>
                                    <img src="example_profile_image.jpg" alt="No Profile Image"
                                        style="max-width: 300px; margin-bottom: 10px;">
                                    <p>No profile image uploaded</p>
                                <?php endif; ?>

                                <!-- Profile Picture Upload Form -->
                                <form action="upload_profile.php" method="post" enctype="multipart/form-data">
                                    <input type="file" name="profile_image" accept="image/*" required>
                                    <button type="submit" name="submit">Upload</button>
                                </form>
                            </div>

                            <h1 data-aos="fade-up">
                                <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Log-in'; ?>
                            </h1>
                            <div class="btn_wrapper">
                                <span class="sub_home_span">Home </span>-<span class="sub_span"> Profile</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- Subscription Info Section -->
    <?php if ($showSubscriptionInfoSection): ?>
        <div class="container-fluid subscription-section shadow-container">
            <table class="table table-bordered table-striped mt-4">
                <thead class="table-header">
                    <tr>
                        <th>Plan</th>
                        <th>Description</th>
                        <th>Date Requested</th>
                        <th>Date End</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($userTransactionsResult && $userTransactionsResult->num_rows > 0) {
                        $row = $userTransactionsResult->fetch_assoc(); // Fetch the latest transaction
                
                        // Handle undefined keys
                        $plan = isset($row["plan"]) ? $row["plan"] : "N/A";
                        $description = isset($row["description"]) ? $row["description"] : "N/A";
                        $created_at = isset($row["created_at"]) ? date("F j, Y | g:i A", strtotime($row["created_at"])) : "N/A";
                        $date_end = isset($row["date_end"]) ? date("F j, Y | g:i A", strtotime($row["date_end"])) : "N/A";
                        $status = isset($row["status"]) ? $row["status"] : "N/A";

                        // Determine the status class
                        $statusClass = '';
                        if ($status === 'Approved') {
                            $statusClass = 'status-approved';
                        } elseif ($status === 'Expired') {
                            $statusClass = 'status-expired';
                        } elseif ($status === 'Disapproved') {
                            $statusClass = 'status-disapproved';
                        }

                        // Determine the plan class
                        $planClass = '';
                        if ($plan === 'elite') {
                            $planClass = 'plan-elite';
                        } elseif ($plan === 'premium') {
                            $planClass = 'plan-premium';
                        } elseif ($plan === 'essential') {
                            $planClass = 'plan-essential';
                        }

                        // Display the latest transaction with colored Plan and Description
                        echo "<tr>
                        <td class='text-center " . $planClass . "'>" . htmlspecialchars($plan) . "</td>
                        <td class='text-center " . $planClass . "'>" . htmlspecialchars($description) . "</td>
                        <td class='text-center'>" . htmlspecialchars($created_at) . "</td>
                        <td class='text-center'>" . htmlspecialchars($date_end) . "</td>
                        <td class='text-center " . $statusClass . "'>" . htmlspecialchars($status) . "</td>
                    </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!DOCTYPE html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BMI, Body Fat, Calorie and Protein Intake Calculator</title>
    </head>

    <!-- Calculator Form Section -->
    <section class="calculator-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-sm-12">
                    <div class="calculator-form form-section">
                        <h2>BMI, Body Fat, Calorie, and Protein Intake Calculator</h2>

                        <!-- Combined Form -->
                        <form method="post" action="" id="calculatorForm">
                            <!-- BMI Section -->
                            <label for="bmiWeight">Weight (kg):</label>
                            <input type="number" name="bmiWeight" id="bmiWeight" min="5"
                                value="<?php echo isset($bmiWeight) ? $bmiWeight : ''; ?>" required>

                            <label for="bmiHeight">Height (cm):</label>
                            <input type="number" name="bmiHeight" id="bmiHeight" min="5"
                                value="<?php echo isset($bmiHeight) ? $bmiHeight : ''; ?>" required>

                            <!-- Body Fat Calculator Section -->
                            <label for="age">Age:</label>
                            <input type="number" id="age" name="age" min="5"
                                value="<?php echo isset($age) ? $age : ''; ?>" required>

                            <label for="waist">Waist (cm):</label>
                            <input type="number" id="waist" name="waist" min="5"
                                value="<?php echo isset($waist) ? $waist : ''; ?>" required>

                            <label for="neck">Neck (cm):</label>
                            <input type="number" id="neck" name="neck" min="5"
                                value="<?php echo isset($neck) ? $neck : ''; ?>" required>

                            <!-- Conditional Display Based on Gender -->
                            <?php if (isset($gender) && $gender === 'female'): ?>
                                <div id="hipSection">
                                    <label for="hip">Hip Circumference (cm):</label>
                                    <input type="number" id="hip" name="hip" min="5"
                                        value="<?php echo isset($hip) ? $hip : ''; ?>" required>

                                    <label for="thigh">Thigh Circumference (cm):</label>
                                    <input type="number" id="thigh" name="thigh" min="5"
                                        value="<?php echo isset($thigh) ? $thigh : ''; ?>" required>
                                </div>
                            <?php endif; ?>

                            <label for="activityLevel">Lifestyle:</label>
                            <select name="activityLevel" id="activityLevel" required>
                                <option value="sedentary" <?php echo isset($activityLevel) && $activityLevel === 'sedentary' ? 'selected' : ''; ?>>
                                    Sedentary
                                </option>
                                <option value="active" <?php echo isset($activityLevel) && $activityLevel === 'active' ? 'selected' : ''; ?>>
                                    Active
                                </option>
                            </select>

                            <label for="fitnessLevel">Fitness Level:</label>
                            <select name="fitnessLevel" id="fitnessLevel" required>
                                <option value="beginner" <?php echo isset($fitnessLevel) && $fitnessLevel === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                <option value="intermediate" <?php echo isset($fitnessLevel) && $fitnessLevel === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                <option value="advanced" <?php echo isset($fitnessLevel) && $fitnessLevel === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                            </select>

                            <!-- Show Calculate button if no data from DB is showing -->
                            <?php if (!isset($bodyFatResults) && !isset($intakeResults)): ?>
                                <button type="submit" class="btn-calculate">Calculate</button>
                            <?php else: ?>
                                <!-- Show Re-calculate button with conditional disabling -->
                                <button type="submit" id="recalculateButton" class="btn-recalculate" <?php echo $disableButton ? 'disabled' : ''; ?>>
                                    Re-calculate
                                </button>
                                <!-- Display remaining days message if button is disabled -->
                                <?php if ($disableButton): ?>
                                    <p class="disabled-message">Re-calculate button will be enabled after
                                        <?php echo $disabledDays; ?> days. <br>
                                        <?php echo $remainingDays; ?> days remaining.
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Infographic Section (Initially hidden) -->
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div id="infographicSection" class="infographic-section">
                        <h3 style="text-align: center;">Input Details:</h3>
                        <p id="infoContent">Click an input field to see its details.</p>
                        <img id="infoImage" src="" alt="Infographic Image">
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- BMI Results Section -->
    <div class="our_schedule_content">
        <div id="resultsSection">
            <div class="results-container" style="border: 1px solid #ddd; padding: 15px;">
                <div class="our_schedule_content">
                    <?php
                    // Set the time zone to ensure the correct time is displayed
                    date_default_timezone_set('Asia/Manila'); // e.g., 'America/New_York'
                    
                    // Set the formatted date based on the source of the data
                    if (isset($intakeResults)) {
                        // Use the current session's date and time
                        $formattedDate = date("F j, Y | g:i A"); // Today's date and time for session data
                    } elseif (isset($bodyFatResults) && isset($bodyFatResults['dateRecorded'])) {
                        // Use the date recorded from the database, if available
                        $formattedDate = date("F j, Y | g:i A", strtotime($bodyFatResults['dateRecorded']));
                    }
                    ?>

                    <?php if (isset($intakeResults)): ?>
                        <!-- Display for Current Session Data -->
                        <h5 class="mt-3" style="font-size: 50px;">RECOMMENDED GOAL<br></h5>
                        <h2 data-aos="fade-up" style="font-size: 50px;" class="mt-3">
                            <?php
                            $goal = strtolower($intakeResults['goal']);
                            if ($goal === 'maintenance') {
                                echo '<span style="color: #007bff;">↔️</span> MAINTENANCE <span style="color: #007bff;">↔️</span>';
                            } elseif ($goal === 'weight-loss') {
                                echo '<span style="color: #ff0000;">⬇️</span> WEIGHT-LOSS <span style="color: #ff0000;">⬇️</span>';
                            } elseif ($goal === 'weight-gain') {
                                echo '<span style="color: #00cc44;">⬆️</span> WEIGHT-GAIN <span style="color: #00cc44;">⬆️</span>';
                            } else {
                                echo strtoupper(str_replace('-', ' ', $intakeResults['goal']));
                            }
                            ?>
                        </h2>

                        <h5 style="font-size: 50px;">AS OF</h5><br>
                        <h2 data-aos="fade-up" style="font-size: 50px;"><u><?php echo $formattedDate; ?></u></h2>

                    <?php elseif (isset($bodyFatResults)): ?>
                        <!-- Display for Database Data -->
                        <h5 class="mt-3" style="font-size: 50px;">RECOMMENDED GOAL<br></h5>
                        <h2 data-aos="fade-up" style="font-size: 50px;" class="mt-3">
                            <?php
                            $goal = strtolower($bodyFatResults['recommendedGoal']);
                            if ($goal === 'maintenance') {
                                echo '<span style="color: #007bff;">↔️</span> MAINTENANCE <span style="color: #007bff;">↔️</span>';
                            } elseif ($goal === 'weight-loss') {
                                echo '<span style="color: #ff0000;">⬇️</span> WEIGHT-LOSS <span style="color: #ff0000;">⬇️</span>';
                            } elseif ($goal === 'weight-gain') {
                                echo '<span style="color: #00cc44;">⬆️</span> WEIGHT-GAIN <span style="color: #00cc44;">⬆️</span>';
                            } else {
                                echo strtoupper(str_replace('-', ' ', $bodyFatResults['recommendedGoal']));
                            }
                            ?>
                        </h2>
                        <h5 style="font-size: 50px;">AS OF</h5><br>
                        <h2 data-aos="fade-up" style="font-size: 50px;"><u><?php echo $formattedDate; ?></u></h2>
                        <h5 style="font-size: 40px;"><?php echo $daysAgoMessage; ?></h5>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="lower-section">
        <div class="horizontal-display">
            <section class="calculator-results">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                            <!-- Current Session Results -->
                            <?php if (isset($intakeResults)): ?>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="results-form form-section">
                                                <h2>Current BMI:</h2>
                                                <?php if (isset($intakeResults['weight'])): ?>
                                                    <p><strong>Your Weight:</strong>
                                                        <?php echo $intakeResults['weight']; ?> kg</p>
                                                <?php endif; ?>
                                                <?php if (isset($intakeResults['height'])): ?>
                                                    <p><strong>Your Height:</strong>
                                                        <?php echo $intakeResults['height']; ?> cm</p>
                                                <?php endif; ?>
                                                <p><strong>Your BMI:</strong>
                                                    <?php echo number_format($bmiResults['bmi'], 2); ?>
                                                </p>
                                                <p><strong>Category:
                                                        <?php
                                                        $bmiCategory = strtolower($bmiCategory);
                                                        switch ($bmiCategory) {
                                                            case 'underweight':
                                                                echo '<span style="color: #1e90ff;">UNDERWEIGHT</span>'; // Light blue
                                                                break;
                                                            case 'normal':
                                                                echo '<span style="color: #32cd32;">NORMAL</span>'; // Green
                                                                break;
                                                            case 'overweight':
                                                                echo '<span style="color: #ffa500;">OVERWEIGHT</span>'; // Orange
                                                                break;
                                                            case 'obese':
                                                                echo '<span style="color: #ff0000;">OBESE</span>'; // Red
                                                                break;
                                                            default:
                                                                echo strtoupper($bmiCategory); // Fallback for unhandled categories
                                                        }
                                                        ?>
                                                    </strong>
                                                </p>
                                                <?php
                                                $bmiDifference = $bmiResults['bmiDifference'];
                                                $lowerUnderweightRange = $bmiResults['bmi'] - $bmiDifference['underweight'];
                                                ?>
                                                <p><strong>UNDERWEIGHT BMI:</strong> 18.50 & below /
                                                    (<?php echo number_format(getWeightFromBMI(18.5, $bmiHeight), 2); ?>
                                                    kg & below)
                                                </p>
                                                <?php
                                                $lowerNormalRange = 18.5;
                                                $upperNormalRange = 24.9;
                                                $lowerOverweightRange = 25;
                                                $upperOverweightRange = 29.9;
                                                $lowerObeseRange = 30;
                                                ?>
                                                <p><strong>NORMAL BMI:</strong> 18.50 - 24.99 /
                                                    (<?php echo number_format(getWeightFromBMI($lowerNormalRange, $bmiHeight), 2); ?>
                                                    kg -
                                                    <?php echo number_format(getWeightFromBMI($lowerOverweightRange, $bmiHeight), 2); ?>
                                                    kg)
                                                </p>
                                                <p><strong>OVERWEIGHT BMI:</strong> 25 - 29.99 /
                                                    (<?php echo number_format(getWeightFromBMI($lowerOverweightRange, $bmiHeight), 2); ?>
                                                    kg -
                                                    <?php echo number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2); ?>
                                                    kg)
                                                </p>
                                                <p><strong>OBESE BMI:</strong>
                                                    <?php echo number_format($lowerObeseRange, 2); ?> & above /
                                                    (<?php echo number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2); ?>
                                                    kg & above)
                                                </p>

                                            <?php elseif (isset($bodyFatResults)): ?>
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <div class="results-form form-section">
                                                                <h2>BMI:</h2>
                                                                <p><strong>Your Weight:</strong>
                                                                    <?php echo $savedUserInfo['bmiWeight']; ?> kg
                                                                </p>
                                                                <p><strong>Your Height:</strong>
                                                                    <?php echo $savedUserInfo['bmiHeight']; ?> cm
                                                                </p>
                                                                <p><strong>Your BMI:</strong>
                                                                    <?php echo number_format($bodyFatResults['bmi'], 2); ?>
                                                                    cm
                                                                </p>
                                                                <p><strong>Category:
                                                                        <?php
                                                                        $bodyFatBmiCategory = strtolower($bodyFatResults['bmiCategory']);
                                                                        switch ($bodyFatBmiCategory) {
                                                                            case 'underweight':
                                                                                echo '<span style="color: #1e90ff;">UNDERWEIGHT</span>';
                                                                                break;
                                                                            case 'normal':
                                                                                echo '<span style="color: #32cd32;">NORMAL</span>';
                                                                                break;
                                                                            case 'overweight':
                                                                                echo '<span style="color: #ffa500;">OVERWEIGHT</span>';
                                                                                break;
                                                                            case 'obese':
                                                                                echo '<span style="color: #ff0000;">OBESE</span>';
                                                                                break;
                                                                            default:
                                                                                echo strtoupper($bodyFatBmiCategory);
                                                                        }
                                                                        ?>
                                                                    </strong>
                                                                </p>
                                                                <p><strong>UNDERWEIGHT BMI:</strong> 18.50 & below /
                                                                    (<?php echo number_format(getWeightFromBMI(18.5, $bmiHeight), 2); ?>
                                                                    kg & below)
                                                                </p>
                                                                <?php
                                                                $lowerNormalRange = 18.5;
                                                                $upperNormalRange = 24.9;
                                                                $lowerOverweightRange = 25;
                                                                $upperOverweightRange = 29.9;
                                                                $lowerObeseRange = 30;
                                                                ?>
                                                                <p><strong>NORMAL BMI:</strong> 18.50 - 24.99 /
                                                                    (<?php echo number_format(getWeightFromBMI($lowerNormalRange, $bmiHeight), 2); ?>
                                                                    kg -
                                                                    <?php echo number_format(getWeightFromBMI($lowerOverweightRange, $bmiHeight), 2); ?>
                                                                    kg)
                                                                </p>
                                                                <p><strong>OVERWEIGHT BMI:</strong> 25 - 29.99 /
                                                                    (<?php echo number_format(getWeightFromBMI($lowerOverweightRange, $bmiHeight), 2); ?>
                                                                    kg -
                                                                    <?php echo number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2); ?>
                                                                    kg)
                                                                </p>
                                                                <p><strong>OBESE BMI:</strong>
                                                                    <?php echo number_format($lowerObeseRange, 2); ?>
                                                                    & above /
                                                                    (<?php echo number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2); ?>
                                                                    kg & above)
                                                                </p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
            </section>

            <!-- Body Fat Results Section -->
            <section class="calculator-results">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="results-form form-section">

                                <?php if (isset($intakeResults)): ?>
                                    <!-- SESSION DATA -->
                                    <h2>Current Body Fat:</h2>
                                    <p><strong>Age:</strong> <?php echo $age; ?> years</p>
                                    <p><strong>Gender:</strong> <?php echo ucfirst($gender); ?></p>
                                    <p><strong>Waist Circumference:</strong> <?php echo $waist; ?> cm</p>
                                    <p><strong>Neck Circumference:</strong> <?php echo $neck; ?> cm</p>
                                    <?php if (isset($hip) && $hip !== '' && $hip != 0): ?>
                                        <p><strong>Hip Circumference:</strong> <?php echo $hip; ?> cm</p>
                                    <?php endif; ?>
                                    <?php if (isset($thigh) && $thigh !== '' && $thigh != 0): ?>
                                        <p><strong>Thigh Circumference:</strong> <?php echo $thigh; ?> cm</p>
                                    <?php endif; ?>
                                    <p><strong>Body Fat Percentage:</strong>
                                        <?php echo number_format($bodyFatPercentage, 2); ?>%</p>
                                    <p><strong>Fat Body Mass:</strong> <?php echo number_format($fatMass, 2); ?> kg
                                    </p>
                                    <p><strong>Lean Body Mass:</strong> <?php echo number_format($leanMass, 2); ?>
                                        kg</p>
                                    <p><strong>Weight / Total Body Mass:</strong> <?php echo $bmiWeight; ?> kg</p>

                                    <!-- Check for negative values in session data -->
                                    <?php
                                    $hasNegativeValueSession = $age < 0 || $waist < 0 || $neck < 0 || $bmiHeight < 0 || $bodyFatPercentage < 0 || $fatMass < 0 || $leanMass < 0 || (isset($hip) && $hip < 0) || (isset($thigh) && $thigh < 0);
                                    ?>

                                    <p><strong>Important Note:</strong>
                                        <?php if ($hasNegativeValueSession): ?>
                                            Seeing negative values? There might be inaccurate body measurements.
                                            <br>Click <button type="button" onclick="unlockCalculatorFields()"
                                                class="btn btn-secondary btn-sm">Re-enter Values</button> to adjust.
                                        <?php endif; ?>
                                        <br><br>
                                        The results of these calculations are estimates based on many assumptions.
                                        For accurate measurements of body fat, consider using tools like a skin
                                        caliper, bioelectric impedance analysis, or hydrostatic density testing.
                                    </p>

                                <?php elseif (isset($bodyFatResults)): ?>
                                    <!-- DATABASE DATA -->
                                    <h2>Body Fat:</h2>
                                    <p><strong>Age:</strong> <?php echo $savedUserInfo['age']; ?> years</p>
                                    <p><strong>Gender:</strong> <?php echo ucfirst($savedUserInfo['gender']); ?></p>
                                    <p><strong>Waist Circumference:</strong> <?php echo $savedUserInfo['waist']; ?>
                                        cm</p>
                                    <p><strong>Neck Circumference:</strong> <?php echo $savedUserInfo['neck']; ?> cm
                                    </p>
                                    <?php if (isset($hip) && $hip !== '' && $hip != 0): ?>
                                        <p><strong>Hip Circumference:</strong> <?php echo $hip; ?> cm</p>
                                    <?php endif; ?>
                                    <?php if (isset($thigh) && $thigh !== '' && $thigh != 0): ?>
                                        <p><strong>Thigh Circumference:</strong> <?php echo $thigh; ?> cm</p>
                                    <?php endif; ?>
                                    <p><strong>Body Fat Percentage:</strong>
                                        <?php echo number_format($bodyFatResults['bodyFatPercentage'], 2); ?>%</p>
                                    <p><strong>Fat Body Mass:</strong>
                                        <?php echo number_format($bodyFatResults['fatMass'], 2); ?> kg</p>
                                    <p><strong>Lean Body Mass:</strong>
                                        <?php echo number_format($bodyFatResults['leanMass'], 2); ?> kg</p>
                                    <p><strong>Weight / Total Body Mass:</strong>
                                        <?php echo $savedUserInfo['bmiWeight']; ?> kg</p>

                                    <!-- Check for negative values in database data -->
                                    <?php
                                    $hasNegativeValueDatabase = $savedUserInfo['age'] < 0 || $savedUserInfo['waist'] < 0 || $savedUserInfo['neck'] < 0 || $savedUserInfo['bmiHeight'] < 0 || $bodyFatResults['bodyFatPercentage'] < 0 || $bodyFatResults['fatMass'] < 0 || $bodyFatResults['leanMass'] < 0 || (isset($hip) && $hip < 0) || (isset($thigh) && $thigh < 0);
                                    ?>

                                    <p><strong>Important Note:</strong>
                                        <?php if ($hasNegativeValueDatabase): ?>
                                            Seeing negative values? There might be inaccurate body measurements.
                                            <br>Click <button type="button" onclick="unlockCalculatorFields()"
                                                class="btn btn-secondary btn-sm">Re-enter Values</button> to adjust.
                                        <?php endif; ?>
                                        <br><br>
                                        The results of these calculations are estimates based on many assumptions.
                                        For accurate measurements of body fat, consider using tools like a skin
                                        caliper, bioelectric impedance analysis, or hydrostatic density testing.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <script>
                function unlockCalculatorFields() {
                    // Unlock all input fields, selects, and the Recalculate button in the calculator form
                    document.querySelectorAll('#calculatorForm input, #calculatorForm select').forEach(field => {
                        field.disabled = false;
                    });
                    document.querySelector('#recalculateButton').disabled = false; // Enable the recalculate button
                }
            </script>


            <!-- Caloric and Protein Intake Results Section -->
            <section class="calculator-results">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="results-form form-section">
                                <?php if (isset($intakeResults)): ?>
                                    <!-- SESSION STUFF -->
                                    <h2>Recommended Daily Calorie and Protein Intake:</h2>
                                    <p><strong>Goal:</strong>
                                        <?php echo ucwords(str_replace('-', ' ', $intakeResults['goal'])); ?>
                                    </p>
                                    <p><strong>Caloric Intake:</strong>
                                        <?php echo number_format($intakeResults['caloricIntake']); ?> calories/day
                                    </p>
                                    <p><strong>Protein Intake:</strong>
                                        <?php echo number_format($intakeResults['proteinIntake']); ?> grams/day</p>
                                    <p><strong>Important Note:</strong> You can find the caloric and protein
                                        contents of
                                        the foods you eat on the nutrition labels on the packages.</p>
                                <?php elseif (isset($bodyFatResults)): ?>
                                    <!-- DATABASE STUFF -->
                                    <h2>Recommended Daily Calorie and Protein Intake:</h2>
                                    <p><strong>Goal:</strong>
                                        <?php echo ucwords(str_replace('-', ' ', $bodyFatResults['recommendedGoal'])); ?>
                                    </p></u>
                                    <p><strong>Caloric Intake:</strong>
                                        <?php echo number_format($bodyFatResults['caloricIntake']); ?> calories/day
                                    </p>
                                    <p><strong>Protein Intake:</strong>
                                        <?php echo number_format($bodyFatResults['proteinIntake']); ?> grams/day</p>
                                    <p><strong>Important Note:</strong> You can find the caloric and protein
                                        contents of
                                        the foods you eat on the nutrition labels on the packages.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Food Recommendations Section -->
            <section class="calculator-results">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="results-form form-section">
                                <?php if (isset($intakeResults)): ?>
                                    <!-- SESSION STUFF -->
                                    <h2>Current Food Recommendations:</h2>
                                    <h2><u>
                                            <?php echo strtoupper(str_replace('-', ' ', $intakeResults['goal'])); ?>
                                        </u></h2>
                                    <ul>
                                        <?php if ($goal === 'weight-loss'): ?>
                                            <?php
                                            $weightlossRecommendations = [
                                                'Chicken breast',
                                                'Fish (tuna, tilapia, salmon)',
                                                'Eggs',
                                                'Spinach',
                                                'Avocado',
                                                'Oats',
                                                'Cottage Cheese',
                                                'Greek Yogurt',
                                                'Milk',
                                                'Nuts and seeds (walnuts, almonds, pumpkin seeds, sunflower seeds)',
                                                'Sweet potato',
                                                'Vegetables (broccoli, bell peppers, onions, green beans, asparagus)',
                                                'Fruits (bananas, apples, oranges, blueberries)'
                                            ];
                                            ?>
                                            <p>Weight loss involves reducing overall body weight through a
                                                combination of a calorie deficit, dietary changes, and increased
                                                physical
                                                activity. The goal
                                                is to improve health and fitness by shedding excess fat and achieving a
                                                healthier body composition.</p>
                                            <?php foreach ($weightlossRecommendations as $recommendation): ?>
                                                <li><?php echo $recommendation; ?></li>
                                            <?php endforeach; ?>
                                        <?php elseif ($goal === 'weight-gain'): ?>
                                            <?php
                                            $weightgainRecommendations = [
                                                'Steak',
                                                'Ground beef',
                                                'Potatoes',
                                                'Rice',
                                                'Sweet potato',
                                                'Whole wheat or wheat bread',
                                                'Peanut butter'
                                            ];
                                            ?>
                                            <p>Weight-gain involves increasing calorie intake and adopting a balanced
                                                diet
                                                to achieve a healthy body mass. Incorporating strength training
                                                exercises
                                                can promote muscle growth.</p>
                                            <?php foreach ($weightgainRecommendations as $recommendation): ?>
                                                <li><?php echo $recommendation; ?></li>
                                            <?php endforeach; ?>
                                        <?php elseif ($goal === 'maintenance'): ?>
                                            <?php
                                            $maintenanceRecommendations = [
                                                'Chicken breast',
                                                'Fish (tuna, tilapia, salmon)',
                                                'Eggs',
                                                'Quinoa',
                                                'Brown rice',
                                                'Mixed vegetables',
                                                'Fruits (bananas, apples, oranges, blueberries)',
                                                'Nuts and seeds',
                                                'Greek yogurt',
                                                'Whole grains'
                                            ];
                                            ?>
                                            <p>Maintenance involves sustaining your current weight and body composition
                                                by
                                                balancing caloric intake with energy expenditure. Focus on a varied and
                                                balanced diet to maintain overall health.</p>
                                            <?php foreach ($maintenanceRecommendations as $recommendation): ?>
                                                <li><?php echo $recommendation; ?></li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                <?php elseif (isset($bodyFatResults)): ?>
                                    <!-- DATABASE STUFF -->
                                    <h2>Food Recommendations:</h2>
                                    <h2><u>
                                            <?php echo strtoupper(str_replace('-', ' ', $bodyFatResults['recommendedGoal'])); ?>
                                        </u></h2>
                                    <ul>
                                        <?php if ($bodyFatResults['recommendedGoal'] === 'weight-loss'): ?>
                                            <?php
                                            $weightlossRecommendations = [
                                                'Chicken breast',
                                                'Fish (tuna, tilapia, salmon)',
                                                'Eggs',
                                                'Spinach',
                                                'Avocado',
                                                'Oats',
                                                'Cottage Cheese',
                                                'Greek Yogurt',
                                                'Milk',
                                                'Nuts and seeds (walnuts, almonds, pumpkin seeds, sunflower seeds)',
                                                'Sweet potato',
                                                'Vegetables (broccoli, bell peppers, onions, green beans, asparagus)',
                                                'Fruits (bananas, apples, oranges, blueberries)'
                                            ];
                                            ?>
                                            <p>Weight loss involves reducing overall body weight through a
                                                combination of a calorie deficit, dietary changes, and increased
                                                physical
                                                activity. The goal
                                                is to improve health and fitness by shedding excess fat and achieving a
                                                healthier body composition.</p>
                                            <?php foreach ($weightlossRecommendations as $recommendation): ?>
                                                <li><?php echo $recommendation; ?></li>
                                            <?php endforeach; ?>
                                        <?php elseif ($bodyFatResults['recommendedGoal'] === 'weight-gain'): ?>
                                            <?php
                                            $weightgainRecommendations = [
                                                'Steak',
                                                'Ground beef',
                                                'Potatoes',
                                                'Rice',
                                                'Sweet potato',
                                                'Whole wheat or wheat bread',
                                                'Peanut butter'
                                            ];
                                            ?>
                                            <p>Weight-gain involves increasing calorie intake and adopting a balanced
                                                diet
                                                to achieve a healthy body mass. Incorporating strength training
                                                exercises
                                                can promote muscle growth.</p>
                                            <?php foreach ($weightgainRecommendations as $recommendation): ?>
                                                <li><?php echo $recommendation; ?></li>
                                            <?php endforeach; ?>
                                        <?php elseif ($bodyFatResults['recommendedGoal'] === 'maintenance'): ?>
                                            <?php
                                            $maintenanceRecommendations = [
                                                'Chicken breast',
                                                'Fish (tuna, tilapia, salmon)',
                                                'Eggs',
                                                'Quinoa',
                                                'Brown rice',
                                                'Mixed vegetables',
                                                'Fruits (bananas, apples, oranges, blueberries)',
                                                'Nuts and seeds',
                                                'Greek yogurt',
                                                'Whole grains'
                                            ];
                                            ?>
                                            <p>Maintenance involves sustaining your current weight and body composition
                                                by
                                                balancing caloric intake with energy expenditure. Focus on a varied and
                                                balanced diet to maintain overall health.</p>
                                            <?php foreach ($maintenanceRecommendations as $recommendation): ?>
                                                <li><?php echo $recommendation; ?></li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Save and Print Button -->
    <div class="container">
        <div class="row">
            <div class="calculator-form form-section border-0">
                <?php
                if (isset($intakeResults)) {
                    // Format the current date for display
                    $formattedDate = date("F j, Y | g:i A");

                    echo '
        <form id="resultsForm">
            <input type="hidden" name="bmi" id="bmi" value="' . $bmi . '">
            <input type="hidden" name="bmiCategory" id="bmiCategory" value="' . $bmiCategory . '">
            <input type="hidden" name="recommendedGoal" id="recommendedGoal" value="' . $recommendedGoal . '">
            <input type="hidden" name="bodyFatPercentage" id="bodyFatPercentage" value="' . $bodyFatPercentage . '">
            <input type="hidden" name="fatMass" id="fatMass" value="' . $fatMass . '">
            <input type="hidden" name="leanMass" id="leanMass" value="' . $leanMass . '">
            <input type="hidden" name="caloricIntake" id="caloricIntake" value="' . $caloricIntake . '">
            <input type="hidden" name="proteinIntake" id="proteinIntake" value="' . $proteinIntake . '">
                <input type="hidden" name="formattedDate" id="formattedDate" value="' . $formattedDate . '">
            <button type="button" id="saveButton">Save Results</button>                    
        </form>

        <div id="message"></div>
            <p><strong>To track your progress, remember to Save Results.</strong> <br>
            <small>Saved Results will be graphed and used to tailor Recommended Meal and Exercise Plans, and will appear on your profile upon revisit.</small></p>
            <p><strong>Results as of:</strong> ' . $formattedDate . '</p>';
                }
                ?>
                <button onclick="printResults()">Print</button>
            </div>
        </div>
    </div>

    <script>
        // Save results using AJAX
        document.getElementById('saveButton').addEventListener('click', function (event) {
            const formData = new FormData(document.getElementById('resultsForm'));

            fetch('store_results.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    const messageDiv = document.getElementById('message');
                    if (data.success) {
                        messageDiv.innerHTML = '<span style="color: #28a745;">' + data.message + '</span>';
                    } else {
                        messageDiv.innerHTML = '<span style="color: #e13a3b;">' + data.message + '</span>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('message').innerHTML = '<span style="color: #e13a3b;">An error occurred while saving the results.</span>';
                });
        });
    </script>


    <script>
        function printResults() {
            // Select main sections for printing
            const resultsSection = document.getElementById('resultsSection').innerHTML;
            const lowerSection = document.querySelector('.lower-section').innerHTML;

            // Combine sections with styles, hiding elements with the 'btn' class
            const printContent = `
        <html>
        <head>
            <title>Print Results</title>
            <style>
                /* Existing styles from the document */
                ${document.querySelector('style').innerHTML}

                /* Additional print-specific styling */
                .btn { display: none; } /* Hide buttons */
                
                /* Optional: Style adjustments to match on-screen layout */
                body { font-family: Arial, sans-serif; margin: 20px; padding: 20px; }
                .container { padding: 20px; margin: 0 auto; }
                .results-form, .calculator-form, .our_schedule_content { 
                    border: 1px solid #ddd; 
                    padding: 15px; 
                    margin-bottom: 20px; 
                    text-align: center; 
                }
                h2 { font-size: 24px; margin-bottom: 10px; }
                p { margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; }
                table, th, td { border: 1px solid black; padding: 10px; text-align: center; }
                th { background-color: #f0f0f0; color: black; }
            </style>
        </head>
        <body>
            ${resultsSection}
            ${lowerSection}
        </body>
        </html>
        `;

            // Open a new window for printing
            const printWindow = window.open('', '_blank', 'height=800,width=800');
            printWindow.document.write(printContent);
            printWindow.document.close();

            // Print after loading content
            printWindow.onload = function () {
                printWindow.print();
                printWindow.close();
            };
        }
    </script>



    <?php if ($showDietPlanningSection): ?>
        <div class="results-container" style="border: 1px solid #ddd; padding: 15px;">
            <div class="our_schedule_content" style="text-align: center;"> <!-- Center the content -->
                <?php if (!isset($intakeResults) && isset($bodyFatResults)): ?>
                    <h5 class="mt-5" style="font-size: 50px;">PROGRESS TRACKING</h5>
                    <h2 class="mt-5" style="font-size: 50px;"><u>
                            <?php echo strtoupper(str_replace('-', ' ', $bodyFatResults['recommendedGoal'])); ?>
                        </u></h2>
                </div>

                <!-- Recommended Goal Description Section -->
                <div id="goalDescription" style="margin-top: 20px; text-align: center;"> <!-- Centered description -->
                    <?php
                    $goalDescription = "";
                    if ($bodyFatResults['recommendedGoal'] === 'weight-loss') {
                        $goalDescription = "Your goal is weight loss. You should aim for a gradual decrease in weight, body fat percentage, fat mass, and BMI over time. In your chart, look for a downward trend in these metrics.";
                    } elseif ($bodyFatResults['recommendedGoal'] === 'weight-gain') {
                        $goalDescription = "Your goal is weight gain. You should aim for a gradual increase in weight and lean mass. In the chart, watch for an upward trend in weight and lean mass while keeping body fat percentage steady.";
                    } elseif ($bodyFatResults['recommendedGoal'] === 'maintenance') {
                        $goalDescription = "Your goal is maintenance. You should aim to keep your weight, BMI, and body fat percentage steady. Look for minimal fluctuations in your chart to ensure you're staying within your maintenance zone.";
                    }
                    ?>
                    <p style="font-size: 20px; line-height: 1.5; margin: 0 auto; max-width: 800px;">
                        <strong><?php echo $goalDescription; ?></strong>
                    </p>
                </div>

                <!-- Chart Section -->
                <div id="chartContainer" style="text-align: center; margin-top: 20px;"> <!-- Center the chart -->
                    <canvas id="bodyReportsChart" width="60" height="20"></canvas>

                </div>

                <!-- Chart Table Section -->
                <?php
                $previousData = null; // To hold the previous row data
                ?>

                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" id="progressTable">
                    <thead>
                        <tr>
                            <th
                                style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: #f0f0f0; color: black;">
                                Date</th>
                            <th
                                style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: rgba(153, 102, 255, 1); color: black;">
                                Weight</th>
                            <th
                                style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: rgba(75, 192, 192, 1); color: black;">
                                BMI</th>
                            <th
                                style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: rgba(255, 99, 132, 1); color: black;">
                                Body Fat Percentage</th>
                            <th
                                style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: rgba(54, 162, 235, 1); color: black;">
                                Fat Mass</th>
                            <th
                                style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: rgba(255, 206, 86, 1); color: black;">
                                Lean Mass</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mergedData as $data): ?>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold;">
                                    <?php echo date('M d, Y', strtotime($data['created_at'])); ?>
                                </td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                    <?php echo round($data['weight']); ?>
                                    <?php if ($previousData): ?>
                                        <?php echo getArrow(round($data['weight']), round($previousData['weight']), $bodyFatResults['recommendedGoal']); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                    <?php echo round($data['bmi']); ?>
                                    <?php if ($previousData): ?>
                                        <?php echo getArrow(round($data['bmi']), round($previousData['bmi']), $bodyFatResults['recommendedGoal']); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                    <?php echo round($data['bodyFatPercentage']); ?>
                                    <?php if ($previousData): ?>
                                        <?php echo getArrow(round($data['bodyFatPercentage']), round($previousData['bodyFatPercentage']), $bodyFatResults['recommendedGoal']); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                    <?php echo round($data['fatMass']); ?>
                                    <?php if ($previousData): ?>
                                        <?php echo getArrow(round($data['fatMass']), round($previousData['fatMass']), $bodyFatResults['recommendedGoal']); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                    <?php echo round($data['leanMass']); ?>
                                    <?php if ($previousData): ?>
                                        <?php echo getArrow(round($data['leanMass']), round($previousData['leanMass']), $bodyFatResults['recommendedGoal']); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                            $previousData = $data; // Set the current data as the previous data for the next iteration
                            ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Print Progress Button -->
                <div style="text-align: center; margin-top: 30px;">
                    <button onclick="printProgress();" class="btn-print"
                        style="background-color: #007bff; color: white; padding: 10px 20px; font-size: 18px; border: none; cursor: pointer;">
                        Print Progress
                    </button>
                </div>

                <style>
                    .green {
                        color: green;
                        font-weight: bold;
                    }

                    .red {
                        color: red;
                        font-weight: bold;
                    }
                </style>
            <?php endif; ?>
        </div>

        <!-- ACTIVITY FEED TRACKER -->
        <section class="activity-feed; mt-4">
            <h3>Daily Activity Tracker</h3>
            <div style="display: flex; justify-content: space-between;">
                <div style="width: 30%;">
                    <h4>Calories Consumed</h4>
                    <canvas id="caloriesChart" width="400" height="200"></canvas>
                </div>
                <div style="width: 30%;">
                    <h4>Protein Intake</h4>
                    <canvas id="proteinChart" width="400" height="200"></canvas>
                </div>
                <div style="width: 30%;">
                    <h4>Exercises Completed</h4>
                    <canvas id="exerciseChart" width="400" height="200"></canvas>
                </div>
            </div>
        </section>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            <?php
            // Determine exercise goal based on fitness level
            $exerciseGoal = 5; // Default goal
            switch (strtolower($fitnessLevel)) {
                case 'beginner':
                    $exerciseGoal = 3;
                    break;
                case 'intermediate':
                    $exerciseGoal = 5;
                    break;
                case 'advanced':
                    $exerciseGoal = 7;
                    break;
                default:
                    $exerciseGoal = 5;
                    break;
            }
            ?>

            document.addEventListener('DOMContentLoaded', () => {
                // Weekly labels and data for the last 7 days
                const labels = <?php echo $labels; ?>;
                const caloriesData = <?php echo $caloriesData; ?>;
                const proteinData = <?php echo $proteinData; ?>;
                const exerciseData = <?php echo $exerciseData; ?>;

                const calorieGoal = <?php echo $bodyFatResults['caloricIntake']; ?>;
                const proteinGoal = <?php echo $bodyFatResults['proteinIntake']; ?>;
                const exerciseGoal = <?php echo $exerciseGoal ?>;  // Directly set exercise goal

                // Calories Chart
                new Chart(document.getElementById('caloriesChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Calories Consumed',
                                data: caloriesData,
                                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Goal',
                                data: Array(labels.length).fill(calorieGoal),
                                type: 'line',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 2,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true, // Keeps the aspect ratio within the defined canvas size
                        scales: {
                            x: { title: { display: true, text: 'Days' } },
                            y: { beginAtZero: true, title: { display: true, text: 'Calories' } }
                        }
                    }
                });

                // Protein Intake Chart
                new Chart(document.getElementById('proteinChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Protein Intake (g)',
                                data: proteinData,
                                backgroundColor: 'rgba(153, 102, 255, 0.7)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Goal',
                                data: Array(labels.length).fill(proteinGoal),
                                type: 'line',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 2,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { title: { display: true, text: 'Days' } },
                            y: { beginAtZero: true, title: { display: true, text: 'Protein (g)' } }
                        }
                    }
                });

                // Exercises Completed Chart
                new Chart(document.getElementById('exerciseChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Exercises Completed',
                                data: exerciseData,
                                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Goal',
                                data: Array(labels.length).fill(exerciseGoal),
                                type: 'line',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                borderWidth: 2,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { title: { display: true, text: 'Days' } },
                            y: { beginAtZero: true, title: { display: true, text: 'Exercises' } }
                        }
                    }
                });
            });
        </script>
    <?php endif; ?>


    <!-- JavaScript to print only chart and table -->
    <script>
        function printProgress() {
            const chartCanvas = document.getElementById('bodyReportsChart');
            const progressTable = document.getElementById('progressTable').outerHTML;
            const goalDescription = document.getElementById('goalDescription').innerHTML; // Capture goal description content
            const goalName = '<?php echo strtoupper(str_replace('-', ' ', $bodyFatResults['recommendedGoal'] ?? 'N/A')); ?>'; // Add null check

            // Convert the canvas to an image
            const chartImage = chartCanvas.toDataURL('image/png');

            // Open a new window for printing
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Print Progress</title>');
            printWindow.document.write('<style>body {font - family: Arial, sans-serif; } table {width: 100%; border-collapse: collapse; } th, td {border: 1px solid #ddd; padding: 8px; text-align: center; } img {max - width: 100%; display: block; margin: auto; }</style>');
            printWindow.document.write('</head><body>');

            // Add content to the print window
            printWindow.document.write('<h2 style="text-align: center;">' + goalName + '</h2>'); // Add the goal name to the print window
            printWindow.document.write('<div style="text-align: center; margin-top: 20px;">' + goalDescription + '</div>'); // Add the goal description
            printWindow.document.write('<img src="' + chartImage + '" alt="Progress Chart"><br>'); // Add the chart image to print
            printWindow.document.write(progressTable); // Add the progress table to print
            printWindow.document.write('</body></html>');

            // Finalize and trigger print
            printWindow.document.close();
            printWindow.focus();
            printWindow.onload = function () {
                printWindow.print();
                printWindow.onafterprint = function () {
                    printWindow.close(); // Close the window after printing
                };
            };
        }

    </script>
    </div>

    <!-- UNSUBSCRIBED USER SECTION -->
    <?php if ($showSubscribeMessage): ?>
        <div class="results-container" style="border: 1px solid #ddd; padding: 15px;">
            <div class="our_schedule_content">
            </div>
            <!-- PRICING TABLES SECTION -->
            <section class="pricing_tables_section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="pricing_tables_content">
                                <h2>SUBSCRIBE</h2>
                                <h5>TO ACCESS THE RECOMMENDED MEAL AND EXERCISE PLANS</h5>
                            </div>
                        </div>
                    </div>
                    <div class="row" data-aos="fade-up">

                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <div class="pricing_tables_box_content">
                                <div class="pricing_tables_box_upper_portion">
                                    <figure class="mb-0">
                                        <img src="./assets/images/pricing_tables_1.png" alt="" class="img-fluid">
                                    </figure>
                                    <div class="pricing_tables_box_image_content">
                                        <span class="price">P45</span>
                                        <span class="month">Monthly</span>
                                    </div>
                                </div>
                                <div class="pricing_tables_box_lower_portion">
                                    <h5>ESSENTIAL TIER</h5>
                                    <ul class="list-unstyled">
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Recommended Meal Plan
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Progress Tracking
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Access to Forums
                                        </li>
                                    </ul>
                                    <!-- Update your HTML with the logSubscription function calls -->
                                    <div class="btn_wrapper">
                                        <a class="join_now_btn text-decoration-none"
                                            href="./checkout.php?plan=essential&price=45&description=Basic%20content%2C%20Bodyweight%20exercises%2C%20Progress%20Tracking"
                                            onclick="logSubscription('Essential')">Subscribe</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <div class="pricing_tables_box_content">
                                <div class="pricing_tables_box_upper_portion">
                                    <figure class="mb-0">
                                        <img src="./assets/images/pricing_tables_2.png" alt="" class="img-fluid">
                                    </figure>
                                    <div class="pricing_tables_box_image_content">
                                        <span class="price">P50</span>
                                        <span class="month">Monthly</span>
                                    </div>
                                </div>
                                <div class="pricing_tables_box_lower_portion">
                                    <h5>PREMIUM TIER</h5>
                                    <ul class="list-unstyled">
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Recommended Meal Plan
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Recommended Exercises
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Progress Tracking
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Access to Forums
                                        </li>
                                    </ul>
                                    <div class="btn_wrapper">
                                        <a class="join_now_btn text-decoration-none"
                                            href="./checkout.php?plan=premium&price=50&description=Intermediate%20content%2C%20Bodyweight%20exercises%2C%20Progress%20Tracking%2C%20Nutritional%20Guidance"
                                            onclick="logSubscription('Premium')">Subscribe</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <div class="pricing_tables_box_content">
                                <div class="pricing_tables_box_upper_portion">
                                    <figure class="mb-0">
                                        <img src="./assets/images/pricing_tables_3.png" alt="" class="img-fluid">
                                    </figure>
                                    <div class="pricing_tables_box_image_content">
                                        <span class="price">P60</span>
                                        <span class="month">Monthly</span>
                                    </div>
                                </div>
                                <div class="pricing_tables_box_lower_portion">
                                    <h5>ELITE TIER</h5>
                                    <ul class="list-unstyled">
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Recommended Meal Plan
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Recommended Exercises
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Progress Tracking
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Access to Forums
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Bonus Exercise routines
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;from
                                            world famous &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;bodybuilders. (Mike
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mentzer,
                                            Arnold &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Schwarzenegger, and
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;more!)
                                        </li>
                                    </ul>
                                    <div class="btn_wrapper">
                                        <a class="join_now_btn text-decoration-none"
                                            href="./checkout.php?plan=elite&price=60&description=Advanced%20content%2C%20Bodyweight%20exercises%2C%20Progress%20Tracking%2C%20Nutritional%20Guidance%2C%20Curated%20weight-lifting%20routines%2C%2024%2F7%20Live%20Support"
                                            onclick="logSubscription('Elite')">Subscribe</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- UNIFIED DIET AND EXERCISE PLANNING SECTION -->
        <?php if ($showDietPlanningSection && isset($goal_name)): ?>
            <?php if (in_array($plan, ['essential', 'premium', 'elite'])): ?>
                <section class="our_schedule_section diet-planning" id="dietPlanSection">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="our_schedule_content">
                                    <?php if (!empty($meal_plan)): ?>
                                        <h5 class="mt-5" style="font-size: 80px;">DIET PLAN</h5>
                                        <h2><u><?php echo strtoupper($goal); ?></u></h2>

                                        <!-- Custom message based on the goal -->
                                        <?php
                                        $goalMessage = '';
                                        $goal = strtolower($goal);
                                        switch ($goal) {
                                            case 'maintenance':
                                                $goalMessage = "The food curated for your diet plan is balanced with moderate calories and high in protein, designed to help you maintain your current body weight while optimizing your macronutrient intake.";
                                                break;
                                            case 'weight-loss':
                                                $goalMessage = "Your diet plan focuses on calorie deficit meals, low in calories but rich in protein, to help you shed excess weight while preserving lean muscle mass.";
                                                break;
                                            case 'weight-gain':
                                                $goalMessage = "This diet plan emphasizes calorie surplus, providing higher calorie meals rich in both protein and healthy fats to support weight gain and muscle building.";
                                                break;
                                            default:
                                                $goalMessage = "Your diet plan is designed according to your specific fitness goals, ensuring a balance of macronutrients tailored to your needs.";
                                                break;
                                        }
                                        ?>
                                        <p style="font-size: 20px; margin-top: 20px;">
                                            <strong><?php echo $goalMessage; ?></strong>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="diet-horizontal-display">
                        <?php
                        $mealIndex = 0;
                        $totalMeals = count($meal_plan);
                        $dailyTotals = array();
                        $today = strtolower(date('l')); // Get the current day in lowercase
                
                        foreach ($days as $day):
                            if (strtolower($day) !== $today) {
                                continue; // Skip rendering for all days except today
                            }
                            $dayDate = date('Y-m-d', strtotime("this $day"));
                            ?>
                            <div class="border-mealplan">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="our_schedule_content">
                                                <h2 class="mt-5"><?php echo $day; ?></h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p style="font-size: 20px;">Click the food AFTER consumption to track your progress.</p>
                                <div id="total-<?php echo strtolower($day); ?>" class="large-counter-text mb-3" data-calories="0"
                                    data-protein="0">
                                    <?php
                                    echo '<u>Total Consumed:</u> <br>';
                                    echo 'Calories: <span id="calories-' . strtolower($day) . '">0</span> / ' . $bodyFatResults['caloricIntake'] . '<br>';
                                    echo 'Protein (g): <span id="protein-' . strtolower($day) . '">0</span> / ' . $bodyFatResults['proteinIntake'] . '<br>';
                                    ?>
                                </div>
                                <p><strong>Tip:</strong> You can eat the foods in any order, as long as you meet the recommended
                                    daily macronutrients.</p>
                                <div class="diet-horizontal-display">
                                    <table class="border border-black" id="mealPlanTable-<?php echo strtolower($day); ?>">
                                        <thead>
                                            <tr>
                                                <th>Time Slot</th>
                                                <?php for ($i = 0; $i < count($timeSlots); $i++) {
                                                    echo '<th>Food Item</th>';
                                                } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($timeSlots as $timeSlot): ?>
                                                <?php
                                                $foodItem = $meal_plan[$mealIndex % $totalMeals];

                                                if (!isset($dailyTotals[$day])) {
                                                    $dailyTotals[$day] = array('calories' => 0, 'protein' => 0);
                                                }
                                                $dailyTotals[$day]['calories'] += $foodItem['energy_kcal'];
                                                $dailyTotals[$day]['protein'] += $foodItem['protein_g'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $timeSlot; ?></td>
                                                    <?php for ($i = 0; $i < count($timeSlots); $i++): ?>
                                                        <?php $foodItem = $meal_plan[$mealIndex % $totalMeals]; ?>
                                                        <td class="mealItem" data-day="<?php echo strtolower($day); ?>"
                                                            data-date="<?php echo $dayDate; ?>" data-time-slot="<?php echo $timeSlot; ?>"
                                                            data-calories="<?php echo $foodItem['energy_kcal']; ?>"
                                                            data-protein="<?php echo $foodItem['protein_g']; ?>">
                                                            <br><?php echo $foodItem['english_name']; ?><br>
                                                            <br><?php echo $foodItem['filipino_name']; ?><br><br>
                                                            <strong>Measure:</strong> <?php echo $foodItem['household_measure']; ?><br>
                                                            <strong>Calories (kcal):</strong> <?php echo $foodItem['energy_kcal']; ?><br>
                                                            <strong>Protein (g):</strong> <?php echo $foodItem['protein_g']; ?><br>
                                                        </td>
                                                        <?php $mealIndex++; ?>
                                                    <?php endfor; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <!-- Tooltip element for displaying totals -->
                                    <div id="tooltip"
                                        style="display: none; position: absolute; padding: 5px; background-color: rgba(0, 0, 0, 0.75); color: white; border-radius: 5px; font-size: 12px; pointer-events: none;">
                                    </div>
                                </div>
                                <div id="total-<?php echo strtolower($day); ?>" class="border border-grey large-counter-text"
                                    data-calories="0" data-protein="0">
                                    <button onclick="printSection('dietPlanSection')" class="btn btn-primary mt-3">Print Diet
                                        Plan</button>
                                    <div class="note">
                                        <b>Meal plan food suggestions are based on the Philippine Department of Science and
                                            Technology, Food and Nutrition Research Institute, Food Exchange List</b>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- UPGRADE SECTION FOR EXERCISE PLAN -->
                <?php if ($plan === 'essential'): ?>
                    <!-- Include upgrade section for essential users -->
                    <section class="pricing_tables_section">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="pricing_tables_content">
                                        <h2>UPGRADE</h2>
                                        <h5>TO ACCESS THE RECOMMENDED EXERCISE PLANS</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center" data-aos="fade-up">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <div class="pricing_tables_box_content">
                                        <div class="pricing_tables_box_upper_portion">
                                            <figure class="mb-0">
                                                <img src="./assets/images/pricing_tables_2.png" alt="" class="img-fluid">
                                            </figure>
                                            <div class="pricing_tables_box_image_content">
                                                <span class="price">P50</span>
                                                <span class="month">Monthly</span>
                                            </div>
                                        </div>
                                        <div class="pricing_tables_box_lower_portion">
                                            <h5>PREMIUM TIER</h5>
                                            <ul class="list-unstyled">
                                                <li><i class="fa-solid fa-check"></i> Recommended Meal Plan</li>
                                                <li><i class="fa-solid fa-check"></i> Recommended Exercises</li>
                                                <li><i class="fa-solid fa-check"></i> Progress Tracking</li>
                                                <li><i class="fa-solid fa-check"></i> Access to Forums</li>
                                            </ul>
                                            <div class="btn_wrapper">
                                                <a class="join_now_btn text-decoration-none"
                                                    href="./checkout.php?plan=premium&price=50&description=Intermediate%20content%2C%20Bodyweight%20exercises%2C%20Progress%20Tracking%2C%20Nutritional%20Guidance"
                                                    onclick="logSubscription('Premium')">Subscribe</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <div class="pricing_tables_box_content">
                                        <div class="pricing_tables_box_upper_portion">
                                            <figure class="mb-0">
                                                <img src="./assets/images/pricing_tables_1.png" alt="" class="img-fluid">
                                            </figure>
                                            <div class="pricing_tables_box_image_content">
                                                <span class="price">P60</span>
                                                <span class="month">Monthly</span>
                                            </div>
                                        </div>
                                        <div class="pricing_tables_box_lower_portion">
                                            <h5>ELITE TIER</h5>
                                            <ul class="list-unstyled">
                                                <li><i class="fa-solid fa-check"></i> Recommended Meal Plan</li>
                                                <li><i class="fa-solid fa-check"></i> Recommended Exercises</li>
                                                <li><i class="fa-solid fa-check"></i> Progress Tracking</li>
                                                <li><i class="fa-solid fa-check"></i> Access to Forums</li>
                                                <li><i class="fa-solid fa-check"></i> Bonus Exercise routines from world-famous
                                                    bodybuilders</li>
                                            </ul>
                                            <div class="btn_wrapper">
                                                <a class="join_now_btn text-decoration-none"
                                                    href="./checkout.php?plan=elite&price=60&description=Advanced%20content%2C%20Bodyweight%20exercises%2C%20Progress%20Tracking%2C%20Nutritional%20Guidance%2C%20Curated%20weight-lifting%20routines%2C%2024%2F7%20Live%20Support"
                                                    onclick="logSubscription('Elite')">Subscribe</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php else: ?>
                    <!-- EXERCISE PLAN (For premium and elite plans) -->
                    <section class="our_schedule_section exercise-planning" id="exercisePlanSection">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="our_schedule_content">
                                        <?php if (!empty($exercise_plan)): ?>
                                            <h5 class="mt-5" style="font-size: 80px;">EXERCISE PLAN</h5>
                                            <h2><u><?php echo strtoupper($fitnessLevel); ?> FITNESS LEVEL</u></h2>

                                            <!-- Custom message based on fitness level -->
                                            <?php
                                            $intensityMessage = '';
                                            switch (strtolower($fitnessLevel)) {
                                                case 'beginner':
                                                    $intensityMessage = "This exercise plan consists of Low Intensity workouts. It’s perfect for beginners looking to ease into a regular workout routine with low-impact exercises.";
                                                    break;
                                                case 'intermediate':
                                                    $intensityMessage = "Your exercise plan is designed with Moderate Intensity workouts. Expect a balanced mix of cardio and strength exercises to improve your fitness level and endurance.";
                                                    break;
                                                case 'advanced':
                                                    $intensityMessage = "Get ready for High Intensity workouts! This plan includes more challenging exercises, designed for advanced fitness enthusiasts aiming to push their limits and maximize results.";
                                                    break;
                                                default:
                                                    $intensityMessage = "This exercise plan is customized to match your fitness goals, offering a balanced approach to help you achieve your desired results.";
                                                    break;
                                            }
                                            ?>
                                            <p style="font-size: 20px; margin-top: 20px; font-weight: bold;">
                                                <?php echo $intensityMessage; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="exercise-horizontal-display">
                            <?php
                            $exerciseIndex = 0;
                            $totalExercises = count($exercise_plan);
                            $dailyExerciseTotals = array();
                            $today = strtolower(date('l')); // Get the current day in lowercase
                
                            foreach ($days as $day):
                                if (strtolower($day) !== $today) {
                                    continue; // Skip rendering for all days except today
                                }
                                ?>

                                <div class="border-mealplan mt-5">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="our_schedule_content">
                                                    <h2 class="mt-5"><?php echo $day; ?></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p style="font-size: 20px;">Track your progress by marking the exercises you've completed.</p>
                                    <?php
                                    $tipMessage = '';
                                    switch (strtolower($fitnessLevel)) {
                                        case 'beginner':
                                            $tipMessage = "Goal: Complete at least 3 exercises each day. <br><br>Keep the intensity low to focus on form and building consistency.";
                                            break;
                                        case 'intermediate':
                                            $tipMessage = "Goal: Complete at least 5 exercises each day. <br><br>Push for a balanced routine with moderate intensity.";
                                            break;
                                        case 'advanced':
                                            $tipMessage = "Goal: Complete at least 7 exercises each day, with high intensity. <br><br>Focus on pushing your limits and maximizing your gains.";
                                            break;
                                        default:
                                            $tipMessage = "Goal: Aim to do at least 5 exercises from all 3 categories in a day (Cardio, Strength, Core).";
                                            break;
                                    }
                                    ?>
                                    <p><strong><?php echo $tipMessage; ?></strong></p>

                                    <div class="diet-horizontal-display">
                                        <table class="border border-black" id="exercisePlanTable-<?php echo strtolower($day); ?>">
                                            <thead>
                                                <tr>
                                                    <th>Time Slot</th>
                                                    <th>Exercise</th>
                                                    <th>Exercise</th>
                                                    <th>Exercise</th>
                                                    <th>Exercise</th>
                                                    <th>Exercise</th>
                                                    <th>Exercise</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($exercisetimeSlots as $timeSlot): ?>
                                                    <tr>
                                                        <td><?php echo $timeSlot; ?></td>
                                                        <?php for ($i = 0; $i < 6; $i++): ?>
                                                            <?php $exerciseItem = $exercise_plan[$exerciseIndex % $totalExercises]; ?>
                                                            <td class="exerciseItem" data-image="<?php echo $exerciseItem['image_link']; ?>"
                                                                data-description="<?php echo $exerciseItem['description']; ?>"
                                                                data-date="<?php echo $dayDate; ?>">
                                                                <br><strong><?php echo $exerciseItem['name']; ?><br></strong>
                                                                <?php echo $exerciseItem['duration']; ?><br><br>
                                                                <strong><?php echo $exerciseItem['intensity']; ?> Intensity</strong><br>
                                                                <?php echo $exerciseItem['category']; ?><br><br>
                                                                <?php echo $exerciseItem['target_body_part']; ?><br><br>
                                                            </td>
                                                            <?php $exerciseIndex++; ?>
                                                        <?php endfor; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                    // Set the minimum number of exercises based on fitness level
                                    $minimumExercises = 5; // Default value
                                    switch (strtolower($fitnessLevel)) {
                                        case 'beginner':
                                            $minimumExercises = 3;
                                            break;
                                        case 'intermediate':
                                            $minimumExercises = 5;
                                            break;
                                        case 'advanced':
                                            $minimumExercises = 7;
                                            break;
                                        default:
                                            $minimumExercises = 5; // Default value
                                            break;
                                    }
                                    ?>

                                    <input type="hidden" id="userFitnessLevel" value="<?php echo $fitnessLevel; ?>">

                                    <div id="total-exercises-<?php echo strtolower($day); ?>"
                                        class="border border-grey large-counter-text">
                                        <?php echo 'Minimum Exercises to Complete: <span class="minimum-to-complete">' . $minimumExercises . '</span><br>'; ?>
                                        <?php echo 'Total Exercises Completed: <span id="exerciseCounter-' . strtolower($day) . '">0</span><br>'; ?>
                                    </div>

                                    <input type="hidden" name="day" value="<?php echo $day; ?>">
                                    <input type="hidden" name="exercise_plan" id="exercisePlanData-<?php echo strtolower($day); ?>">
                                    <button onclick="printSection('exercisePlanSection')" class="btn btn-primary mt-3">Print Exercise
                                        Plan</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <script>
            function printSection(sectionId) {
                var section = document.getElementById(sectionId);

                // Clone the section to capture the current state
                var clonedSection = section.cloneNode(true);

                // Apply inline styles for completed, uncompleted, and blurred items
                clonedSection.querySelectorAll('.exerciseItem.completed').forEach(function (item) {
                    item.style.backgroundColor = 'green';
                    item.style.color = 'white';
                });

                clonedSection.querySelectorAll('.exerciseItem.blurred').forEach(function (item) {
                    item.style.backgroundColor = 'gray';
                    item.style.color = 'lightgray';
                });

                // Create a new document for printing
                var printWindow = window.open('', '', 'height=500, width=800');
                printWindow.document.write('<html><head><title>Print Plan</title>');

                // Add inline CSS for printing the dynamically changed styles and hide buttons
                var styles = `
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; padding: 20px; }
            table { width: 100%; border-collapse: collapse; }
            table, th, td { border: 1px solid black; padding: 10px; text-align: center; }
            th { background-color: #f0f0f0; color: black; }
            .exerciseItem.completed { background-color: green; color: white; }

                .exerciseItem.blurred { background-color: gray; color: lightgray; }
                .btn { display: none; } /* Hide print button */
        </style>
        `;

                printWindow.document.write(styles);
                printWindow.document.write('</head><body>');
                printWindow.document.write(clonedSection.innerHTML); // Write the cloned content with inline styles
                printWindow.document.write('</body></html>');
                printWindow.document.close();

                // Automatically open the print dialog for the print window (single print window)
                printWindow.focus();
                printWindow.onload = function () {
                    printWindow.print();
                    printWindow.onafterprint = function () {
                        printWindow.close(); // Close the window after printing
                    };
                };
            }
        </script>


        <!-- QUOTE SECTION -->
        <section class="quote_section">
            <div class="container">
                <div class="row" data-aos="fade-right">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="quote_content">
                            <h2>“
                                <?php echo htmlspecialchars($quote['quote']); ?>”
                            </h2>
                            <div class="quote_content_wrapper">
                                <div class="quote_wrapper">
                                    <h6>
                                        <?php echo htmlspecialchars($quote['author']); ?>
                                    </h6>
                                    <span>
                                        <?php echo htmlspecialchars($quote['title']); ?>
                                    </span>
                                    <?php if (!empty($quote['image_path'])): ?>
                                        <figure class="quote_image mb-0">
                                            <img src="<?php echo htmlspecialchars($quote['image_path']); ?>" alt=""
                                                class="img-fluid">
                                        </figure>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <figure class="quote_left_icon left_icon mb-0">
                    <img src="./assets/images/quote_left_icon.png" alt="" class="img-fluid">
                </figure>
                <figure class="quote_right_icon right_icon mb-0">
                    <img src="./assets/images/quote_right_icon.png" alt="" class="img-fluid">
                </figure>
            </div>
        </section>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Function to update visibility of sections based on gender selection
            function updateVisibility() {
                var gender = document.getElementById('gender').value;
                var hipSection = document.getElementById('hipSection');
                var thighSection = document.getElementById('thighSection');

                if (gender === 'female') {
                    hipSection.style.display = 'block';
                    thighSection.style.display = 'block';
                } else {
                    hipSection.style.display = 'none';
                    thighSection.style.display = 'none';
                }
            }
        });

        // INPUT DETAILS SLIDER + INFOGRAPHICS
        document.addEventListener("DOMContentLoaded", function () {
            const infoSection = document.getElementById('infographicSection');
            const infoContent = document.getElementById('infoContent');
            const infoImage = document.getElementById('infoImage');

            const tooltips = {
                bmiWeight: {
                    text: "<strong>Weight:</strong> Enter your current weight in kilograms. Weight is a key factor in determining your overall health and fitness level. <br> <br>This will be used to calculate your BMI and body fat percentage. ",
                    image: "https://www.wikihow.com/images/thumb/3/3b/Weigh-Yourself-Step-1.jpg/v4-460px-Weigh-Yourself-Step-1.jpg.webp"
                },
                bmiHeight: {
                    text: "<strong>Height:</strong> Enter your height in centimeters. Combined with height, it is essential for calculating your BMI.",
                    image: "https://www.wikihow.com/images/thumb/9/97/Measure-Your-Height-by-Yourself-Step-12-Version-3.jpg/aid1624233-v4-728px-Measure-Your-Height-by-Yourself-Step-12-Version-3.jpg.webp"
                },
                age: {
                    text: "<strong>Age:</strong> Combined with weight and height, will help determine how your body stores fat and estimate amount.",
                    image: "https://www.wikihow.com/images/thumb/2/25/Calculate-Body-Fat-With-a-Tape-Measure-Step-5-Version-3.jpg/v4-460px-Calculate-Body-Fat-With-a-Tape-Measure-Step-5-Version-3.jpg.webp"
                },
                waist: {
                    text: "<strong>Waist:</strong> Measure the circumference of your waist at the narrowest point. <br><br>This will be used to help determine bodyfat percentage (US Navy Method).",
                    image: "https://www.wikihow.com/images/thumb/6/60/Measure-Your-Waist-Step-3-Version-3.jpg/aid1375483-v4-728px-Measure-Your-Waist-Step-3-Version-3.jpg.webp"
                },
                neck: {
                    text: "<strong>Neck:</strong> Measure the circumference of your neck at its narrowest point. <br><br>This will be used to help determine bodyfat percentage (US Navy Method).",
                    image: "https://www.wikihow.com/images/thumb/2/2a/Calculate-Body-Fat-With-a-Tape-Measure-Step-6-Version-3.jpg/v4-460px-Calculate-Body-Fat-With-a-Tape-Measure-Step-6-Version-3.jpg.webp"
                },
                hip: {
                    text: "<strong>Hip:</strong> For females, measure the widest part of your hips. <br><br>This will be used to help determine bodyfat percentage (US Navy Method).",
                    image: "https://www.wikihow.com/images/thumb/f/f7/Measure-Hips-Step-5-Version-5.jpg/aid2669718-v4-728px-Measure-Hips-Step-5-Version-5.jpg.webp"
                },
                thigh: {
                    text: "<strong>Thigh:</strong> Measure the circumference of your thigh at its widest part. <br><br>This will be used to help determine bodyfat percentage (US Navy Method).",
                    image: "https://www.wikihow.com/images/thumb/5/51/Take-Measurements-%28For-Women%29-Step-23-Version-5.jpg/v4-460px-Take-Measurements-%28For-Women%29-Step-23-Version-5.jpg.webp"
                },
                activityLevel: {
                    text: "<strong>Lifestyle: <br><br>Sedentary</strong>  involves minimal movement throughout the day, typically involving sitting for long periods (e.g., desk job). <br><strong><br>Active</strong> means engaging in regular physical activity, whether through a demanding job or consistent movement like walking or standing.",
                    image: "https://www.shutterstock.com/image-photo/engineering-teamwork-planning-blueprint-office-600nw-2264233877.jpg"
                },
                fitnessLevel: {
                    text: "<strong>Fitness Level:</strong> <br><br><strong>Beginner</strong> - You are just starting your fitness journey or returning after a long break. <br><br><strong>Intermediate</strong> - You exercise regularly with moderate intensity and have a consistent workout routine. <br><br><strong>Advanced</strong> - You have high fitness experience, perform frequent, high-intensity workouts, and maintain an intense training schedule.",
                    image: "https://elements-resized.envatousercontent.com/envato-shoebox/56c8/1f24-a7ab-4dbc-ba8a-a71a92179c86/108_.00_05_42_09.Still178--low_res-scale-1_40x.jpg?w=1600&cf_fit=scale-down&mark-alpha=18&mark=https%3A%2F%2Felements-assets.envato.com%2Fstatic%2Fwatermark4.png&q=85&format=auto&s=992d98767864e39631da28d8d9b7aced26518afc31b939ceceae4a2637257dd5"
                }
            };

            // Add event listeners to all input and select fields
            document.querySelectorAll('input, select').forEach(input => {
                input.addEventListener('focus', (e) => {
                    const fieldName = e.target.id;
                    const tooltip = tooltips[fieldName] || { text: "Click an input field to see its details.", image: "" };

                    infoContent.innerHTML = tooltip.text; // Set innerHTML to render <br> tags
                    if (tooltip.image) {
                        infoImage.src = tooltip.image;
                        infoImage.style.display = 'block';
                    } else {
                        infoImage.style.display = 'none';
                    }

                    infoSection.classList.add('active');
                });

                input.addEventListener('blur', () => {
                    // Optionally hide the infographic on blur
                    // infoSection.classList.remove('active');
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('bodyReportsChart').getContext('2d');
            const data = <?php echo json_encode($mergedData); ?>;
            const labels = data.map(item => new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }));
            const bmiData = data.map(item => item.bmi);
            const bodyFatPercentageData = data.map(item => item.bodyFatPercentage);
            const fatMassData = data.map(item => item.fatMass);
            const leanMassData = data.map(item => item.leanMass);
            const weightData = data.map(item => item.weight);

            // Recommended goal fetched from PHP
            const recommendedGoal = '<?php echo $bodyFatResults["recommendedGoal"]; ?>';

            // Creating gradient backgrounds for the lines
            const gradientWeight = ctx.createLinearGradient(0, 0, 0, 400);
            gradientWeight.addColorStop(0, 'rgba(153, 102, 255, 0.9)');
            gradientWeight.addColorStop(1, 'rgba(153, 102, 255, 0.3)');

            const gradientBMI = ctx.createLinearGradient(0, 0, 0, 400);
            gradientBMI.addColorStop(0, 'rgba(75, 192, 192, 0.9)');
            gradientBMI.addColorStop(1, 'rgba(75, 192, 192, 0.3)');

            const gradientBodyFat = ctx.createLinearGradient(0, 0, 0, 400);
            gradientBodyFat.addColorStop(0, 'rgba(255, 99, 132, 0.9)');
            gradientBodyFat.addColorStop(1, 'rgba(255, 99, 132, 0.3)');

            const gradientFatMass = ctx.createLinearGradient(0, 0, 0, 400);
            gradientFatMass.addColorStop(0, 'rgba(54, 162, 235, 0.9)');
            gradientFatMass.addColorStop(1, 'rgba(54, 162, 235, 0.3)');

            const gradientLeanMass = ctx.createLinearGradient(0, 0, 0, 400);
            gradientLeanMass.addColorStop(0, 'rgba(255, 206, 86, 0.9)');
            gradientLeanMass.addColorStop(1, 'rgba(255, 206, 86, 0.3)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Weight',
                            data: weightData,
                            borderColor: 'rgba(153, 102, 255, 1)',
                            backgroundColor: gradientWeight,
                            borderWidth: 5,
                            pointStyle: 'star',
                            pointRadius: 5,
                            pointHoverRadius: 10,
                            pointBackgroundColor: 'rgba(153, 102, 255, 1)',
                        },
                        {
                            label: 'BMI',
                            data: bmiData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: gradientBMI,
                            borderWidth: 5,
                            pointStyle: 'circle',
                            pointRadius: 5,
                            pointHoverRadius: 10,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                        },
                        {
                            label: 'Body Fat Percentage',
                            data: bodyFatPercentageData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: gradientBodyFat,
                            borderWidth: 5,
                            pointStyle: 'rect',
                            pointRadius: 5,
                            pointHoverRadius: 10,
                            pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                        },
                        {
                            label: 'Fat Mass',
                            data: fatMassData,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: gradientFatMass,
                            borderWidth: 5,
                            pointStyle: 'triangle',
                            pointRadius: 5,
                            pointHoverRadius: 10,
                            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                        },
                        {
                            label: 'Lean Mass',
                            data: leanMassData,
                            borderColor: 'rgba(255, 206, 86, 1)',
                            backgroundColor: gradientLeanMass,
                            borderWidth: 5,
                            pointStyle: 'rectRot',
                            pointRadius: 5,
                            pointHoverRadius: 10,
                            pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Body Stats',
                                font: {
                                    size: 18
                                }
                            },
                            ticks: {
                                font: {
                                    size: 18,
                                },
                                callback: function (value) {
                                    return Math.round(value); // Convert ticks to whole numbers
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date',
                                font: {
                                    size: 18
                                }
                            },
                            ticks: {
                                font: {
                                    size: 18,
                                },
                                color: 'black'
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        intersect: false, // Enable hovering over the lines, not just points
                    },
                    plugins: {
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0,0,0,0.7)',
                            titleFont: {
                                size: 20, // Bigger font for title
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 20 // Bigger font for body
                            },
                            padding: {
                                top: 10,
                                right: 15,
                                bottom: 10,
                                left: 15
                            },
                            callbacks: {
                                title: function (tooltipItems) {
                                    const currentIndex = tooltipItems[0].dataIndex;
                                    const prevIndex = currentIndex > 0 ? currentIndex - 1 : null;
                                    const goal = recommendedGoal.charAt(0).toUpperCase() + recommendedGoal.slice(1); // Capitalize goal
                                    if (prevIndex !== null) {
                                        const prevDate = labels[prevIndex];
                                        const currentDate = labels[currentIndex];
                                        return `${prevDate} - ${currentDate}\nGoal: ${goal}`; // Adding the goal to the title
                                    }
                                    return `${tooltipItems[0].label}\nGoal: ${goal}`;
                                },
                                label: function (tooltipItem) {
                                    const dataset = tooltipItem.dataset;
                                    const index = tooltipItem.dataIndex;
                                    const value = Math.round(tooltipItem.raw); // Current value

                                    if (index > 0) {
                                        const previousValue = Math.round(dataset.data[index - 1]); // Previous value
                                        return `${dataset.label}: ${previousValue} → ${value}`;
                                    }
                                    return `${dataset.label}: ${value}`;
                                },
                                afterLabel: function (tooltipItem) {
                                    const dataset = tooltipItem.dataset;
                                    const index = tooltipItem.dataIndex;

                                    if (index > 0) {
                                        const previousValue = dataset.data[index - 1];
                                        const currentValue = dataset.data[index];
                                        const difference = Math.round(currentValue - previousValue); // Round the difference

                                        let colorText = ''; // No coloring

                                        // Define text-based indicators for the differences
                                        if (recommendedGoal === 'weight-loss') {
                                            colorText = difference < 0 ? '⬇️ ' : '⬆️ '; // Down arrow for loss, up arrow for gain
                                        } else if (recommendedGoal === 'weight-gain') {
                                            colorText = difference > 0 ? '⬆️ ' : '⬇️ '; // Up arrow for gain, down arrow for loss
                                        } else if (recommendedGoal === 'maintenance') {
                                            colorText = Math.abs(difference) === 0 ? '↔️ ' : (difference > 0 ? '⬆️ ' : '⬇️ '); // Steady arrow for no change
                                        }

                                        return `Difference: ${colorText}${difference >= 0 ? '+' : ''}${difference}`;
                                    }
                                    return null;
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Progress Tracking Chart',
                            font: {
                                size: 22,
                            },
                            padding: {
                                top: 10,
                                bottom: 30
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 16
                                }
                            }
                        }
                    }
                }
            });
        });

        // Function to log activity to the database via AJAX
        function logActivity(type, data) {
            fetch('log_activity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    type: type,
                    data: data
                })
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        console.log('Activity logged successfully');
                    } else {
                        console.error('Failed to log activity:', result.message);
                    }
                })
                .catch(error => console.error('Error logging activity:', error));
        }

        // Function to attach event listeners to meal items
        function attachEventListeners() {
            const mealItems = document.querySelectorAll('.mealItem');
            const tooltip = document.getElementById('tooltip'); // Tooltip element

            mealItems.forEach(item => {
                item.addEventListener('mouseenter', (e) => {
                    const day = item.getAttribute('data-day');
                    const calories = parseFloat(item.getAttribute('data-calories'));
                    const protein = parseFloat(item.getAttribute('data-protein'));

                    const totalElement = document.getElementById(`total-${day}`);
                    const currentCalories = parseFloat(totalElement.getAttribute('data-calories')) || 0;
                    const currentProtein = parseFloat(totalElement.getAttribute('data-protein')) || 0;

                    const maxCalories = parseFloat(document.querySelector(`#calories-${day}`).nextSibling.nodeValue.split("/")[1].trim()) || 0;
                    const maxProtein = parseFloat(document.querySelector(`#protein-${day}`).nextSibling.nodeValue.split("/")[1].trim()) || 0;

                    const calorieColor = currentCalories >= maxCalories ? 'lightgreen' : 'white';
                    const proteinColor = currentProtein >= maxProtein ? 'lightgreen' : 'white';

                    tooltip.innerHTML = `<strong style="color: white;"><u>Total Consumed:</u></strong><br>
                <span style="color:${calorieColor};">Calories: ${currentCalories} / ${maxCalories}</span><br>
                <span style="color:${proteinColor};">Protein: ${currentProtein} g / ${maxProtein} g</span>`;

                    tooltip.style.display = 'block';
                });

                item.addEventListener('mousemove', (e) => {
                    tooltip.style.top = `${e.pageY + 10}px`;
                    tooltip.style.left = `${e.pageX + 10}px`;
                });

                item.addEventListener('mouseleave', () => {
                    tooltip.style.display = 'none';
                });

                item.addEventListener('click', () => {
                    const day = item.getAttribute('data-day');
                    const date = item.getAttribute('data-date'); // Fetch the specific date
                    const calories = parseFloat(item.getAttribute('data-calories'));
                    const protein = parseFloat(item.getAttribute('data-protein'));

                    const totalElement = document.getElementById(`total-${day}`);
                    const currentCalories = parseFloat(totalElement.getAttribute('data-calories')) || 0;
                    const currentProtein = parseFloat(totalElement.getAttribute('data-protein')) || 0;

                    const maxCalories = parseFloat(document.querySelector(`#calories-${day}`).nextSibling.nodeValue.split("/")[1].trim()) || 0;
                    const maxProtein = parseFloat(document.querySelector(`#protein-${day}`).nextSibling.nodeValue.split("/")[1].trim()) || 0;

                    if (currentCalories >= maxCalories && !item.classList.contains('consumed')) {
                        alert('You have reached the maximum calorie intake for the day.');
                        return;
                    }

                    if (item.classList.contains('consumed')) {
                        return;
                    }

                    item.classList.add('consumed');
                    item.style.backgroundColor = 'green'; // Inline style for print
                    item.style.color = 'white'; // Inline style for print

                    totalElement.setAttribute('data-calories', currentCalories + calories);
                    totalElement.setAttribute('data-protein', currentProtein + protein);

                    const calorieElement = document.getElementById(`calories-${day}`);
                    const proteinElement = document.getElementById(`protein-${day}`);

                    const newCalorieValue = parseFloat(totalElement.getAttribute('data-calories'));
                    calorieElement.innerText = newCalorieValue;
                    calorieElement.style.color = newCalorieValue >= maxCalories ? 'lightgreen' : 'black';

                    const newProteinValue = parseFloat(totalElement.getAttribute('data-protein'));
                    proteinElement.innerText = newProteinValue;
                    proteinElement.style.color = newProteinValue >= maxProtein ? 'lightgreen' : 'black';

                    const calorieColor = newCalorieValue >= maxCalories ? 'lightgreen' : 'white';
                    const proteinColor = newProteinValue >= maxProtein ? 'lightgreen' : 'white';

                    tooltip.innerHTML = `<strong style="color: white;"><u>Total Consumed:</u></strong><br>
                <span style="color:${calorieColor};">Calories: ${newCalorieValue} / ${maxCalories}</span><br>
                <span style="color:${proteinColor};">Protein: ${newProteinValue} g / ${maxProtein} g</span>`;

                    if (newCalorieValue >= maxCalories) {
                        alert('You have reached the maximum calorie intake for the day.');
                    }

                    // Log the food consumption activity
                    logActivity('food', {
                        foodItem: item.innerText.trim(), // Get the food name from the cell
                        calories,
                        protein,
                        date // Pass the date for correct logging
                    });
                });
            });
        }

        // Function to attach event listeners to exercise items
        function attachExerciseEventListeners() {
            const exerciseItems = document.querySelectorAll('.exerciseItem');
            let tooltip = null;

            exerciseItems.forEach(item => {
                item.addEventListener('click', () => {
                    const date = item.getAttribute('data-date'); // Fetch the specific date for each exercise item
                    item.classList.toggle('completed');
                    const day = item.closest('table').id.split('-')[1];
                    updateExerciseCounter(day);

                    // Log the exercise activity
                    const exerciseName = item.querySelector('strong').innerText.trim(); // Get the exercise name
                    const caloriesBurnt = 100; // Adjust based on your logic for calories burnt

                    logActivity('exercise', {
                        day,
                        exerciseName,
                        caloriesBurnt,
                        date // Pass the date for correct logging
                    });
                });

                item.addEventListener('mouseenter', (e) => {
                    const imageUrl = item.getAttribute('data-image');
                    const description = item.getAttribute('data-description');

                    if (imageUrl || description) {
                        if (!tooltip) {
                            tooltip = document.createElement('div');
                            tooltip.className = 'exercise-tooltip';
                            tooltip.style.position = 'absolute';
                            tooltip.style.zIndex = '1000';
                            document.body.appendChild(tooltip);
                        }

                        tooltip.innerHTML = `
                    <div style="background-color: white; padding: 15px; max-width: 400px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); border-radius: 15px;">
                        <img src="${imageUrl}" alt="Exercise Image" style="width: 100%; max-width: 400px; height: auto; border-radius: 10px;">
                        <p style="margin-top: 10px; font-size: 18px;">${description}</p>
                    </div>
                `;

                        tooltip.style.display = 'block';
                        tooltip.style.left = `${e.pageX + 10}px`;
                        tooltip.style.top = `${e.pageY + 10}px`;
                    }
                });

                item.addEventListener('mousemove', (e) => {
                    if (tooltip) {
                        tooltip.style.left = `${e.pageX + 10}px`;
                        tooltip.style.top = `${e.pageY + 10}px`;
                    }
                });

                item.addEventListener('mouseleave', () => {
                    if (tooltip) {
                        tooltip.style.display = 'none';
                    }
                });
            });
        }


        document.addEventListener('DOMContentLoaded', function () {
            const fitnessLevel = '<?php echo $fitnessLevel; ?>';
            let maxExercises;

            switch (fitnessLevel.toLowerCase()) {
                case 'beginner':
                    maxExercises = 5;
                    break;
                case 'intermediate':
                    maxExercises = 8;
                    break;
                case 'advanced':
                    maxExercises = 12;
                    break;
                default:
                    maxExercises = 6;
            }

            const exerciseTables = document.querySelectorAll('table[id^="exercisePlanTable-"]');
            exerciseTables.forEach(table => {
                const exercises = table.querySelectorAll('.exerciseItem');
                const shuffledExercises = Array.from(exercises).sort(() => Math.random() - 0.5);

                shuffledExercises.forEach((exercise, index) => {
                    if (index >= maxExercises) {
                        exercise.classList.add('blurred');
                    } else {
                        exercise.classList.remove('blurred');
                    }
                });
            });
        });

        // Function to get minimum exercises based on fitness level
        function getMinimumExercises(fitnessLevel) {
            switch (fitnessLevel.toLowerCase()) {
                case 'beginner':
                    return 3;
                case 'intermediate':
                    return 5;
                case 'advanced':
                    return 7;
                default:
                    return 5;
            }
        }

        // Function to update the exercise counter
        function updateExerciseCounter(day) {
            const fitnessLevel = document.getElementById('userFitnessLevel').value;
            const completedExercises = document.querySelectorAll(`#exercisePlanTable-${day} .exerciseItem.completed`).length;
            const minimumExercises = getMinimumExercises(fitnessLevel);
            const exerciseCounter = document.getElementById(`exerciseCounter-${day}`);
            exerciseCounter.innerText = completedExercises;

            const minimumToComplete = document.querySelector(`#total-exercises-${day} .minimum-to-complete`);
            if (completedExercises >= minimumExercises) {
                minimumToComplete.innerHTML = `<span style="color: green;">${minimumExercises} - Minimum hit! Good job!</span>`;
            } else {
                minimumToComplete.innerHTML = `${minimumExercises}`;
            }
        }

        // Initialize the event listeners
        document.addEventListener('DOMContentLoaded', () => {
            attachEventListeners();
            attachExerciseEventListeners();

            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            days.forEach(day => {
                updateExerciseCounter(day);
            });
        });


    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/video-popup.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/owl.carousel.js"></script>
    <script src="assets/js/carousel.js"></script>
    <script src="assets/js/video-section.js"></script>
    <script src="assets/js/counter.js"></script>
    <script src="assets/js/animation.js"></script>
</body>

</html>