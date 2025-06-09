<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

$result = $conn->query("SELECT * FROM businesses ORDER BY id DESC");
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage Businesses</h1>
  </section>
  <section class="content">
    <a href="add-business.php" class="btn btn-primary mb-3">Add New Business</a>
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Category</th><th>Location</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td>
                <a href="/yellow_diary_admin_full/admin/edit-business.php?id=<?php echo $row['id']; ?>">Edit</a>
                  <a href="/yellow_diary_admin_full/admin/delete-business.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this business?');">Delete</a>

            </td>

          </tr>
        <?php } ?>
      </tbody>
    </table>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
