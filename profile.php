<?php
session_start();

include 'database.php';

$username = $_SESSION['username'];
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
    // Check if height is provided and not zero
    if ($height !== null && $height != 0) {
        // BMI Formula: BMI = weight (kg) / (height (m) * height (m))
        $heightInMeters = $height / 100; // Convert height to meters
        return $weight / ($heightInMeters * $heightInMeters);
    } else {
        // Return some default value or handle it as per your application logic
        return 0;
    }
}

// Process user input and insert into the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assume you have variables like $age, $waist, $neck, $gender, $hip, and $thigh from user input
    $age = $_POST["age"];
    $waist = $_POST["waist"];
    $neck = $_POST["neck"];
    $hip = isset($_POST["hip"]) ? $_POST["hip"] : '';
    $thigh = isset($_POST["thigh"]) ? $_POST["thigh"] : '';
    $activityLevel = $_POST["activityLevel"];
    $bmiWeight = $_POST["bmiWeight"]; // Updated variable name
    $bmiHeight = $_POST["bmiHeight"]; // Updated variable name
    $username = $_SESSION['username']; // Assuming the username is stored in the session

    // Insert user input into database
    $sql = "INSERT INTO users_info (username, age, waist, neck, hip, thigh, activityLevel, bmiWeight, bmiHeight) 
            VALUES ('$username', '$age', '$waist', '$neck', '$hip', '$thigh', '$activityLevel', '$bmiWeight', '$bmiHeight')";

    if ($mysqli->query($sql) === TRUE) {
        echo '<span style="color: #007bff;">Profile updated successfully.</span>';
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
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
if ($gender === 'female') {
    $bodyFatPercentage = calculateBodyFatPercentageForWomen($waist, $neck, $hip, $bmiHeight);
} else {
    $bodyFatPercentage = calculateBodyFatPercentageForMen($waist, $neck, $bmiHeight);
}

// Calculate fat mass and lean mass
$fatMass = ($bodyFatPercentage / 100) * $bmiWeight;
$leanMass = $bmiWeight - $fatMass;

// Calculate ideal body weight using different formulas
$heightInInches = $bmiHeight / 2.54; // Convert height to inches
$inchesOverFiveFeet = $heightInInches - 60;

if ($gender === 'male') {
    $hamwiIBW = 106 + (6 * $inchesOverFiveFeet);
    $devineIBW = 50.0 + 2.3 * $inchesOverFiveFeet;
    $robinsonIBW = 52.0 + 1.9 * $inchesOverFiveFeet;
    $millerIBW = 56.2 + 1.41 * $inchesOverFiveFeet;
} else {
    $hamwiIBW = 100 + (5 * $inchesOverFiveFeet);
    $devineIBW = 45.5 + 2.3 * $inchesOverFiveFeet;
    $robinsonIBW = 49.0 + 1.7 * $inchesOverFiveFeet;
    $millerIBW = 53.1 + 1.36 * $inchesOverFiveFeet;
}

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

// Convert the weight from pounds to kilograms
$hamwiIBW_kg = $hamwiIBW * 0.45359237;

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
    'hamwiIBW' => $hamwiIBW,
    'devineIBW' => $devineIBW,
    'robinsonIBW' => $robinsonIBW,
    'millerIBW' => $millerIBW,
    'lowerNormalRange' => $lowerNormalRange,
    'upperNormalRange' => $upperNormalRange,
];


// Function to calculate body fat percentage using Navy Method for men
function calculateBodyFatPercentageForMen($waist, $neck, $height)
{
    return (495 / (1.0324 - 0.19077 * log10($waist - $neck) + 0.15456 * log10($height))) - 450;
}

// Function to calculate body fat percentage using Navy Method for women
function calculateBodyFatPercentageForWomen($waist, $neck, $hip, $height)
{
    return (495 / (1.29579 - 0.35004 * log10($waist + $hip - $neck) + 0.221 * log10($height))) - 450;
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


function getExercises($recommendedGoal, $exerciseType)
{

    include 'database.php';

    // Define intensity based on recommended goal
    $intensity = '';
    switch ($recommendedGoal) {
        case 'maintenance':
            $intensity = 'Moderate'; // Moderate or high intensity for maintenance
            break;
        case 'weight-loss':
            $intensity = 'Low'; // Low intensity for weight loss
            break;
        case 'weight-gain':
            $intensity = 'Low'; // Low intensity for weight gain
            break;
        default:
            $intensity = ''; // No specific intensity
            break;
    }

    // Query exercises based on recommended goal, intensity, and exercise type
    $query = "SELECT * FROM exercises WHERE intensity = ? AND exercise_type = ?";
    $statement = $mysqli->prepare($query);
    $statement->bind_param('ss', $intensity, $exerciseType);
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
        echo "Error retrieving exercises: " . $mysqli->error;
        return [];
    }
}

$exerciseType = isset($_POST['exerciseType']) ? $_POST['exerciseType'] : 'Bodyweight';
if (isset($intakeResults['goal'])) {
    $exercise_plan = getExercises($intakeResults['goal'], $exerciseType);
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
    $sql = "SELECT age, waist, neck, gender, hip, thigh, activityLevel, bmiWeight, bmiHeight FROM users_info WHERE username = ? ORDER BY created_at DESC LIMIT 1";


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

    // Bind result variables
    $stmt->bind_result($age, $waist, $neck, $gender, $hip, $thigh, $activityLevel, $bmiWeight, $bmiHeight);

    // Fetch the data
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    // Check if any data is retrieved
    if ($age !== null && $waist !== null && $neck !== null && $activityLevel !== null) {
        // Return the retrieved user information
        return [
            'age' => $age,
            'waist' => $waist,
            'neck' => $neck,
            'hip' => $hip,
            'thigh' => $thigh,
            'activityLevel' => $activityLevel,
            'bmiWeight' => $bmiWeight,
            'bmiHeight' => $bmiHeight
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
        'bmiHeight' => 0
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
            'hamwiIBW_kg' => $userResults['hamwiIBW_kg'],
            'devineIBW' => $userResults['devineIBW'],
            'robinsonIBW' => $userResults['robinsonIBW'],
            'millerIBW' => $userResults['millerIBW'],
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
            'hamwiIBW_kg' => $row['hamwiIBW_kg'],
            'devineIBW' => $row['devineIBW'],
            'robinsonIBW' => $row['robinsonIBW'],
            'millerIBW' => $row['millerIBW'],
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
    $generatedText = "Generated as of";
} elseif (isset($bodyFatResults['created_at']) && $bodyFatResults['created_at'] !== null) {
    // If intakeResults timestamp is not available but bodyFatResults timestamp is, use it
    $timestamp = strtotime($bodyFatResults['created_at']);
    $formattedDate = date("F j, Y   |   g:i A", $timestamp);
    $generatedText = "Last generated on";
} else {
    // If neither timestamp is available
    $formattedDate = "No timestamp available";
    $generatedText = "";
}

// Fetch user info from transactions table
$userTransactionsSql = "SELECT * FROM transactions WHERE username = ?";
$userTransactionsStmt = $mysqli->prepare($userTransactionsSql);
$userTransactionsStmt->bind_param('s', $username);
$userTransactionsStmt->execute();
$userTransactionsResult = $userTransactionsStmt->get_result();

$query = "SELECT * FROM quotes ORDER BY RAND() LIMIT 1";
$result = $mysqli->query($query);

// Check if the query was successful
if ($result && $result->num_rows > 0) {
    $quote = $result->fetch_assoc();
} else {
    $quote = null;
}
// Fetch user transaction status and plan
$query = "SELECT status, plan FROM transactions WHERE username = ?";
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

// Check if the status is not "Pending" or "Disapproved"
$showDietPlanningSection = ($status !== 'Pending' && $status !== 'Disapproved' && $status !== null);

// Show subscription status if status is not "Disapproved" or not null
$showSubscriptionInfoSection = ($status !== 'Disapproved' && $status !== null);

// Show subscribe message if status is null or pending or disapproved or plan is null
// Show subscribe message if status is null or pending or disapproved or plan is null
$showSubscribeMessage = ($status === null && $status == 'Pending' && $status == 'Disapproved' && $plan === null);

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
    $exercise_plan = getExercises($goal_name, $exerciseType); // Pass only the recommended goal
}

// Assuming the logged-in user's username is stored in the session
$loggedInUsername = $_SESSION['username'];

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

$disabledDays = 14;
$remainingDays = $disabledDays;

if ($lastCalculationDate) {
    $remainingDays = $disabledDays - (new DateTime())->diff(new DateTime($lastCalculationDate))->days;
    // Ensure remaining days is not negative
    $remainingDays = max($remainingDays, 0);
}

$disableButton = $remainingDays > 0;


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

        .calculator-section {
            text-align: center;
            padding: 50px 0;
        }

        .calculator-form,
        .results-form {
            max-width: 500px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .calculator-form h2,
        .results-form h2 {
            margin-bottom: 20px;
        }

        .calculator-form label,
        .results-form label {
            display: block;
            margin-bottom: 10px;
        }

        .calculator-form input,
        .calculator-form select,
        .results-form input,
        .results-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
        }

        .calculator-form button,
        .results-form button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }

        .calculator-results,
        .result-container {
            margin-top: 30px;
            text-align: left;
        }

        .result-container h2 {
            margin-top: 20px;
            margin-bottom: 30px;
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
            /* ^ affects the width of the horizontal scroll container of the table*/
            max-width: 100%;
            /* ^ affects the width of the viewport of the page*/
            text-align: center;
        }

        .large-counter-text {
            font-size: 1.2em;
            /* Adjust the size as needed */
            font-weight: bold;
        }

        .border-mealplan {
            border: 2px solid #d9d9d9;
            margin-right: 20px;
            /* Adjust the margin value as needed */
        }

        .note {
            font-size: 1.0em;
            text-align: center;
            position: relative;
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

        .border-mealplan {
            border: 2px solid #d9d9d9;
            margin-right: 20px;
            /* Adjust as needed */
        }

        .large-counter-text {
            font-size: 1.2em;
            /* Adjust as needed */
            font-weight: bold;
        }

        /* Recalculate Button Disabled State*/
        .btn-calculate {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        .btn-recalculate {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        .btn-recalculate[disabled] {
            background-color: #a8d0ff;
            /* Lighter shade */
            color: #fff;
            cursor: not-allowed;
        }

        .disabled-message {
            color: #e13a3b;
            /* orange color for the message */
            font-size: 14px;
            margin-top: 5px;
        }
    </style>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #resultsSection,
            #resultsSection * {
                visibility: visible;
            }

            #resultsSection {
                position: absolute;
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

                            <h1 data-aos="fade-up"><?php echo $_SESSION['username']; ?></h1>
                            <div class="btn_wrapper">
                                <span class="sub_home_span">Home </span>-<span class="sub_span"> Profile</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php if ($showSubscriptionInfoSection): ?>
        <div class="container-fluid">
            <table class="table table-bordered table-striped mt-4" style="width: 100%;">
                <thead>
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
                        // Output data of each row
                        while ($row = $userTransactionsResult->fetch_assoc()) {
                            // Handle undefined keys
                            $id = isset($row["id"]) ? $row["id"] : "";
                            $plan = isset($row["plan"]) ? $row["plan"] : "";
                            $description = isset($row["description"]) ? $row["description"] : "";
                            $price = isset($row["price"]) ? $row["price"] : "";
                            $gcash_number = isset($row["gcash_number"]) ? $row["gcash_number"] : "";
                            $reference_number = isset($row["reference_number"]) ? $row["reference_number"] : "";
                            $created_at = isset($row["created_at"]) ? date("F j, Y | g:i A", strtotime($row["created_at"])) : "";
                            $date_end = isset($row["date_end"]) ? date("F j, Y | g:i A", strtotime($row["date_end"])) : "";
                            $status = isset($row["status"]) ? $row["status"] : "";

                            // Add style attribute to center align the values
                            echo "<tr>
                        <td style='text-align: center;'>" . htmlspecialchars($plan) . "</td>
                        <td style='text-align: center;'>" . htmlspecialchars($description) . "</td>
                        <td style='text-align: center;'>" . htmlspecialchars($created_at) . "</td>
                        <td style='text-align: center;'>" . htmlspecialchars($date_end) . "</td>
                        <td style='text-align: center;'>" . htmlspecialchars($status) . "</td>";
                        }
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

    <section class="calculator-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="calculator-form form-section">
                        <h2>BMI, Body Fat, Calorie and Protein Intake Calculator</h2>

                        <!-- Combined Form -->
                        <form method="post" action="" id="calculatorForm">
                            <!-- BMI Section -->
                            <label for="bmiWeight">Weight (kg):</label>
                            <input type="number" name="bmiWeight" id="bmiWeight" min="5"
                                value="<?php echo isset($bmiWeight) ? $bmiWeight : ''; ?>" <?php echo isset($disableButton) && $disableButton ? 'disabled' : ''; ?> required>

                            <label for="bmiHeight">Height (cm):</label>
                            <input type="number" name="bmiHeight" id="bmiHeight" min="5"
                                value="<?php echo isset($bmiHeight) ? $bmiHeight : ''; ?>" <?php echo isset($disableButton) && $disableButton ? 'disabled' : ''; ?> required>

                            <!-- Body Fat Calculator Section -->
                            <label for="age">Age:</label>
                            <input type="number" id="age" name="age" min="5"
                                value="<?php echo isset($age) ? $age : ''; ?>" <?php echo isset($disableButton) && $disableButton ? 'disabled' : ''; ?> required>

                            <label for="waist">Waist (cm):</label>
                            <input type="number" id="waist" name="waist" min="5"
                                value="<?php echo isset($waist) ? $waist : ''; ?>" <?php echo isset($disableButton) && $disableButton ? 'disabled' : ''; ?> required>

                            <label for="neck">Neck (cm):</label>
                            <input type="number" id="neck" name="neck" min="5"
                                value="<?php echo isset($neck) ? $neck : ''; ?>" <?php echo isset($disableButton) && $disableButton ? 'disabled' : ''; ?> required>

                            <!-- Conditional Display Based on Gender -->
                            <?php if (isset($gender) && $gender === 'female'): ?>
                                <div id="hipSection">
                                    <label for="hip">Hip Circumference (cm):</label>
                                    <input type="number" id="hip" name="hip" min="5"
                                        value="<?php echo isset($hip) ? $hip : ''; ?>" <?php echo isset($disableButton) && $disableButton ? 'disabled' : ''; ?> required>

                                    <label for="thigh">Thigh Circumference (cm):</label>
                                    <input type="number" id="thigh" name="thigh" min="5"
                                        value="<?php echo isset($thigh) ? $thigh : ''; ?>" <?php echo isset($disableButton) && $disableButton ? 'disabled' : ''; ?> required>
                                </div>
                            <?php else: ?>
                                <div id="hipSection" style="display: none;">
                                    <label for="hip">Hip Circumference (cm):</label>
                                    <input type="number" id="hip" name="hip" value="<?php echo isset($hip) ? $hip : ''; ?>"
                                        <?php echo isset($disableButton) && $disableButton ? 'disabled' : ''; ?>>

                                    <label for="thigh">Thigh Circumference (cm):</label>
                                    <input type="number" id="thigh" name="thigh"
                                        value="<?php echo isset($thigh) ? $thigh : ''; ?>" <?php echo isset($disableButton) && $disableButton ? 'disabled' : ''; ?>>
                                </div>
                            <?php endif; ?>

                            <label for="activityLevel">Lifestyle:</label>
                            <select name="activityLevel" <?php echo isset($disableButton) && $disableButton ? 'disabled' : ''; ?> required>
                                <option value="sedentary" <?php echo isset($activityLevel) && $activityLevel === 'sedentary' ? 'selected' : ''; ?>>Sedentary - Much resting and very
                                    little physical exercise.</option>
                                <option value="active" <?php echo isset($activityLevel) && $activityLevel === 'active' ? 'selected' : ''; ?>>Active - Every day tasks require physical activity.</option>
                            </select>

                            <!-- Show Calculate button if no data from DB is showing -->
                            <?php if (!isset($bodyFatResults) && !isset($intakeResults)): ?>
                                <button type="submit">Calculate</button>
                            <?php else: ?>
                                <!-- Show Recalculate button if data from DB is showing -->
                                <button type="submit" id="recalculateButton" class="btn-recalculate" <?php echo $disableButton ? 'disabled' : ''; ?>>
                                    Re-calculate
                                </button>
                                <?php if ($disableButton): ?>
                                    <p class="disabled-message">Re-calculate button will be enabled after 14 days. <br>
                                        <?php echo $remainingDays; ?> days remaining.
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- BMI Results Section -->
    <div class="our_schedule_content">
        <h2>----- RESULTS -----</h2>
    </div>
    <div id="resultsSection">
        <div class="results-container" style="border: 1px solid #ddd; padding: 15px;">
            <div class="our_schedule_content">
                <?php if (!isset($intakeResults) && isset($bodyFatResults)): ?>
                    <h2 class="mt-5">LAST GENERATED ON <br></h2>
                    <h5><?php echo $formattedDate; ?> </h5>
                <?php endif; ?>
            </div>
        </div>

        <div class="lower-section">
            <div class="horizontal-display">
                <section class="calculator-results">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="results-form form-section">
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
                                                        <p><strong>Weight Category:</strong> <?php echo $bmiCategory; ?></p>
                                                        <?php
                                                        $bmiDifference = $bmiResults['bmiDifference'];
                                                        $lowerUnderweightRange = $bmiResults['bmi'] - $bmiDifference['underweight'];
                                                        ?>
                                                        <p><strong>Underweight BMI:</strong> 18.50 & below /
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
                                                        <p><strong>Normal BMI:</strong> 18.50 - 24.99 /
                                                            (<?php echo number_format(getWeightFromBMI($lowerNormalRange, $bmiHeight), 2); ?>
                                                            kg -
                                                            <?php echo number_format(getWeightFromBMI($lowerOverweightRange, $bmiHeight), 2); ?>
                                                            kg)
                                                        </p>
                                                        <p><strong>Overweight BMI:</strong> 25 - 29.99 /
                                                            (<?php echo number_format(getWeightFromBMI($lowerOverweightRange, $bmiHeight), 2); ?>
                                                            kg -
                                                            <?php echo number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2); ?>
                                                            kg)
                                                        </p>
                                                        <p><strong>Obese BMI:</strong>
                                                            <?php echo number_format($lowerObeseRange, 2); ?> & above /
                                                            (<?php echo number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2); ?>
                                                            kg & above)
                                                        </p>

                                                    <?php elseif (isset($bodyFatResults)): ?>
                                                        <h2>BMI:</h2>
                                                        <p><strong>Your Weight:</strong>
                                                            <?php echo $savedUserInfo['bmiWeight']; ?> kg</p>
                                                        <p><strong>Your Height:</strong>
                                                            <?php echo $savedUserInfo['bmiHeight']; ?> cm</p>
                                                        <p><strong>Your BMI:</strong>
                                                            <?php echo number_format($bodyFatResults['bmi'], 2); ?> cm</p>
                                                        <p><strong>Weight Category:</strong>
                                                            <?php echo $bodyFatResults['bmiCategory']; ?>
                                                        </p>
                                                        <p><strong>Underweight BMI:</strong> 18.50 & below /
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
                                                        <p><strong>Normal BMI:</strong> 18.50 - 24.99 /
                                                            (<?php echo number_format(getWeightFromBMI($lowerNormalRange, $bmiHeight), 2); ?>
                                                            kg -
                                                            <?php echo number_format(getWeightFromBMI($lowerOverweightRange, $bmiHeight), 2); ?>
                                                            kg)
                                                        </p>
                                                        <p><strong>Overweight BMI:</strong> 25 - 29.99 /
                                                            (<?php echo number_format(getWeightFromBMI($lowerOverweightRange, $bmiHeight), 2); ?>
                                                            kg -
                                                            <?php echo number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2); ?>
                                                            kg)
                                                        </p>
                                                        <p><strong>Obese BMI:</strong>
                                                            <?php echo number_format($lowerObeseRange, 2); ?> & above /
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
                                        <!-- SESSION STUFF -->
                                        <h2>Current Body Fat:</h2>
                                        <p><strong>Age:</strong> <?php echo $age; ?> years</p>
                                        <p><strong>Gender:</strong> <?php echo ucfirst($gender); ?></p>
                                        <p><strong>Waist Circumference:</strong> <?php echo $waist; ?> cm
                                        </p>
                                        <p><strong>Neck Circumference:</strong> <?php echo $neck; ?> cm</p>
                                        <p><strong>Height:</strong> <?php echo $bmiHeight; ?> cm</p>
                                        <?php if (isset($hip) && $hip !== '' && $hip != 0): ?>
                                            <p><strong>Hip Circumference:</strong> <?php echo $hip; ?> cm</p>
                                        <?php endif; ?>

                                        <?php if (isset($thigh) && $thigh !== '' && $thigh != 0): ?>
                                            <p><strong>Thigh Circumference:</strong> <?php echo $thigh; ?> cm</p>
                                        <?php endif; ?>


                                        <p><strong>Body Fat Percentage:</strong>
                                            <?php echo number_format($bodyFatPercentage, 2); ?>%</p>
                                        <p><strong>Fat Body Mass:</strong>
                                            <?php echo number_format($fatMass, 2); ?> kg</p>
                                        <p><strong>Lean Body Mass:</strong>
                                            <?php echo number_format($leanMass, 2); ?> kg
                                        </p>
                                        <p><strong>Important Note:</strong> The results of these
                                            calculations are only an estimate since they are based on many different
                                            assumptions to make
                                            them as applicable to as many people as possible. For more accurate measurements
                                            of body
                                            fat, the use of instruments such as skin caliper, bioelectric impedance
                                            analysis, or hydrostatic density testing is necessary.</p>
                                    <?php elseif ($bodyFatResults): ?>

                                        <!-- DATABASE STUFF -->
                                        <h2>Body Fat:</h2>
                                        <p><strong>Age:</strong> <?php echo $savedUserInfo['age']; ?> years
                                        </p>
                                        <p><strong>Gender:</strong>
                                            <?php echo ucfirst($savedUserInfo['gender']); ?></p>
                                        <p><strong>Waist Circumference:</strong>
                                            <?php echo $savedUserInfo['waist']; ?> cm
                                        </p>
                                        <p><strong>Neck Circumference:</strong>
                                            <?php echo $savedUserInfo['neck']; ?> cm</p>
                                        <p><strong>Height:</strong>
                                            <?php echo $savedUserInfo['bmiHeight']; ?> cm</p>
                                        <?php if (isset($hip) && $hip !== '' && $hip != 0): ?>
                                            <p><strong>Hip Circumference:</strong> <?php echo $hip; ?> cm</p>
                                        <?php endif; ?>

                                        <?php if (isset($thigh) && $thigh !== '' && $thigh != 0): ?>
                                            <p><strong>Thigh Circumference:</strong> <?php echo $thigh; ?> cm</p>
                                        <?php endif; ?>
                                        <p><strong>Body Fat Percentage:</strong>
                                            <?php echo number_format($bodyFatResults['bodyFatPercentage'], 2); ?>%
                                        </p>
                                        <p><strong>Fat Body Mass:</strong>
                                            <?php echo number_format($bodyFatResults['fatMass'], 2); ?> kg</p>
                                        <p><strong>Lean Body Mass:</strong>
                                            <?php echo number_format($bodyFatResults['leanMass'], 2); ?> kg</p>
                                        <p><strong>Important Note:</strong> The results of these
                                            calculations are only an estimate since they are based on many different
                                            assumptions to make
                                            them as applicable to as many people as possible. For more accurate measurements
                                            of body
                                            fat, the use of instruments such as skin caliper, bioelectric impedance
                                            analysis, or hydrostatic density testing is necessary.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Ideal Weight Section -->
                <section class="calculator-results">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="results-form form-section">
                                    <?php if (isset($intakeResults)): ?>
                                        <!-- SESSION STUFF -->
                                        <h2>Current Ideal Weight:</h2>
                                        <p>Hamwi (1964): <?php echo number_format($hamwiIBW_kg, 2); ?> kg</p>
                                        <p>Devine (1974): <?php echo number_format($devineIBW, 2); ?> kg</p>
                                        <p>Robinson (1983): <?php echo number_format($robinsonIBW, 2); ?> kg</p>
                                        <p>Miller (1983): <?php echo number_format($millerIBW, 2); ?> kg</p>
                                    <?php elseif (isset($bodyFatResults)): ?>
                                        <!-- DATABASE STUFF -->
                                        <h2>Ideal Weight:</h2>
                                        <p><strong>Hamwi (1964):</strong>
                                            <?php echo number_format($bodyFatResults['hamwiIBW_kg'], 2); ?> kg</p>
                                        <p><strong>Devine (1974):</strong>
                                            <?php echo number_format($bodyFatResults['devineIBW'], 2); ?> kg</p>
                                        <p><strong>Robinson (1983):</strong>
                                            <?php echo number_format($bodyFatResults['robinsonIBW'], 2); ?> kg</p>
                                        <p><strong>Miller (1983):</strong>
                                            <?php echo number_format($bodyFatResults['millerIBW'], 2); ?> kg</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Caloric and Protein Intake Results Section -->
                <section class="calculator-results">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="results-form form-section">
                                    <?php if (isset($intakeResults)): ?>
                                        <!-- SESSION STUFF -->
                                        <h2>Current Recommended Goal, Calorie and Protein Intake:</h2>
                                        <p><strong>Recommended Goal:</strong>
                                            <?php echo ucwords(str_replace('-', ' ', $intakeResults['goal'])); ?></p>
                                        <p><strong>Lifestyle:</strong> <?php echo ucfirst($activityLevel); ?></p>
                                        <p><strong>Caloric Intake:</strong>
                                            <?php echo number_format($intakeResults['caloricIntake']); ?> calories/day</p>
                                        <p><strong>Protein Intake:</strong>
                                            <?php echo number_format($intakeResults['proteinIntake']); ?> grams/day</p>
                                        <p><strong>Important Note:</strong> You can find the caloric and protein contents of
                                            the foods you eat on the nutrition labels on the packages.</p>
                                    <?php elseif (isset($bodyFatResults)): ?>
                                        <!-- DATABASE STUFF -->
                                        <h2>Recommended Goal, Calorie and Protein Intake:</h2>
                                        <p><strong>Recommended Goal:</strong>
                                            <?php echo ucwords(str_replace('-', ' ', $bodyFatResults['recommendedGoal'])); ?>
                                        </p>
                                        <p><strong>Lifestyle:</strong>
                                            <?php echo ucfirst($bodyFatResults['activityLevel']); ?>
                                        </p>
                                        <p><strong>Caloric Intake:</strong>
                                            <?php echo number_format($bodyFatResults['caloricIntake']); ?> calories/day</p>
                                        <p><strong>Protein Intake:</strong>
                                            <?php echo number_format($bodyFatResults['proteinIntake']); ?> grams/day</p>
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
                                        <h2><?php echo ucwords(str_replace('-', ' ', $goal)); ?></h2>
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
                                                    combination of a calorie deficit, dietary changes, and increased physical
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
                                                <p>Weight-gain involves increasing calorie intake and adopting a balanced diet
                                                    to achieve a healthy body mass. Incorporating strength training exercises
                                                    can promote muscle growth.</p>
                                                <?php foreach ($weightgainRecommendations as $recommendation): ?>
                                                    <li><?php echo $recommendation; ?></li>
                                                <?php endforeach; ?>
                                            <?php elseif ($goal === 'maintenance'): ?>
                                                <?php
                                                $maintenanceRecommendations = [
                                                    'Chicken breast',
                                                    'Fish',
                                                    'Eggs',
                                                    'Quinoa',
                                                    'Brown rice',
                                                    'Mixed vegetables',
                                                    'Fruits (apple, orange, berries)',
                                                    'Nuts and seeds',
                                                    'Greek yogurt',
                                                    'Whole grains'
                                                ];
                                                ?>
                                                <p>Maintenance involves sustaining your current weight and body composition by
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
                                        <h2><?php echo ucwords(str_replace('-', ' ', $bodyFatResults['recommendedGoal'])); ?>
                                        </h2>
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
                                                    combination of a calorie deficit, dietary changes, and increased physical
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
                                                <p>Weight-gain involves increasing calorie intake and adopting a balanced diet
                                                    to achieve a healthy body mass. Incorporating strength training exercises
                                                    can promote muscle growth.</p>
                                                <?php foreach ($weightgainRecommendations as $recommendation): ?>
                                                    <li><?php echo $recommendation; ?></li>
                                                <?php endforeach; ?>
                                            <?php elseif ($bodyFatResults['recommendedGoal'] === 'maintenance'): ?>
                                                <?php
                                                $maintenanceRecommendations = [
                                                    'Chicken breast',
                                                    'Fish',
                                                    'Eggs',
                                                    'Quinoa',
                                                    'Brown rice',
                                                    'Mixed vegetables',
                                                    'Fruits (apple, orange, berries)',
                                                    'Nuts and seeds',
                                                    'Greek yogurt',
                                                    'Whole grains'
                                                ];
                                                ?>
                                                <p>Maintenance involves sustaining your current weight and body composition by
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
    </div>

    <!-- Print Button -->
    <div class="container">
        <div class="row">
            <div class="calculator-form form-section border-0">
                <button onclick="printResults()">Print Results</button>
            </div>
        </div>
    </div>

    <script>
        function printResults() {
            var content = document.getElementById('resultsSection').innerHTML;
            var myWindow = window.open('', '', 'width=800,height=600');
            myWindow.document.write('<html><head><title>Print Results</title><style>button { display: none; }</style></head><body>');
            myWindow.document.write(content);
            myWindow.document.write('</body></html>');
            myWindow.document.close();
            myWindow.print();
        }
    </script>



    <?php if ($showDietPlanningSection): ?>
        <div class="results-container" style="border: 1px solid #ddd; padding: 15px;">
            <div class="our_schedule_content">
                <?php if (!isset($intakeResults) && isset($bodyFatResults)): ?>
                    <h2>PROGRESS TRACKING</h2>
                    <h5><?php echo $formattedDate; ?> </h5>
                </div>
                <div id="chartContainer">
                    <canvas id="bodyReportsChart"></canvas>
                </div>
                <div id="tableContainer">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <thead>
                            <tr>
                                <th
                                    style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: #f0f0f0; color: black;">
                                    Date
                                </th>
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
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                        <?php echo date('M d, Y', strtotime($data['created_at'])); ?>
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                        <?php echo htmlspecialchars($data['weight']); ?>
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                        <?php echo htmlspecialchars($data['bmi']); ?>
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                        <?php echo htmlspecialchars($data['bodyFatPercentage']); ?>
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                        <?php echo htmlspecialchars($data['fatMass']); ?>
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                        <?php echo htmlspecialchars($data['leanMass']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>


    <?php
    if (isset($intakeResults)) {
        echo '
    <div class="calculator-form form-section border-white">
        <form id="resultsForm" method="POST" action="store_results.php">
            <input type="hidden" name="bmi" id="bmi" value="' . $bmi . '">
            <input type="hidden" name="bmiCategory" id="bmiCategory" value="' . $bmiCategory . '">
            <input type="hidden" name="recommendedGoal" id="recommendedGoal" value="' . $recommendedGoal . '">
            <input type="hidden" name="bodyFatPercentage" id="bodyFatPercentage" value="' . $bodyFatPercentage . '">
            <input type="hidden" name="fatMass" id="fatMass" value="' . $fatMass . '">
            <input type="hidden" name="leanMass" id="leanMass" value="' . $leanMass . '">
            <input type="hidden" name="hamwiIBW_kg" id="hamwiIBW_kg" value="' . $hamwiIBW_kg . '">
            <input type="hidden" name="devineIBW" id="devineIBW" value="' . $devineIBW . '">
            <input type="hidden" name="robinsonIBW" id="robinsonIBW" value="' . $robinsonIBW . '">
            <input type="hidden" name="millerIBW" id="millerIBW" value="' . $millerIBW . '">
            <input type="hidden" name="caloricIntake" id="caloricIntake" value="' . $caloricIntake . '">
            <input type="hidden" name="proteinIntake" id="proteinIntake" value="' . $proteinIntake . '">
            <button type="submit">Save Results</button>
        </form>
        <div id="message"></div>
        <p><strong>To track your progress, remember to Save Results.</strong> <br><small> Saved Results will be graphed and used to tailor Recommended Meal and <br> Exercise Plans, and will appear on your profile upon revisit.</small></p>
    </div>';
    }
    ?>
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


        <!-- CURRENT SESSION (ESSENTIAL TIER) Diet Planning Section -->
        <?php if ($showDietPlanningSection && isset($goal_name)): ?>
            <?php if ($plan === 'essential'): ?>
                <section class="our_schedule_section diet-planning">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="our_schedule_content">
                                    <?php if (!empty($meal_plan)): ?>
                                        <h5>DIET PLAN</h5>
                                        <h2>RECOMMENDED DIET PLAN FOR<br><?php echo strtoupper($goal_name); ?></h2>
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

                        foreach ($days as $day): ?>
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
                                <p>Click the food you have consumed to track your progress.
                                </p>
                                <p><strong>Tip:</strong> You can also eat the foods in any order, as long as you meet the
                                    recommended daily macronutrients.</p>
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
                                                            data-time-slot="<?php echo $timeSlot; ?>"
                                                            data-calories="<?php echo $foodItem['energy_kcal']; ?>"
                                                            data-protein="<?php echo $foodItem['protein_g']; ?>">
                                                            <br><?php echo $foodItem['english_name']; ?><br>
                                                            <br><?php echo $foodItem['filipino_name']; ?><br><br>
                                                            <strong>Protein (g):</strong> <?php echo $foodItem['protein_g']; ?><br>
                                                            <strong>Calories (kcal):</strong>
                                                            <?php echo $foodItem['energy_kcal']; ?><br>
                                                            <strong>Measure:</strong>
                                                            <?php echo $foodItem['household_measure']; ?><br><br>
                                                        </td>
                                                        <?php $mealIndex++; ?>
                                                    <?php endfor; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="total-<?php echo strtolower($day); ?>" class="border border-grey large-counter-text"
                                    data-calories="0" data-protein="0">
                                    <?php
                                    echo 'Calories: <span id="calories-' . strtolower($day) . '">0</span> / ' . $bodyFatResults['caloricIntake'] . '<br>';
                                    echo 'Protein (g): <span id="protein-' . strtolower($day) . '">0</span> / ' . $bodyFatResults['proteinIntake'] . '<br>';
                                    ?>
                                    <div class="note">
                                        <b>Meal plan food suggestions are based on the Philippine Department of Science and
                                            Technology, Food and Nutrition Research Institute, Food Exchange List</b>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
            </div>

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
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Recommended Meal
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Plan
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Recommended
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exercises
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
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Recommended Meal
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Plan
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Recommended
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exercises
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Progress Tracking
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Access to Forums
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>Bonus Exercise
                                            routines
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;from
                                            world famous &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;bodybuilders.
                                            (Mike
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
            </section>

            <!-- ESSENTIAL TIER QUOTE SECTION -->
            <section class="quote_section">
                <div class="container">
                    <div class="row" data-aos="fade-right">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="quote_content">
                                <h2>“<?php echo htmlspecialchars($quote['quote']); ?>”</h2>
                                <div class="quote_content_wrapper">
                                    <div class="quote_wrapper">
                                        <h6><?php echo htmlspecialchars($quote['author']); ?></h6>
                                        <span><?php echo htmlspecialchars($quote['title']); ?></span>
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
        <?php endif; ?>
    <?php endif; ?>


    <!-- AUTO GENERATED (PREMIUM AND ELITE TIER) DIET PLAN -->
    <?php if ($showDietPlanningSection && isset($goal_name)): ?>
        <?php if ($plan === 'premium' || $plan === 'elite'): ?>
            <section class="our_schedule_section diet-planning">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="our_schedule_content">
                                <?php if (!empty($meal_plan)): ?>
                                    <h5>DIET PLAN</h5>
                                    <h2>RECOMMENDED DIET PLAN FOR<br><?php echo strtoupper($goal_name); ?></h2>
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

                    foreach ($days as $day): ?>
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
                            <p>Click the food you have consumed to track your progress.
                            <p><strong>Tip:</strong> You can also eat the foods in any order, as long as you meet the
                                recommended
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
                                                        data-time-slot="<?php echo $timeSlot; ?>"
                                                        data-calories="<?php echo $foodItem['energy_kcal']; ?>"
                                                        data-protein="<?php echo $foodItem['protein_g']; ?>">
                                                        <br><?php echo $foodItem['english_name']; ?><br>
                                                        <br><?php echo $foodItem['filipino_name']; ?><br><br>
                                                        <strong>Protein (g):</strong> <?php echo $foodItem['protein_g']; ?><br>
                                                        <strong>Calories (kcal):</strong>
                                                        <?php echo $foodItem['energy_kcal']; ?><br>
                                                        <strong>Measure:</strong>
                                                        <?php echo $foodItem['household_measure']; ?><br><br>
                                                    </td>
                                                    <?php $mealIndex++; ?>
                                                <?php endfor; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="total-<?php echo strtolower($day); ?>" class="border border-grey large-counter-text"
                                data-calories="0" data-protein="0">
                                <?php
                                echo 'Calories: <span id="calories-' . strtolower($day) . '">0</span> / ' . $bodyFatResults['caloricIntake'] . '<br>';
                                echo 'Protein (g): <span id="protein-' . strtolower($day) . '">0</span> / ' . $bodyFatResults['proteinIntake'] . '<br>';
                                ?>
                                <div class="note">
                                    <b>Meal plan food suggestions are based on the Philippine Department of Science and
                                        Technology,
                                        Food and Nutrition Research Institute, Food Exchange List</b>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                </div>
            </section>

            <!-- AUTO GENERATED (PREMIUM AND ELITE TIER) EXERCISE PLAN -->
            <?php if (isset($goal_name)): ?>
                <?php if ($plan === 'premium' || $plan === 'elite'): ?>
                    <section class="our_schedule_section exercise-planning">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="our_schedule_content">
                                        <?php if (!empty($exercise_plan)): ?>
                                            <h5>EXERCISE PLAN</h5>
                                            <h2>RECOMMENDED EXERCISE PLAN FOR<br><?php echo strtoupper($goal_name); ?></h2>
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

                            foreach ($days as $day): ?>
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
                                    <p>Track your progress by marking the exercises you've completed.
                                    </p>
                                    <p><strong>Tip: Aim to do at least 5 exercises from all 3 categories in a day (Cardio, Strength,
                                            Core).</strong></p>
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
                                                    <?php $exerciseItem = $exercise_plan[$exerciseIndex % $totalExercises]; ?>
                                                    <tr>
                                                        <td><?php echo $timeSlot; ?></td>
                                                        <?php for ($i = 0; $i < 6; $i++): ?>
                                                            <?php $exerciseItem = $exercise_plan[$exerciseIndex % $totalExercises]; ?>
                                                            <td class="exerciseItem">
                                                                <br><strong><?php echo $exerciseItem['name']; ?><br></strong><?php echo $exerciseItem['duration']; ?><br><br>
                                                                <strong><?php echo $exerciseItem['intensity']; ?>
                                                                    Intensity</strong><br><?php echo $exerciseItem['category']; ?><br><br>
                                                            </td>
                                                            <?php $exerciseIndex++; ?>
                                                        <?php endfor; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="total-exercises-<?php echo strtolower($day); ?>" class="border border-grey large-counter-text">
                                        <?php echo 'Minimum Exercises to Complete: <span class="minimum-to-complete">5</span><br>'; ?>
                                        <?php echo 'Total Exercises Completed: <span id="exerciseCounter-' . strtolower($day) . '">0</span><br>'; ?>
                                    </div>
                                    <input type="hidden" name="day" value="<?php echo $day; ?>">
                                    <input type="hidden" name="exercise_plan" id="exercisePlanData-<?php echo strtolower($day); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endif; ?>

            <!-- QUOTE SECTION -->
            <section class="quote_section">
                <div class="container">
                    <div class="row" data-aos="fade-right">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="quote_content">
                                <h2>“<?php echo htmlspecialchars($quote['quote']); ?>”</h2>
                                <div class="quote_content_wrapper">
                                    <div class="quote_wrapper">
                                        <h6><?php echo htmlspecialchars($quote['author']); ?></h6>
                                        <span><?php echo htmlspecialchars($quote['title']); ?></span>
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
        <?php endif; ?>
    <?php endif; ?>


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

        // Function to attach event listeners to meal items
        function attachEventListeners() {
            const mealItems = document.querySelectorAll('.mealItem');
            console.log('Attaching event listeners to meal items');
            mealItems.forEach(item => {
                item.addEventListener('click', () => {
                    console.log('Meal item clicked:', item);
                    const day = item.getAttribute('data-day');
                    const calories = parseFloat(item.getAttribute('data-calories'));
                    const protein = parseFloat(item.getAttribute('data-protein'));

                    // Check if intake has reached the threshold
                    const totalElement = document.getElementById(`total-${day}`);
                    const currentCalories = parseFloat(totalElement.getAttribute('data-calories')) || 0;
                    const currentProtein = parseFloat(totalElement.getAttribute('data-protein')) || 0;

                    // Get max values from the span elements in the HTML
                    const maxCalories = parseFloat(document.querySelector(`#calories-${day}`).parentElement.textContent.split("/")[1].trim()) || 0;
                    const maxProtein = parseFloat(document.querySelector(`#protein-${day}`).parentElement.textContent.split("/")[1].trim()) || 0;

                    // Prevent selecting if the intake has reached or exceeded the threshold and the item is not already consumed
                    if ((currentCalories >= maxCalories && !item.classList.contains('consumed')) ||
                        (currentProtein >= maxProtein && !item.classList.contains('consumed'))) {
                        alert('You have reached the maximum intake for calories or protein.');
                        return;
                    }

                    // If already consumed, do nothing (prevent unselecting)
                    if (item.classList.contains('consumed')) {
                        return;
                    }

                    // Mark the item as consumed and disable further clicks
                    item.classList.add('consumed');
                    item.style.pointerEvents = 'none'; // Disable further interaction

                    // Update daily totals
                    totalElement.setAttribute('data-calories', currentCalories + calories);
                    totalElement.setAttribute('data-protein', currentProtein + protein);

                    // Update the display values for calories and protein
                    document.getElementById(`calories-${day}`).innerText = totalElement.getAttribute('data-calories');
                    document.getElementById(`protein-${day}`).innerText = totalElement.getAttribute('data-protein');

                    // Check if the intake is already at or above the threshold
                    if (parseFloat(totalElement.getAttribute('data-calories')) >= maxCalories) {
                        alert('You have reached the maximum calorie intake for the day.');
                    }

                    if (parseFloat(totalElement.getAttribute('data-protein')) >= maxProtein) {
                        alert('You have reached the maximum protein intake for the day.');
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Attach event listeners when the page loads
            attachEventListeners();
            attachExerciseEventListeners();
        });

        // Function to attach event listeners to exercise items
        function attachExerciseEventListeners() {
            const exerciseItems = document.querySelectorAll('.exerciseItem');
            exerciseItems.forEach(item => {
                item.addEventListener('click', () => {
                    item.classList.toggle('completed');
                    const day = item.closest('table').id.split('-')[1]; // Extract table ID
                    updateExerciseCounter(day);
                });
            });
        }

        // Update the exercise counter for the specific day
        function updateExerciseCounter(day) {
            const completedExercises = document.querySelectorAll(`#exercisePlanTable-${day} .exerciseItem.completed`).length;
            const minimumExercises = 5; // Minimum required exercises
            const exerciseCounter = document.getElementById(`exerciseCounter-${day}`);
            exerciseCounter.innerText = completedExercises;

            const minimumToComplete = document.querySelector(`#total-exercises-${day} .minimum-to-complete`);
            if (completedExercises >= minimumExercises) {
                minimumToComplete.innerHTML = `<span style="color: green;">${minimumExercises} - Minimum hit! Good job!</span>`;
            } else {
                minimumToComplete.innerHTML = `${minimumExercises}`;
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('bodyReportsChart').getContext('2d');
            const data = <?php echo json_encode($mergedData); ?>;
            const labels = data.map(item => new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }));
            const bmiData = data.map(item => item.bmi);
            const bodyFatPercentageData = data.map(item => item.bodyFatPercentage);
            const fatMassData = data.map(item => item.fatMass);
            const leanMassData = data.map(item => item.leanMass);
            const weightData = data.map(item => item.weight);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Weight',
                            data: weightData,
                            borderColor: 'rgba(153, 102, 255, 1)',
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderWidth: 3,
                            pointStyle: 'star',
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(153, 102, 255, 1)',
                        },
                        {
                            label: 'BMI',
                            data: bmiData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 3,
                            pointStyle: 'circle',
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                        },
                        {
                            label: 'Body Fat Percentage',
                            data: bodyFatPercentageData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 3,
                            pointStyle: 'rect',
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                        },
                        {
                            label: 'Fat Mass',
                            data: fatMassData,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderWidth: 3,
                            pointStyle: 'triangle',
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                        },
                        {
                            label: 'Lean Mass',
                            data: leanMassData,
                            borderColor: 'rgba(255, 206, 86, 1)',
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            borderWidth: 3,
                            pointStyle: 'rectRot',
                            pointRadius: 5,
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
                                text: 'Value',
                                font: {
                                    size: 18
                                }
                            },
                            ticks: {
                                font: {
                                    size: 18,
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
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Saved Results',
                            font: {
                                size: 18,
                                color: 'black'
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                        },
                    }
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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



<style>
    .mealItem {
        cursor: pointer;
    }

    .mealItem.consumed {
        background-color: green;
        color: white;
    }

    .exerciseItem {
        cursor: pointer;
    }

    .exerciseItem.completed {
        background-color: green;
        color: white;
    }

    </script>< !-- Latest compiled JavaScript --><script src="assets/js/jquery-3.6.0.min.js"></script><script src="assets/js/popper.min.js"></script><script src="assets/js/video-popup.js"></script><script src="assets/js/bootstrap.min.js"></script><script src="assets/js/custom.js"></script><script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script><script src="assets/js/owl.carousel.js"></script><script src="assets/js/carousel.js"></script><script src="assets/js/video-section.js"></script><script src="assets/js/counter.js"></script><script src="assets/js/animation.js"></script></body></html>