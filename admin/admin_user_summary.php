<?php
// dashboard_users_summary.php

require 'database_connect.php';

// Make sure only admin can access this part
if (!isset($_SESSION['admin'])) {
    echo "Access denied.";
    exit();
}

// Query to get user counts by type
$sql = "
    SELECT 
        COUNT(*) AS total_users,
        SUM(user_type = 'admin') AS total_admins,
        SUM(user_type = 'general') AS total_generals,
        SUM(user_type = 'guest') AS total_guests
    FROM users
";

$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $total_users = $row['total_users'];
    $total_admins = $row['total_admins'];
    $total_generals = $row['total_generals'];
    $total_guests = $row['total_guests'];
} else {
    $total_users = $total_admins = $total_generals = $total_guests = 0;
}

$conn->close();
?>
