<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'admin/includes/database_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $userType = $_POST['userType'] ?? '';

    if (empty($email) || empty($password) || empty($userType)) {
        exit("All fields are required.");
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND user_type = ?");
        $stmt->execute([$email, $userType]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_type'] = $user['user_type'];

            echo "Login successful!";

            // Optional: redirect based on user type
            /*
            switch ($user['user_type']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'business':
                    header("Location: business_dashboard.php");
                    break;
                case 'general':
                    header("Location: index.php");
                    break;
            }
            exit;
            */
        } else {
            echo "Invalid email, password, or user type.";
        }

    } catch (PDOException $e) {
        echo "Login failed: " . $e->getMessage();
    }

} else {
    echo "Invalid request method.";
}
?>
