<!DOCTYPE html>
<?php
session_start();

// Include the database configuration file
include_once 'database.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.html");
    exit();
}

// Retrieve user's information from the session (assuming you stored it during login)
$loggedInUsername = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$userInfo = [];

// Retrieve user's information from the database based on the username
if ($loggedInUsername) {
    $stmt = $mysqli->prepare("SELECT email, CONCAT(firstname, ' ', lastname) AS name FROM registration WHERE username = ?");

    if ($stmt) {
        $stmt->bind_param("s", $loggedInUsername);

        if ($stmt->execute()) {
            $stmt->bind_result($userInfo['email'], $userInfo['name']);
            $stmt->fetch();
            $stmt->close();
        } else {
            // Handle execution error
            die("Error executing the statement: " . $stmt->error);
        }
    } else {
        // Handle preparation error
        die("Error in preparing the statement: " . $mysqli->error);
    }
} else {
    // Handle the case where username is not set in the session
    die("Error: Username not found in the session.");
}
?>

<head>
    <title>Contact | FITLIFE PRO</title>
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
                                <li class="nav-item active">
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
                            <h1 data-aos="fade-up">CONTACT US</h1>
                            <div class="btn_wrapper">
                                <span class="sub_home_span">Home </span>-<span class="sub_span"> Contact</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- FIND US SECTION -->

    <!-- CONTACT US SECTION -->
    <section class="contact_us_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 d-lg-block d-md-flex align-items-center">
                    <div class="contact_us_image">
                        <figure class="mb-0">
                            <img src="./assets/images/contact_us_image.png" alt="">
                        </figure>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="contact_us_form_content" data-aos="fade-right">
                        <h5>CONTACT US</h5>
                        <h2>SEND OUR TEAM A MESSAGE</h2>
                        <form id="contactpage" method="POST" action="./contact-form.php">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group mb-0">
                                        <!-- Auto-fill name if logged in -->
                                        <input type="text" name="name" id="fullname" class="form-placeholder-color"
                                            placeholder="Name" value="<?php echo $userInfo['name']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group mb-0">
                                        <!-- Auto-fill email if logged in -->
                                        <input type="email" name="email" id="emailaddrs" class="form-placeholder-color"
                                            placeholder="Email"
                                            value="<?php echo isset($userInfo['email']) ? $userInfo['email'] : ''; ?>"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group mb-0">
                                        <input type="text" name="sub" id="title"
                                            class="form-placeholder-color form_style" placeholder="Subject">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class=" form-group mb-0">
                                        <textarea rows="3" name="msg" id="comment" class="form-placeholder-color"
                                            placeholder="Message"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="btn_wrapper">
                                <button type="submit" name="btnsubmit" id="submitbutton">Send now<i
                                        class="fa-solid fa-play"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- CONTACT MAP SECTION -->

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
                            <h5>REPREHENDERIT IN VOUTA VELIT ESSE CILLUM</h5>
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