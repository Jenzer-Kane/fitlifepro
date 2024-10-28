<?php
include 'database.php';

function logAdminActivity($mysqli, $adminUsername, $activity)
{
    // Prepare the SQL statement for logging
    $stmt = $mysqli->prepare("INSERT INTO admin_activity_log (admin_username, activity) VALUES (?, ?)");

    // Check if the statement was prepared successfully
    if (!$stmt) {
        error_log("Prepare failed in logAdminActivity: " . $mysqli->error);
        return;
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("ss", $adminUsername, $activity);

    if (!$stmt->execute()) {
        error_log("Execute failed in logAdminActivity: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();
}
?>