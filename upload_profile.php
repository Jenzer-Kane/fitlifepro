<?php
session_start();

// Check if user is logged in or redirect as needed
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Configuration for uploaded file
$target_dir = "uploads/"; // Directory where uploaded files will be stored
$target_file = $target_dir . basename($_FILES["profile_image"]["name"]); // Path of the uploaded file
$uploadOk = 1; // Flag to indicate if upload is successful
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // File extension

// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if ($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}

// Check file size (adjust as needed)
if ($_FILES["profile_image"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Allow certain file formats (adjust as needed)
if (
    $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif"
) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        echo "The file " . htmlspecialchars(basename($_FILES["profile_image"]["name"])) . " has been uploaded.";

        // Store the path to the uploaded file in the database
        $username = $_SESSION['username'];
        $profile_image_path = $target_file;

        // Connect to database
        $conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');
        if ($conn->connect_error) {
            die('Connection Failed: ' . $conn->connect_error);
        }

        // Update the user's profile image path in the database
        $stmt = $conn->prepare("UPDATE registration SET profile_image = ? WHERE username = ?");
        $stmt->bind_param("ss", $profile_image_path, $username);

        if ($stmt->execute()) {
            echo "Profile picture updated successfully.";
        } else {
            echo "Error updating profile picture: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();

        // Redirect to profile page or wherever appropriate
        header("Location: profile.php");
        exit();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>