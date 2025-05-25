<?php
    session_start();
    require 'database_connect.php';  // make sure $conn is defined

    if (isset($_SESSION['user_id']) && isset($_SESSION['login_time'])) {
        $user_id = $_SESSION['user_id'];
        $login_time = $_SESSION['login_time'];
        $logout_time = time();

        $session_duration = $logout_time - $login_time; // seconds

        // Update total_time_spent by adding session duration
        $stmt = $conn->prepare("UPDATE users SET total_time_spent = total_time_spent + ? WHERE id = ?");
        $stmt->bind_param("ii", $session_duration, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    session_unset();
    session_destroy();

    header("Location: http://localhost/project/yellow-diary/admin/admin.php");
    exit();
?>
