<!DOCTYPE html>
<?php session_start();
?>
<html lang="zxx">

<head>
    <title>About | FITLIFE PRO</title>
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
                                <li class="nav-item active">
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
                                if (isset($_SESSION['username'])) {
                                    // If user is logged in, show a personalized greeting and logout button
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
                            <h1 data-aos="fade-up">ABOUT US</h1>
                            <div class="btn_wrapper">
                                <span class="sub_home_span">Home </span>-<span class="sub_span"> About</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- ABOUT US SECTION -->
    <section class="sub-banner_about_section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12">
                    <div class="sub-banner_aboutus_image" data-aos="fade-up">
                        <figure class="mb-0">
                            <img src="./assets/images/about_us.jpg" alt="" class="img-fluid rounded shadow-lg">
                        </figure>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12">
                    <div class="sub-banner_aboutus_content" data-aos="fade-right">
                        <h5>About Us</h5>
                        <h2 class="mb-4">WELCOME TO FITLIFE PRO</h2>
                        <p class="mb-4">At FitLife Pro, we believe that fitness is a journey, not a destination. Our
                            platform is designed to make fitness an enjoyable and accessible part of your everyday life,
                            no matter your goals. Whether you aim to lose weight, gain muscle, or simply maintain a
                            healthy lifestyle, we’ve got you covered with the best tools and community support.</p>
                        <p><strong>Here’s what FitLife Pro can do for you:</strong></p>
                        <ul class="about-features-list">
                            <li><i class="fa-solid fa-check"></i> Personalized workout plans tailored to your goals and
                                fitness level.</li>
                            <li><i class="fa-solid fa-check"></i> Custom meal plans for every body type, goal, and diet
                                preference.</li>
                            <li><i class="fa-solid fa-check"></i> Easy-to-use progress trackers to keep you motivated
                                and accountable.</li>
                            <li><i class="fa-solid fa-check"></i> A vibrant community where you can share, learn, and
                                grow together.</li>
                        </ul>
                        <div class="quote-box mt-4">
                            <blockquote class="blockquote text-center">
                                <p>"Physical fitness doesn’t just transform the body. It strengthens the mind and builds
                                    discipline and confidence. Fitness is not about perfection, but persistence." -
                                    Jenzer, Founder</p>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .about-features-list {
            list-style: none;
            padding-left: 0;
        }

        .about-features-list li {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .about-features-list i {
            color: #28a745;
            margin-right: 10px;
        }

        .quote-box {
            background-color: #f9f9f9;
            padding: 20px;
            border-left: 5px solid #e13b3b;
            border-radius: 5px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
    </style>


    <!-- STATISTICS SECTION CHECK THE ORIGINAL 

    <figure class="stats_left_icon left_icon mb-0">
        <img src="./assets/images/stats_left_icon.png" alt="" class="img-fluid">
    </figure>
    <figure class="stats_right_icon right_icon mb-0">
        <img src="./assets/images/stats_right_icon.png" alt="" class="img-fluid">
    </figure>
    </div>
    </div> -->

    <!-- CHOOSE US SECTION -->
    <section class="choose_us_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="choose_us_content">
                        <h5>WHY CHOOSE US</h5>
                        <h2>BUILD YOUR BEST BODY</h2>
                    </div>
                </div>
            </div>
            <div class="row" data-aos="fade-up">
                <div class="owl-carousel owl-theme">
                    <div class="item">
                        <div class="choose_us_box_content">
                            <figure><img src="./assets/images/choose_us_1.png" alt="" class="img-fluid"></figure>
                            <h6>PROGRESS MONITORING</h6>
                            <p>Log and track your progress with our built-in progress logging.</p>
                            <div class="btn_wrapper">
                                <a class="read_more_btn text-decoration-none" href="./services.php">Read more</a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="choose_us_box_content">
                            <figure><img src="./assets/images/choose_us_2.png" alt="" class="img-fluid"></figure>
                            <h6>NUTRITIONAL GUIDANCE</h6>
                            <p>Get optimal bulking and cutting guidance with meals.</p>
                            <div class="btn_wrapper">
                                <a class="read_more_btn text-decoration-none" href="./services.php">Read more</a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="choose_us_box_content">
                            <figure><img src="./assets/images/choose_us_3.png" alt="" class="img-fluid"></figure>
                            <h6>BODY-WEIGHT EXERCISES</h6>
                            <p>Get body-weight exercises and programs sent to your phone.</p>
                            <div class="btn_wrapper">
                                <a class="read_more_btn text-decoration-none" href="./services.php">Read more</a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="choose_us_box_content">
                            <figure><img src="./assets/images/choose_us_4.png" alt="" class="img-fluid"></figure>
                            <h6>WEIGHT LIFTING</h6>
                            <p>Get personalized weight-lifting routines according to your physique goals.</p>
                            <div class="btn_wrapper">
                                <a class="read_more_btn text-decoration-none" href="./services.php">Read more</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- CONTACT US SECTION -->

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