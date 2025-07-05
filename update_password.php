<?php
include "admin/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (
        empty($_POST['token']) ||
        empty($_POST['new_password']) ||
        empty($_POST['cpass'])
    ) {
        echo "<p style='color:red;'>❌ All fields are required.</p>";
        exit;
    }

    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['cpass'];

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        echo "<p style='color:red;'>❌ Passwords do not match.</p>";
        exit;
    }

    // Optional: Check password length
    if (strlen($new_password) < 6) {
        echo "<p style='color:red;'>❌ Password must be at least 6 characters long.</p>";
        exit;
    }

    // Hash the new password securely
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Verify the token is valid and not expired
    $stmt = $conn->prepare("SELECT id FROM businesses WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $business_id = $row['id'];

        // Update password and clear the token
        $update = $conn->prepare("UPDATE businesses SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
        $update->bind_param("si", $hashed_password, $business_id);

        if ($update->execute()) {
            // Output success message and redirect
            echo "
            <html>
            <head>
                <title>Password Reset</title>
                <script>
                    setTimeout(function() {
                        window.location.href = 'business_login.php';
                    }, 4000); // 4 seconds
                </script>
            </head>
            <body>
                <h2 style='color: green;'>✅ Password has been reset successfully. You can now log in.</h2>
                <p>Redirecting to login page in 4 seconds...</p>
            </body>
            </html>
            ";
        } else {
            echo "<p style='color:red;'>❌ Failed to update password. Please try again.</p>";
        }

    } else {
        echo "<p style='color:red;'>❌ Invalid or expired token.</p>";
    }
}
?>
