<?php
/*
 * File Name: admin_signup.php
 * Authors: Sudip Paneru
 * Date Of Creation: 2025/5/20
 * Description: A program to register an admin by inserting data into the database.
 */
try {
// Include the database connection
require 'database_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize inputs
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Basic validation
    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "Please fill in all fields.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    if ($password !== $confirmPassword) {
        echo "Passwords do not match.";
        exit;
    }
    // Check for existing email
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "Email is already registered.";
        exit;
    }
    $checkStmt->close();

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL insert
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, user_type) VALUES (?, ?, ?, 'admin')");
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("sss", $fullName, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "Admin registered successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
}catch(Exception $e) {
    $conn->close();
}
?>
