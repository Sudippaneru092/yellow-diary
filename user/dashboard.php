<?php
session_start();
include "../admin/db.php";
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}
$userId = $_SESSION['user_id'];
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id = $userId");
$user = mysqli_fetch_assoc($userQuery);
?>
<?php include "includes/user_header.php"; ?>
<?php include "includes/user_sidebar.php"; ?>

<div class="content-wrapper">
  <!-- Page header -->
  <section class="content-header">
    <div class="container-fluid">
      <h1>User Dashboard</h1>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
        <div class="card-body">
          <h3>Welcome, <?php echo htmlspecialchars($user['name']); ?></h3>
          <p>You can manage your business listings, profile, and settings here.</p>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include "includes/user_footer.php"; ?>
