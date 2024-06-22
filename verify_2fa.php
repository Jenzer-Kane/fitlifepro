<?php
session_start();

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php'; // Adjust the path as necessary

// Check if the necessary session data is set
if (!isset($_SESSION['email'])) {
    // Redirect to the login page if not set
    header("Location: login.html");
    exit();
}

// Function to generate a random 2FA code
function generate2FACode($length = 6)
{
    return strtoupper(substr(str_shuffle(str_repeat($x = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length));
}

// Generate a 2FA code if it's not already set
if (!isset($_SESSION['2fa_code'])) {
    $twoFactorCode = generate2FACode();
    $_SESSION['2fa_code'] = $twoFactorCode;

    // Send 2FA code to the user's email
    $userEmail = $_SESSION['email'];
    $subject = "FitLifePro - Login Authentication Code";
    $message = "Your login authentication code is: $twoFactorCode";

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fitlifepro2024@gmail.com'; // Gmail email address
        $mail->Password = 'wnoa azlq gxqc peef'; // The app password generated
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('fitlifepro2024@gmail.com', 'FitLifePro');
        $mail->addAddress($userEmail); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        echo "Failed to send Login authentication code. Please try again later. Mailer Error: {$mail->ErrorInfo}";
        exit();
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 2FA code is set in the POST data
    if (isset($_POST['two_factor_code'])) {
        $enteredCode = $_POST['two_factor_code'];

        // Verify the entered 2FA code against the session-stored code
        if ($enteredCode === $_SESSION['2fa_code']) {
            // Clear the 2FA code from the session after successful verification
            unset($_SESSION['2fa_code']);

            // Set a session variable to indicate the user is authenticated
            $_SESSION['authenticated'] = true;

            // Redirect to index.php after successful verification
            header("Location: index.php");
            exit();
        } else {
            // Incorrect 2FA code
            echo "<span style='color:red; font-size:20px;'>Incorrect 2FA code.</span><span style='font-size:20px;'> Please try again.";
            echo '<html>
                <head>
                    <title>Redirecting...</title>
                    <script>
                        var countdown = 3;
                        function updateCountdown() {
                            document.getElementById("countdown").innerHTML = countdown;
                            countdown--;
                            if (countdown < 0) {
                                window.location.href = "verify_2fa.php";
                            } else {
                                setTimeout(updateCountdown, 1000);
                            }
                        }
                        setTimeout(updateCountdown, 1000);
                    </script>
                </head>
                <body>
                    <p>Redirecting in <span id="countdown">3</span> seconds...</p>
                </body>
            </html>';
            exit();
        }
    } else {
        echo "Form data not received correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Verification | FITLIFE PRO</title>
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
                                    <a class="nav-link" href="">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="">About</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="">Services</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="">Collaborators</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="">Pricing</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link contact_btn" href="">Contact</a>
                                </li>
                                <?php
                                if (isset($_SESSION['username'])) {
                                    // If user is logged in, show name and logout button
                                    echo '<li class="nav-item"><a class="nav-link" href="#">' . '<a href="">' . $_SESSION['username'] . '</a>' . '</a></li>';
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
        </header>


        <!-- BODY -->
        <style>
            body {
                background-size: cover;
            }

            .wrapper-container {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                width: 100%;
                /* Ensure full width */
            }

            .wrapper {
                width: 420px;
                background: transparent;
                border: 2px solid rgba(255, 255, 255, .2);
                backdrop-filter: blur(20px);
                box-shadow: 0 0 10px rgba(0, 0, 0, .2);
                color: #fff;
                border-radius: 10px;
                padding: 30px 40px;
            }

            .wrapper h1 {
                font-size: 36px;
                text-align: center;
            }

            .wrapper .input-box {
                width: 100%;
                height: 50px;
                margin: 30px 0;
            }

            .input-box input {
                width: 100%;
                height: 50px;
                background: transparent;
                border: none;
                outline: none;
                border: 2px solid rgba(255, 255, 255, .2);
                border-radius: 40px;

            }

            .input-box input::placeholder {
                color: #FFF;
            }

            .input-box i {
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 20px;
            }

            .input-box {
                margin-bottom: 20px;
            }

            .input-box input {
                width: 100%;
                padding: 10px;
                margin-top: 5px;
                border: none;
                border-bottom: 1px solid #FFF;
                background-color: transparent;
                color: #FFF;
                outline: none;
            }

            .input-box i {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                left: 10px;
            }

            .wrapper .remember-forgot {
                display: flex;
                justify-content: space-between;
                font-size: 14.5px;
                margin: -15px 0 15px;
            }

            .remember-forgot label input {
                accent-color: #fff;
                margin-right: 3px;
            }

            .remember-forgot a {
                color: #FFF;
                text-decoration: none;
            }

            .remember-forgot a:hover {
                text-decoration: underline;
            }

            .wrapper .btn {
                width: 100%;
                height: 45px;
                background: #fff;
                border: none;
                outline: none;
                border-radius: 40px;
                box-shadow: 0 0 10px rgba(0, 0, 0, .1);
                cursor: pointer;
                font-size: 16px;
                color: #333;
                font-weight: 600;
            }

            .wrapper .register-link {
                font-size: 14.5px;
                text-align: center;
                margin-top: 20px 0 15px;
            }

            .register-link p a {
                color: fff;
                text-decoration: none;
                font-weight: 600;
            }

            .register-link p a:hover {
                color: #61dafb;
                /* Set your desired link color */
                text-decoration: underline;
            }
        </style>
        </head>

        <body>
            <div class="wrapper-container">
                <div class="wrapper">
                    <form action="verify_2fa.php" method="post">
                        <h1>LOGIN <br>AUTHENTICATION</h1>
                        <div class="register-link">
                            <p>Enter the Login Code sent to your email,<br>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </p>
                        </div>
                        <div class="input-box">
                            <input type="text" class="form-control" placeholder="" name="two_factor_code" required>
                        </div>
                        <button type="submit" class="btn">Submit</button>
                </div>
                </form>
            </div>
    </div>
</body>

</html>

</div>
</div>
</div>
</div>
</div>
</div>
</section>
</div>
<!-- PRICING TABLES SECTION -->


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
                            <li class="icons"><a href="#"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                            </li>
                            <li class="icons"><a href="#"><i class="fa-brands fa-twitter" aria-hidden="true"></i></a>
                            </li>
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