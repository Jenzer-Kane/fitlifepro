<!DOCTYPE html>
<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.html");
    exit();
}

// Database connection file
require 'database.php';

// $mysqli connection
if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to generate a unique transaction ID
function generateUniqueTransactionID()
{
    return uniqid('FITLIFE_', true);
}

// Retrieve user's information from the session (assuming you stored it during login)
$loggedInUsername = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$userInfo = [];

// Retrieve user's information from the database based on the username
if ($loggedInUsername) {
    $stmt = $mysqli->prepare("SELECT email, firstname, lastname FROM registration WHERE username = ?");

    if (!$stmt) {
        die("Error in preparing the statement: " . $mysqli->error);
    }

    $stmt->bind_param("s", $loggedInUsername);

    if (!$stmt->execute()) {
        die("Error executing the statement: " . $stmt->error);
    }

    $stmt->bind_result($userInfo['email'], $userInfo['firstname'], $userInfo['lastname']);
    $stmt->fetch();
    $stmt->close();
} else {
    // Handle the case where username is not set in the session
    die("Error: Username not found in the session.");
}
?>
<html lang="zxx">

<head>
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
    <title>Subscription Checkout | FITLIFE PRO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="assets/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    <!-- Add any additional stylesheets or scripts if needed -->
    <style>
        .tier-image {
            max-width: 200px;
            max-height: 200px;
            width: auto;
            height: auto;
            border-radius: 5px;
            margin: 0 auto;
            display: block;
        }

        .card-title-container {
            text-align: center;
        }

        .card-title {
            display: inline-block;
            padding: 5px 10px;
            color: white;
            text-align: center;
        }

        /* Add form styling */
        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Style for form sections */
        .form-section {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }

        .qr-code-section {
            margin-top: 20px;
            text-align: center;
        }

        .qr-code-image {
            max-width: 800px;
            max-height: 800px;
            width: auto;
            height: auto;
            border-radius: 5px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Subscription Checkout</h2>

        <?php
        // Check if the form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Insert the transaction into the database
            $transactionID = generateUniqueTransactionID();
            $firstname = $userInfo['firstname'];
            $lastname = $userInfo['lastname'];
            $userEmail = $_POST['email'];
            $plan = $_GET['plan'];
            $price = $_GET['price'];
            $description = urldecode($_GET['description']);
            $gcashNumber = $_POST['gcashNumber'];
            $referenceNumber = $_POST['referenceNumber'];

            // Prepare and execute the query
            $query = "INSERT INTO transactions (transaction_id, firstname, lastname, username, user_email, plan, price, description, gcash_number, reference_number) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);

            if (!$stmt) {
                die("Prepare failed: " . htmlspecialchars($mysqli->error));
            }

            $stmt->bind_param(
                "ssssssdsss",
                $transactionID,
                $firstname,
                $lastname,
                $loggedInUsername,
                $userEmail,
                $plan,
                $price,
                $description,
                $gcashNumber,
                $referenceNumber
            );

            if (!$stmt->execute()) {
                // Error handling: Unable to insert into the database
                echo '<div class="alert alert-danger" role="alert">Error processing subscription: ' . htmlspecialchars($stmt->error) . '</div>';
            } else {
                // Success message
                echo '<div class="alert alert-success" role="alert">Thank you for subscribing to FitLifePro! Your subscription has been submitted for approval.</div>';
                echo '<div class="alert alert-success" role="alert">Please allow up to 24 hours for processing. You will receive an email confirmation once your subscription is approved.</div>';

                // Countdown and redirect
                echo '<div id="countdown" class="alert alert-info" role="alert">Redirecting in <span id="countdown-number">10</span> seconds...</div>';

                // Redirect to profile.php after countdown
                echo '<script>
                        var count = 10;
                        var countdown = document.getElementById("countdown-number");
                        var redirectInterval = setInterval(function() {
                            count--;
                            countdown.textContent = count;
                            if (count <= 0) {
                                clearInterval(redirectInterval);
                                window.location.href = "profile.php";
                            }
                        }, 1000);
                    </script>';
            }

            $stmt->close();
        }
        ?>

        <!-- Subscription Plan Details Section -->
        <?php
        // Define an associative array with tier-specific image placeholders
        $tierImages = [
            'essential' => './assets/images/TIER1.png',
            'premium' => './assets/images/TIER2.png',
            'elite' => './assets/images/TIER3.png',
        ];

        $tierQRImages = [
            'essential' => './assets/images/qr_code_essential.png',
            'premium' => './assets/images/qr_code_premium.png',
            'elite' => './assets/images/qr_code_elite.png',
        ];

        // Retrieve plan details from query parameters
        $plan = isset($_GET['plan']) ? $_GET['plan'] : '';
        $price = isset($_GET['price']) ? $_GET['price'] : '';
        $description = isset($_GET['description']) ? urldecode($_GET['description']) : '';

        if ($plan && $price && $description) {
            // Display tier-specific image placeholder
            echo '<img src="' . $tierImages[$plan] . '" class="card-img-top tier-image" alt="Subscription Plan Image">';

            // Display selected plan details with colored navigation bars
            echo '<div class="card">';
            echo '<h5 class="card-title p-2 mb-3" style="background-color: ';
            if ($plan === 'essential') {
                echo 'lightblue';
            } elseif ($plan === 'premium') {
                echo 'lightcoral';
            } elseif ($plan === 'elite') {
                echo 'mediumpurple'; // Change to a different color for Elite Tier
            }
            echo ';">' . strtoupper($plan) . ' TIER</h5>';

            echo '<div class="card-body">';
            echo '<p class="card-text" style="font-weight: bold;"><strong>Price:</strong> P' . $price . ' Monthly</p>';
            echo '<p class="card-text" style="font-weight: bold;"><strong>Includes:</strong> ' . $description . '</p>';
            echo '</div>';
            echo '</div>';
        } else {
            // Handle the case where plan details are not provided
            echo '<div class="alert alert-danger" role="alert">Invalid plan details.</div>';
        }
        ?>

        <!-- Subscription Checkout Form -->
        <form method="post" action="">
            <!-- Personal Details Section -->
            <div class="form-section">
                <h3>Personal Details</h3>
                <!-- Display user information -->
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName"
                        value="<?php echo htmlspecialchars($userInfo['firstname']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName"
                        value="<?php echo htmlspecialchars($userInfo['lastname']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($userInfo['email']); ?>" readonly>
                </div>
            </div>

            <!-- QR Code Image Placeholder Section -->
            <div class="form-section">
                <h3>GCash Payment</h3>
                <div class="form-group">
                    <label for="qrCode">Scan the QR Code and pay required amount.</label>
                    <img src="<?php echo $tierQRImages[$plan]; ?>" class="qr-code-image" alt="QR Code" />
                </div>
            </div>

            <!-- GCash and Reference Number Section -->
            <div class="form-section">
                <h3>GCash Payment Details</h3>
                <div class="form-group">
                    <label for="gcashNumber">GCash Number:</label>
                    <input type="text" class="form-control" id="gcashNumber" name="gcashNumber" required>
                </div>
                <div class="form-group">
                    <label for="referenceNumber">Reference Number:</label>
                    <input type="text" class="form-control" id="referenceNumber" name="referenceNumber" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Complete Subscription</button>
        </form>
    </div>
</body>

</html>