<?php
session_start();
include 'database.php'; // Includes the MySQLi connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Retrieve username from session
$username = $_SESSION['username'];

// Retrieve days from POST data
$days = isset($_POST['days']) ? $_POST['days'] : [];
// Retrieve meal plans from POST data
$mealPlans = [];
foreach ($days as $day) {
    $dayKey = 'meal_plan_' . strtolower($day);
    if (isset($_POST[$dayKey])) {
        $mealPlans[$day] = json_decode($_POST[$dayKey], true);
    }
}

// Debug: Print POST data and mealPlans
echo '<pre>';
print_r($_POST);
print_r($mealPlans);
echo '</pre>';

try {
    // Start a transaction
    $mysqli->begin_transaction();

    // Prepare and execute DELETE statement
    $stmt = $mysqli->prepare("DELETE FROM meal_plans WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param('s', $username);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();

    // Prepare and execute INSERT statement
    $stmt = $mysqli->prepare("INSERT INTO meal_plans (username, day, time_slot, food_item) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    foreach ($mealPlans as $day => $mealPlan) {
        foreach ($mealPlan as $meal) {
            // Debug: Print data being inserted
            echo "Inserting: " . $username . ", " . $day . ", " . $meal['timeSlot'] . ", " . $meal['foodItem'] . "<br>";

            $stmt->bind_param('ssss', $username, $day, $meal['timeSlot'], $meal['foodItem']);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
        }
    }
    $stmt->close();

    // Commit the transaction
    $mysqli->commit();
    echo "Meal plans saved successfully!";
} catch (Exception $e) {
    // Rollback the transaction if something failed
    $mysqli->rollback();
    echo "Error saving meal plans: " . $e->getMessage();
}

// Close the connection
$mysqli->close();
?>