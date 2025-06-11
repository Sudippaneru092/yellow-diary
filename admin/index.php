<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

// Get counts for dashboard cards
$businessCount = $conn->query("SELECT COUNT(*) AS total FROM businesses")->fetch_assoc()['total'];
$postCount = $conn->query("SELECT COUNT(*) AS total FROM posts")->fetch_assoc()['total'];
$adminCount = $conn->query("SELECT COUNT(*) AS total FROM admins")->fetch_assoc()['total'];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <h1>Dashboard</h1>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Businesses box -->
        <div class="col-lg-4 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?= $businessCount ?></h3>
              <p>Businesses</p>
            </div>
            <div class="icon">
              <i class="fas fa-building"></i>
            </div>
          </div>
        </div>

        <!-- Posts box -->
        <div class="col-lg-4 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?= $postCount ?></h3>
              <p>Posts</p>
            </div>
            <div class="icon">
              <i class="fas fa-newspaper"></i>
            </div>
          </div>
        </div>

        <!-- Admins box -->
        <div class="col-lg-4 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?= $adminCount ?></h3>
              <p>Admins</p>
            </div>
            <div class="icon">
              <i class="fas fa-users-cog"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
