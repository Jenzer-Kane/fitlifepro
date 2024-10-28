<?php
session_start();

// Include database connection and logger
include 'database.php';
include 'logger.php'; // Include the logger script

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Set session username as "Superadmin" if the user is a superadmin
if (isset($_SESSION['superadmin']) && $_SESSION['superadmin'] === true) {
    $_SESSION['username'] = 'Superadmin';
}

// Check if thread ID is provided in the URL
if (isset($_GET['id'])) {
    $threadId = $_GET['id'];
    $adminUsername = $_SESSION['username']; // Assuming admin's username is stored in the session

    // Prepare and execute the SQL statement to delete the thread
    $stmt = $mysqli->prepare("DELETE FROM threads WHERE id = ?");
    $stmt->bind_param('i', $threadId);

    if ($stmt->execute()) {
        // Log the deletion of the thread
        logAdminActivity($mysqli, $adminUsername, "Deleted Thread with ID: {$threadId} in Forum ID: {$forum_id}");

        $_SESSION['message'] = "Thread deleted successfully";
    } else {
        $_SESSION['message'] = "Error deleting thread: " . $stmt->error;
    }

    $stmt->close();
    header("Location: admin_threads.php"); // Redirect to admin threads page after deletion
    exit();
} else {
    $_SESSION['message'] = "Invalid request";
    header("Location: admin_forum.php"); // Redirect to admin forum page if thread ID is not provided
    exit();
}

$mysqli->close(); // Close the database connection
?>