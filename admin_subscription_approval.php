<?php
session_start();

// Include database connection and logger
include 'database.php';
include 'logger.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Log page view for Transactions section
logAdminActivity($mysqli, $_SESSION['admin'], "Viewed Transactions List");

// Retrieve data from the transactions table
$sql = "SELECT * FROM transactions ORDER BY username, created_at DESC";
$result = $mysqli->query($sql);

// Function to mask email for privacy
function maskEmail($email)
{
    $parts = explode("@", $email);
    $namePart = substr($parts[0], 0, 2) . str_repeat("*", strlen($parts[0]) - 2);
    return $namePart . "@" . $parts[1];
}

// Function to mask GCash number for privacy
function maskGCashNumber($gcashNumber)
{
    if (strlen($gcashNumber) <= 1) {
        return $gcashNumber; // Return as is if the number is too short
    }

    // Mask all but the last 4 digits
    return str_repeat("*", strlen($gcashNumber) - 4) . substr($gcashNumber, -4);
}


?>

<!-- HTML content for admin dashboard -->
<html>

<head>
    <title>Transactions | FITLIFE PRO ADMIN</title>
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

        .approved-text {
            color: green;
            font-weight: bold;
        }

        .disapproved-text {
            color: red;
            font-weight: bold;
        }

        .pending-text {
            color: blue;
            font-weight: bold;
        }

        .expired-text {
            color: gray;
            font-weight: bold;
        }

        .reveal-container {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .reveal-btn {
            font-size: 14px;
            cursor: pointer;
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
                                <li class="nav-item active">
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
    </div> <!-- Closing banner-section-outer -->

    <div class="container-fluid"> <!-- Changed from container to container-fluid -->
        <h2>Transactions</h2>
        <h5>Existing Transactions. Click Transaction ID for more details.</h5>

        <!-- Search bar for filtering transactions -->
        <div class="search-container">
            <input type="text" id="transactionSearch" placeholder="Search transactions..."
                oninput="filterTransactions()" class="form-control">
        </div>


        <!-- Status Filter Buttons -->
        <div class="status-btns mb-3">
            <button onclick="filterByStatus('All')" class="btn-status">All</button>
            <button onclick="filterByStatus('Pending')" class="btn-status btn-info">Pending</button>
            <button onclick="filterByStatus('Approved')" class="btn-status btn-success">Approved</button>
            <button onclick="filterByStatus('Disapproved')" class="btn-status btn-danger">Disapproved</button>
            <button onclick="filterByStatus('Expired')" class="btn-status btn-dark">Expired</button>
        </div>

        <?php
        // Display success message if present in the URL
        $message = isset($_GET['message']) ? $_GET['message'] : '';
        if (!empty($message)) {
            echo '<div class="alert alert-success text-center" role="alert">' . htmlspecialchars($message) . '</div>';
        }
        ?>
        <table id="transactionTable" class="table table-bordered table-striped" style="width: 100%;">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>User Email</th>
                    <th>Plan</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>GCash Number</th>
                    <th>Reference Number</th>
                    <th>Date Requested</th>
                    <th>Date End</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $currentUsername = null;
                while ($row = $result->fetch_assoc()) {
                    $transaction_id = $row["transaction_id"] ?? '';
                    $username = $row["username"] ?? '';
                    $firstname = $row["firstname"] ?? '';
                    $lastname = $row["lastname"] ?? '';
                    $user_email = $row["user_email"] ?? '';
                    $displayEmail = maskEmail($user_email);
                    $plan = $row["plan"] ?? '';
                    $description = $row["description"] ?? '';
                    $price = $row["price"] ?? '';
                    $gcash_number = $row["gcash_number"] ?? '';
                    $displayGCash = maskGCashNumber($gcash_number);
                    $reference_number = $row["reference_number"] ?? '';
                    $created_at = isset($row["created_at"]) ? date("F j, Y | g:i A", strtotime($row["created_at"])) : '';
                    $date_end = isset($row["date_end"]) ? date("F j, Y | g:i A", strtotime($row["date_end"])) : '';
                    $status = $row["status"] ?? '';
                    $statusClass = strtolower($status) . '-text';

                    if ($username !== $currentUsername) {
                        if ($currentUsername !== null) {
                            echo "<tr><td colspan='14' style='background-color: #f8f9fa;'></td></tr>";
                        }
                        $currentUsername = $username;
                    }

                    echo "<tr class='transaction-row' data-status='$status'>
                        <td style='text-align: center;'><a href='view_user.php?username=" . urlencode($username) . "'>" . htmlspecialchars($transaction_id) . "</a></td>
                        <td style='text-align: center;'>" . htmlspecialchars($username) . "</td>
                        <td style='text-align: center;'>" . htmlspecialchars($firstname) . "</td>
                        <td style='text-align: center;'>" . htmlspecialchars($lastname) . "</td>
                        <td style='text-align: center;'>
                            <span class='reveal-container'>
                                <span id='emailMasked$transaction_id'>" . htmlspecialchars($displayEmail) . "</span>
                                <span id='emailUnmasked$transaction_id' style='display: none;'>" . htmlspecialchars($user_email) . "</span>
                                <i class='fas fa-eye reveal-btn' onclick='toggleVisibility(\"email\", \"$transaction_id\")' style='color: black;'></i>
                            </span>
                        </td>
                        <td style='text-align: center;'>" . htmlspecialchars($plan) . "</td>
                        <td style='text-align: center;'>" . htmlspecialchars($description) . "</td>
                        <td style='text-align: center;'>" . htmlspecialchars($price) . "</td>
                        <td style='text-align: center;'>
                            <span class='reveal-container'>
                                <span id='gcashMasked$transaction_id'>" . htmlspecialchars($displayGCash) . "</span>
                                <span id='gcashUnmasked$transaction_id' style='display: none;'>" . htmlspecialchars($gcash_number) . "</span>
                                <i class='fas fa-eye reveal-btn' onclick='toggleVisibility(\"gcash\", \"$transaction_id\")' style='color: black;'></i>
                            </span>
                        </td>
                        <td style='text-align: center;'>" . htmlspecialchars($reference_number) . "</td>
                        <td style='text-align: center; white-space: nowrap;'>" . htmlspecialchars($created_at) . "</td>
                        <td style='text-align: center; white-space: nowrap;'>" . htmlspecialchars($date_end) . "</td>
                        <td style='text-align: center; white-space: nowrap;' class='$statusClass'>" . htmlspecialchars($status) . "</td>
                        <td style='text-align: center;'>";

                    if ($status === 'Pending') {
                        echo "<form id='emailForm$transaction_id' action='process_approval.php' method='post'>
                            <input type='hidden' name='reference_number' value='$reference_number'>
                            <input type='hidden' name='email' id='email_$transaction_id'>
                            <input type='hidden' name='plan' id='plan_$transaction_id'>
                            <input type='hidden' name='description' id='description_$transaction_id' value='$description'>
                            <input type='hidden' name='price' id='price_$transaction_id'>
                            <input type='hidden' name='status' id='status_$transaction_id' value='Pending'>
                            <button type='submit' name='approve' onclick='populateFields($transaction_id, \"$user_email\", \"$plan\", \"$price\", \"$description\", \"Approved\", \"$status\")' class='btn btn-success'>Approve</button>
                            <button type='submit' name='disapprove' onclick='populateFields($transaction_id, \"$user_email\", \"$plan\", \"$price\", \"$description\", \"Disapproved\", \"$status\")' class='btn btn-danger'>Disapprove</button>
                        </form>";
                    }
                    echo "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>

        function filterTransactions() {
            const searchTerm = document.getElementById("transactionSearch").value.toLowerCase();
            const rows = document.querySelectorAll("#transactionTable tbody tr");

            rows.forEach(row => {
                let rowContainsSearchTerm = false;

                row.querySelectorAll("td").forEach(cell => {
                    if (cell.innerText.toLowerCase().includes(searchTerm)) {
                        rowContainsSearchTerm = true;
                    }
                });

                // Show the row if it contains the search term in any cell; otherwise, hide it
                row.style.display = rowContainsSearchTerm ? "" : "none";
            });
        }


        function filterByStatus(status) {
            const rows = document.querySelectorAll('.transaction-row');
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                if (status === 'All' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // JavaScript function to hide / unhide fields 
        function toggleVisibility(type, id) {
            let masked = document.getElementById(type + "Masked" + id);
            let unmasked = document.getElementById(type + "Unmasked" + id);
            let icon = masked.nextElementSibling;

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
        // JavaScript function to populate fields and submit the form
        function populateFields(id, email, plan, price, description, status) {
            var form = document.getElementById('emailForm' + id);
            document.getElementById('email_' + id).value = email;
            document.getElementById('plan_' + id).value = plan;
            document.getElementById('price_' + id).value = price;
            document.getElementById('description_' + id).value = description;
            document.getElementById('status_' + id).value = status;

            // Use AJAX to submit the form
            var formData = new FormData(form);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'process_approval.php', true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    handleApprovalSuccess();
                } else {
                    // Handle errors here
                    alert('There was an error with the request.');
                }
            };

            xhr.send(formData);
        }

        // JavaScript function to handle the success of approval
        function handleApprovalSuccess() {
            alert('Transaction approved. Subscription Confirmation Email sent successfully!'); // Customize this alert message
            location.reload(); // Reload the page to reflect the updated status
        }

        // AJAX function to log admin activity for approval/disapproval
        function logAdminActivityAJAX(id, action) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'log_approval.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send(`transaction_id=${id}&action=${action}`);
        }
    </script>

</html>

<?php
// Close the database connection
$mysqli->close();
?>