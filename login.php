<?php
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the email and password are set in the POST data
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if it's a superadmin login attempt
        if ($email === 'superadmin' && $password === '0000') {
            $_SESSION['admin'] = true;
            $_SESSION['superadmin'] = true; // Superadmin session variable
            header("Location: admin_dashboard.php");
            exit();
        }

        // Check if it's a regular admin login attempt
        if ($email === 'admin' && $password === '0000') {
            $_SESSION['admin'] = true;
            $_SESSION['superadmin'] = false; // Regular admin, not superadmin
            header("Location: admin_dashboard.php");
            exit();
        }

        // Database connection for regular user login
        $conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');
        if ($conn->connect_error) {
            die('Connection Failed: ' . $conn->connect_error);
        } else {
            // Retrieve user data from the database based on the entered email
            $stmt = $conn->prepare("SELECT username, password, is_verified FROM registration WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($username, $hashedPassword, $isVerified);
            $stmt->fetch();
            $stmt->close();

            if ($hashedPassword !== null) {
                // Verify the entered password against the hashed password
                if (password_verify($password, $hashedPassword)) {
                    if ($isVerified == 1) {
                        // Password is correct and email is verified
                        $_SESSION['email'] = $email; // Store email in the session for future use
                        $_SESSION['username'] = $username; // Store username in the session for future use
                        $_SESSION['admin'] = false;  // Not an admin
                        $_SESSION['superadmin'] = false; // Not a superadmin
                        header("Location: verify_2fa.php"); // Redirect to the 2FA verification page
                        exit();
                    } else {
                        // Email not verified
                        echo '<div style="color:red; font-size:20px;">Your email is not verified. Please check your email for the verification code.</div>';
                        header("Location: verify_email.html");
                        exit();
                    }
                } else {
                    // Incorrect password
                    echo "<span style='color:red; font-size:20px;'>Incorrect password.</span><span style='font-size:20px;'> Please try again.";
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
                // Invalid email
                echo "<span style='color:red; font-size:20px;'>Invalid email.</span><span style='font-size:20px;'> Please try again.";
                echo '<html>
                    <head>
                        <title>Redirecting...</title>
                        <script>
                            var countdown = 3;
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
                        <p>Redirecting in <span id="countdown">3</span> seconds...</p>
                    </body>
                </html>';
                exit();
            }

            $conn->close();
        }
    } else {
        echo "Form data not received correctly.";
    }
}
?>