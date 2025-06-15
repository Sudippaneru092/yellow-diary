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
<div class="content-wrapper" style="background-color: #f9f9f9;">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <h1 class="text-dark font-weight-bold">Yellow Diary Admin Dashboard</h1>
      <p class="text-muted">Manage your directory - businesses, posts, and admins</p>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Businesses box -->
        <div class="col-lg-4 col-6">
          <div class="small-box" style="background-color: #FFC107; color: #212529; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <div class="inner">
              <h3><?= $businessCount ?></h3>
              <p>Registered Businesses</p>
            </div>
            <div class="icon">
              <i class="fas fa-store"></i>
            </div>
          </div>
        </div>

        <!-- Posts box -->
        <div class="col-lg-4 col-6">
          <div class="small-box" style="background-color: #17A2B8; color: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <div class="inner">
              <h3><?= $postCount ?></h3>
              <p>Total Posts</p>
            </div>
            <div class="icon">
              <i class="fas fa-bullhorn"></i>
            </div>
          </div>
        </div>

        <!-- Admins box -->
        <div class="col-lg-4 col-6">
          <div class="small-box" style="background-color: #6C757D; color: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <div class="inner">
              <h3><?= $adminCount ?></h3>
              <p>Active Admins</p>
            </div>
            <div class="icon">
              <i class="fas fa-user-shield"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Optional: Summary Section -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card" style="border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.05);">
            <div class="card-body">
              <h5 class="card-title text-dark">Welcome to Yellow Diary Admin Panel</h5>
              <p class="card-text text-muted">Here you can manage all the business listings, community posts, and administrator accounts.</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
