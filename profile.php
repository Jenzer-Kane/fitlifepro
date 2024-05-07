<?php
session_start();

// Initialize results arrays
$intakeResults = null; // For caloric and protein intake
$bmiResults = null; // For BMI calculation

// Declare $lowerNormalRange and $upperNormalRange outside the if block
$lowerNormalRange = null;
$upperNormalRange = null;

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

    // Retrieve user input for caloric and protein intake
    $gmiWeight = floatval($_POST['weight']);
    $activityLevel = $_POST['activityLevel'];
    $goal = $_POST['goal'];

    // Constants for caloric and protein calculations (adjust as needed)
    $caloriesPerKg = 30; // Adjust based on individual factors
    $proteinRatioBulking = 1.8; // Adjust based on individual factors for bulking
    $proteinRatioCutting = 1.2; // Adjust based on individual factors for cutting

    // Calculate caloric and protein intake based on the goal
    if ($goal === 'bulking') {
        // Bulking: aim for 1.8g of protein per kg of body weight and a 300-500 calorie surplus
        $proteinIntake = $bmiWeight * $proteinRatioBulking;
        $caloricIntake = $bmiWeight * $caloriesPerKg + rand(300, 500);
    } elseif ($goal === 'cutting') {
        // Cutting: aim for 1.2g of protein per kg of body weight and a 200-500 calorie deficit
        $proteinIntake = $bmiWeight * $proteinRatioCutting;
        $caloricIntake = $bmiWeight * $caloriesPerKg - rand(200, 500);
    }

    // Adjust caloric intake based on activity level
    if ($activityLevel === 'active') {
        $caloricIntake += 200; // Add 200 calories for active individuals
    }

    // Store the results for displaying in the HTML later
    $intakeResults = [
        'weight' => $bmiWeight,
        'height' => $bmiHeight,
        'caloricIntake' => $caloricIntake,
        'proteinIntake' => $proteinIntake,
    ];
}

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
                                    // If user is logged in, show a personalized greeting, cart icon, and logout button
                                    echo '<li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i></a></li>'; // Add this line for the cart icon
                                
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

    <<!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>BMI, Calorie and Protein Intake Calculator</title>
        </head>

        <body>
            <section class="calculator-section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="calculator-form form-section">
                                <h2>BMI, Calorie and Protein Intake Calculator</h2>

                                <!-- BMI, Calorie and Protein Intake Calculator Form -->
                                <form method="post" action="" id="calculatorForm" onsubmit="return validateForm()">
                                    <!-- BMI Section -->
                                    <label for="bmiWeight">Weight (kg):</label>
                                    <input type="text" name="bmiWeight" required>

                                    <label for="bmiHeight">Height (cm):</label>
                                    <input type="text" name="bmiHeight" required>

                                    <label for="activityLevel">Activity Level:</label>
                                    <select name="activityLevel" required>
                                        <option value="sedentary">Sedentary</option>
                                        <option value="active">Active</option>
                                    </select>

                                    <label for="goal">Select your goal:</label>
                                    <select name="goal" required>
                                        <option value="bulking">Bulking</option>
                                        <option value="cutting">Cutting</option>
                                    </select>

                                    <button type="submit">Calculate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Results Section -->
            <section class="calculator-results">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="results-form form-section">
                                <?php
                                if (isset($bmiResults['bmi'])) {
                                    $bmi = $bmiResults['bmi'];

                                    echo '<h2>BMI Results:</h2>';

                                    // Display Weight
                                    echo (isset($intakeResults['weight'])) ? '<p><strong>Your Weight:</strong> ' . $intakeResults['weight'] . ' kg</p>' : '';

                                    // Display Height
                                    echo (isset($intakeResults['height'])) ? '<p><strong>Your Height:</strong> ' . $intakeResults['height'] . ' cm</p>' : '';

                                    // Display Your BMI
                                    echo '<p><strong>Your BMI:</strong> ' . number_format($bmi, 2) . '</p>';

                                    // Display BMI Range
                                    $bmiDifference = $bmiResults['bmiDifference'];
                                    $lowerNormalRange = $bmi - $bmiDifference['normalWeight'];
                                    $upperNormalRange = $bmi + $bmiDifference['normalWeight'];
                                    echo '<p><strong>Normal Range:</strong> ' . number_format($lowerNormalRange, 2) . ' - ' . number_format($upperNormalRange, 2) . ' / (' . number_format(getWeightFromBMI($lowerNormalRange, $bmiHeight), 2) . ' kg - ' . number_format(getWeightFromBMI($upperNormalRange, $bmiHeight), 2) . ' kg)</p>';

                                    // Display Underweight Range
                                    $lowerUnderweightRange = $bmi - $bmiDifference['underweight'];
                                    echo '<p><strong>Underweight Range:</strong> 0 - ' . number_format($lowerUnderweightRange, 2) . ' / (0 kg - ' . number_format(getWeightFromBMI($lowerUnderweightRange, $bmiHeight), 2) . ' kg)</p>';

                                    // Display Overweight Range
                                    $upperOverweightRange = $bmi + $bmiDifference['overweight'];
                                    echo '<p><strong>Overweight Range:</strong> ' . number_format($upperNormalRange, 2) . ' - ' . number_format($upperOverweightRange, 2) . ' / (' . number_format(getWeightFromBMI($upperNormalRange, $bmiHeight), 2) . ' kg - ' . number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2) . ' kg)</p>';

                                    // Display Obese Range
                                    $upperObeseRange = $bmi + $bmiDifference['overweight'] + $bmiDifference['normalWeight'];
                                    echo '<p><strong>Obese Range:</strong> ' . number_format($upperOverweightRange, 2) . ' and above / (' . number_format(getWeightFromBMI($upperOverweightRange, $bmiHeight), 2) . ' kg - above)</p>';

                                }

                                /// Function to convert BMI to weight
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

                                // Display Caloric and Protein Intake Results
                                if (isset($intakeResults)) {
                                    echo '<h2>Recommended Calorie and Protein Intake:</h2>';
                                    echo '<p><strong>Your Weight:</strong> ' . $intakeResults['weight'] . ' kg</p>';
                                    echo '<p><strong>Your Height:</strong> ' . $intakeResults['height'] . ' cm</p>';
                                    echo '<p><strong>Goal:</strong> ' . ucfirst($goal) . '</p>';
                                    echo '<p><strong>Lifestyle:</strong> ' . ucfirst($activityLevel) . '</p>';
                                    echo '<p><strong>Caloric Intake:</strong> ' . $intakeResults['caloricIntake'] . ' calories/day</p>';
                                    echo '<p><strong>Protein Intake:</strong> ' . $intakeResults['proteinIntake'] . ' grams/day</p>';

                                    echo '<p><strong>Important Note:</strong> You can find the caloric and protein contents of the foods you eat on the nutrition labels on the packages.</p>';

                                    echo '<h2>Food Recommendations:</h2>';
                                    echo '<h2>' . ucfirst($goal) . '</h2>';
                                    echo '<ul>';

                                    if ($goal === 'cutting') {
                                        $cuttingRecommendations = [
                                            'Chicken breast',
                                            'Fish (tuna, salmon, tilapia)',
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
                                        echo 'Cutting is a fitness phase dedicated to shedding excess body fat while preserving muscle mass. This is achieved through a combination of calorie deficit, cardiovascular exercise, and targeted resistance training. The goal is to achieve a lean and defined physique. <br><br/>';
                                        foreach ($cuttingRecommendations as $recommendation) {
                                            echo '<li>' . $recommendation . '</li>';
                                        }
                                    } elseif ($goal === 'bulking') {
                                        $bulkingRecommendations = [
                                            'Steak',
                                            'Ground beef',
                                            'Potatoes',
                                            'Rice',
                                            'Sweet potato',
                                            'Whole wheat or wheat bread',
                                            'Peanut butter'

                                        ];
                                        echo 'Bulking is a fitness phase emphasizing a calorie surplus to stimulate muscle growth. The objective is to increase overall body mass, particularly muscle, through resistance training and a calorie-rich diet. <br><br/>';
                                        foreach ($bulkingRecommendations as $recommendation) {
                                            echo '<li>' . $recommendation . '</li>';
                                        }
                                    }

                                    echo '</ul>';
                                }
                                ?>
                                <!-- Print Results button -->
                                <?php if ($intakeResults !== null || $bmiResults !== null): ?>
                                    <button onclick="printResult()">Print Results</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <script>
                // BMI Validation Function
                function validateForm() {
                    var bmiWeightInput = document.querySelector('[name="bmiWeight"]');
                    var bmiHeightInput = document.querySelector('[name="bmiHeight"]');
                    var weightInput = document.querySelector('[name="weight"]');
                    var activityLevelSelect = document.querySelector('[name="activityLevel"]');
                    var goalSelect = document.querySelector('[name="goal"]');

                    // Check if all required fields are filled with valid numeric values
                    if (!isNaN(bmiWeightInput.value.trim()) && !isNaN(bmiHeightInput.value.trim())
                        && !isNaN(weightInput.value.trim()) && activityLevelSelect.value !== '' && goalSelect.value !== '') {
                        return true; // Allow form submission
                    } else {
                        alert('Please fill in all required fields with valid numeric values.'); // Provide feedback to the user
                        return false; // Prevent form submission
                    }
                }


                function printResult() {
                    // Clone the results section along with styles and fonts
                    var resultsForm = document.querySelector('.results-form').cloneNode(true);

                    // Remove the "Print Result" button from the cloned content
                    var printButton = resultsForm.querySelector('button');
                    if (printButton) {
                        printButton.remove();
                    }

                    // Create a new window for printing
                    var printWindow = window.open('', '_blank');

                    // Include necessary stylesheets in the new window
                    var stylesheets = document.querySelectorAll('link[rel="stylesheet"]');
                    stylesheets.forEach(function (stylesheet) {
                        printWindow.document.head.appendChild(stylesheet.cloneNode(true));
                    });

                    // Include inline styles in the new window
                    var inlineStyles = document.querySelectorAll('style');
                    inlineStyles.forEach(function (style) {
                        printWindow.document.head.appendChild(style.cloneNode(true));
                    });

                    // Append the cloned content to the new window
                    printWindow.document.body.appendChild(resultsForm);

                    // Print the new window
                    printWindow.document.title = 'Calculation Result | FITLIFE PRO';
                    printWindow.print();
                }
            </script>
            <!-- Latest compiled JavaScript -->
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