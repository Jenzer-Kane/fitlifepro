<?php
include 'database.php'; // Include your database connection file
session_start(); // Start the session to access session variables

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username']; // Get the username from the session
        $day = $_POST['day'];
        $mealPlanData = json_decode($_POST['meal_plan'], true);

        // Prepare SQL statement
        $stmt = $mysqli->prepare("INSERT INTO meal_plans (username, day, time_slot, food_item) VALUES (?, ?, ?, ?)");

        foreach ($mealPlanData as $meal) {
            $timeSlot = $meal['timeSlot'];
            $foodItem = $meal['foodItem'];

            // Bind parameters and execute
            $stmt->bind_param('ssss', $username, $day, $timeSlot, $foodItem);
            $stmt->execute();
        }

        // Redirect or display a success message
        header('Location: profile.php');
        exit();
    } else {
        // Handle case where user is not logged in
        echo "User not logged in.";
    }
}
?>