<?php
session_start();

include_once 'database.php';

// Check if connection is successful
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

require 'vendor/autoload.php'; // Autoload the Composer dependencies

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = isset($_POST['sub']) ? $_POST['sub'] : ''; // Define $subject and set it to an empty string if not provided
    $message = $_POST['msg'];

    // Validate input (add more validation as needed)
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo ("Error: Name, email, subject, and message are required fields");
        exit();
    }

    // Create a new PHPMailer instance
    $mail = new PHPMailer();

    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'fitlifepro2024@gmail.com'; // Gmail email address
    $mail->Password = 'wnoa azlq gxqc peef'; // The app password generated
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Sender and recipient
    $mail->setFrom($email, $name);
    $mail->addAddress('fitlifepro2024@gmail.com'); // company's email address

    // Email content
    $mail->Subject = $subject;
    $mail->Body = $message;

    // Send the email
    if ($mail->send()) {
        // Insert the data into the database
        $stmt = $mysqli->prepare("INSERT INTO contact_form (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            // Success: Both email sent and data inserted into the database
            echo ("Email sent successfully! The team will get back to you and your concern!");
            
            // Countdown message for redirection
            echo '<p id="countdown">Redirecting in 5 seconds...</p>';
            // Redirect to contact.php after 5 seconds
            echo '<script>
                    let seconds = 5;
                    setInterval(function() {
                        document.getElementById("countdown").innerHTML = "Redirecting in " + seconds + " seconds...";
                        seconds--;
                        if (seconds < 0) {
                            window.location.href = "contact.php";
                        }
                    }, 1000);
                  </script>';
        } else {
            // Error inserting data into the database
            echo ("Error submitting the form to the database.");
        }

        $stmt->close();
    } else {
        // Error sending the email
        echo ("Error sending the email.");
    }
}
