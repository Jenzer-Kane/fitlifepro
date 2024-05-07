<!DOCTYPE html>
<?php
session_start();

// Database connection file
require 'database.php';

// Fetch subscriber counts for each tier
$query = "SELECT plan, subscriber_count FROM subscriptions";
$result = $mysqli->query($query);

// Initialize an associative array to store subscriber counts
$subscriberCounts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subscriberCounts[$row['plan']] = $row['subscriber_count'];
    }
}

// Close the database connection
$mysqli->close();
?>

<html lang="zxx">

<head>
    <title>Pricing | FITLIFE PRO</title>
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
                                <li class="nav-item">
                                    <a class="nav-link" href="./services.php">Services</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./collaborators.php">Collaborators</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="./pricing.php">Pricing</a>
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
                            <h1 data-aos="fade-up">PRICING</h1>
                            <div class="btn_wrapper">
                                <span class="sub_home_span">Home </span>-<span class="sub_span"> Pricing</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
                            <h5>ESSENTIAL TIER</h5>
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Basic content
                                </li>
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Bodyweight exercises
                                </li>
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Progress Tracking
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
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Intermediate content
                                </li>
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Bodyweight exercises
                                </li>
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Progress Tracking
                                </li>
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Nutritional Guidance
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
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Advanced content
                                </li>
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Bodyweight exercises
                                </li>
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Progress Tracking
                                </li>
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Nutritional Guidance
                                </li>
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>Curated weight-lifting routines
                                </li>

                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>24/7 Live Support
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

    <!-- QUOTE SECTION -->
    <section class="quote_section">
        <div class="container">
            <div class="row" data-aos="fade-right">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="quote_content">
                        <h2>“FAST POSITIVES, SLOW NEGATIVES.”</h2>
                        <div class="quote_content_wrapper">
                            <div class="quote_wrapper">
                                <h6>MIKE MENTZER</h6>
                                <span>Professional Trainer Classic Physique</span>
                                <figure class="quote_image mb-0">
                                    <img src="./assets/images/MIKE.png" alt="" class="img-fluid">
                                </figure>
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