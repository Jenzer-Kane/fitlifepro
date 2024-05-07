<?php
if (isset($_POST['username'])) {
    $username = $_POST['username'];
    
    $conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');
    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM registration WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Send response based on availability
    if ($row['count'] > 0) {
        echo "<span style='color:red;'>Username not available.</span>";
    } else {
        echo "<span style='color:green;'>Username available.</span>";
    }

    $stmt->close();
    $conn->close();
}
?>
