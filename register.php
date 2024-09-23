<?php
session_start();

// Check if the necessary POST data is set
if (
    isset($_POST['username']) &&
    isset($_POST['firstname']) &&
    isset($_POST['lastname']) &&
    isset($_POST['gender']) &&
    isset($_POST['email']) &&
    isset($_POST['password'])
) {
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Password validation rules
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    }
    if (!preg_match('/[\W]/', $password)) {
        $errors[] = "Password must contain at least one special character.";
    }

    // If there are any errors, display them
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<span style='color:red;'>$error</span><br>";
        }
        // Redirect to registration page after displaying errors
        echo '<html>
                <head>
                    <title>Redirecting...</title>
                    <script>
                        var countdown = 5;
                        function updateCountdown() {
                            document.getElementById("countdown").innerHTML = countdown;
                            countdown--;
                            if (countdown < 0) {
                                window.location.href = "register.html";
                            } else {
                                setTimeout(updateCountdown, 1000);
                            }
                        }
                        setTimeout(updateCountdown, 1000);
                    </script>
                </head>
                <body>
                    <p>Redirecting in <span id="countdown">5</span> seconds...</p>
                </body>
            </html>';
        exit(); // Stop further execution
    }

    // Generate a verification code
    $verificationCode = substr(md5(uniqid(mt_rand(), true)), 0, 6);

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');
    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("INSERT INTO registration(username, firstname, lastname, gender, email, password, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("sssssss", $username, $firstname, $lastname, $gender, $email, $hashedPassword, $verificationCode);

        // Execute the prepared statement
        $executionResult = $stmt->execute();

        if ($executionResult) {
            // Store necessary information in session
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;

            // Redirect the user to the verification page
            header("Location: verify_email.php");
            exit();
        } else {
            // Check for duplicate entry error
            if ($conn->errno == 1062) {
                echo "<span style='color:red; font-size:20px;'>Username or Email already exists.</span><span style='font-size:20px;'> Please choose a different one.";
                echo '<html>
                    <head>
                        <title>Redirecting...</title>
                        <script>
                            var countdown = 5;
                            function updateCountdown() {
                                document.getElementById("countdown").innerHTML = countdown;
                                countdown--;
                                if (countdown < 0) {
                                    window.location.href = "register.html";
                                } else {
                                    setTimeout(updateCountdown, 1000);
                                }
                            }
                            setTimeout(updateCountdown, 1000);
                        </script>
                    </head>
                    <body>
                        <p>Redirecting in <span id="countdown">5</span> seconds...</p>
                    </body>
                </html>';
            } else {
                // Handle other errors
                echo "Error: " . $conn->error;
            }
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    }
} else {
    // Handle the case where POST data is not set
    echo "Error: Incomplete form submission.";
}
?>