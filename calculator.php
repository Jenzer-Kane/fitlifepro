<?php
session_start();

$intakeResults = null; // Initialize $intakeResults

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user input
    $weightKg = floatval($_POST['weight']);
    $activityLevel = $_POST['activityLevel'];
    $goal = $_POST['goal'];

    // Constants for caloric and protein calculations (adjust as needed)
    $caloriesPerKg = 30; // Adjust based on individual factors
    $proteinRatioBulking = 1.8; // Adjust based on individual factors for bulking
    $proteinRatioCutting = 1.2; // Adjust based on individual factors for cutting

    // Calculate caloric and protein intake based on the goal
    if ($goal === 'bulking') {
        // Bulking: aim for 1.8g of protein per kg of body weight and a 300-500 calorie surplus
        $proteinIntake = $weightKg * $proteinRatioBulking;
        $caloricIntake = $weightKg * $caloriesPerKg + rand(300, 500);
    } elseif ($goal === 'cutting') {
        // Cutting: aim for 1.2g of protein per kg of body weight and a 200-500 calorie deficit
        $proteinIntake = $weightKg * $proteinRatioCutting;
        $caloricIntake = $weightKg * $caloriesPerKg - rand(200, 500);
    }

    // Adjust caloric intake based on activity level
    if ($activityLevel === 'active') {
        $caloricIntake += 200; // Add 200 calories for active individuals
    }

    // Store the results for displaying in the HTML later
    $intakeResults = [
        'weight' => $weightKg,
        'caloricIntake' => $caloricIntake,
        'proteinIntake' => $proteinIntake,
    ];

}
?>

<!DOCTYPE html>
<head>
    <title>Calculator | FITLIFE PRO</title>
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
    <link rel="icon" type="image/png" sizes="192x192"  href="./assets/images/favicon/android-icon-192x192.png">
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
            z-index: 1; /* Ensure the banner content is above the background image */
        }

        .banner_video {
            position: relative;
            z-index: 1; /* Ensure the video icon is above the background image */
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
                        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
                                    <a class="nav-link" href="./trainers.php">Trainers</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./pricing.php">Pricing</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="./calculator.php">Calculator</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link contact_btn" href="./contact.php">Contact</a>
                                </li>
                                <?php
                                     if (isset($_SESSION['username'])) {
                                        // If user is logged in, show a personalized greeting, cart icon, and logout button
                                        echo '<li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i></a></li>'; // Add this line for the cart icon
                                        echo '<li class="nav-item"><a class="nav-link" href="#">' . '<a href="profile.php">' . $_SESSION['username'] . '</a>' . '</a></li>';
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
                        <h1 data-aos="fade-up">CALCULATOR</h1>
                        <div class="btn_wrapper">
                            <span class="sub_home_span">Home </span>-<span class="sub_span"> Calculator</span>
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
<body>
    <section class="calculator-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="calculator-form form-section">
                        <h2>Calorie and Protein Intake Calculator</h2>

                        <form method="post" action="" id="calculatorForm" onsubmit="return validateForm()">
                            <label for="weight">Weight (kg):</label>
                            <input type="text" name="weight" required>

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

    <section class="calculator-results">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="results-form form-section">
                        <?php
                      // Display the results if available
                        if (isset($intakeResults)) {
                            echo '<h2>Recommended Intake:</h2>';
                            echo '<p><strong>Your Weight:</strong> ' . $weightKg . ' kg</p>';
                            echo '<p><strong>Goal:</strong> ' . ucfirst($goal) . '</p>'; // Display the goal
                            echo '<p><strong>Lifestyle:</strong> ' . ucfirst($activityLevel) . '</p>'; // Display the lifestyle
                            echo '<p><strong>Caloric Intake:</strong> ' . $caloricIntake . ' calories/day</p>';
                            echo '<p><strong>Protein Intake:</strong> ' . $proteinIntake . ' grams/day</p>';

                            // Additional information
                            echo '<p><strong>Important Note:</strong> You can find the caloric and protein contents of the foods you eat on the nutrition labels on the packages. </p>';

                            // Recommendations based on goal
                            echo '<h2>Food Recommendations:</h2>';
                            echo '<ul>';

                            if ($goal === 'cutting') {
                                // Recommendations for cutting
                                $cuttingRecommendations = [
                                    'Chicken breast', 'Fish (tuna, salmon, tilapia)', 'Eggs', 'Spinach', 'Avocado',
                                    'Oats', 'Cottage Cheese', 'Greek Yogurt', 'Milk', 'Nuts and seeds (walnuts, almonds, pumpkin seeds, sunflower seeds)',
                                    'Sweet potato', 'Vegetables (broccoli, bell peppers, onions, green beans, asparagus)',
                                    'Fruits (bananas, apples, oranges, blueberries)'
                                ];
                                foreach ($cuttingRecommendations as $recommendation) {
                                    echo '<li>' . $recommendation . '</li>';
                                }
                            } elseif ($goal === 'bulking') {
                                // Recommendations for bulking
                                $bulkingRecommendations = [
                                    'Steak', 'Ground beef', 'Potatoes', 'Rice', 'Sweet potato',
                                    'Whole wheat or wheat bread', 'Peanut butter'
                                ];
                                foreach ($bulkingRecommendations as $recommendation) {
                                    echo '<li>' . $recommendation . '</li>';
                                }
                            }

                            echo '</ul>';
                    
                        }
                        ?>

                         <!-- Print Results button only appears after results are displayed -->
                         <?php if ($intakeResults !== null) : ?>
                            <button onclick="printResult()">Print Results</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
    function validateForm() {
        var weightInput = document.querySelector('[name="weight"]');
        var activityLevelSelect = document.querySelector('[name="activityLevel"]');
        var goalSelect = document.querySelector('[name="goal"]');

        // Check if all required fields are filled
        if (weightInput.value.trim() !== '' && activityLevelSelect.value !== '' && goalSelect.value !== '') {
            return true; // Allow form submission
        } else {
            alert('Please fill in all required fields.'); // Provide feedback to the user
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