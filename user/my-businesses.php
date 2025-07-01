<?php
session_start();
include "../admin/db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

$userId = $_SESSION['user_id'];
$query = "SELECT * FROM businesses WHERE user_id = $userId ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<?php include "includes/user_header.php"; ?>
<?php include "includes/user_sidebar.php"; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <h1>My Business Listings</h1>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <!-- Table card -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">All Businesses You Added</h3>
          <div class="card-tools">
            <a href="add-business.php" class="btn btn-primary btn-sm">
              <i class="fas fa-plus-circle"></i> Add New Business
            </a>
          </div>
        </div>
        
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Category</th>
                <th>City</th>
                <th>Status</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($result) > 0): ?>
                <?php $sn = 1; while($row = mysqli_fetch_assoc($result)): ?>
                  <tr>
                    <td><?php echo $sn++; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['city']); ?></td>
                    <td>
                      <span class="badge 
                        <?php echo ($row['status'] === 'approved') ? 'badge-success' : 
                                    (($row['status'] === 'pending') ? 'badge-warning' : 'badge-secondary'); ?>">
                        <?php echo ucfirst($row['status']); ?>
                      </span>
                    </td>
                    <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">No businesses found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>
</div>

<?php include "includes/user_footer.php"; ?>
