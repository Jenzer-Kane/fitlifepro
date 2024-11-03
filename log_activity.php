<?php
session_start();
include 'database.php'; // Ensure you include your database connection

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $type = $data['type']; // 'food' or 'exercise'
    $username = $_SESSION['username']; // Assuming username is stored in session
    $date = date('Y-m-d');

    if ($type === 'food') {
        $foodItem = $data['data']['foodItem'];
        $calories = $data['data']['calories'];
        $protein = $data['data']['protein'];

        // Prepare and execute insert statement
        $stmt = $mysqli->prepare("INSERT INTO food_activity_log (username, date, food_item, calories_consumed, protein_consumed) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $username, $date, $foodItem, $calories, $protein);
    } elseif ($type === 'exercise') {
        $exerciseName = $data['data']['exerciseName'];
        $caloriesBurnt = $data['data']['caloriesBurnt'];

        // Prepare and execute insert statement
        $stmt = $mysqli->prepare("INSERT INTO exercise_activity_log (username, date, exercise_name, calories_burnt, completion_status) VALUES (?, ?, ?, ?, ?)");
        $completionStatus = 1; // Assuming completed by default when logging
        $stmt->bind_param("sssis", $username, $date, $exerciseName, $caloriesBurnt, $completionStatus);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No data received']);
}

$mysqli->close();
?>