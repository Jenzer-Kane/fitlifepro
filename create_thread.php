<?php
session_start();

include 'database.php';
include 'logger.php'; // Include the logger script

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $forum_id = intval($_POST['forum_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $username = $_SESSION['username'];

    $sql = "INSERT INTO threads (forum_id, title, content, username) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $forum_id, $title, $content, $username);

    if ($stmt->execute()) {
        // Log thread creation
        logAdminActivity($conn, $username, "Created a New Thread with Title '{$title}' in Forum ID: {$forum_id}");

        // Redirect to the specific forum page
        header("Location: forum.php?id=" . $forum_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>