<?php
include "admin/db.php";
include "mail_config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $result = mysqli_query($conn, "SELECT * FROM businesses WHERE email='$email'");

    if (mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        mysqli_query($conn, "UPDATE businesses SET reset_token='$token', token_expiry='$expiry' WHERE email='$email'");
        $reset_link = "http://yourdomain.com/reset_password.php?token=$token"; // ⚠️ change domain

        if (sendResetEmail($email, $reset_link)) {
            echo "Password reset link has been sent to your business email.";
        } else {
            echo "Email sending failed. Please try again.";
        }
    } else {
        echo "Email not found.";
    }
}
?>
