<?php
$host = "localhost";
$username = "root";
$password = ""; // Leave blank for default XAMPP
$database = "yellow_diarys";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
