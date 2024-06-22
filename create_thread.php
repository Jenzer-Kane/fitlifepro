<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
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
        header("Location: forum.php?id=" . $forum_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>