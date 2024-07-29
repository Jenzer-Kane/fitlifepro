<?php
include 'database.php'; // Include your database connection file
session_start(); // Start the session to access session variables

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username']; // Assuming the username is stored in the session
    $day = $_POST['day'];
    $exercisePlanData = $_POST['exercise_plan'];

    $sql = "INSERT INTO exercise_plans (username, day, exercise_plan_data, created_at) VALUES (?, ?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE exercise_plan_data = VALUES(exercise_plan_data), updated_at = NOW()";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("sss", $username, $day, $exercisePlanData);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Exercise plan saved successfully.";
        } else {
            $_SESSION['message'] = "Error saving exercise plan.";
        }

        $stmt->close();
    }

    $mysqli->close();
}

header("Location: profile.php");
exit;
?>