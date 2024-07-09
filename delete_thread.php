<?php
session_start();

// Include database connection
include 'database.php'; // Adjust this to your database connection script

// Check if admin is logged in (you may adjust this to check for user permissions or other criteria)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Check if thread id is provided in the URL
if (isset($_GET['id'])) {
    $threadId = $_GET['id'];

    // Prepare and execute the SQL statement to delete the thread
    $stmt = $mysqli->prepare("DELETE FROM threads WHERE id = ?");
    $stmt->bind_param('i', $threadId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Thread deleted successfully";
    } else {
        $_SESSION['message'] = "Error deleting thread: " . $stmt->error;
    }

    $stmt->close();
    header("Location: admin_threads.php"); // Redirect to admin forum page after deletion
    exit();
} else {
    $_SESSION['message'] = "Invalid request";
    header("Location: admin_forum.php"); // Redirect to admin forum page if thread id is not provided
    exit();
}

$mysqli->close(); // Close the database connection
?>