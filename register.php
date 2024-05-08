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

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');
    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("INSERT INTO registration(username, firstname, lastname, gender, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $firstname, $lastname, $gender, $email, $hashedPassword);

        // Execute the prepared statement
        $executionResult = $stmt->execute();

        if ($executionResult) {
            echo "Registration Successful!";

            // Redirect after successful registration
            echo '<html>
                    <head>
                        <title>Redirecting...</title>
                        <script>
                            var countdown = 5;
                            function updateCountdown() {
                                document.getElementById("countdown").innerHTML = countdown;
                                countdown--;
                                if (countdown < 0) {
                                    window.location.href = "login.html";
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
            // Check for duplicate entry error
            if ($conn->errno == 1062) {
                echo "<span style='color:red;'>Username or Email already exists.</span> Please choose a different one.";
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