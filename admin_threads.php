<?php
session_start();

include 'logger.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Set session username as "Superadmin" if the user is a superadmin
if (isset($_SESSION['superadmin']) && $_SESSION['superadmin'] === true) {
    $_SESSION['username'] = 'Superadmin';
}

$conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Fetch all threads
$sql = "SELECT t.id, t.title, t.content, t.username, t.created_at, f.name AS forum_name 
        FROM threads t 
        JOIN forums f ON t.forum_id = f.id";
$result = $conn->query($sql);

if (!$result) {
    die('Query failed: ' . $conn->error);
}
function format_date($date)
{
    return date('F j, Y | g:i A', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forums | FITLIFE PRO ADMIN</title>
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

        /* Minimalist Search Bar Styling */
        .search-container input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 20px;
            margin-bottom: 15px;
            transition: border-color 0.3s;
        }

        .search-container input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
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

        .table-responsive {
            width: 100%;
            /* Adjust this value to set the width of the table */
        }
    </style>
</head>

<body>
    <div class="banner-section-outer">
        <header>
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Logo outside the navbar -->
                    <a class="navbar-brand mb-0" href="./admin_dashboard.php">
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
                                    <a class="nav-link" href="./admin_dashboard.php">Members</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./admin_subscription_approval.php">Transactions</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./admin_forum.php">Forums</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="./admin_threads.php">Threads</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./admin_content.php">Content</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link contact_btn" href="./admin_messages.php">Inquiries</a>
                                </li>
                                <!-- Add Admin Log link if Superadmin is logged in -->
                                <?php if (isset($_SESSION['superadmin']) && $_SESSION['superadmin'] === true): ?>
                                    <li class="nav-item"><a class="nav-link" href="./admin_log.php">Logs</a></li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <?php
                                    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
                                        echo '<li class="nav-item"><a class="nav-link" href="admin_dashboard.php">' . ($_SESSION['superadmin'] ? 'Superadmin' : 'Admin') . '</a></li>';
                                    } elseif (isset($_SESSION['username'])) {
                                        echo '<li class="nav-item"><a class="nav-link" href="#">' . '<a href="profile.php">' . $_SESSION['username'] . '</a>' . '</a></li>';
                                    } else {
                                        echo '<li class="nav-item"><a class="nav-link login_btn" href="./login.html">Login</a></li>';
                                        echo '<li class="nav-item"><a class="nav-link login_btn" href="./register.html">Register</a></li>';
                                    }
                                    ?>
                                <li class="nav-item">
                                    <a class="nav-link login_btn" href="logout.php">Logout</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </header>
    </div>
    <div class="container">
        <h2>Thread and Replies Management</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        ?>

        <h5>Existing Threads. Click to view Thread replies.</h5>
        <!-- Search bar for filtering threads -->
        <div class="search-container">
            <input type="text" id="threadSearch" placeholder="Search threads..." oninput="filterThreads()">
        </div>
        <table class="table table-bordered table-striped" id="threadTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Thread</th>
                    <th>Forum Located</th>
                    <th>Description</th>
                    <th>Author</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td style="white-space: nowrap;">
                            <a href="admin_replies.php?thread_id=<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                        <td><?= htmlspecialchars($row['forum_name']) ?></td>
                        </td>
                        <td style="white-space: nowrap;"><?= htmlspecialchars($row['content']) ?></td>

                        <td>
                            <a href="view_user.php?username=<?= urlencode($row['username']) ?>">
                                <?= htmlspecialchars($row['username']) ?>
                            </a>
                        </td>
                        <td style="white-space: nowrap;"><?= format_date($row['created_at']) ?></td>
                        <td>
                            <a href="delete_thread.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this thread?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function filterThreads() {
            const searchTerm = document.getElementById("threadSearch").value.toLowerCase();
            const rows = document.querySelectorAll("#threadTable tbody tr");

            rows.forEach(row => {
                const rowContainsSearchTerm = Array.from(row.cells).some(cell =>
                    cell.textContent.toLowerCase().includes(searchTerm)
                );
                row.style.display = rowContainsSearchTerm ? "" : "none";
            });
        }
    </script>

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

<?php
$conn->close();
?>