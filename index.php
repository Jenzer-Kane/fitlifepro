<!DOCTYPE html>
<?php
session_start();
?>
<html lang="zxx">

<head>
    <title>Home | FITLIFE PRO</title>
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

        .team_member_box_content2 img {
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
    </style>
</head>

<body>
    <div class="banner-section-outer">
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
                                <li class="nav-item active">
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
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <!-- BANNER SECTION -->
        <section class="banner-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div id="banner_slider" class="carousel slide" data-ride="carousel">
                            <ul class="carousel-indicators">
                                <li data-target="#banner_slider" data-slide-to="0" class="active"></li>
                                <li data-target="#banner_slider" data-slide-to="1"></li>
                                <li data-target="#banner_slider" data-slide-to="2"></li>
                                <li data-target="#banner_slider" data-slide-to="3"></li>
                            </ul>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <div class="banner-section-content">
                                        <div class="banner-section-wrapper">
                                            <h3>Elevate your fit life with</h3>
                                            <h1 data-aos="fade-up">FITLIFE PRO</h1>
                                            <figure class="white_line mb-0">
                                                <img src="./assets/images/banner_white_line.png" alt=""
                                                    class="img-fluid">
                                            </figure>
                                        </div>
                                        <p data-aos="fade-right">A personalized gym routine curator and nutritional
                                            guidance platform.</p>
                                        <div class="btn_wrapper" data-aos="fade-up">
                                            <a class="text-decoration-none join_now_btn" href="./services.php">Join us
                                                now
                                                <i class="fa-solid fa-play"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="banner-section-content">
                                        <div class="banner-section-wrapper">
                                            <h3>Featuring Routines for</h3>
                                            <h1 data-aos="fade-up">WEIGHTS</h1>
                                            <figure class="white_line mb-0">
                                                <img src="./assets/images/banner_white_line.png" alt=""
                                                    class="img-fluid">
                                            </figure>
                                        </div>
                                        <p data-aos="fade-right">Get optimal weight-lifting routines according to
                                            your physique goals.</p>
                                        <div class="btn_wrapper" data-aos="fade-up">
                                            <a class="text-decoration-none join_now_btn" href="./pricing.php">Join us
                                                now
                                                <i class="fa-solid fa-play"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="banner-section-content">
                                        <div class="banner-section-wrapper">
                                            <h3>Featuring Optimal Guidance for</h3>
                                            <h1 data-aos="fade-up">NUTRITION</h1>
                                            <figure class="white_line mb-0">
                                                <img src="./assets/images/banner_white_line.png" alt=""
                                                    class="img-fluid">
                                            </figure>
                                        </div>
                                        <p data-aos="fade-right">Get optimal bulking and cutting guidance with meals.
                                        </p>
                                        <div class="btn_wrapper" data-aos="fade-up">
                                            <a class="text-decoration-none join_now_btn" href="./pricing.php">Join us
                                                now
                                                <i class="fa-solid fa-play"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="banner-section-content">
                                        <div class="banner-section-wrapper">
                                            <h3>SUBSCRIBE TO FITLIFE PRO AND TRAIN</h3>
                                            <h1 data-aos="fade-up">OPTIMALLY</h1>
                                            <figure class="white_line mb-0">
                                                <img src="./assets/images/banner_white_line.png" alt=""
                                                    class="img-fluid">
                                            </figure>
                                        </div>
                                        <p data-aos="fade-right">Get body-weight exercises and optimal programs to suit
                                            your fitness goals! </p>
                                        <div class="btn_wrapper" data-aos="fade-up">
                                            <a class="text-decoration-none join_now_btn" href="./pricing.php">Join us
                                                now
                                                <i class="fa-solid fa-play"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- REMOVED VIDEO ICON -->
        <div class="banner_video position-relative">
            <a class="popup-vimeo"
                href="https://video-previews.elements.envatousercontent.com/h264-video-previews/9b19c081-53dc-4345-a05e-0c9a4a52f3e6/26111213.mp4">
                <figure class="video_img mb-0">
                </figure>
            </a>
        </div>
    </div>


    <!-- ABOUT US SECTION -->
    <section class="aboutus_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 d-lg-block d-md-flex align-items-center">
                    <div class="aboutus_image">
                        <figure class="mb-0">
                            <img src="./assets/images/aboutus_image.png" alt="">
                        </figure>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12" data-aos="fade-right">
                    <div class="aboutus_content">
                        <h5>ABOUT US</h5>
                        <h2>WELCOME TO FITLIFE PRO</h2>
                        <p>FitLife Pro envisions a world where health and wellness are accessible to everyone.
                            We aspire to be the go-to platform that not only
                            guides individuals in achieving their fitness goals
                            but also fosters a supportive community that celebrates
                            and encourages each other's progress.
                            Through our innovative approach, we aim to inspire a
                            global movement towards a healthier and more active lifestyle.</p>

                        <div class="btn_wrapper">
                            <a class="text-decoration-none readmore_btn" href="./about.php">Read more<i
                                    class="fa-solid fa-play"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- CHOOSE US SECTION -->


    <!-- OUR SERVICES SECTION -->
    <section class="our_services_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="our_services_content">
                        <h5>OUR SERVICES</h5>
                        <h2>SOLUTIONS FOR MOVING BETTER & FEELING HEALTHIER</h2>
                    </div>
                </div>
            </div>
            <figure class="our_services_left_icon left_icon mb-0">
                <img src="./assets/images/our_services_left_icon.png" alt="" class="img-fluid">
            </figure>
            <figure class="our_services_right_icon right_icon mb-0">
                <img src="./assets/images/our_services_right_icon.png" alt="" class="img-fluid">
            </figure>
        </div>
    </section>

    <!-- FACILITIES SECTION -->
    <section class="facilities_section">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="facilities_image position-relative">
                        <figure class="mb-0"><img src="./assets/images/weightlifting.png" alt="" class="img-fluid">
                        </figure>
                        <div class="facilities_content">
                            <figure><img src="./assets/images/PNG/DUMBBELL.png" alt="" class="img-fluid"></figure>
                            <h5 class="text-white">WEIGHT-LIFTING ROUTINE CURATOR</h5>
                            <p class="text-white">A diverse content library with video tutorials, articles, and expert
                                advice on fitness routines,
                                nutrition, and overall well-being sets FitLife Pro apart,
                                providing comprehensive educational support.</p>
                            <div class="btn_wrapper">
                                <a class="text-decoration-none readmore_btn" href="./services.php">Read more
                                    <i class="fa-solid fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="facilities_image position-relative">
                        <figure class="mb-0"><img src="./assets/images/bodyweight.png" alt="" class="img-fluid">
                        </figure>
                        <div class="facilities_content">
                            <figure><img src="./assets/images/facilities_icon_2.png" alt="" class="img-fluid"></figure>
                            <h5 class="text-white">BODYWEIGHT EXERCISES</h5>
                            <p class="text-white">Dive into a diverse collection of bodyweight exercises in FitLife
                                Pro's content library,
                                featuring engaging video tutorials, informative articles,
                                and expert advice on mastering effective routines.</p>
                            <div class="btn_wrapper">
                                <a class="text-decoration-none readmore_btn" href="./services.php">Read more
                                    <i class="fa-solid fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" data-aos="fade-up">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="facilities_image position-relative">
                        <figure class="mb-0"><img src="./assets/images/nutrition.png" alt="" class="img-fluid"></figure>
                        <div class="facilities_content">
                            <figure><img src="./assets/images/PNG/APPLE.png" alt="" class="img-fluid"></figure>
                            <h5 class="text-white">NUTRITIONAL GUIDANCE</h5>
                            <p class="text-white">FitLife Pro goes beyond exercise,
                                offering personalized dietary recommendations based on fitness goals,
                                dietary preferences, and health conditions to ensure a
                                holistic approach to health.</p>
                            <div class="btn_wrapper">
                                <a class="text-decoration-none readmore_btn" href="./services.php">Read more
                                    <i class="fa-solid fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="facilities_image position-relative">
                        <figure class="mb-0"><img src="./assets/images/tracking.png" alt="" class="img-fluid"></figure>
                        <div class="facilities_content">
                            <figure><img src="./assets/images/PNG/track.png" alt="" class="img-fluid"></figure>
                            <h5 class="text-white">PROGRESS TRACKING</h5>
                            <p class="text-white">FitLife Pro offers interactive tools for users to track progress,
                                set goals, and receive reminders, enhancing
                                the user experience and motivation.</p>
                            <div class="btn_wrapper">
                                <a class="text-decoration-none readmore_btn" href="./services.php">Read more
                                    <i class="fa-solid fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn_wrapper">
                <a class="text-decoration-none join_now_btn" href="./pricing.php">Join us now
                    <i class="fa-solid fa-play"></i>
                </a>
            </div>
        </div>
    </section>
    <!-- TEAM MEMBERS SECTION -->
    <section class="team_member_section team_member_background_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="team_member_content">
                        <h5>COLLABORATORS</h5>
                        <h2>MADE POSSIBLE WITH THE HELP OF EXPERTS</h2>
                    </div>
                </div>
            </div>
            <div class="row" data-aos="fade-up">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="team_member_box_content2">
                        <div class="team_member_box_upper_portion">
                            <figure class="mb-0">
                                <img src="./assets/images/ELIZAR.png" alt="" class="img-fluid">
                            </figure>
                        </div>
                        <div class="team_member_box_lower_portion">
                            <h5>ELIZAR MANAGAY</h5>
                            <p>Physical Therapy Specialist</p>
                            <div class="team_member_social_icons">
                                <a href="#" class="text-decoration-none">
                                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="team_member_box_content2">
                        <div class="team_member_box_upper_portion">
                            <figure class="mb-0">
                                <img src="./assets/images/EMMALETH.jpg" alt="" class="img-fluid">
                            </figure>
                        </div>
                        <div class="team_member_box_lower_portion">
                            <h5>EMMALETH MANAGAY</h5>
                            <p>Health & Nutrition</p>
                            <div class="team_member_social_icons">
                                <a href="#" class="text-decoration-none">
                                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="team_member_box_content2">
                        <div class="team_member_box_upper_portion">
                            <figure class="mb-0">
                                <img src="./assets/images/CHRISTIAN.png" alt="" class="img-fluid">
                            </figure>
                        </div>
                        <div class="team_member_box_lower_portion">
                            <h5>CHRISTIAN DUQUE</h5>
                            <p>Fitness Instructor and Trainer, Men's Classic Physique Athlete</p>
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
                        <div class="team_member_box_content2">
                            <div class="team_member_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/JOSH.png" alt="" class="img-fluid">
                                </figure>
                            </div>
                            <div class="team_member_box_lower_portion">
                                <h5>JOSH DIAZ</h5>
                                <p>Gym and Fitness Enthusiast</p>
                                <div class="team_member_social_icons">
                                    <a href="https://www.instagram.com/91.80.002" class="text-decoration-none">
                                        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="team_member_box_content2">
                            <div class="team_member_box_upper_portion">
                                <figure class="mb-0">
                                    <img src="./assets/images/JULIE.png" alt="" class="img-fluid">
                                </figure>
                            </div>
                            <div class="team_member_box_lower_portion">
                                <h5>JULLIE CAPINO</h5>
                                <p>Gym and Fitness Enthusiast, Graphic Artist</p>
                                <div class="team_member_social_icons">
                                    <a href="https://www.instagram.com/julliecpn" class="text-decoration-none">
                                        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="team_member_box_content2">
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
                        <div class="team_member_box_content2">
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
                        <div class="team_member_box_content2">
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
                        <div class="team_member_box_content2">
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

                </div>
                <figure class="team_member_left_icon left_icon mb-0">
                    <img src="./assets/images/team_member_left_icon.png" alt="" class="img-fluid">
                </figure>
                <figure class="team_member_right_icon right_icon mb-0">
                    <img src="./assets/images/team_member_right_icon.png" alt="" class="img-fluid">
                </figure>
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
                            <a href="./index.html">
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
    <div id="blog-model-1" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
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
                                <img src="./assets/images/our_news_1.png" alt="blog-img" class="img-fluid">
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
                            <h5>QUIS AUTEM VEA EUM IURE REPREHENDERIT</h5>
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
                                            <input type="text" name="name" id="fname" class="form-placeholder-color"
                                                placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="tel" name="phone" id="phonenumber"
                                                class="form-placeholder-color form_style" placeholder="Phone">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="email" name="email" id="emailaddress"
                                                class="form-placeholder-color" placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="text" name="sub" id="subject"
                                                class="form-placeholder-color form_style" placeholder="Subject">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class=" form-group mb-0">
                                            <textarea rows="3" name="msg" id="message" class="form-placeholder-color"
                                                placeholder="Message"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="btnsubmit" id="sendbtn">Send now<i
                                        class="fa-solid fa-play"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="blog-model-2" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
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
                                <img src="./assets/images/our_news_2.png" alt="blog-img" class="img-fluid">
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
                            <h5 class="contact">CONTACT US</h5>
                            <h2>SEND US A MESSAGE & JOIN OUR TEAM</h2>
                            <form class="contact-form blog-model-form">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="text" name="name" id="blog_sec_fname"
                                                class="form-placeholder-color" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="tel" name="phone" id="blog_sec_phonenumber"
                                                class="form-placeholder-color form_style" placeholder="Phone">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="email" name="email" id="blog_sec_emailaddress"
                                                class="form-placeholder-color" placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <input type="text" name="sub" id="blog_sec_subject"
                                                class="form-placeholder-color form_style" placeholder="Subject">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class=" form-group mb-0">
                                            <textarea rows="3" name="msg" id="blog_sec_message"
                                                class="form-placeholder-color" placeholder="Message"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="btnsubmit" id="blog_sec_sendbtn">Send now<i
                                        class="fa-solid fa-play"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                <span>By Jenzer Managay</span>
                                <span class="float-lg-right">Nov 17, 2022</span>
                            </div>
                            <div class="social_icons">
                                <ul class="list-unstyled">
                                    <li><a href="#"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fa-brands fa-twitter" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fa-brands fa-instagram mr-0" aria-hidden="true"></i></a>
                                    </li>
                                </ul>
                            </div>
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