<?php
session_start();
include '../admin/db.php';
if (!isset($_SESSION['business_id'])) {
    header("Location: ../business_login.php");
    exit();
}

$business_id = $_SESSION['business_id'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];

    $business = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM businesses WHERE id = $business_id"));
    if ($business['password'] === $old) {
        mysqli_query($conn, "UPDATE businesses SET password = '$new' WHERE id = $business_id");
        $message = "Password changed!";
    } else {
        $message = "Old password is incorrect!";
    }
}
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header"><h1>Change Password</h1></section>
  <section class="content container-fluid">
    <?php if ($message): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="password" name="old_password" class="form-control" placeholder="Old Password" required><br>
      <input type="password" name="new_password" class="form-control" placeholder="New Password" required><br>
      <button type="submit" class="btn btn-warning">Change Password</button>
    </form>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
