<?php
session_start();



// DB connection
$conn = new mysqli("localhost", "root", "", "yellow_diary");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect input
$email = $_POST['email'];
$password = $_POST['password'];
$userType = $_POST['userType'];

// Get user from DB
$stmt = $conn->prepare("SELECT * from users id, full_name, password, user_type FROM users WHERE email = ? ");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($user['user_type'] !== $userType) {
        die("Incorrect account type.");
    }

    if (password_verify($password, $user['password'])) {
        // Login success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_type'] = $user['user_type'];

        echo "Login successful!";
        // Redirect by role
        // header("Location: dashboard.php");
    } else {
        echo "Invalid password.";
    }
} else {
    echo "User not found.";
}
$stmt->close();
$conn->close();
?>
