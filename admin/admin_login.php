<?php
// File Name: admin_login.php
// Author: Sudip Paneru
// Date: 2025/05/20
// Description: Admin login for dashboard

session_start();

try {
    require 'includes/database_connect.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND user_type = 'Admin' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {
                // Set session variables
                $_SESSION['admin'] = $admin['email'];
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['username'] = $admin['full_name'];
                $_SESSION['email'] = $admin['email'];
                $_SESSION['avatar'] = !empty($admin['avatar']) ? $admin['avatar'] : 'https://via.placeholder.com/80';
                $_SESSION['login_time'] = time();
                $_SESSION['user_type'] = $admin['user_type'];


                header("Location: /project/yellow-diary/admin/dashboard.php");
                exit();
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "Admin not found or not authorized.";
        }

        $stmt->close();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
