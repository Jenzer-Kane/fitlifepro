<?php
// Database connection
session_start();

// Check if username is set in session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    // Handle the case where username is not set in session
    echo "Error: Username is not set in session.";
}

$servername = "localhost"; // Usually 'localhost' if you are running locally
$username = "root";
$password = "";
$dbname = "fitlifepro_register";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Retrieve form data
$username = $_SESSION['username'];
$bmi = $_POST['bmi'];
$bmiCategory = $_POST['bmiCategory'];
$bodyFatPercentage = $_POST['bodyFatPercentage'];
$fatMass = $_POST['fatMass'];
$leanMass = $_POST['leanMass'];
$hamwiIBW_kg = $_POST['hamwiIBW_kg'];
$devineIBW = $_POST['devineIBW'];
$robinsonIBW = $_POST['robinsonIBW'];
$millerIBW = $_POST['millerIBW'];
$caloricIntake = $_POST['caloricIntake'];
$proteinIntake = $_POST['proteinIntake'];

// Prepare and bind
$stmt = $mysqli->prepare("INSERT INTO users_results (username, bmi, bmiCategory, bodyFatPercentage, fatMass, leanMass, hamwiIBW_kg, devineIBW, robinsonIBW, millerIBW, caloricIntake, proteinIntake, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp())");
$stmt->bind_param("ssssssssssss", $username, $bmi, $bmiCategory, $bodyFatPercentage, $fatMass, $leanMass, $hamwiIBW_kg, $devineIBW, $robinsonIBW, $millerIBW, $caloricIntake, $proteinIntake);

// Execute the statement
if ($stmt->execute()) {
    echo "Results saved successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$mysqli->close();
?>