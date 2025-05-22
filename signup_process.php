<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
require_once 'admin/database_connect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $fullName = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $userType = $_POST['userType'] ?? '';

    // Validate basic inputs
    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword) || empty($userType)) {
        exit("All required fields must be filled.");
    }

    if ($password !== $confirmPassword) {
        exit("Passwords do not match.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Start DB transaction
        $pdo->beginTransaction();

        // Insert into users table
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, user_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $hashedPassword, $userType]);

        // Get the inserted user ID
        $userId = $pdo->lastInsertId();

        // If business user, insert into business_profiles table
        if ($userType === 'business') {
            $businessName = $_POST['businessName'] ?? '';
            $businessDescription = $_POST['businessDescription'] ?? '';
            $businessLocation = $_POST['businessLocation'] ?? '';
            $businessCategory = $_POST['businessCategory'] ?? '';

            // Handle file upload
            $logoFileName = null;
            if (!empty($_FILES['businessLogo']['name']) && $_FILES['businessLogo']['error'] === 0) {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $logoFileName = $uploadDir . time() . '_' . basename($_FILES['businessLogo']['name']);
                move_uploaded_file($_FILES['businessLogo']['tmp_name'], $logoFileName);
            }

            $stmt = $pdo->prepare("INSERT INTO business_profiles (user_id, business_name, business_description, business_location, business_category, business_logo) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $businessName, $businessDescription, $businessLocation, $businessCategory, $logoFileName]);
        }

        // Commit transaction
        $pdo->commit();
        echo "Signup successful!";

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Signup failed: " . $e->getMessage();
    }

} else {
    echo "Invalid request method.";
}
?>
