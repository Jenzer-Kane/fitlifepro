<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

// Function to send email
function sendEmail($to, $plan, $price, $description)
{
    // Create a PHPMailer instance
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'fitlifepro2024@gmail.com'; // Gmail email address
        $mail->Password = 'wnoa azlq gxqc peef'; // The app password  generated
        $mail->SMTPSecure = 'tls'; // Use 'tls' or 'ssl'
        $mail->Port = 587; // TCP port to connect to

        // Sender information
        $mail->setFrom('fitlifepro2024@gmail.com', 'FitLifePro'); // company email address

        // Recipient information
        $mail->addAddress($to);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Subscription Confirmation';
        $mail->Body = 'Thank you for subscribing to our service! Your subscription details: Plan: ' . $plan . ', Price: ' . $price . ', Description: ' . $description;

        // Attach a file based on the subscription tier
        $attachmentPath = '';
        switch ($plan) {
            case 'essential':
                $attachmentPath = './src/ESSENTIALTIER.pdf';
                break;
            case 'premium':
                $attachmentPath = './src/PREMIUMTIER.pdf';
                break;
            case 'elite':
                $attachmentPath = './src/ELITETIER.pdf';
                break;
            default:
                // Handle the case when the plan is not recognized
                return false;
        }

        $mail->addAttachment($attachmentPath, $plan . '_TIER.pdf');

        // Send the email
        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

// Example usage:
// Check if form is submitted and required data is available
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['plan'], $_POST['price'], $_POST['description'])) {
    $to = $_POST['email'];
    $plan = $_POST['plan'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Send email
    if (sendEmail($to, $plan, $price, $description)) {
        echo 'Email sent successfully!';
    } else {
        echo 'Error sending email.';
    }
} else {
    echo 'Invalid request.';
}
?>