<?php

$servername = "localhost"; // Usually 'localhost' if you are running locally
$username = "root";
$password = "";
$dbname = "fitlifepro_register";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

