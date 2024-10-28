<?php
session_start();

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

if (isset($_GET['id'])) {
    $reply_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM replies WHERE id = ?");
    $stmt->bind_param("i", $reply_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Reply deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete reply.";
    }

    $stmt->close();
}

$conn->close();
header("Location: admin_replies.php");
exit();
?>