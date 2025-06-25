<?php
include "admin/db.php";
$token = $_GET['token'];

$result = mysqli_query($conn, "SELECT * FROM businesses WHERE reset_token='$token' AND token_expiry > NOW()");

if (mysqli_num_rows($result) > 0):
?>
<form action="update_password.php" method="post">
  <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
  <label>New Password:</label>
  <input type="password" name="new_password" required>
  <button type="submit">Reset Password</button>
</form>
<?php else: ?>
<p>Token is invalid or has expired. Please request a new one.</p>
<?php endif; ?>
