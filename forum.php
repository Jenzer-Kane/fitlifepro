<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$forum_id = intval($_GET['id']);

// Retrieve forum information
$sql = "SELECT * FROM forums WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("i", $forum_id);
$stmt->execute();
$forum = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$forum) {
    die('Forum not found.');
}

// Retrieve threads related to the forum
$sql = "SELECT * FROM threads WHERE forum_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("i", $forum_id);
$stmt->execute();
$threads = $stmt->get_result();
$stmt->close();
function format_date($date)
{
    return date('F j, Y | g:i A', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Forums | FITLIFE PRO</title>
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

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
            font-size: 1.0em;
            text-align: center;
            position: relative;
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
                                    <a class="nav-link" href="./pricing.php">Pricing</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./collaborators.php">Collaborators</a>
                                </li>
                                <li class="nav-item active">
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
                            <h1 data-aos="fade-up"><?= htmlspecialchars($forum['name']) ?></h1>
                            <div class="btn_wrapper">
                                <span class="sub_home_span">Forums </span>-<span
                                    class="sub_span"><?= htmlspecialchars($forum['name']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="container">
        <h3 class="mt-5"><?= htmlspecialchars($forum['description']) ?></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        <h5 class="mt-2">Thread Title</h5>
                    </th>
                    <th>
                        <h5>Description</h5>
                    </th>
                    <th>
                        <h5>Author</h5>
                    </th>
                    <th>
                        <h5>Date Created</h5>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if ($threads->num_rows > 0): ?>
                    <?php while ($thread = $threads->fetch_assoc()): ?>
                        <tr>
                            <td style="white-space: nowrap;">
                                <h5><a href="thread.php?id=<?= $thread['id'] ?>"><?= htmlspecialchars($thread['title']) ?></a>
                            </td>
                            <td style="white-space: nowrap;"><?= htmlspecialchars($thread['content']) ?></td>
                            <td><?= htmlspecialchars($thread['username']) ?></td>
                            <td style="white-space: nowrap;"><?= format_date($thread['created_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No threads available.</td>
                    <?php endif; ?>
            </tbody>
        </table>
        <a href="new_thread.php?forum_id=<?= $forum_id ?>" class="btn btn-primary">Create New Thread</a>
    </div>

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