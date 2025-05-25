<?php
/* File Name: admin_login.php
    Authors : Sudip Paneru
    Date Of Creation: 2025/5/20
    Description : A program for the admin to login to dashboard
*/
try {
session_start();
require 'database_connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Get admin from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Verify password
       if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['email'];   // email stored for admin check
            $_SESSION['user_id'] = $admin['id'];    // add user ID to session
            $_SESSION['login_time'] = time();       // track login time

            header("Location: http://localhost/project/yellow-diary/admin/dashboard.php");
            exit();
        }
        else {
            echo "Incorrect password.";
        }
    } else {
        echo "Admin not found.";
    }

    $stmt->close();
}
} catch (Exception $e) {
    echo "". $e->getMessage();
    $conn->close();
}
?>
