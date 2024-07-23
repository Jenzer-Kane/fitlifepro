<?php
include 'database.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = $_POST['day'];
    $mealPlanData = json_decode($_POST['meal_plan'], true);

    foreach ($mealPlanData as $meal) {
        $timeSlot = $meal['timeSlot'];
        $foodItem = $meal['foodItem'];

        // Save to database
        $stmt = $mysqli->prepare("INSERT INTO meal_plans (day, time_slot, food_item) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $day, $timeSlot, $foodItem);
        $stmt->execute();
    }

    // Redirect or display a success message
    header('Location: profile.php');
    exit();
}
?>