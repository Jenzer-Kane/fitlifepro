<?php
session_start();
// Load Composer's autoloader
require 'vendor/autoload.php';

// Include database connection and logger
include 'database.php';
include 'logger.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Set session username as "Superadmin" if the user is a superadmin
if (isset($_SESSION['superadmin']) && $_SESSION['superadmin'] === true) {
    $_SESSION['username'] = 'Superadmin';
}

// Get the admin's username from the session
$adminUsername = $_SESSION['admin'] ?? 'admin';

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
        $mail->Password = 'wnoa azlq gxqc peef'; // The app password generated
        $mail->SMTPSecure = 'tls'; // Use 'tls' or 'ssl'
        $mail->Port = 587; // TCP port to connect to

        // Sender information
        $mail->setFrom('fitlifepro2024@gmail.com', 'FitLifePro'); // company email address

        // Recipient information
        $mail->addAddress($to);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Subscription Confirmation';

        // Email body with styles
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .message-content { 
                    border: 1px solid #ddd; 
                    padding: 20px; 
                    background-color: #f9f9f9; 
                }
                .message-header {
                    font-size: 18px;
                    font-weight: bold;
                    color: #007bff;
                }
                .message-body {
                    margin-top: 10px;
                    font-size: 16px;
                }
                .message-footer {
                    margin-top: 20px;
                    font-size: 14px;
                    color: gray;
                }
            </style>
        </head>
        <body>
            <div class='message-content'>
                <div class='message-header'>FitLife Pro - Subscription Confirmation</div>
                <div class='message-body'>
                    Dear valued user,<br><br>
                    We are pleased to inform you that your subscription has been approved. Below are your subscription details:
                    <br><br>
                    <strong>Plan:</strong> $plan<br>
                    <strong>Price:</strong> $price<br>
                    <strong>Description:</strong> $description<br><br>
                    Thank you for choosing FitLifePro! We are excited to support you in your fitness journey.
                </div>
                <div class='message-footer'>
                    This is an automated message from FitLifePro. Please do not reply to this email.<br>
                    Best regards,<br>FitLifePro Team
                </div>
            </div>
        </body>
        </html>
        ";

        // Attach a file based on the subscription tier
        // $attachmentPath = '';
        //switch ($plan) {
        //  case 'essential':
        //     $attachmentPath = './src/ESSENTIALTIER.pdf';
        //      break;
        // case 'premium':
        //      $attachmentPath = './src/PREMIUMTIER.pdf';
        //      break;
        //  case 'elite':
        //     $attachmentPath = './src/ELITETIER.pdf';
        //      break;
        //  default:
        //      echo 'Unrecognized subscription plan: ' . htmlspecialchars($plan);
        //      return false; // Handle unrecognized plan
        //   }

        // if (!file_exists($attachmentPath)) {
        //      echo 'Attachment not found: ' . $attachmentPath;
        //     return false;
        //   }

        // $mail->addAttachment($attachmentPath, $plan . '_TIER.pdf');

        // Send the email
        return $mail->send();
    } catch (Exception $e) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        return false;
    }
}

// Check if form is submitted and required data is available
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reference_number'], $_POST['email'], $_POST['plan'], $_POST['price'], $_POST['description'])) {
    // Get data from the form
    $reference_number = $_POST['reference_number'];
    $email = $_POST['email'];
    $plan = $_POST['plan'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Check which button was clicked (Approve or Disapprove)
    if (isset($_POST['approve'])) {
        // Perform approval logic
        $updateStatusQuery = "UPDATE transactions SET status = 'Approved' WHERE reference_number = ?";
        $stmt = $mysqli->prepare($updateStatusQuery);
        if ($stmt) {
            $stmt->bind_param("s", $reference_number);
            if ($stmt->execute()) {
                // Log the approval action
                logAdminActivity($mysqli, $adminUsername, "Approved transaction with Reference Number: $reference_number");

                // Send email notification
                if (sendEmail($email, $plan, $price, $description)) {
                    // Fetch the approved transaction details
                    $approvedTransaction = $mysqli->query("SELECT * FROM transactions WHERE reference_number = '$reference_number'");
                    if ($approvedTransaction && $approvedTransaction->num_rows > 0) {
                        $transactionData = $approvedTransaction->fetch_assoc();
                        $approvedTransactionDetails = "Transaction Approved. Transaction ID: " . $transactionData['transaction_id'] . " | Reference Number: " . $transactionData['reference_number'] . " | User Email: " . $transactionData['user_email'] . " | Plan: " . $transactionData['plan'] . " | Price: " . $transactionData['price'];
                        header("Location: admin_subscription_approval.php?message=" . urlencode($approvedTransactionDetails));
                        exit();
                    } else {
                        echo 'Error: Transaction details not found.';
                    }
                } else {
                    echo 'Error sending email.';
                }
            } else {
                echo 'Error updating status: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            echo 'Error preparing statement: ' . $mysqli->error;
        }
    } elseif (isset($_POST['disapprove'])) {
        // Perform disapproval logic
        $updateStatusQuery = "UPDATE transactions SET status = 'Disapproved' WHERE reference_number = ?";
        $stmt = $mysqli->prepare($updateStatusQuery);
        if ($stmt) {
            $stmt->bind_param("s", $reference_number);
            if ($stmt->execute()) {
                // Log the disapproval action
                logAdminActivity($mysqli, $adminUsername, "Disapproved Transaction with Reference Number: $reference_number");

                // Fetch the disapproved transaction details
                $disapprovedTransaction = $mysqli->query("SELECT * FROM transactions WHERE reference_number = '$reference_number'");
                if ($disapprovedTransaction && $disapprovedTransaction->num_rows > 0) {
                    $transactionData = $disapprovedTransaction->fetch_assoc();
                    $disapprovedTransactionDetails = "Transaction Disapproved. Transaction ID: " . $transactionData['transaction_id'] . " | Reference Number: " . $transactionData['reference_number'] . " | User Email: " . $transactionData['user_email'] . " | Plan: " . $transactionData['plan'] . " | Price: " . $transactionData['price'];
                    header("Location: admin_subscription_approval.php?message=" . urlencode($disapprovedTransactionDetails));
                    exit();
                } else {
                    echo 'Error: Transaction details not found.';
                }
            } else {
                echo 'Error updating status: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            echo 'Error preparing statement: ' . $mysqli->error;
        }
    } else {
        echo 'Invalid request.';
    }
} else {
    echo 'Invalid request.';
}
?>