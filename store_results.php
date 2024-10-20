<?php
// Database connection
session_start();

// Check if username is set in session
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Error: Username is not set in session.']);
    exit();
}

$username = $_SESSION['username'];

$servername = "localhost"; // Usually 'localhost' if you are running locally
$dbusername = "root";
$password = "";
$dbname = "fitlifepro_register";

// Create connection
$mysqli = new mysqli($servername, $dbusername, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $mysqli->connect_error]);
    exit();
}

// Retrieve form data
$bmi = $_POST['bmi'];
$bmiCategory = $_POST['bmiCategory'];
$bodyFatPercentage = $_POST['bodyFatPercentage'];
$fatMass = $_POST['fatMass'];
$leanMass = $_POST['leanMass'];
$caloricIntake = $_POST['caloricIntake'];
$proteinIntake = $_POST['proteinIntake'];
$recommendedGoal = $_POST['recommendedGoal'];

// Prepare and bind
$stmt = $mysqli->prepare("INSERT INTO users_results (username, bmi, bmiCategory, bodyFatPercentage, fatMass, leanMass, caloricIntake, proteinIntake, recommendedGoal, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp())");

if ($stmt) {
    $stmt->bind_param("sssssssss", $username, $bmi, $bmiCategory, $bodyFatPercentage, $fatMass, $leanMass, $caloricIntake, $proteinIntake, $recommendedGoal);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Results saved successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving results: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $mysqli->error]);
}

// Close connection
$mysqli->close();
?>