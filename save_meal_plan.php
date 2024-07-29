<?php
include 'database.php'; // Include your database connection file
session_start(); // Start the session to access session variables

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username']; // Assuming the username is stored in the session
    $day = $_POST['day'];
    $mealPlanData = $_POST['meal_plan'];

    $sql = "INSERT INTO meal_plans (username, day, meal_plan_data, created_at) VALUES (?, ?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE meal_plan_data = VALUES(meal_plan_data), updated_at = NOW()";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("sss", $username, $day, $mealPlanData);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Meal plan saved successfully.";
        } else {
            $_SESSION['message'] = "Error saving meal plan.";
        }

        $stmt->close();
    }

    $mysqli->close();
}

header("Location: profile.php");
exit;
?>