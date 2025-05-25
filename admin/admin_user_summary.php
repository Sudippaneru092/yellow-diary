<?php
require_once 'database_connect.php';

try {
    $sql = "
        SELECT 
            COUNT(*) AS total_users,
            SUM(user_type = 'admin') AS total_admins,
            SUM(user_type = 'general') AS total_generals,
            SUM(user_type = 'guest') AS total_guests,
            AVG(total_time_spent) AS avg_time_spent
        FROM users
    ";

    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("SQL error: " . $conn->error);
    }

    $row = $result->fetch_assoc();
    if ($row) {
        $total_users = $row['total_users'];
        $total_admins = $row['total_admins'];
        $total_generals = $row['total_generals'];
        $total_guests = $row['total_guests'];
        $avg_time_spent = round($row['avg_time_spent'] / 120, 5); // hours
    } else {
        throw new Exception("Failed to fetch result row.");
    }

} catch (Exception $e) {
    $message = date('Y-m-d H:i:s') . " - " . $e->getMessage() . PHP_EOL;
    error_log($message);

    $logFile = __DIR__ . "/logs/dashboard_errors.log";
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    file_put_contents($logFile, $message, FILE_APPEND);

    $total_users = $total_admins = $total_generals = $total_guests = 0;
    $avg_time_spent = 0;
    $conn->close();
}
?>
