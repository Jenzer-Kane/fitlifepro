<?php
session_start();

// Include database connection
include 'database.php';
include 'logger.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Set session username as "Superadmin" if the user is a superadmin
if (isset($_SESSION['superadmin']) && $_SESSION['superadmin'] === true) {
    $_SESSION['username'] = 'Superadmin';
}

// Get the username from the URL
$username = isset($_GET['username']) ? $_GET['username'] : '';

// Log the admin's action for viewing this user's details
logAdminActivity($mysqli, $_SESSION['admin'], "Viewed Member Details for $username");

function maskEmail($email)
{
    $parts = explode("@", $email);
    $namePart = substr($parts[0], 0, 2) . str_repeat("*", strlen($parts[0]) - 2);
    return $namePart . "@" . $parts[1];
}

function maskGCashNumber($gcashNumber)
{
    if (strlen($gcashNumber) <= 1) {
        return $gcashNumber; // Return as is if the number is too short
    }

    // Mask all but the last 4 digits
    return str_repeat("*", strlen($gcashNumber) - 4) . substr($gcashNumber, -4);
}

// Fetch user info from users_info table
$userInfoSql = "SELECT * FROM users_info WHERE username = ?";
$userInfoStmt = $mysqli->prepare($userInfoSql);
$userInfoStmt->bind_param('s', $username);
$userInfoStmt->execute();
$userInfoResult = $userInfoStmt->get_result();
$userAdditionalInfo = $userInfoResult->fetch_assoc();

// Fetch user results from users_results table
$userResultsSql = "SELECT * FROM users_results WHERE username = ?";
$userResultsStmt = $mysqli->prepare($userResultsSql);
$userResultsStmt->bind_param('s', $username);
$userResultsStmt->execute();
$userResultsResult = $userResultsStmt->get_result();

$userResultsData = [];
if ($userResultsResult->num_rows > 0) {
    while ($result = $userResultsResult->fetch_assoc()) {
        $userResultsData[] = $result;
    }
}

// Fetch user weight data from users_info table for graphs
$userWeightQuery = "SELECT created_at AS date, bmiWeight FROM users_info WHERE username = ? ORDER BY created_at";
$userWeightStmt = $mysqli->prepare($userWeightQuery);
$userWeightStmt->bind_param('s', $username);
$userWeightStmt->execute();
$userWeightResults = $userWeightStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Merge weight data with user results based on closest created_at timestamp
$mergedData = [];
foreach ($userResultsData as $result) {
    $date = $result['created_at'];
    $closestWeight = null;

    // Find the closest weight entry on or before the result date
    foreach ($userWeightResults as $weightEntry) {
        if ($weightEntry['date'] <= $date) {
            $closestWeight = $weightEntry['bmiWeight'];
        } else {
            break;
        }
    }

    $mergedData[] = array_merge($result, ['weight' => $closestWeight]);
}

// Prepare data for Chart.js
$chartData = [
    'dates' => array_column($mergedData, 'created_at'),
    'bmi' => array_column($mergedData, 'bmi'),
    'bodyFatPercentage' => array_column($mergedData, 'bodyFatPercentage'),
    'fatMass' => array_column($mergedData, 'fatMass'),
    'leanMass' => array_column($mergedData, 'leanMass'),
    'weight' => array_column($mergedData, 'weight')
];

$encodedChartData = json_encode($chartData);


// Fetch user info from registration table
$userRegistrationInfoSql = "SELECT * FROM registration WHERE username = ?";
$userRegistrationInfoStmt = $mysqli->prepare($userRegistrationInfoSql);
$userRegistrationInfoStmt->bind_param('s', $username);
$userRegistrationInfoStmt->execute();
$userRegistrationResult = $userRegistrationInfoStmt->get_result();
$userInfo = $userRegistrationResult->fetch_assoc();

// Fetch user info from transactions table
$userTransactionsSql = "SELECT * FROM transactions WHERE username = ?";
$userTransactionsStmt = $mysqli->prepare($userTransactionsSql);
$userTransactionsStmt->bind_param('s', $username);
$userTransactionsStmt->execute();
$userTransactionsResult = $userTransactionsStmt->get_result();

// Fetch all threads with forum details
$sql = "SELECT t.id, t.title, t.content, t.username, t.created_at, f.name AS forum_name 
        FROM threads t 
        JOIN forums f ON t.forum_id = f.id";
$result = $mysqli->query($sql);

// Filter threads by username
$userThreads = [];
while ($row = $result->fetch_assoc()) {
    if ($row['username'] === $username) {
        $userThreads[] = $row;
    }
}

// Prepare the SQL query to fetch replies with thread and forum details
$repliesSql = "SELECT r.id, r.thread_id, r.username, r.content, r.created_at, 
                      t.title AS thread_title, t.username AS thread_author, f.name AS forum_name
               FROM replies r
               JOIN threads t ON r.thread_id = t.id
               JOIN forums f ON t.forum_id = f.id
               WHERE r.username = ?";
$repliesStmt = $mysqli->prepare($repliesSql);
$repliesStmt->bind_param('s', $username);
$repliesStmt->execute();
$repliesResult = $repliesStmt->get_result();

$userReplies = [];
while ($row = $repliesResult->fetch_assoc()) {
    $userReplies[] = $row;
}

function format_date($date)
{
    return date('F j, Y | g:i A', strtotime($date));
}
?>

<html>

<head>

    <head>
        <title>Member Details | FITLIFE PRO ADMIN</title>
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
                color: black;
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
                                <li class="nav-item active">
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

    <div class="container-fluid">
        <h2>Member Details for <?php echo htmlspecialchars($username); ?></h2>
        <?php if ($userInfo): ?>
            <table class="table table-bordered table-striped" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Date Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style='text-align: center;'><?php echo htmlspecialchars($userInfo['username']); ?></td>
                        <td style='text-align: center;'>
                            <span class='reveal-container'>
                                <span
                                    id='emailMasked<?php echo htmlspecialchars($userInfo["username"]); ?>'><?php echo htmlspecialchars(maskEmail($userInfo['email'])); ?></span>
                                <span id='emailUnmasked<?php echo htmlspecialchars($userInfo["username"]); ?>'
                                    style='display: none;'><?php echo htmlspecialchars($userInfo['email']); ?></span>
                                <i class='fas fa-eye reveal-btn'
                                    onclick='toggleVisibility("email", "<?php echo htmlspecialchars($userInfo["username"]); ?>")'
                                    style='color: black;'></i>
                            </span>
                        </td>
                        <td style='text-align: center;'><?php echo htmlspecialchars($userInfo['firstname']); ?></td>
                        <td style='text-align: center;'><?php echo htmlspecialchars($userInfo['lastname']); ?></td>
                        <td style='text-align: center;'><?php echo htmlspecialchars($userInfo['gender']); ?></td>
                        <td style='text-align: center;'>
                            <?php echo isset($userInfo['created_at']) ? date("F j, Y | g:i A", strtotime($userInfo['created_at'])) : "N/A"; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>No information found for this user.</p>
        <?php endif; ?>



        <h2>Transactions</h2>
        <?php
        $message = isset($_GET['message']) ? $_GET['message'] : '';
        if (!empty($message)) {
            echo '<div class="alert alert-success text-center" role="alert">' . htmlspecialchars($message) . '</div>';
        }
        ?>
        <table class="table table-bordered table-striped" style="width: 100%;">
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
                if ($userTransactionsResult && $userTransactionsResult->num_rows > 0) {
                    while ($row = $userTransactionsResult->fetch_assoc()) {
                        $id = $row["id"] ?? "";
                        $username = $row["username"] ?? "";
                        $transaction_id = $row["transaction_id"] ?? "";
                        $firstname = $row["firstname"] ?? "";
                        $lastname = $row["lastname"] ?? "";
                        $user_email = $row["user_email"] ?? "";
                        $plan = $row["plan"] ?? "";
                        $description = $row["description"] ?? "";
                        $price = $row["price"] ?? "";
                        $gcash_number = $row["gcash_number"] ?? "";
                        $reference_number = $row["reference_number"] ?? "";
                        $created_at = isset($row["created_at"]) ? date("F j, Y | g:i A", strtotime($row["created_at"])) : "";
                        $date_end = isset($row["date_end"]) ? date("F j, Y | g:i A", strtotime($row["date_end"])) : "";
                        $status = $row["status"] ?? "";

                        // Set CSS classes for status display
                        $statusClass = '';
                        if ($status === 'Approved') {
                            $statusClass = 'approved-text';
                        } elseif ($status === 'Disapproved') {
                            $statusClass = 'disapproved-text';
                        } elseif ($status === 'Pending') {
                            $statusClass = 'pending-text';
                        } elseif ($status === 'Expired') {
                            $statusClass = 'expired-text';
                        }

                        echo "<tr>
        <td style='text-align: center;'>" . htmlspecialchars($transaction_id) . "</td>
        <td style='text-align: center;'>" . htmlspecialchars($username) . "</td>
        <td style='text-align: center;'>" . htmlspecialchars($firstname) . "</td>
        <td style='text-align: center;'>" . htmlspecialchars($lastname) . "</td>
        <td style='text-align: center;'>
            <span class='reveal-container'>
                <span id='emailMasked$id'>" . htmlspecialchars(maskEmail($user_email)) . "</span>
                <span id='emailUnmasked$id' style='display: none;'>" . htmlspecialchars($user_email) . "</span>
                <i class='fas fa-eye reveal-btn' onclick='toggleVisibility(\"email\", \"$id\")' style='color: black;'></i>
            </span>
        </td>
        <td style='text-align: center;'>" . htmlspecialchars($plan) . "</td>
        <td style='text-align: center;'>" . htmlspecialchars($description) . "</td>
        <td style='text-align: center;'>" . htmlspecialchars($price) . "</td>
        <td style='text-align: center;'>
            <span class='reveal-container'>
                <span id='gcashMasked$id'>" . htmlspecialchars(maskGCashNumber($gcash_number)) . "</span>
                <span id='gcashUnmasked$id' style='display: none;'>" . htmlspecialchars($gcash_number) . "</span>
                <i class='fas fa-eye reveal-btn' onclick='toggleVisibility(\"gcash\", \"$id\")' style='color: black;'></i>
            </span>
        </td>
        <td style='text-align: center;'>" . htmlspecialchars($reference_number) . "</td>
        <td style='text-align: center;'>" . htmlspecialchars($created_at) . "</td>
        <td style='text-align: center;'>" . htmlspecialchars($date_end) . "</td>
        <td style='text-align: center;' class='$statusClass'>" . htmlspecialchars($status) . "</td>
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
            </form>";
                        }

                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='14' style='text-align: center;'>No transactions found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>Body Reports</h2>
        <?php if (count($mergedData) > 0): ?>
            <div class="chart-container">
                <canvas id="bodyReportsChart"></canvas>
            </div>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th
                            style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: #f0f0f0; color: black;">
                            Date Generated</th>
                        <th
                            style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: rgba(153, 102, 255, 1); color: black;">
                            Weight</th>
                        <th
                            style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: rgba(75, 192, 192, 1); color: black;">
                            BMI</th>
                        <th>BMI Category</th>
                        <th>Recommended Goal</th>
                        <th
                            style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: rgba(255, 99, 132, 1); color: black;">
                            Body Fat Percentage</th>
                        <th
                            style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: rgba(54, 162, 235, 1); color: black;">
                            Fat Mass</th>
                        <th
                            style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: rgba(255, 206, 86, 1); color: black;">
                            Lean Mass</th>
                        <th>Recommended Daily Caloric Intake</th>
                        <th>Recommended Daily Protein Intake</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mergedData as $result): ?>
                        <tr>
                            <td style='text-align: center;'>
                                <?php
                                $formattedDate = isset($result['created_at']) ? date("F j, Y | g:i A", strtotime($result['created_at'])) : "";
                                echo htmlspecialchars($formattedDate);
                                ?>
                            </td>
                            <td style='text-align: center;'>
                                <?php echo isset($result['weight']) ? htmlspecialchars($result['weight']) : 'N/A'; ?>
                            </td>
                            <td style='text-align: center;'><?php echo htmlspecialchars($result['bmi']); ?></td>
                            <td style='text-align: center;'><?php echo htmlspecialchars($result['bmiCategory']); ?></td>
                            <td style='text-align: center;'><?php echo htmlspecialchars($result['recommendedGoal']); ?></td>
                            <td style='text-align: center;'><?php echo htmlspecialchars($result['bodyFatPercentage']); ?></td>
                            <td style='text-align: center;'><?php echo htmlspecialchars($result['fatMass']); ?></td>
                            <td style='text-align: center;'><?php echo htmlspecialchars($result['leanMass']); ?></td>
                            <td style='text-align: center;'><?php echo htmlspecialchars($result['caloricIntake']); ?></td>
                            <td style='text-align: center;'><?php echo htmlspecialchars($result['proteinIntake']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No body reports found for this user.</p>
        <?php endif; ?>

        <h2>Threads Created</h2>
        <?php if (count($userThreads) > 0): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thread</th>
                        <th>Forum Located</th>
                        <th>Description</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userThreads as $thread): ?>
                        <tr>
                            <td style='text-align: center;'><?= htmlspecialchars($thread['id']) ?></td>
                            <td style='text-align: center;'>
                                <a href="admin_replies.php?thread_id=<?= urlencode($thread['id']) ?>">
                                    <?= htmlspecialchars($thread['title']) ?>
                                </a>
                            </td>
                            <td style='text-align: center;'><?= htmlspecialchars($thread['forum_name']) ?></td>
                            <td style="white-space: nowrap;"><?= htmlspecialchars($thread['content']) ?></td>
                            <td style='text-align: center;'><?= format_date($thread['created_at']) ?></td>
                            <td style='text-align: center;'>
                                <a href="delete_thread.php?id=<?= urlencode($thread['id']) ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this thread?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No threads created by this user.</p>
        <?php endif; ?>

        <h2>Replies Made</h2>
        <?php if (count($userReplies) > 0): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thread Located</th>
                        <th>Forum Located</th>
                        <th>Reply Content</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userReplies as $reply): ?>
                        <tr>
                            <td style='text-align: center;'><?= htmlspecialchars($reply['id']) ?></td>
                            <td style='text-align: center;'>
                                <a href="admin_replies.php?thread_id=<?= urlencode($reply['thread_id']) ?>">
                                    <?= htmlspecialchars($reply['thread_title']) ?>
                                </a>
                            </td>
                            <td style='text-align: center;'><?= htmlspecialchars($reply['forum_name']) ?></td>
                            <td style="white-space: nowrap;"><?= htmlspecialchars($reply['content']) ?></td>
                            <td style='text-align: center;'>
                                <?= date("F j, Y | g:i A", strtotime($reply['created_at'])) ?>
                            </td>
                            <td style='text-align: center;'>
                                <a href="delete_reply.php?id=<?= urlencode($reply['id']) ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this reply?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No replies found by this user.</p>
        <?php endif; ?>
    </div>

    <script>
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

        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('bodyReportsChart').getContext('2d');
            const data = <?php echo $encodedChartData; ?>;
            const labels = data.dates.map(date => new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }));
            const bmiData = data.bmi;
            const bodyFatPercentageData = data.bodyFatPercentage;
            const fatMassData = data.fatMass;
            const leanMassData = data.leanMass;
            const weightData = data.weight;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Weight',
                            data: weightData,
                            borderColor: 'rgba(153, 102, 255, 1)',
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderWidth: 3,
                            pointStyle: 'crossRot',
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(153, 102, 255, 1)',
                        },
                        {
                            label: 'BMI',
                            data: bmiData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 3,
                            pointStyle: 'circle',
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                        },
                        {
                            label: 'Body Fat Percentage',
                            data: bodyFatPercentageData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 3,
                            pointStyle: 'rect',
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                        },
                        {
                            label: 'Fat Mass',
                            data: fatMassData,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderWidth: 3,
                            pointStyle: 'triangle',
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                        },
                        {
                            label: 'Lean Mass',
                            data: leanMassData,
                            borderColor: 'rgba(255, 206, 86, 1)',
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            borderWidth: 3,
                            pointStyle: 'rectRot',
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                        }

                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Value',
                                font: {
                                    size: 18
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date',
                                font: {
                                    size: 18
                                }
                            },
                            ticks: {
                                font: {
                                    size: 14,
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Saved Results',
                            font: {
                                size: 18
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                        },
                    }
                }
            });
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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


</html>

<?php
$mysqli->close();
?>