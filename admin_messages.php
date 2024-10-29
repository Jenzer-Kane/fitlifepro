<?php
session_start();

// Include database connection
include 'database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Set session username as "Superadmin" if the user is a superadmin
if (isset($_SESSION['superadmin']) && $_SESSION['superadmin'] === true) {
    $_SESSION['username'] = 'Superadmin';
}

// Retrieve data from the contact_form table, including username from the registration table
$sql = "
    SELECT cf.*, u.username 
    FROM contact_form cf
    LEFT JOIN registration u ON cf.email = u.email
";
$result = $mysqli->query($sql);

// Function to mask email for privacy
function maskEmail($email)
{
    $parts = explode("@", $email);
    $namePart = substr($parts[0], 0, 2) . str_repeat("*", max(strlen($parts[0]) - 2, 0));
    return $namePart . "@" . $parts[1];
}

?>

<!-- HTML content for admin dashboard -->
<html>

<head>
    <title>Members | FITLIFE PRO ADMIN</title>
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
                                    <a class="nav-link active" href="./admin_dashboard.php">Members</a>
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
                                <li class="nav-item active">
                                    <a class="nav-link contact_btn" href="./admin_messages.php">Inquiries</a>
                                </li>
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

    <div class="container-fluid">
        <h2>Inquiries</h2>
        <h5>Existing Inquiries. Click User Email for more details.</h5>
        <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
                <tr>
                    <th>User Email</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date Received</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $email = $row["email"] ?? "";
                        $maskedEmail = maskEmail($email);  // Mask the email for privacy
                        $username = $row["username"] ?? "";
                        $name = $row["name"] ?? "";
                        $subject = $row["subject"] ?? "";
                        $message = $row["message"] ?? "";
                        $created_at = isset($row["created_at"]) ? date("F j, Y | g:i A", strtotime($row["created_at"])) : "";

                        echo "<tr>
                <td style='text-align: center;'>
                    <span id='emailMasked{$username}'>{$maskedEmail}</span>
                    <span id='emailUnmasked{$username}' style='display: none;'>{$email}</span>
                    <i class='fas fa-eye' onclick='toggleVisibility(\"{$username}\")' style='cursor: pointer;'></i>
                </td>
                <td style='text-align: center;'><a href='view_user.php?username=$username'>$username</a></td>
                <td style='text-align: center;'>{$name}</td>
                <td style='text-align: center;'>{$subject}</td>
                <td style='text-align: center;'>{$message}</td>
                <td style='text-align: center;'>{$created_at}</td>
            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align: center;'>No inquiries found.</td></tr>";
                }
                ?>
            </tbody>

        </table>
    </div>

</html>
<script>

    function toggleVisibility(username) {
        const masked = document.getElementById(`emailMasked${username}`);
        const unmasked = document.getElementById(`emailUnmasked${username}`);
        const icon = masked.nextElementSibling;

        if (unmasked.style.display === "none") {
            unmasked.style.display = "inline";
            masked.style.display = "none";
            icon.style.color = "#007bff";  // Change to blue when revealed
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            unmasked.style.display = "none";
            masked.style.display = "inline";
            icon.style.color = "black";  // Change back to black when hidden
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
</script>


<?php
// Close the database connection
$mysqli->close();
?>