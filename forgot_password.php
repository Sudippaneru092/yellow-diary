<?php include "header.php"; ?>
<div class="container mt-5">
  <h2>Forgot Password</h2>
  <form action="send_reset_link.php" method="post">
    <label>Business Email:</label>
    <input type="email" name="email" required class="form-control mb-2">
    <button type="submit" class="btn btn-warning">Send Reset Link</button>
  </form>
</div>
<?php include "footer.php"; ?>
