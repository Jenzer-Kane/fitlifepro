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

// Set session username as "Superadmin" if the user is a superadmin
if (isset($_SESSION['superadmin']) && $_SESSION['superadmin'] === true) {
    $_SESSION['username'] = 'Superadmin';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $forumName = $_POST['forum_name'];
    $forumDescription = $_POST['forum_description'];

    // Prepare and execute the SQL statement
    $stmt = $mysqli->prepare("INSERT INTO forums (name, description) VALUES (?, ?)");
    $stmt->bind_param('ss', $forumName, $forumDescription);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Forum created successfully";

        // Log the action
        logAdminActivity($mysqli, $_SESSION['admin'], "Created Forum: '$forumName' with Description: '$forumDescription'");
    } else {
        $_SESSION['message'] = "Error creating forum: " . $stmt->error;
    }

    $stmt->close();
    header("Location: admin_forum.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request";
    header("Location: admin_forum.php");
    exit();
}

$mysqli->close();
?>