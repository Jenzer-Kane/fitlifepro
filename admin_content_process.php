<?php
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Set session username as "Superadmin" if the user is a superadmin
if (isset($_SESSION['superadmin']) && $_SESSION['superadmin'] === true) {
    $_SESSION['username'] = 'Superadmin';
}

// Database connection and logger
$conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');
include 'logger.php'; // Include logger to record admin actions

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

    // Log the action
    logAdminActivity($conn, $_SESSION['username'], "Added New Exercise: $exercise_name in category $category");
} else {
    $_SESSION['message'] = 'Failed to add exercise: ' . $conn->error;

    // Log the failure
    logAdminActivity($conn, $_SESSION['username'], "Failed to Add Exercise: $exercise_name");
}

$stmt->close();
$conn->close();

header("Location: admin_content.php"); // Redirect back to the form page
exit();
?>