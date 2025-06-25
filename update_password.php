<?php
include "admin/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $result = mysqli_query($conn, "SELECT * FROM businesses WHERE reset_token='$token' AND token_expiry > NOW()");

    if (mysqli_num_rows($result) > 0) {
        mysqli_query($conn, "UPDATE businesses SET password='$new_password', reset_token=NULL, token_expiry=NULL WHERE reset_token='$token'");
        echo "Password has been reset successfully. You can now log in.";
    } else {
        echo "Invalid or expired token.";
    }
}
?>
