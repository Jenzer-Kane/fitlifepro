<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verification_code'])) {
    $verificationCode = $_POST['verification_code'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');
    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    }

    // Get the current user's username from the session
    $username = $_SESSION['username'];

    // Retrieve the stored verification code from the database
    $stmt = $conn->prepare("SELECT verification_code FROM registration WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($storedCode);
    $stmt->fetch();
    $stmt->close();

    if ($storedCode === $verificationCode) {
        // Update the user's status to verified
        $stmt = $conn->prepare("UPDATE registration SET is_verified = 1 WHERE username = ?");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            echo "Email verified successfully! You can now log in.";
            // Optionally, you can redirect to the login page
            header("Location: login.html");
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "<div style='color:red; font-size:20px;'>Invalid verification code.</div><div style='font-size:20px;'> Please try again.";

        echo '<p id="countdown">Redirecting in 5 seconds...</p>';
        // Redirect to verify_email.html after 5 seconds
        echo '<script>
             let seconds = 5;
             setInterval(function() {
                 document.getElementById("countdown").innerHTML = "Redirecting in " + seconds + " seconds...";
                 seconds--;
                 if (seconds < 0) {
                     window.location.href = "verify_email.html";
                 }
             }, 1000);
           </script>';
    }

    $conn->close();
} else {
    echo "Error: Verification code not set.";
}
?>