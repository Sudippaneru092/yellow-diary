<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM businesses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Redirect to avoid resubmission
    header("Location: manage_business.php?deleted=1");
    exit;
}

// Fetch businesses
$result = $conn->query("SELECT * FROM businesses ORDER BY created_at DESC");

?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage Businesses</h1>
    <a href="add_business.php" class="btn btn-primary mb-3">Add New Business</a>
  </section>

  <section class="content">
    <?php if (isset($_GET['deleted'])): ?>
      <div class="alert alert-success">Business deleted successfully.</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Category</th>
          <th>Location</th>
          <th>Description</th>
          <th>Image</th>
          <th>URL</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['category']) ?></td>
              <td><?= htmlspecialchars($row['location']) ?></td>
              <td><?= htmlspecialchars(substr($row['description'], 0, 50)) ?>...</td>
              <td>
                <?php if (!empty($row['image'])): ?>
                  <img src="<?= htmlspecialchars($row['image']) ?>" alt="Image" style="width: 80px; height:auto;" />
                <?php else: ?>
                  N/A
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($row['url'])): ?>
                  <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank">Link</a>
                <?php else: ?>
                  N/A
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['created_at']) ?></td>
              <td>
             <a href="edit_business.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>



                <a href="manage_business.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this business?');">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="9" class="text-center">No businesses found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
