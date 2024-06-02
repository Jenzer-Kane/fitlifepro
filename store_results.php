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
$hamwilBW_kg = $_POST['hamwilBW_kg'];
$devineBW = $_POST['devineBW'];
$robinsonIBW = $_POST['robinsonIBW'];
$millerIBW = $_POST['millerIBW'];
$caloricIntake = $_POST['caloricIntake'];
$proteinIntake = $_POST['proteinIntake'];

// Prepare and bind
$stmt = $mysqli->prepare("INSERT INTO users_results (username, bmi, bmiCategory, bodyFatPercentage, fatMass, leanMass, hamwilBW_kg, devineBW, robinsonIBW, millerIBW, caloricIntake, proteinIntake, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp())");
$stmt->bind_param("ssssssssssss", $username, $bmi, $bmiCategory, $bodyFatPercentage, $fatMass, $leanMass, $hamwilBW_kg, $devineBW, $robinsonIBW, $millerIBW, $caloricIntake, $proteinIntake);

// Execute the statement
if ($stmt->execute()) {
    echo "New record created successfully";
    // Redirect to profile.php after success message
    header("Location: profile.php");
    exit; // Ensure no further code is executed after the redirect
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$mysqli->close();
?>