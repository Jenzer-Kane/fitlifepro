<?php
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Form data
$exercise_name = $_POST['exercise_name'];
$description = $_POST['description'];
$category = $_POST['category'];

// Insert query
$sql = "INSERT INTO exercises (exercise_name, description, category) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $exercise_name, $description, $category);

if ($stmt->execute()) {
    $_SESSION['message'] = 'Exercise added successfully.';
} else {
    $_SESSION['message'] = 'Failed to add exercise: ' . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: admin_content.php"); // Redirect back to the form page
exit();
?>