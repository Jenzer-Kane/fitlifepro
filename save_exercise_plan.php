<?php
include 'database.php'; // Include your database connection file
session_start(); // Start the session to access session variables

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username']; // Get the username from the session
        $day = $_POST['day'];
        $exercisePlanData = json_decode($_POST['exercise_plan'], true);

        // Prepare SQL statement
        $stmt = $mysqli->prepare("INSERT INTO exercise_plans (username, day, time_slot, exercise_item) VALUES (?, ?, ?, ?)");

        foreach ($exercisePlanData as $exercise) {
            $timeSlot = $exercise['timeSlot'];
            $exerciseItem = $exercise['exerciseItem'];

            // Bind parameters and execute
            $stmt->bind_param('ssss', $username, $day, $timeSlot, $exerciseItem);
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