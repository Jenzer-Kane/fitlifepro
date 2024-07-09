<?php
session_start();

// Include database connection
include 'database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $forumId = $_GET['id'];

    // Prepare and execute the SQL statement
    $stmt = $mysqli->prepare("DELETE FROM forums WHERE id = ?");
    $stmt->bind_param('i', $forumId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Forum deleted successfully";
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