<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: indexforum.php");
    exit();
}

$thread_id = intval($_GET['id']);

$conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Fetch thread information including forum name and thread creator's profile image
$stmt = $conn->prepare("SELECT t.title, t.content, t.username, t.created_at, f.name AS forum_name, 
                        COALESCE(r.profile_image, 'assets/images/no_pfp.jpg') AS thread_creator_image 
                        FROM threads t 
                        JOIN forums f ON t.forum_id = f.id 
                        LEFT JOIN registration r ON t.username = r.username 
                        WHERE t.id = ?");
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$result = $stmt->get_result();
$thread = $result->fetch_assoc();
$stmt->close();

if (!$thread) {
    echo "Thread not found.";
    exit();
}

// Handling post request to add replies
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("INSERT INTO replies (thread_id, username, content) VALUES (?, ?, ?)");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("iss", $thread_id, $username, $content);
    if ($stmt->execute()) {
        header("Location: thread.php?id=$thread_id");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch replies along with profile images of the repliers
$replies_stmt = $conn->prepare("SELECT r.username, r.content, r.created_at, u.profile_image 
                                FROM replies r 
                                LEFT JOIN registration u ON r.username = u.username 
                                WHERE r.thread_id = ? 
                                ORDER BY r.created_at ASC");
$replies_stmt->bind_param("i", $thread_id);
$replies_stmt->execute();
$replies_result = $replies_stmt->get_result();
$replies_stmt->close();

function format_date($date)
{
    return date('F j, Y, g:i a', strtotime($date));
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

        .large-counter-text {
            font-size: 1.2em;
            /* Adjust as needed */
            font-weight: bold;
        }

        .thread-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .thread-container h5 {
            margin-bottom: 20px;
        }

        .thread-container img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .thread-description {
            font-size: 1.5rem;
            /* Increase the font size */
            font-weight: bold;
            /* Make it bold */
            line-height: 1.6;
            /* Add some spacing between lines */
            margin-top: 20px;
            /* Add margin to separate it from the rest */
            color: #333;
            /* Ensure the color is distinct */
        }

        .profile-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 10px;
            border: 1px solid #ccc;
        }

        h5 {
            font-size: 1.2rem;
            font-weight: normal;
        }

        .text-muted {
            font-size: 0.9rem;
        }

        .reply {
            display: flex;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .reply img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 15px;
        }

        .reply .username {
            font-weight: bold;
            color: #007bff;
        }

        .reply .date-replied {
            font-size: 12px;
            color: #888;
        }

        .reply-content {
            flex-grow: 1;
        }

        .post-reply-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .post-reply-section h5 {
            margin-bottom: 15px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        @media (max-width: 768px) {
            .thread-container {
                margin-top: 20px;
            }

            .reply {
                flex-direction: column;
                align-items: flex-start;
            }

            .reply img {
                margin-bottom: 10px;
            }
        }

        .author-label {
            color: green;
            font-weight: 300;
            /* Thinner font */
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
                            <h1 data-aos="fade-up"><?= htmlspecialchars($thread['title']) ?></h1>
                            <div class="btn_wrapper">
                                <span class="sub_home_span">Exercises </span>-<span class="sub_span">Thread Title</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <body>
        <!-- Main Content Section -->
        <div class="container mt-5">
            <!-- Thread Container -->
            <div class="thread-container">
                <h5>Thread Description:</h5>
                <div class="d-flex align-items-center mb-3">
                    <img src="<?= htmlspecialchars($thread['thread_creator_image']) ?>" alt="Thread Creator"
                        class="profile-image">
                    <div>
                        <h6>Created by: <?= htmlspecialchars($thread['username']) ?></h6> <!-- Author of the thread -->
                        <p class="text-muted"><?= format_date($thread['created_at']) ?></p>
                    </div>
                </div>
                <!-- Make the thread description larger and more prominent -->
                <p class="thread-description"><?= nl2br(htmlspecialchars($thread['content'])) ?></p>
            </div>

            <!-- Replies Section -->
            <h5 class="mt-5 mb-4">Replies:</h5>
            <?php while ($reply = $replies_result->fetch_assoc()): ?>
                <div class="reply">
                    <img src="<?= htmlspecialchars($reply['profile_image']) ?>" alt="Reply Creator" class="profile-image">
                    <div class="reply-content">
                        <p class="username">
                            <strong><?= htmlspecialchars($reply['username']) ?></strong>
                            <?php if ($reply['username'] === $thread['username']): ?>
                                <span class="author-label">(Author)</span>
                            <?php endif; ?>
                        </p>
                        <p><?= nl2br(htmlspecialchars($reply['content'])) ?></p>
                        <small class="date-replied">Replied on <?= format_date($reply['created_at']) ?></small>
                    </div>
                </div>
            <?php endwhile; ?>

            <!-- Post Reply Section -->
            <?php if (isset($_SESSION['username'])): ?>
                <div class="post-reply-section mt-5">
                    <h5>Post a Reply</h5>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="content">Reply Content</label>
                            <textarea id="content" name="content" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Reply</button>
                    </form>
                </div>
            <?php else: ?>
                <p><a href="login.php">Log in</a> to post a reply.</p>
            <?php endif; ?>
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