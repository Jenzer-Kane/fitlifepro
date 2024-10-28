<?php
session_start();

// Include database connection and logger
include 'database.php';
include 'logger.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Set session username as "Superadmin" if the user is a superadmin
if (isset($_SESSION['superadmin']) && $_SESSION['superadmin'] === true) {
    $_SESSION['username'] = 'Superadmin';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $forumId = $_POST['forum_id'];
    $forumName = $_POST['forum_name'];
    $forumDescription = $_POST['forum_description'];

    // Retrieve current forum data for logging previous values
    $stmt = $mysqli->prepare("SELECT name, description FROM forums WHERE id = ?");
    $stmt->bind_param('i', $forumId);
    $stmt->execute();
    $stmt->bind_result($currentName, $currentDescription);
    $stmt->fetch();
    $stmt->close();

    // Prepare and execute the SQL update statement
    $stmt = $mysqli->prepare("UPDATE forums SET name = ?, description = ? WHERE id = ?");
    $stmt->bind_param('ssi', $forumName, $forumDescription, $forumId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Forum updated successfully";

        // Log the name change if it's different from the current name
        if ($forumName !== $currentName) {
            logAdminActivity($mysqli, $_SESSION['admin'], "Changed Forum Name for ID: $forumId from '$currentName' to '$forumName'");
        }

        // Log the description change if it's different from the current description
        if ($forumDescription !== $currentDescription) {
            logAdminActivity($mysqli, $_SESSION['admin'], "Changed Forum Description for ID: $forumId from '$currentDescription' to '$forumDescription'");
        }
    } else {
        $_SESSION['message'] = "Error updating forum: " . $stmt->error;
    }

    $stmt->close();
    header("Location: admin_forum.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request";
    header("Location: admin_forum.php");
    exit();
}

$mysqli->close();
?>