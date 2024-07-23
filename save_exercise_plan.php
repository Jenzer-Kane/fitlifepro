<?php
include 'db_connect.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = $_POST['day'];
    $exercisePlanData = json_decode($_POST['exercise_plan'], true);

    foreach ($exercisePlanData as $exercise) {
        $timeSlot = $exercise['timeSlot'];
        $exerciseItem = $exercise['exerciseItem'];

        // Save to database
        $stmt = $mysqli->prepare("INSERT INTO exercise_plans (day, time_slot, exercise_item) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $day, $timeSlot, $exerciseItem);
        $stmt->execute();
    }

    // Redirect or display a success message
    header('Location: profile.php');
    exit();
}
?>