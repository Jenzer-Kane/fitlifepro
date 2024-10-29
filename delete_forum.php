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

if (isset($_GET['id'])) {
    $forumId = $_GET['id'];

    // Retrieve forum name before deletion for logging
    $stmt = $mysqli->prepare("SELECT name, description FROM forums WHERE id = ?");
    $stmt->bind_param('i', $forumId);
    $stmt->execute();
    $stmt->bind_result($forumName, $forumDescription);
    $stmt->fetch();
    $stmt->close();

    // Prepare and execute the SQL statement
    $stmt = $mysqli->prepare("DELETE FROM forums WHERE id = ?");
    $stmt->bind_param('i', $forumId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Forum deleted successfully";

        // Log the action
        logAdminActivity($mysqli, $_SESSION['admin'], "Deleted Forum: '$forumName' with Description: '$forumDescription'");
    } else {
        $_SESSION['message'] = "Error deleting forum: " . $stmt->error;
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