<?php
session_start();

// Include database connection
include 'database.php';

// Check if admin is logged in and is a superadmin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['superadmin']) || $_SESSION['superadmin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Fetch the admin activity log
$sql = "SELECT * FROM admin_activity_log ORDER BY timestamp DESC";
$result = $mysqli->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Activity Log | FITLIFE PRO ADMIN</title>
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
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/js/bootstrap.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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

        .team_member_box_content2 img {
            border-radius: 50%;
            overflow: hidden;
            width: 300px;
            height: 300px;
            object-fit: cover;
        }

        .table-responsive {
            width: 100%;
        }

        /* Styling for the search bar */
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

        /* Minimalist Button Styling */
        .status-btns .btn-status {
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            margin: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
            cursor: pointer;
        }

        .status-btns .btn-status:hover {
            opacity: 0.85;
        }

        .status-btns .btn-status:focus {
            outline: none;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }

        .btn-info {
            background-color: #007bff;
            color: white;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-dark {
            background-color: #343a40;
            color: white;
        }

        .btn-status {
            background-color: #6c757d;
            color: white;
        }

        /* Row color styles */
        .added-row {
            background-color: #d4edda !important;
        }

        .deleted-row {
            background-color: #f8d7da !important;
        }

        .updated-row {
            background-color: #d1ecf1 !important;
        }
    </style>
</head>

<body>
    <div class="banner-section-outer">
        <header>
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <a class="navbar-brand mb-0" href="./admin_dashboard.php">
                        <figure class="mb-0">
                            <img src="./assets/images/fitlife_pro_logo2.png" alt="" class="img-fluid">
                        </figure>
                    </a>
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
                                <li class="nav-item">
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
                                    <li class="nav-item active"><a class="nav-link" href="./admin_log.php">Logs</a></li>
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
                                </li>
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


    <body>
        <div class="container">
            <h2>Admin Activity Log</h2>

            <!-- Search bar and filter buttons -->
            <div class="search-container mb-3">
                <input type="text" id="logSearch" placeholder="Search activity logs..." oninput="filterLogs()">
            </div>

            <div class="status-btns mb-3">
                <button onclick="filterByAction('All')" class="btn-status">All</button>
                <button onclick="filterByAction('Added')" class="btn-status btn-success">Added</button>
                <button onclick="filterByAction('Deleted')" class="btn-status btn-danger">Deleted</button>
                <button onclick="filterByAction('Updated')" class="btn-status btn-info">Updated</button>
            </div>

            <div class="table-responsive" style="max-height: 1000px; overflow-y: auto; border: 1px solid #ccc;">
                <table class="table table-bordered table-striped" id="logTable">
                    <thead>
                        <tr>
                            <th>Admin Username</th>
                            <th>Activity</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $activity = htmlspecialchars($row['activity']);
                                $rowClass = '';
                                if (strpos($activity, 'Added') !== false) {
                                    $rowClass = 'added-row';
                                } elseif (strpos($activity, 'Deleted') !== false) {
                                    $rowClass = 'deleted-row';
                                } elseif (strpos($activity, 'Updated') !== false) {
                                    $rowClass = 'updated-row';
                                }

                                echo "<tr class='{$rowClass}' data-action='{$rowClass}'>
                                <td>" . htmlspecialchars($row['admin_username']) . "</td>
                                <td>" . $activity . "</td>
                                <td>" . date('F j, Y | g:i A', strtotime($row['timestamp'])) . "</td>
                              </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' class='text-center'>No activity found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>

        <script>
            function filterLogs() {
                const searchTerm = document.getElementById("logSearch").value.toLowerCase();
                const rows = document.querySelectorAll("#logTable tbody tr");

                rows.forEach(row => {
                    const rowText = Array.from(row.cells).map(cell => cell.textContent.toLowerCase()).join(" ");
                    row.style.display = rowText.includes(searchTerm) ? "" : "none";
                });
            }

            function filterByAction(action) {
                const rows = document.querySelectorAll("#logTable tbody tr");

                rows.forEach(row => {
                    const rowAction = row.getAttribute("data-action");
                    if (action === 'All') {
                        row.style.display = "";
                    } else if ((action === 'Added' && rowAction === 'added-row') ||
                        (action === 'Deleted' && rowAction === 'deleted-row') ||
                        (action === 'Updated' && rowAction === 'updated-row')) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
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
$mysqli->close();
?>