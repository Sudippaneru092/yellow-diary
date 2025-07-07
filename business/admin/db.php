<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'yellow_diarys';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set MySQL timezone to UTC (recommended)
$conn->query("SET time_zone = '+00:00'");

// Also, set PHP timezone to UTC for consistency (optional here, but good to do)
date_default_timezone_set('UTC');

?>
