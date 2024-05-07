<?php
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the username and password are set in the POST data
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check if it's an admin login attempt
        if ($username === '0000' && $password === 'admin') {
            $_SESSION['admin'] = true;
            header("Location: admin_dashboard.php");
            exit();
        }

        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');
        if ($conn->connect_error) {
            die('Connection Failed: ' . $conn->connect_error);
        } else {
            // Retrieve hashed password from the database based on the entered username
            $stmt = $conn->prepare("SELECT password FROM registration WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();
            $stmt->close();

            if ($hashedPassword !== null) {
                // Verify the entered password against the hashed password
                if (password_verify($password, $hashedPassword)) {
                    // Password is correct
                    $_SESSION['username'] = $username; // Store username in the session for future use
                    header("Location: index.php"); // Redirect to the index page
                    exit();
                } else {
                    // Incorrect password
                    echo "<span style='color:red;'>Incorrect password.</span> Please try again.";
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
                    exit();
                }
            } else {
                // Invalid username
                echo "<span style='color:red;'>Invalid username.</span> Please try again.";
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
                exit();
            }

            // Handle other error scenarios
            echo "<span style='color:red;'>An unexpected error occurred.</span> Please try again.";
            exit();

            $conn->close();
        }
    } else {
        echo "Form data not received correctly.";
    }
}
?>