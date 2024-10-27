<!DOCTYPE html>
<?php
session_start();

// Database connection file
require 'database.php';

$query = "SELECT * FROM quotes ORDER BY RAND() LIMIT 1";
$result = $mysqli->query($query);

// Check if the query was successful
if ($result && $result->num_rows > 0) {
    $quote = $result->fetch_assoc();
} else {
    $quote = null;
}

// Close the database connection
$mysqli->close();
?>

<head>
    <title>Services | FITLIFE PRO</title>
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

        .popup {
            display: none;
            /* Initially hidden */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 30px;
            background: white;
            border: 2px solid #e13a3b;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            /* Smooth fade-in effect */
            border-radius: 15px;
            /* Add rounded corners */
        }

        .popup-overlay {
            display: none;
            /* Initially hidden */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .popup-close {
            cursor: pointer;
            float: right;
            font-size: 20px;
            color: #e13a3b;
        }

        .popup-content {
            font-size: 16px;
            text-align: center;
            /* Center the content horizontally */
        }

        .popup-button-container {
            text-align: center;
            /* Center the button horizontally */
            margin-top: 20px;
            /* Space above the button */
        }

        .popup-button {
            background-color: #007bff;
            /* Blue color for the button */
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            /* Rounded corners for the button */
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            /* Smooth transitions for background and text color */
            text-decoration: none;
            /* Remove underline from link */
            display: inline-block;
            /* Ensure link behaves like a button */
        }

        .popup-button:hover {
            background-color: #0056b3;
            /* Darker shade of blue on hover */
            color: white;
            /* Ensure text is white on hover */
            text-decoration: none;
            /* Ensure underline is removed on hover */
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
    </style>
</head>
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
                                <li class="nav-item active">
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
                                if (isset($_SESSION['username'])) {
                                    // If user is logged in, show name and logout button
                                
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
                            <h1 data-aos="fade-up">OUR SERVICES</h1>
                            <div class="btn_wrapper">
                                <span class="sub_home_span">Home </span>-<span class="sub_span"> Services</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Popup HTML -->
    <div id="popup-overlay" class="popup-overlay"></div>
    <div id="popup" class="popup">
        <span class="popup-close" onclick="closePopup()">&times;</span>
        <div class="popup-content">
            <h2>Try our free BMI calculator!</h2>
            <h4>Learn about your BMI, Body Fat Percentage and more!</h4>
            <h2>Register Now to access!</h2>
            <div class="popup-button-container">
                <a href="./register.html" class="popup-button">Register</a>
            </div>
        </div>
    </div>


    <!-- PRICING TABLES SECTION -->
    <section class="pricing_tables_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="pricing_tables_content">
                        <h5>SUBSCRIPTION TIERS</h5>
                        <h2>CHOOSE YOUR SUBSCRIPTION TIER</h2>
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
                            <h5 style="color: lightblue;">ESSENTIAL TIER</h5>
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
                            <h5 style="color: lightcoral">PREMIUM TIER</h5>
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
                            <h5 style="color: #9370DB">ELITE TIER</h5>
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

    <!-- OUR SCHEDULE SECTION -->
    <section class="our_schedule_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="our_schedule_content">
                        <h5>SCHEDULE</h5>
                        <h2>SAMPLE WEIGHT-LIFTING AND BODYWEIGHT EXERCISE ROUTINE AND SCHEDULE </h2>
                    </div>
                </div>
            </div>
            <div class="table_wrapper border border-black" data-aos="fade-up">
                <table>
                    <tr>
                        <th>
                            <figure class="mb-0">
                                <img src="./assets/images/services_table_icon.png" alt="" class="img-fluid">
                            </figure>
                        </th>
                        <th>SUN</th>
                        <th>MON</th>
                        <th>TUE</th>
                        <th>WED</th>
                        <th>THU</th>
                        <th>FRI</th>
                    </tr>
                    <tr>
                        <td>08:00 AM</td>
                        <td>
                            <h6>CARDIO</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                        <td>
                            <h6>CARDIO</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                        <td>
                            <h6>CARDIO</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                        <td>
                            <h6>CARDIO</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                        <td>
                            <h6>CARDIO</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                        <td>
                            <h6>CARDIO</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                    </tr>
                    <tr>
                        <td>10:00 AM</td>
                        <td>
                            <h6>YOGA</h6>
                            <p class="mb-0">Yoga Session</p>
                        </td>
                        <td>
                            <h6>YOGA</h6>
                            <p class="mb-0">Yoga Session</p>
                        </td>
                        <td></td>
                        <td>
                            <h6>YOGA</h6>
                            <p class="mb-0">Yoga Session</p>
                        </td>
                        <td>
                            <h6>YOGA</h6>
                            <p class="mb-0">Yoga Session</p>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>04:00 PM</td>
                        <td>
                            <h6>GYM</h6>
                            <p class="mb-0">Mass Gain</p>
                        </td>
                        <td></td>
                        <td>
                            <h6>GYM</h6>
                            <p class="mb-0">Mass Gain</p>
                        </td>
                        <td>
                            <h6>GYM</h6>
                            <p class="mb-0">Mass Gain</p>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>08:00 PM</td>
                        <td>
                            <h6>CROSSFIT</h6>
                            <p class="mb-0">Streching</p>
                        </td>
                        <td>
                            <h6>CROSSFIT</h6>
                            <p class="mb-0">Streching</p>
                        </td>
                        <td>
                            <h6>CROSSFIT</h6>
                            <p class="mb-0">Streching</p>
                        </td>
                        <td></td>
                        <td>
                            <h6>CROSSFIT</h6>
                            <p class="mb-0">Streching</p>
                        </td>
                        <td>
                            <h6>CROSSFIT</h6>
                            <p class="mb-0">Streching</p>
                        </td>
                    </tr>
                    <tr>
                        <td>10:00 PM</td>
                        <td>
                            <h6>BOXING</h6>
                            <p class="mb-0">Boxing</p>
                        </td>
                        <td>
                            <h6>BOXING</h6>
                            <p class="mb-0">Boxing</p>
                        </td>
                        <td>
                            <h6>BOXING</h6>
                            <p class="mb-0">Boxing</p>
                        </td>
                        <td>
                            <h6>BOXING</h6>
                            <p class="mb-0">Boxing</p>
                        </td>
                        <td>
                            <h6>BOXING</h6>
                            <p class="mb-0">Boxing</p>
                        </td>
                        <td>
                            <h6>BOXING</h6>
                            <p class="mb-0">Boxing</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </section>

    <!-- OUR SCHEDULE SECTION -->
    <section class="our_schedule_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="our_schedule_content">
                        <h5>NUTRITION</h5>
                        <h2>SAMPLE NUTRITIONAL GUIDANCE AND MEAL PLAN</h2>
                    </div>
                </div>
            </div>
            <div class="table_wrapper border border-black" data-aos="fade-up">
                <table>
                    <tr>
                        <th>
                            <figure class="mb-0">
                                <img src="./assets/images/services_table_icon.png" alt="" class="img-fluid">
                            </figure>
                        </th>
                        <th>SUN</th>
                        <th>MON</th>
                        <th>TUE</th>
                        <th>WED</th>
                        <th>THU</th>
                        <th>FRI</th>
                    </tr>
                    <tr>
                        <td>08:00 AM</td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                    </tr>
                    <tr>
                        <td>10:00 AM</td>

                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                    </tr>
                    <tr>
                        <td>04:00 PM</td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                    </tr>
                    <tr>
                        <td>08:00 PM</td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                    </tr>
                    <tr>
                        <td>10:00 PM</td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Cardio Excersices</p>
                        </td>
                        <td>
                            <h6>CARBS</h6>
                            <p class="mb-0">Rice, Potatoes, Pork</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                        <td>
                            <h6>PROTEIN</h6>
                            <p class="mb-0">Eggs, Chicken, Tuna</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </section>



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



    <!-- OUR NEWS SECTION -->
    <section class="our_news_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="our_news_content">
                        <h5>OUR NEWS</h5>
                        <h2>LATEST UPDATES</h2>
                    </div>
                </div>
            </div>
            <div class="row" data-aos="fade-up">
                <div class="owl-carousel owl-theme">
                    <div class="item">
                        <div class="our_news_box_content">
                            <div class="our_news_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/our_news_1.png" alt="" class="img-fluid">
                                </figure>
                                <div class="our_news_box_image_content">
                                    <h4>17</h4>
                                    <span class="month">Nov</span>
                                </div>
                            </div>
                            <div class="our_news_box_lower_portion">
                                <h5>LAUNCH OF FITLIFE PRO</h5>
                                <p>Initial launch of FITLIFE Pro Official Website.</p>

                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="our_news_box_content">
                            <div class="our_news_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/our_news_2.png" alt="" class="img-fluid">
                                </figure>
                                <div class="our_news_box_image_content">
                                    <h4>20</h4>
                                    <span class="month">Nov</span>
                                </div>
                            </div>
                            <div class="our_news_box_lower_portion">
                                <h5>SERVICE TYPES ADDED</h5>
                                <p>Added service types that will be accessible after subscription.</p>

                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="our_news_box_content">
                            <div class="our_news_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/our_news_3.png" alt="" class="img-fluid">
                                </figure>
                                <div class="our_news_box_image_content">
                                    <h4>23</h4>
                                    <span class="month">Jan</span>
                                </div>
                            </div>
                            <div class="our_news_box_lower_portion">
                                <h5>SUBSCRIPTION PRICES ADDED</h5>
                                <p>Tiered Subscription Plans added: Essential, Premium, Elite</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FOOTER SECTION -->
    <section class="footer-section">
        <div class="container">
            <div class="middle-portion">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <div class="about_col">
                            <a href="./index.php">
                                <figure>
                                    <img src="./assets/images/fitlife_pro_logo.png" alt="" class="img-fluid">
                                </figure>
                            </a>
                            <p>Social Media Platforms:</p>
                            <ul class="list-unstyled">
                                <li class="icons"><a href="#"><i class="fa-brands fa-facebook-f"
                                            aria-hidden="true"></i></a></li>
                                <li class="icons"><a href="#"><i class="fa-brands fa-twitter"
                                            aria-hidden="true"></i></a></li>
                                <li class="icons"><a href="#"><i class="fa-brands fa-instagram mr-0"
                                            aria-hidden="true"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12 d-md-block d-none">
                        <div class="links_col">
                            <h6>QUICK LINKS</h6>
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fa-solid fa-caret-right"></i>
                                    <a href="./about.php">About</a>
                                </li>
                                <li>
                                    <i class="fa-solid fa-caret-right"></i>
                                    <a href="./services.php">Services</a>
                                </li>
                                <li>
                                    <i class="fa-solid fa-caret-right"></i>
                                    <a href="./collaborators.php">collaborators</a>
                                </li>
                                <li>
                                    <i class="fa-solid fa-caret-right"></i>
                                    <a href="./pricing.php">Pricing</a>
                                </li>
                                <li>
                                    <i class="fa-solid fa-caret-right"></i>
                                    <a href="./contact.php">Contact</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="contact_col">
                            <h6>CONTACT US</h6>
                            <ul class="list-unstyled">
                                <li>
                                <li>
                                    <p>Email:</p>
                                </li>
                                <li class="mail">
                                    <a href="mailto:Info@fitlifepro.com"
                                        class="text-decoration-none">Info@fitlifepro.com</a>
                                </li>
                                <li>
                                    <p>Phone:</p>
                                </li>
                                <li class="mb-0">
                                    <a href="tel:+61383766284" class="text-decoration-none">+61 3 8376 6284</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom-portion">
            <div class="copyright col-xl-12">
                <p>Copyright 2022 FitLife Pro. All Rights Reserved.</p>
            </div>
        </div>
        <figure class="mb-0 footer_left_icon left_icon">
            <img src="./assets/images/footer_left_icon.png" alt="" class="img-fluid">
        </figure>
        <figure class="mb-0 footer_right_icon right_icon">
            <img src="./assets/images/footer_right_icon.png" alt="" class="img-fluid">
        </figure>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltip = document.createElement('div');
            tooltip.style.position = 'absolute';
            tooltip.style.display = 'none';
            tooltip.style.padding = '12px 18px';
            tooltip.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
            tooltip.style.color = '#333';
            tooltip.style.border = '1px solid #ddd';
            tooltip.style.borderRadius = '8px';
            tooltip.style.fontSize = '15px';
            tooltip.style.boxShadow = '0px 8px 16px rgba(0, 0, 0, 0.1)';
            tooltip.style.maxWidth = '280px';
            tooltip.style.zIndex = '1000';
            tooltip.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            tooltip.style.opacity = '0';
            tooltip.style.transform = 'translateY(10px)';
            document.body.appendChild(tooltip);

            const tierDescriptions = {
                'ESSENTIAL TIER': 'The Essential Tier is perfect for those starting their fitness journey. Get a recommended meal plan, track your progress, and engage with others in the forums to build a healthier lifestyle.',
                'PREMIUM TIER': 'The Premium Tier elevates your fitness routine with recommended exercises, meal plans, progress tracking, and forum access, designed for those seeking noticeable improvements.',
                'ELITE TIER': 'The Elite Tier offers exclusive bodybuilding routines, advanced nutritional guidance, and 24/7 support. Push your limits and achieve extraordinary results with our top-tier offering.'
            };

            const tierColors = {
                'ESSENTIAL TIER': 'lightblue',
                'PREMIUM TIER': 'lightcoral',
                'ELITE TIER': '#9370DB'
            };

            const tierBoxes = document.querySelectorAll('.pricing_tables_box_content');

            tierBoxes.forEach(box => {
                const tierName = box.querySelector('h5').innerText.trim().toUpperCase(); // Get the tier name in uppercase

                // Show tooltip on mouse enter
                box.addEventListener('mouseenter', (e) => {
                    const description = tierDescriptions[tierName] || 'Subscription details not available.';
                    const titleColor = tierColors[tierName] || '#333'; // Default to black if no color is found
                    tooltip.innerHTML = `<strong style="font-size: 16px; color: ${titleColor};">${tierName}:</strong> <br><span style="font-size: 14px;">${description}</span>`;
                    tooltip.style.display = 'block';
                    tooltip.style.opacity = '1';
                    tooltip.style.transform = 'translateY(0)';

                    // Position tooltip
                    const rect = box.getBoundingClientRect();
                    tooltip.style.left = `${rect.left + window.pageXOffset + 20}px`;
                    tooltip.style.top = `${rect.top + window.pageYOffset - 10}px`;
                });

                // Hide tooltip on mouse leave
                box.addEventListener('mouseleave', () => {
                    tooltip.style.opacity = '0';
                    tooltip.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        tooltip.style.display = 'none';
                    }, 300); // Wait for the transition to complete
                });

                // Update tooltip position on mouse move
                box.addEventListener('mousemove', (e) => {
                    tooltip.style.left = `${e.pageX + 20}px`;
                    tooltip.style.top = `${e.pageY - 10}px`;
                });
            });
        });


        let hasScrolled = false;
        let popupTimer;

        function showPopup() {
            document.getElementById('popup').style.display = 'block';
            document.getElementById('popup-overlay').style.display = 'block';
            // Trigger AOS animation
            AOS.refresh(); // Refresh AOS to ensure it recognizes the newly displayed element
            setTimeout(() => {
                document.getElementById('popup').style.opacity = 1; // Fade-in effect
            }, 100); // Slight delay to ensure display change takes effect
        }

        function closePopup() {
            document.getElementById('popup').style.opacity = 0;
            setTimeout(() => {
                document.getElementById('popup').style.display = 'none';
                document.getElementById('popup-overlay').style.display = 'none';
            }, 500); // Match duration with opacity transition
        }

        function onScroll() {
            if (hasScrolled) return; // Prevent multiple triggers
            hasScrolled = true;

            // Set a timer to show the popup after a delay
            popupTimer = setTimeout(() => {
                var isLoggedIn = <?php echo json_encode(!isset($_SESSION['username'])); ?>;
                if (isLoggedIn) {
                    showPopup();
                }
            }, 3000); // Adjust delay here (3000ms = 3 seconds)
        }

        window.addEventListener('scroll', onScroll);

        // Optional: Hide popup if user scrolls back up
        window.addEventListener('scroll', () => {
            if (window.scrollY < 100) { // Adjust threshold here
                hasScrolled = false;
                clearTimeout(popupTimer);
                document.getElementById('popup').style.opacity = 0;
                setTimeout(() => {
                    document.getElementById('popup').style.display = 'none';
                    document.getElementById('popup-overlay').style.display = 'none';
                }, 500);
            }
        });

        // Initialize AOS
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init();
        });
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
</body>

</html>