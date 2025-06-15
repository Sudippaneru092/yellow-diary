<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';
// Handle deletion

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM businesses WHERE id = $id");
    $_SESSION['success'] = "Business deleted successfully.";
    header("Location: manage_business.php");
    exit;
}


include 'includes/header.php';
include 'includes/sidebar.php';


?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage Businesses</h1>
    <a href="add_business.php" class="btn btn-primary mb-3">+ Add New Business</a>
  </section>

  <section class="content">
    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-dark">
            <tr>
              <th>#</th>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Location</th>
              <th>Status</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $result = $conn->query("SELECT * FROM businesses ORDER BY id DESC");
            if ($result->num_rows > 0):
              while ($row = $result->fetch_assoc()):
            ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td>
                  <?php if (!empty($row['image'])): ?>
                    <img src="<?= $row['image'] ?>" width="60" height="40">
                  <?php else: ?>
                    N/A
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td>
                  <span class="badge badge-<?= $row['status'] == 'approved' ? 'success' : 'secondary' ?>">
                    <?= ucfirst($row['status']) ?>
                  </span>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td>
                  <a href="edit_business.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                  <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                     onclick="return confirm('Are you sure you want to delete this business?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="8">No businesses found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
