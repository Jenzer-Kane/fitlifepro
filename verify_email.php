<?php
session_start();

// Check if the necessary session data is set
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    // Redirect to the login page if not set
    header("Location: login.html");
    exit();
}

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php'; // Adjust the path as necessary

// Database connection
require 'database.php'; // Adjust the path as necessary

// Function to generate a random verification code
function generateVerificationCode($length = 6)
{
    return strtoupper(substr(str_shuffle(str_repeat($x = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length));
}

// Generate a verification code
$verificationCode = generateVerificationCode();

// Update the database with the verification code
$username = $_SESSION['username'];
$query = "UPDATE registration SET verification_code = ? WHERE username = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ss", $verificationCode, $username);
$stmt->execute();
$stmt->close();

// Send verification code to the user's email
$userEmail = $_SESSION['email'];
$subject = "FitLifePro - Email Verification Code";
$message = "Your email verification code is: $verificationCode";

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'fitlifepro2024@gmail.com'; // Gmail email address
    $mail->Password = 'wnoa azlq gxqc peef'; // The app password generated
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('fitlifepro2024@gmail.com', 'FitLifePro');
    $mail->addAddress($userEmail);                         // Add a recipient

    // Content
    $mail->isHTML(true);                                   // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body = $message;

    $mail->send();
    echo "Great! All you need is to verify your email.<br>";
    echo "A Verification Code has been sent to $userEmail";

    // Countdown message for redirection
    echo '<p id="countdown">Redirecting in 10 seconds...</p>';
    // Redirect to login.php after 5 seconds
    echo '<script>
             let seconds = 10;
             setInterval(function() {
                 document.getElementById("countdown").innerHTML = "Redirecting in " + seconds + " seconds...";
                 seconds--;
                 if (seconds < 0) {
                     window.location.href = "verify_email.html";
                 }
             }, 1000);
           </script>';

} catch (Exception $e) {
    echo "Failed to send verification code. Please try again later. Mailer Error: {$mail->ErrorInfo}";
}
?>