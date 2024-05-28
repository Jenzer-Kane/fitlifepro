<?php
session_start();

include 'database.php';

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
        $caloricIntake = $bmiWeight * $caloriesPerKg + 500;
    } elseif ($recommendedGoal === 'weight-loss') {
        // Cutting: aim for 1.2g of protein per kg of body weight and a 500 calorie deficit
        $proteinIntake = $bmiWeight * $proteinRatioCutting;
        $caloricIntake = $bmiWeight * $caloriesPerKg - 500;
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
    $gender = $_POST['gender'];
    $waist = floatval($_POST['waist']);
    $neck = floatval($_POST['neck']);
    $hip = isset($_POST['hip']) ? floatval($_POST['hip']) : 0;
    $thigh = isset($_POST['thigh']) ? floatval($_POST['thigh']) : 0;

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

    // Convert the weight from pounds to kilograms
    $hamwiIBW_kg = $hamwiIBW * 0.45359237;

    // Calculate healthy BMI range
    $lowerNormalRange = 18.5 * (($bmiHeight / 100) ** 2);
    $upperNormalRange = 24.9 * (($bmiHeight / 100) ** 2);

    // Store the body fat results for displaying in the HTML later
    $bodyFatResults = [
        'age' => $age,
        'gender' => $gender,
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
}

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
$timeSlots = ['Breakfast', 'Snack 1', 'Lunch', 'Snack 2', 'Dinner'];
$exercisetimeSlots = ['213', 'Snack 1', 'Lunch', 'Snack 2', 'Dinner'];

function getExercises($recommendedGoal)
{
    include 'database.php';

    // Adjust the SQL query based on the recommended goal
    switch ($recommendedGoal) {
        case 'weight-loss':
            $intensity = 'Low';
            break;
        case 'weight-gain':
            $intensity = 'High';
            break;
        case 'maintenance':
            $intensity = 'Moderate';
            break;
        default:
            $intensity = '';
            break;
    }

    // Fetch exercises based on the recommended goal and intensity
    $query = "SELECT * FROM exercises WHERE intensity = '$intensity'";
    $result = $mysqli->query($query);

    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "Error retrieving exercises: " . $mysqli->error;
        return [];
    }
}


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
            font-size: 1.2em;
            text-align: center;
            position: relative;
            top: -100px;
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
                                    <a class="nav-link" href="./pricing.php">Pricing</a>
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
                            <h1 data-aos="fade-up">Profile</h1>
                            <div class="btn_wrapper">
                                <span class="sub_home_span">Home </span>-<span class="sub_span"> Profile</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!DOCTYPE html>
    <html lang="en">

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
                        <form method="post" action="" id="calculatorForm" onsubmit="return validateForm()">
                            <!-- BMI Section -->
                            <label for="bmiWeight">Weight (kg):</label>
                            <input type="text" name="bmiWeight" id="bmiWeight" required>

                            <label for="bmiHeight">Height (cm):</label>
                            <input type="text" name="bmiHeight" id="bmiHeight" required>

                            <!-- Body Fat Calculator Section -->
                            <label for="age">Age:</label>
                            <input type="number" id="age" name="age" required>

                            <label for="gender">Gender:</label>
                            <select id="gender" name="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>

                            <label for="waist">Waist (cm):</label>
                            <input type="number" id="waist" name="waist" required>

                            <label for="neck">Neck (cm):</label>
                            <input type="number" id="neck" name="neck" required>

                            <!-- Only for females -->
                            <div id="hipSection" style="display: none;">
                                <label for="hip">Hip Circumference (cm):</label>
                                <input type="text" name="hip" id="hip">

                                <label for="thigh">Thigh Circumference (cm):</label>
                                <input type="text" name="thigh" id="thigh">
                            </div>

                            <!-- Calorie and Protein Intake Section -->
                            <label for="activityLevel">Lifestyle:</label>
                            <select name="activityLevel" required>
                                <option value="sedentary">Sedentary - Much resting and very little physical
                                    exercise.</option>
                                <option value="active">Active - Every day tasks require physical activity.
                                </option>
                            </select>

                            <button type="submit">Calculate</button>
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
    <div class="results-container" style="border: 1px solid #ddd; padding: 15px;">
        <div class="lower-section">
            <div class="horizontal-display">
                <section class="calculator-results">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="results-form form-section">
                                    <?php
                                    if (isset($bmiResults['bmi'])) {
                                        $bmi = $bmiResults['bmi'];

                                        echo '<h2>BMI:</h2>';

                                        // Display Weight
                                        echo (isset($intakeResults['weight'])) ? '<p><strong>Your Weight:</strong> ' . $intakeResults['weight'] . ' kg</p>' : '';

                                        // Display Height
                                        echo (isset($intakeResults['height'])) ? '<p><strong>Your Height:</strong> ' . $intakeResults['height'] . ' cm</p>' : '';

                                        // Display Your BMI
                                        echo '<p><strong>Your BMI:</strong> ' . number_format($bmi, 2) . '</p>';

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

                                        // Display BMI category
                                        echo '<p><strong>Weight Category:</strong> ' . $bmiCategory . '</p>';

                                        // Display BMI Range
                                        $bmiDifference = $bmiResults['bmiDifference'];

                                        // Underweight Range
                                        $lowerUnderweightRange = $bmi - $bmiDifference['underweight'];
                                        echo '<p><strong>Underweight BMI:</strong> 18.50 & below ' . ' / (' . number_format(getWeightFromBMI(18.5, $bmiHeight), 2) . ' kg & below)</p>';
                                        //  Normal Range
                                        $lowerNormalRange = 18.5;
                                        $upperNormalRange = 24.9;
                                        //  Overweight Range
                                        $lowerOverweightRange = 25;
                                        $upperOverweightRange = 29.9;
                                        // Obese Range
                                        $lowerObeseRange = 30;
                                        echo '<p><strong>Normal BMI:</strong> 18.50 - 24.99 / (' . number_format(getWeightFromBMI($lowerNormalRange, $bmiHeight), 2) . ' kg - ' . number_format(getWeightFromBMI($lowerOverweightRange, $bmiHeight), 2) . ' kg)</p>';
                                        echo '<p><strong>Overweight BMI:</strong> 25 - 29.99 / (' . number_format(getWeightFromBMI($lowerOverweightRange, $bmiHeight), 2) . ' kg - ' . number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2) . ' kg)</p>';
                                        echo '<p><strong>Obese BMI:</strong> ' . number_format($lowerObeseRange, 2) . ' & above ' . ' / (' . number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2) . ' kg & above)</p>';
                                    }
                                    ?>
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
                                    <?php
                                    if (isset($bodyFatResults)) {
                                        // Display the body fat percentage
                                        echo '<h2>Body Fat:</h2>';
                                        echo '<p><strong>Age:</strong> ' . $age . ' years</p>';
                                        echo '<p><strong>Gender:</strong> ' . ucfirst($gender) . '</p>';
                                        echo '<p><strong>Waist Circumference:</strong> ' . $waist . ' cm</p>';
                                        echo '<p><strong>Neck Circumference:</strong> ' . $neck . ' cm</p>';
                                        echo '<p><strong>Height:</strong> ' . $bmiHeight . ' cm</p>';
                                        echo '<p><strong>Hip Circumference:</strong> ' . $hip . ' cm</p>';
                                        echo '<p><strong>Thigh Circumference:</strong> ' . $thigh . ' cm</p>';
                                        echo '<p><strong>Body Fat Percentage:</strong> ' . number_format($bodyFatPercentage, 2) . '%</p>';
                                        echo '<p><strong>Fat Body Mass:</strong> ' . number_format($fatMass, 2) . ' kg</p>';
                                        echo '<p><strong>Lean Body Mass:</strong> ' . number_format($leanMass, 2) . ' kg</p>';
                                        echo '<p><strong>Important Note:</strong> The results of these calculations are only an estimate since they are based on many different assumptions to make them as applicable to as many people as possible. For more accurate measurements of body fat, the use of instruments such as skin caliper, bioelectric impedance analysis or hydrostatic density testing is necessary.</p>';
                                    }

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
                                    ?>
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
                                    <?php
                                    if (isset($bodyFatResults)) {
                                        // Display results
                                        echo '<h2>Ideal Weight:</h2>';
                                        echo '<p>Hamwi (1964): ' . number_format($hamwiIBW_kg, 2) . ' kg</p>';
                                        echo '<p>Devine (1974): ' . number_format($devineIBW, 2) . ' kg</p>';
                                        echo '<p>Robinson (1983): ' . number_format($robinsonIBW, 2) . ' kg</p>';
                                        echo '<p>Miller (1983): ' . number_format($millerIBW, 2) . ' kg</p>';
                                    }
                                    ?>
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
                                    <?php
                                    // Display Caloric and Protein Intake Results
                                    if (isset($intakeResults)) {
                                        echo '<h2>Recommended Goal, Calorie and Protein Intake:</h2>';
                                        echo '<p><strong>Recommended Goal:</strong> ' . ucwords(str_replace('-', ' ', $intakeResults['goal'])) . '</p>';
                                        echo '<p><strong>Lifestyle:</strong> ' . ucfirst($activityLevel) . '</p>';
                                        echo '<p><strong>Caloric Intake:</strong> ' . $intakeResults['caloricIntake'] . ' calories/day</p>';
                                        echo '<p><strong>Protein Intake:</strong> ' . $intakeResults['proteinIntake'] . ' grams/day</p>';

                                        echo '<p><strong>Important Note:</strong> You can find the caloric and protein contents of the foods you eat on the nutrition labels on the packages.</p>';
                                    }
                                    ?>
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
                                    <?php
                                    if (isset($intakeResults)) {
                                        // Display food recommendations based on goal
                                        echo '<h2>Food Recommendations:</h2>';
                                        echo '<h2>' . ucwords(str_replace('-', ' ', $goal)) . '</h2>';
                                        echo '<ul>';

                                        if ($goal === 'weight-loss') {
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
                                            echo 'Weight loss involves reducing overall body weight through a combination of a calorie deficit, dietary
                                        changes, and increased physical activity. The goal is to improve health and fitness by shedding excess fat
                                        and achieving a healthier body composition. <br><br />';
                                            foreach ($weightlossRecommendations as $recommendation) {
                                                echo '<li>' . $recommendation . '</li>';
                                            }
                                        } elseif ($goal === 'weight-gain') {
                                            $weightgainRecommendations = [
                                                'Steak',
                                                'Ground beef',
                                                'Potatoes',
                                                'Rice',
                                                'Sweet potato',
                                                'Whole wheat or wheat bread',
                                                'Peanut butter'
                                            ];
                                            echo 'Weight-gain involves increasing calorie intake and adopting a balanced diet to achieve a healthy body
            mass. Incorporating strength training exercises can promote muscle growth. <br><br />';
                                            foreach ($weightgainRecommendations as $recommendation) {
                                                echo '<li>' . $recommendation . '</li>';
                                            }
                                        } elseif ($goal === 'maintenance') {
                                            $maintenanceRecommendations = [
                                                '<p><strong>Chicken breast</p></strong>',
                                                '<p><strong>Fish</p></strong>',
                                                '<p><strong>Eggs</p></strong>',
                                                '<p><strong>Quinoa</p></strong>',
                                                '<p><strong>Brown rice</p></strong>',
                                                '<p><strong>Mixed vegetables</p></strong>',
                                                '<p><strong>Fruits (apple, orange, berries)</p></strong>',
                                                '<p><strong>Nuts and seeds</p></strong>',
                                                '<p><strong>Greek yogurt</p></strong>',
                                                '<p><strong>Whole grains</p></strong>'
                                            ];
                                            echo '<p><strong>Maintenance involves sustaining your current weight and body composition by balancing
                                        caloric intake with energy expenditure. Focus on a varied and balanced diet to maintain overall
                                        health.</p></strong> <br><br />';
                                            foreach ($maintenanceRecommendations as $recommendation) {
                                                echo '<li>' . $recommendation . '</li>';
                                            }
                                        }
                                        echo '</ul>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Diet Planning Section -->
    <section class="our_schedule_section diet-planning">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="our_schedule_content">
                        <?php if (!empty($meal_plan)): ?>
                            <h5>MEAL PLAN</h5>
                            <h2>TAILORED MEAL PLAN FOR<br>
                                <?php echo strtoupper($goal_name); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="diet-horizontal-display">
                <?php
                // Initialize counters for round-robin distribution
                $mealIndex = 0;
                $totalMeals = count($meal_plan);

                // Initialize $dailyTotals array
                $dailyTotals = array();

                // Loop through each day to create a separate table
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
                        <?php
                        // Display Caloric and Protein Intake Results
                        if (isset($intakeResults)) {
                            echo '<p>Regenerate the meal plan with the Regenerate button.</strong></p>';
                            echo '<p>Click the food you have already consumed to track your progress, this disables the Regenerate button.</p>';
                            echo '<p><strong>Note:</strong> You can eat the foods in any order, as long as you meet the recommended daily macronutrients.</strong></p>';
                        } ?>
                        <!-- Add a container div for each day's table and shuffle button -->
                        <div class="diet-horizontal-display">
                            <table class="border border-black" id="mealPlanTable-<?php echo strtolower($day); ?>">
                                <thead>
                                    <tr>
                                        <th>Time Slot</th>
                                        <?php
                                        // Generate column headers for food items
                                        for ($i = 0; $i < count($timeSlots); $i++) {
                                            echo '<th>Food Item</th>';
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($timeSlots as $timeSlot): ?>
                                        <?php
                                        // Select a food item for the current time slot and day
                                        $foodItem = $meal_plan[$mealIndex % $totalMeals];

                                        // Update total calories and protein for the day
                                        if (!isset($dailyTotals[$day])) {
                                            $dailyTotals[$day] = array('calories' => 0, 'protein' => 0);
                                        }
                                        $dailyTotals[$day]['calories'] += $foodItem['energy_kcal'];
                                        $dailyTotals[$day]['protein'] += $foodItem['protein_g'];
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $timeSlot; ?>
                                            </td>
                                            <?php
                                            // Fill the remaining cells with food items
                                            for ($i = 0; $i < count($timeSlots); $i++) {
                                                $foodItem = $meal_plan[$mealIndex % $totalMeals];
                                                echo '<td class="mealItem" data-day="' . strtolower($day) . '" data-time-slot="' . $timeSlot . '" data-calories="' . $foodItem['energy_kcal'] . '" data-protein="' . $foodItem['protein_g'] . '">';
                                                echo '<br>' . $foodItem['english_name'] . '<br>';
                                                echo '<br>' . $foodItem['filipino_name'] . '<br>' . '<br>';
                                                echo '<strong>Protein (g):</strong> ' . $foodItem['protein_g'] . '<br>';
                                                echo '<strong>Calories (kcal):</strong> ' . $foodItem['energy_kcal'] . '<br>';
                                                echo '<strong>Measure:</strong> ' . $foodItem['household_measure'] . '<br>' . '<br>';
                                                echo '</td>';
                                                $mealIndex++;
                                            }
                                            ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Add the total calories and protein for each day -->
                        <div id="total-<?php echo strtolower($day); ?>" class="border border-grey large-counter-text"
                            data-calories="0" data-protein="0">
                            <?php
                            echo 'Calories: <span id="calories-' . strtolower($day) . '">0</span> / ' . $intakeResults['caloricIntake'] . '<br>';
                            echo 'Protein (g): <span id="protein-' . strtolower($day) . '">0</span> / ' . $intakeResults['proteinIntake'] . '<br>';
                            ?>
                            <!-- Add the shuffle button for each day -->
                            <div class="calculator-form form-section border-0">
                                <button class="shuffle-button"
                                    onclick="shuffleMealPlan('<?php echo strtolower($day); ?>')">Regenerate</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
    </section>
    <div class="note">
        <?php
        if (isset($intakeResults)) {
            echo '<b>Meal plan food suggestions are based on the Philippine Department of Science and Technology, Food and Nutrition Research Institute, Food Exchange List</b>';
        }
        ?>
    </div>


    <!-- Exercise Planning Section -->
    <section class="our_schedule_section exercise-planning">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="our_schedule_content">
                        <?php if (!empty($meal_plan)): ?>
                            <h5>EXERCISE PLAN</h5>
                            <h2>TAILORED EXERCISE PLAN FOR<br><?php echo strtoupper($goal_name); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="exercise-horizontal-display">
                <?php
                // Initialize counters for round-robin distribution
                $exerciseIndex = 0;
                $totalExercises = count($exercise_plan);
                $dailyExerciseTotals = array();

                // Loop through each day to create a separate section
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
                        <?php if (isset($intakeResults)): ?>
                            <p><strong>Track your progress by marking the exercises you've completed.</strong></p>
                        <?php endif; ?>
                        <!-- Add a container div for each day's table and shuffle button -->
                        <div class="diet-horizontal-display">
                            <table class="border border-black" id="exercisePlanTable-<?php echo strtolower($day); ?>">
                                <thead>
                                    <tr>
                                        <th>Time Slot</th>
                                        <th>Exercise</th>
                                        <th>Description</th>
                                        <th>Duration</th>
                                        <th>Category</th>
                                        <th>Intensity</th>
                                        <th>Exercise Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($exercisetimeSlots as $timeSlot): ?>
                                        <?php
                                        // Select an exercise for the current time slot and day
                                        $exerciseItem = $exercise_plan[$exerciseIndex % $totalExercises];

                                        // Update total reps and duration for the day
                                        if (!isset($dailyExerciseTotals[$day])) {
                                            $dailyExerciseTotals[$day] = array('duration' => 0);
                                        }

                                        ?>
                                        <tr>
                                            <td><?php echo $timeSlot; ?></td>
                                            <td><?php echo $exerciseItem['name'];
                                            ?><br> <?php echo $exerciseItem['duration']; ?></td>
                                            <td><?php echo $exerciseItem['name'];
                                            ?><br> <?php echo $exerciseItem['duration']; ?></td>
                                            <td><?php echo $exerciseItem['name'];
                                            ?><br> <?php echo $exerciseItem['duration']; ?></td>
                                            <td><?php echo $exerciseItem['name'];
                                            ?><br> <?php echo $exerciseItem['duration']; ?></td>
                                            <td><?php echo $exerciseItem['name'];
                                            ?><br> <?php echo $exerciseItem['duration']; ?></td>
                                            <td><?php echo $exerciseItem['name'];
                                            ?><br> <?php echo $exerciseItem['duration']; ?></td>
                                        </tr>
                                        <?php $exerciseIndex++; ?>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                        <!-- Add the total reps and duration for each day -->
                        <div id="total-<?php echo strtolower($day); ?>" class="border border-grey large-counter-text"
                            data-reps="0" data-duration="0">
                            <?php
                            echo 'Total Repetitions: <span id="reps-' . strtolower($day) . '">0</span><br>';
                            echo 'Total Duration (mins): <span id="duration-' . strtolower($day) . '">0</span><br>';
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
    </section>
    </div>


    <script>
        // Body Fat Calculator Validation Function
        function validateBodyFatForm() {
            var age = document.getElementById('age').value;
            var waist = document.getElementById('waist').value;
            var neck = document.getElementById('neck').value;

            // Check if age, waist, and neck are valid numbers
            if (isNaN(age) || isNaN(waist) || isNaN(neck) || age <= 0 || waist <= 0 || neck <= 0) {
                alert('Please enter valid numeric values for age, waist, and neck circumference.');
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }

        // Function to calculate weight from BMI and height
        function getWeightFromBMI($bmi, $height) {
            // Check if height is provided and not zero
            if ($height !== null && $height != 0) {
                // Height in meters
                $heightInMeters = $height / 100;

                // Calculate weight from BMI
                $weight = $bmi * ($heightInMeters * $heightInMeters);

                return $weight;
            } else {
                // Return some default value or handle it as per your application logic
                return 0;
            }
        }

        // Weight and Height form function to allow only numbers in the input field
        function allowOnlyNumbers(event) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ([46, 8, 9, 27, 13, 110].indexOf(event.keyCode) !== -1 ||
                // Allow: Ctrl+A
                (event.keyCode === 65 && event.ctrlKey === true) ||
                // Allow: Ctrl+C
                (event.keyCode === 67 && event.ctrlKey === true) ||
                // Allow: Ctrl+X
                (event.keyCode === 88 && event.ctrlKey === true) ||
                // Allow: home, end, left, right
                (event.keyCode >= 35 && event.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105)) {
                event.preventDefault();
            }
        }

        // Attach the allowOnlyNumbers function to the keydown event of the input fields
        document.getElementById("bmiWeight").addEventListener("keydown", allowOnlyNumbers);
        document.getElementById("bmiHeight").addEventListener("keydown", allowOnlyNumbers);

        // BMI Validation Function
        function validateForm() {
            var bmiWeight = document.getElementById('bmiWeight').value;
            var bmiHeight = document.getElementById('bmiHeight').value;

            // Check if bmiWeight and bmiHeight are valid numbers
            if (isNaN(bmiWeight) || isNaN(bmiHeight) || bmiWeight <= 0 || bmiHeight <= 0) {
                alert('Please enter valid numeric values for weight and height.');
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }

        // show hip and thigh when gender is female
        document.getElementById('gender').addEventListener('change', function () {
            var hipSection = document.getElementById('hipSection');
            if (this.value === 'female') {
                hipSection.style.display = 'block';
            } else {
                hipSection.style.display = 'none';
            }
        });

        // Function to attach event listeners to meal items
        function attachEventListeners() {
            const mealItems = document.querySelectorAll('.mealItem');
            mealItems.forEach(item => {
                item.addEventListener('click', () => {
                    const day = item.getAttribute('data-day');
                    const calories = parseFloat(item.getAttribute('data-calories'));
                    const protein = parseFloat(item.getAttribute('data-protein'));

                    // Check if intake has reached the threshold
                    const totalElement = document.getElementById(`total-${day}`);
                    const currentCalories = parseFloat(totalElement.getAttribute('data-calories')) || 0;
                    const currentProtein = parseFloat(totalElement.getAttribute('data-protein')) || 0;
                    const maxCalories = <?php echo isset($intakeResults) ? $intakeResults['caloricIntake'] : 0; ?>;
                    const maxProtein = <?php echo isset($intakeResults) ? $intakeResults['proteinIntake'] : 0; ?>;

                    // Check if the intake is already at or above the threshold
                    if (currentCalories >= maxCalories) {
                        // Disable clicking if intake is at or above threshold
                        return;
                    }

                    // Toggle the green background
                    item.classList.toggle('consumed');

                    // Update daily totals
                    if (item.classList.contains('consumed')) {
                        totalElement.setAttribute('data-calories', currentCalories + calories);
                        totalElement.setAttribute('data-protein', currentProtein + protein);
                    } else {
                        totalElement.setAttribute('data-calories', currentCalories - calories);
                        totalElement.setAttribute('data-protein', currentProtein - protein);
                    }

                    totalElement.innerHTML = `
                <div class="large-counter-text">
                    Calories: <span id="calories-${day}">${totalElement.getAttribute('data-calories')}</span> / ${maxCalories}<br>
                    Protein (g): <span id="protein-${day}">${totalElement.getAttribute('data-protein')}</span> / ${maxProtein}
                </div>
            `;
                });
            });
        }


        document.addEventListener('DOMContentLoaded', () => {
            // Attach event listeners when the page loads
            attachEventListeners();
        });


        function shuffleMealPlan(day) {
            const table = document.getElementById('mealPlanTable-' + day);
            const timeSlots = <?php echo json_encode($timeSlots); ?>;
            let mealPlan = <?php echo json_encode($meal_plan); ?>;
            // Separate meal plan items by food exchange group
            const groupedItems = {};
            mealPlan.forEach(item => {
                if (!groupedItems[item.food_exchange_group]) {
                    groupedItems[item.food_exchange_group] = [];
                }
                groupedItems[item.food_exchange_group].push(item);
            });

            // Array to hold the shuffled meal plan
            let shuffledPlan = [];

            // Add one item from each group to the shuffled plan
            Object.values(groupedItems).forEach(group => {
                shuffledPlan.push(group[Math.floor(Math.random() * group.length)]);
            });

            // Shuffle the remaining items
            const remainingItems = mealPlan.filter(item => !shuffledPlan.includes(item));
            shuffledPlan = shuffledPlan.concat(remainingItems.sort(() => Math.random() - 0.5));

            // Generate table rows for the shuffled plan
            let mealIndex = 0;
            table.querySelector('tbody').innerHTML = '';
            timeSlots.forEach(timeSlot => {
                const row = document.createElement('tr');
                row.innerHTML = `<td>${timeSlot}</td>`;
                for (let i = 0; i < timeSlots.length; i++) {
                    const foodItem = shuffledPlan[mealIndex % shuffledPlan.length];
                    const cell = document.createElement('td');
                    cell.classList.add('mealItem');
                    cell.setAttribute('data-day', day);
                    cell.setAttribute('data-time-slot', timeSlot);
                    cell.setAttribute('data-calories', foodItem['energy_kcal']);
                    cell.setAttribute('data-protein', foodItem['protein_g']);
                    cell.innerHTML = `
        <div class="mealItemContent">
            <br>${foodItem['english_name']}<br><br>
            ${foodItem['filipino_name']}<br><br>
            <strong>Protein (g):</strong> ${foodItem['protein_g']}<br>
            <strong>Calories (kcal):</strong> ${foodItem['energy_kcal']}<br>
            <strong>Measure:</strong> ${foodItem['household_measure']}<br><br>
        </div>
    `;
                    row.appendChild(cell);
                    mealIndex++;
                }
                table.querySelector('tbody').appendChild(row);
            });

            // Reattach event listeners after shuffling
            attachEventListeners();
        }

        // Adjust the frequency of showing workouts based on the recommended goal
        function adjustWorkoutFrequency(recommendedGoal) {
            switch (recommendedGoal) {
                case 'weight-loss':
                    return 'Less frequently (e.g., every 2-3 days)';
                case 'weight-gain':
                    return 'More frequently (e.g., every day or every other day)';
                case 'maintenance':
                    return 'Frequently (e.g., every day or every other day)';
                default:
                    return 'Unknown';
            }
        }
    </script>

    <style>
        .mealItem {
            cursor: pointer;
        }

        .mealItem.consumed {
            background-color: green;
            color: white;
        }

        </script>< !-- Latest compiled JavaScript --><script src="assets/js/jquery-3.6.0.min.js"></script><script src="assets/js/popper.min.js"></script><script src="assets/js/video-popup.js"></script><script src="assets/js/bootstrap.min.js"></script><script src="assets/js/custom.js"></script><script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script><script src="assets/js/owl.carousel.js"></script><script src="assets/js/carousel.js"></script><script src="assets/js/video-section.js"></script><script src="assets/js/counter.js"></script><script src="assets/js/animation.js"></script></body></html>