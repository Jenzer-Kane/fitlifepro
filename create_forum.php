<?php
session_start();

// Include database connection
include 'database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $forumName = $_POST['forum_name'];
    $forumDescription = $_POST['forum_description'];

    // Prepare and execute the SQL statement
    $stmt = $mysqli->prepare("INSERT INTO forums (name, description) VALUES (?, ?)");
    $stmt->bind_param('ss', $forumName, $forumDescription);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Forum created successfully";
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