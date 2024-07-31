<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

// Include database connection
include 'database.php';

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
        // Perform approval logic here

        // Fetch the user email associated with the transaction
        $transactionQuery = "SELECT * FROM transactions WHERE reference_number = '$reference_number'";
        $result = $mysqli->query($transactionQuery);
        if ($result && $result->num_rows > 0) {
            $transactionData = $result->fetch_assoc();
            $userEmail = $transactionData['user_email'];

            // Delete all previous transactions by this user except the new one
            $deleteOldTransactionsQuery = "DELETE FROM transactions WHERE user_email = '$userEmail' AND reference_number != '$reference_number'";
            if ($mysqli->query($deleteOldTransactionsQuery) === TRUE) {
                // Update status of the new transaction to "Approved"
                $updateStatusQuery = "UPDATE transactions SET status = 'Approved' WHERE reference_number = '$reference_number'";
                if ($mysqli->query($updateStatusQuery) === TRUE) {
                    // Status updated successfully

                    if (sendEmail($email, $plan, $price, $description)) {
                        // Email sent successfully
                        // Fetch transaction details from the database
                        $approvedTransaction = $mysqli->query("SELECT * FROM transactions WHERE reference_number = '$reference_number'");
                        if ($approvedTransaction && $approvedTransaction->num_rows > 0) {
                            $transactionData = $approvedTransaction->fetch_assoc();
                            $approvedTransactionDetails = "Transaction approved. Transaction ID: " . $transactionData['transaction_id'] . " | Reference Number: " . $transactionData['reference_number'];
                            // Redirect back to admin_subscription_approval.php with success message
                            header("Location: admin_subscription_approval.php?message=" . urlencode($approvedTransactionDetails));
                            exit();
                        } else {
                            // Transaction details not found
                            echo 'Error: Transaction details not found.';
                        }
                    } else {
                        // Error sending email
                        echo 'Error sending email.';
                    }
                } else {
                    // Error updating status
                    echo 'Error updating status: ' . $mysqli->error;
                }
            } else {
                // Error deleting old transactions
                echo 'Error deleting old transactions: ' . $mysqli->error;
            }
        } else {
            // Transaction not found
            echo 'Error: Transaction not found.';
        }
    } elseif (isset($_POST['disapprove'])) {
        // Perform disapproval logic here
        // For example, update the database to mark the transaction as disapproved

        // Update status in the database to "Disapproved"
        $updateStatusQuery = "UPDATE transactions SET status = 'Disapproved' WHERE reference_number = '$reference_number'";
        if ($mysqli->query($updateStatusQuery) === TRUE) {
            // Status updated successfully
            // Redirect back to admin_subscription_approval.php
            header("Location: admin_subscription_approval.php");
            exit();
        } else {
            // Error updating status
            echo 'Error updating status: ' . $mysqli->error;
        }
    } else {
        // No action specified
        echo 'Invalid request.';
    }
} else {
    // Form data not complete or invalid request method
    echo 'Invalid request.';
}
?>