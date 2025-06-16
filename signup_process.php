<?php
require 'admin/db.php';
// DB connection (adjust these to match your setup)
$conn = new mysqli("localhost", "root", "", "yellow_diarys");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$fullName = $_POST['fullName'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];
$userType = $_POST['userType'];

// Check if passwords match
if ($password !== $confirmPassword) {
    die("Passwords do not match.");
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    die("Email already registered.");
}
$stmt->close();

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Handle optional business data
$businessName = $businessDescription = $businessLocation = $businessCategory = $businessLogoPath = null;

if ($userType === "business") {
    $businessName = $_POST['businessName'];
    $businessDescription = $_POST['businessDescription'];
    $businessLocation = $_POST['businessLocation'];
    $businessCategory = $_POST['businessCategory'];

    // Handle file upload
    if (isset($_FILES['businessLogo']) && $_FILES['businessLogo']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES["businessLogo"]["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowed)) {
            if (move_uploaded_file($_FILES["businessLogo"]["tmp_name"], $targetFile)) {
                $businessLogoPath = $targetFile;
            } else {
                die("Error uploading file.");
            }
        } else {
            die("Invalid file type.");
        }
    }
}

// Insert user into database
$stmt = $conn->prepare("INSERT INTO users (full_name, email, password, user_type, business_name, business_description, business_location, business_category, business_logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $fullName, $email, $hashedPassword, $userType, $businessName, $businessDescription, $businessLocation, $businessCategory, $businessLogoPath);

if ($stmt->execute()) {
    echo "Signup successful!";
    // Redirect or display message
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
