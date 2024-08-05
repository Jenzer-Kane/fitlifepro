<?php
session_start();

// Include database connection
include 'database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Retrieve data from the transactions table
$sql = "SELECT * FROM transactions";
$result = $mysqli->query($sql);
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
                                <li class="nav-item">
                                    <?php

                                    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
                                        // If admin is logged in, display "Admin" instead of username
                                        echo '<li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin</a></li>';

                                    } elseif (isset($_SESSION['username'])) {
                                        // If user is logged in, show name and logout button
                                        echo '<li class="nav-item"><a class="nav-link" href="#">' . '<a href="profile.php">' . $_SESSION['username'] . '</a>' . '</a></li>';
                                    } else {
                                        // If user is not logged in, show login and register buttons
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
    </div> <!-- Closing banner-section-outer -->

    <div class="container-fluid"> <!-- Changed from container to container-fluid -->
        <h2>Transactions</h2>
        <h5>Existing Transactions. Click Transaction ID for more Info.</h5>
        <?php
        // Display success message if present in the URL
        $message = isset($_GET['message']) ? $_GET['message'] : '';
        if (!empty($message)) {
            echo '<div class="alert alert-success text-center" role="alert">' . htmlspecialchars($message) . '</div>';
        }
        ?>
        <table class="table table-bordered table-striped" style="width: 100%;">
            <!-- Added style="width: 100%;" to ensure table takes up whole width -->
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
                if ($result && $result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        // Handle undefined keys
                        $id = isset($row["id"]) ? $row["id"] : "";
                        $username = isset($row["username"]) ? $row["username"] : "";
                        $transaction_id = isset($row["transaction_id"]) ? $row["transaction_id"] : "";
                        $firstname = isset($row["firstname"]) ? $row["firstname"] : "";
                        $lastname = isset($row["lastname"]) ? $row["lastname"] : "";
                        $user_email = isset($row["user_email"]) ? $row["user_email"] : "";
                        $plan = isset($row["plan"]) ? $row["plan"] : "";
                        $description = isset($row["description"]) ? $row["description"] : "";
                        $price = isset($row["price"]) ? $row["price"] : "";
                        $gcash_number = isset($row["gcash_number"]) ? $row["gcash_number"] : "";
                        $reference_number = isset($row["reference_number"]) ? $row["reference_number"] : "";
                        $created_at = isset($row["created_at"]) ? date("F j, Y | g:i A", strtotime($row["created_at"])) : "";
                        $date_end = isset($row["date_end"]) ? date("F j, Y | g:i A", strtotime($row["date_end"])) : "";
                        $status = isset($row["status"]) ? $row["status"] : "";

                        // Add style attribute to center align the values
                        echo "<tr>
<td style='text-align: center;'><a href='view_user.php?username=" . urlencode($username) . "'>" . htmlspecialchars($transaction_id) . "</a></td>
                            <td style='text-align: center;'>" . $username . "</td>
                            <td style='text-align: center;'>" . $firstname . "</td>
                            <td style='text-align: center;'>" . $lastname . "</td>
                            <td style='text-align: center;'>" . $user_email . "</td>
                            <td style='text-align: center;'>" . $plan . "</td>
                            <td style='text-align: center;'>" . $description . "</td>
                            <td style='text-align: center;'>" . $price . "</td>
                            <td style='text-align: center;'>" . $gcash_number . "</td>
                            <td style='text-align: center;'>" . $reference_number . "</td>
                            <td style='text-align: center;'>" . $created_at . "</td>
                            <td style='text-align: center;'>" . $date_end . "</td>
                            <td style='text-align: center;'>" . $status . "</td>
                            <td style='text-align: center;'>";

                        if ($status === 'Pending') {
                            echo "
                                <form id='emailForm$id' action='process_approval.php' method='post'>
                                    <input type='hidden' name='reference_number' value='$reference_number'>
                                    <input type='hidden' name='email' id='email_$id'>
                                    <input type='hidden' name='plan' id='plan_$id'>
                                    <input type='hidden' name='description' id='description_$id' value='$description'>
                                    <input type='hidden' name='price' id='price_$id'>
                                    <input type='hidden' name='status' id='status_$id' value='Pending'>
                                    <button type='submit' name='approve' onclick='populateFields($id, \"$user_email\", \"$plan\", \"$price\", \"$description\", \"Approved\", \"$status\")' class='btn btn-success'>Approve</button>
                                    <button type='submit' name='disapprove' onclick='populateFields($id, \"$user_email\", \"$plan\", \"$price\", \"$description\", \"Disapproved\", \"$status\")' class='btn btn-danger'>Disapprove</button>

                                </form>
                            ";
                        }

                        echo "</td></tr>";
                    }
                } else {
                    // Display a message if no data is found
                    echo "<tr><td colspan='10' style='text-align: center;'>No transactions found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
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
    </script>

</html>

<?php
// Close the database connection
$mysqli->close();
?>