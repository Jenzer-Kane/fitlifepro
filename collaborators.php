<!DOCTYPE html>
<?php session_start(); ?>
<html lang="zxx">

<head>
    <title>Collaborators | FITLIFE PRO</title>
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

        .team_member_box_upper_portion img {
            border-radius: 50%;
            overflow: hidden;
            /* Ensure the image stays within the circular boundary */
            width: 300px;
            /* Set the desired width */
            height: 300px;
            /* Set the desired height */
            object-fit: cover;
            /* Maintain the aspect ratio and cover the container */
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
                                <li class="nav-item active">
                                    <a class="nav-link" href="./collaborators.php">Collaborators</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./pricing.php">Pricing</a>
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
                            <h1 data-aos="fade-up">OUR COLLABORATORS</h1>
                            <div class="btn_wrapper">
                                <span class="sub_home_span">Home </span>-<span class="sub_span"> Collaborators</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- TEAM MEMBERS SECTION -->
    <section class="team_member_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="team_member_content">
                        <h2>MADE POSSIBLE WITH THE HELP OF EXPERTS</h2>
                    </div>
                </div>
            </div>
            <div class="row" data-aos="fade-up">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="team_member_box_content">
                        <div class="team_member_box_upper_portion">
                            <figure class="mb-0">
                                <img src="./assets/images/ELIZAR.png" alt="" class="img-fluid">
                            </figure>
                        </div>
                        <div class="team_member_box_lower_portion">
                            <h5>ELIZAR MANAGAY</h5>
                            <p>Physical Therapy Specialist</p>
                            <p> Experience and expertise as a physical therapy specialist. With a 5-year track record in
                                the field, he has successfully treated a diverse range of patients, demonstrating a deep
                                understanding of rehabilitation and therapeutic techniques. </p>
                            <div class="team_member_social_icons">
                                <a href="#" class="text-decoration-none">
                                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="team_member_box_content">
                        <div class="team_member_box_upper_portion">
                            <figure class="mb-0">
                                <img src="./assets/images/EMMALETH.jpg" alt="" class="img-fluid">
                            </figure>
                        </div>
                        <div class="team_member_box_lower_portion">
                            <h5>EMMALETH MANAGAY</h5>
                            <p>Health & Nutrition</p>
                            <p>Highly qualified hospital nurse with specialized knowledge in kinesthetics and nutrition.
                                Her educational background and practical experience abroad equip her to address holistic
                                health concerns in clinical and community practice.</p>
                            <div class="team_member_social_icons">
                                <a href="#" class="text-decoration-none">
                                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="team_member_box_content">
                        <div class="team_member_box_upper_portion">
                            <figure class="mb-0">
                                <img src="./assets/images/CHRISTIAN.png" alt="" class="img-fluid">
                            </figure>
                        </div>
                        <div class="team_member_box_lower_portion">
                            <h5>CHRISTIAN DUQUE</h5>
                            <p>Fitness Instructor and Trainer, Men's Classic Physique Athlete</p>
                            <p>Men’s Classic Physique Athlete having a background in bodybuilding competitions, offering
                                knowledge and guidance in lifestyle, weight management, bodybuilding, strength and
                                conditioning. </p>
                            <div class="team_member_social_icons">
                                <a href="https://www.facebook.com/christianduque112203" class="text-decoration-none">
                                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="team_member_content">
                            <h2>AND COMMUNITY</h2>
                        </div>
                    </div>
                </div>
                <div class="row" data-aos="fade-up">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="team_member_box_content">
                            <div class="team_member_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/JOSH.png" alt="" class="img-fluid">
                                </figure>
                            </div>
                            <div class="team_member_box_lower_portion">
                                <h5>JOSH DIAZ</h5>
                                <p>Gym and Fitness Enthusiast, Cosplayer</p>
                                <div class="team_member_social_icons">
                                    <a href="https://www.instagram.com/91.80.002" class="text-decoration-none">
                                        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="team_member_box_content">
                            <div class="team_member_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/JULIE.png" alt="" class="img-fluid">
                                </figure>
                            </div>
                            <div class="team_member_box_lower_portion">
                                <h5>JULLIE CAPINO</h5>
                                <p>Gym and Fitness Enthusiast, Graphic Artist, Cosplayer</p>
                                <div class="team_member_social_icons">
                                    <a href="https://www.instagram.com/julliecpn" class="text-decoration-none">
                                        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="team_member_box_content">
                            <div class="team_member_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/AMADEUS.jpg" alt="" class="img-fluid">
                                </figure>
                            </div>
                            <div class="team_member_box_lower_portion">
                                <h5>AARON DELOS REYES</h5>
                                <p>Gym and Fitness Enthusiast, Cosplayer</p>
                                <div class="team_member_social_icons">
                                    <a href="https://www.facebook.com/lerolerolero34" class="text-decoration-none">
                                        <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                    </a>
                                    <a href="https://www.instagram.com/coffeenlofis" class="text-decoration-none">
                                        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="team_member_box_content">
                            <div class="team_member_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/AARON.jpg" alt="" class="img-fluid">
                                </figure>
                            </div>
                            <div class="team_member_box_lower_portion">
                                <h5>AARON GERONIMO</h5>
                                <p>Gym and Fitness Enthusiast, Trader</p>
                                <div class="team_member_social_icons">
                                    <a href="https://www.facebook.com/aaronjulius.geronimo"
                                        class="text-decoration-none">
                                        <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                    </a>
                                    <a href="https://www.instagram.com/aarongeronimo_" class="text-decoration-none">
                                        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="team_member_box_content">
                            <div class="team_member_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/ANGEL.jpg" alt="" class="img-fluid">
                                </figure>
                            </div>
                            <div class="team_member_box_lower_portion">
                                <h5>ANGEL LUMANGYAO</h5>
                                <p>Gym and Fitness Enthusiast, Cosplayer</p>
                                <div class="team_member_social_icons">
                                    <a href="https://www.facebook.com/angel.lumangyao" class="text-decoration-none">
                                        <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="team_member_box_content">
                            <div class="team_member_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/RAVEN.jpg" alt="" class="img-fluid">
                                </figure>
                            </div>
                            <div class="team_member_box_lower_portion">
                                <h5>RAINIER JOCSING</h5>
                                <p>Gym and Fitness Enthusiast, Cosplayer</p>
                                <div class="team_member_social_icons">
                                    <a href="https://www.facebook.com/lerolerolero34" class="text-decoration-none">
                                        <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                    </a>
                                    <a href="https://www.instagram.com/coffeenlofis" class="text-decoration-none">
                                        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                    </a>
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
                        <h2>“SUCCESS USUALLY COMES TO THOSE WHO ARE TOO BUSY TO BE LOOKING FOR IT.”</h2>
                        <div class="quote_content_wrapper">
                            <div class="quote_wrapper">
                                <h6>ARNOLD SCHWARZENEGGER</h6>
                                <span>Mr. Olympia 7 Time World Champion</span>
                                <figure class="quote_image mb-0">
                                    <img src="./assets/images/ARNOLD.png" alt="" class="img-fluid">
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
                                    <a href="./collaborators.php">Collaborators</a>
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
    <!-- BLOG SECTION POPUP -->

    <div id="blog-model-3" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="./assets/images/our_news_3.png" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="blog-content pl-0 pr-0">
                            <div class="blog-auteher-title">
                                <span>By Elina Parker</span>
                                <span class="float-lg-right">Oct 13, 2022</span>
                            </div>
                            <div class="social_icons">
                                <ul class="list-unstyled">
                                    <li><a href="#"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fa-brands fa-twitter" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fa-brands fa-instagram mr-0" aria-hidden="true"></i></a>
                                    </li>
                                </ul>
                            </div>
                            <h5>SOLUTA NOBIS OSE ALIGEN OPTIO CUMUE</h5>
                            <p>
                                Duis aute irure dolor in reprehenderi in voluptate velit esse cillum dolore eu fugiat
                                nulla pariatur.
                                in reprehenderit in voluptateesse occaecat cuida at non proident, sunt in culpa qui
                                officia deserun.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam tempor
                                eros a tellus auctor, nec suscipit nunc dignissim. Ut suscipit gravida augue sed
                                elementum. Sed sed luctus nisl. Donec scelerisque nisi in sodales mattis. Vestibulum
                                suscipit odio ac enim blandit sollicitudin. Aliquam ultrices sem quis urna placerat
                                interdum. Etiam rutrum, quam sagittis tristique mollis, libero arcu scelerisque erat,
                                eget tincidunt eros diam quis nunc.
                            </p>
                            <h5 class="contact">CONTACT US</h5>
                            <h2>SEND US A MESSAGE & JOIN OUR TEAM</h2>
                            <form class="contact-form blog-model-form">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="text" name="name" id="blog_third_fname"
                                                class="form-placeholder-color" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="tel" name="phone" id="blog_third_phonenumber"
                                                class="form-placeholder-color form_style" placeholder="Phone">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="email" name="email" id="blog_third_emailaddress"
                                                class="form-placeholder-color" placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="text" name="sub" id="blog_third_subject"
                                                class="form-placeholder-color form_style" placeholder="Subject">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class=" form-group mb-0">
                                            <textarea rows="3" name="msg" id="blog_third_message"
                                                class="form-placeholder-color" placeholder="Message"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="btnsubmit" id="blog_third_sendbtn">Send now<i
                                        class="fa-solid fa-play"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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