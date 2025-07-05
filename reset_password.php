<?php
include "admin/db.php";
session_start();

// Check if token is present in the URL
if (!isset($_GET['token'])) {
    echo "<p style='color: red;'>❌ Invalid request. No token provided.</p>";
    exit;
}

$token = $_GET['token'];

// Validate token and ensure it's not expired
$stmt = $conn->prepare("SELECT * FROM businesses WHERE reset_token = ? AND token_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 400px; margin: auto; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; }
        button { padding: 10px 20px; }
    </style>
</head>
<body>

<?php if ($result && $result->num_rows > 0): ?>
    <h2>Reset Your Password</h2>
    <form action="update_password.php" method="post">
        <!-- Token passed as hidden field -->
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label>New Password:</label>
        <input type="password" name="new_password" required>

        <label>Confirm Password:</label>
        <input type="password" name="cpass" required>

        <button type="submit">Reset Password</button>
    </form>
<?php else: ?>
    <p style="color: red;">❌ This reset link is invalid or has expired. Please <a href="forgot_password.php">request a new one</a>.</p>
<?php endif; ?>

</body>
</html>
