<?php
session_start();

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

$conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Ensure a valid reply ID is provided
if (isset($_GET['id']) && isset($_GET['thread_id'])) {
    $reply_id = $_GET['id'];
    $thread_id = $_GET['thread_id'];

    // Fetch reply content before deletion for logging
    // Fetch reply content and username before deletion for logging
    $stmt = $conn->prepare("SELECT content, username FROM replies WHERE id = ?");
    $stmt->bind_param("i", $reply_id);
    $stmt->execute();
    $stmt->bind_result($reply_content, $reply_author);
    $stmt->fetch();
    $stmt->close();

    // Proceed to delete the reply
    $delete_stmt = $conn->prepare("DELETE FROM replies WHERE id = ?");
    $delete_stmt->bind_param("i", $reply_id);

    if ($delete_stmt->execute()) {
        // Log the successful deletion, including the author of the reply
        logAdminActivity($conn, $_SESSION['admin'], "Deleted Reply ID: $reply_id for Thread ID: $thread_id by User: '$reply_author'. Content: '$reply_content'");
        $_SESSION['message'] = "Reply deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete reply.";
    }

    $delete_stmt->close();
} else {
    $_SESSION['message'] = "Invalid reply ID.";
}

$conn->close();
header("Location: admin_replies.php?thread_id=$thread_id");
exit();
?>