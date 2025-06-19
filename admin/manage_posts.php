<?php
// MUST BE AT THE VERY TOP - NO WHITESPACE BEFORE
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

// Handle status update (Approve/Reject)
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if (in_array($action, ['approve', 'reject'])) {
        $newStatus = $action === 'approve' ? 'approved' : 'rejected';
        $stmt = $conn->prepare("UPDATE posts SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $newStatus, $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Post has been $newStatus.";
        header('Location: manage_posts.php');
        exit;
    }
}

// Fetch all posts with business info
$sql = "SELECT p.*, b.name AS business_name FROM posts p LEFT JOIN businesses b ON p.business_id = b.id ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Posts - Yellow Diary</title>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css" />
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <?php 
  // Include header and sidebar AFTER the session check
  include 'includes/header.php';
  include 'includes/sidebar.php'; 
  ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Manage Posts</h1>
    </section>

    <section class="content">
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
      <?php endif; ?>

      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Business</th>
            <th>Category</th>
            <th>Location</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($post = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $post['id'] ?></td>
              <td><?= htmlspecialchars($post['title']) ?></td>
              <td><?= htmlspecialchars($post['business_name'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($post['category']) ?></td>
              <td><?= htmlspecialchars($post['location']) ?></td>
              <td>
                <?php
                  $statusClass = 'badge-secondary';
                  if ($post['status'] === 'approved') $statusClass = 'badge-success';
                  elseif ($post['status'] === 'rejected') $statusClass = 'badge-danger';
                  elseif ($post['status'] === 'pending') $statusClass = 'badge-warning';
                ?>
                <span class="badge <?= $statusClass ?>"><?= ucfirst($post['status']) ?></span>
              </td>
              <td><?= $post['created_at'] ?></td>
              <td>
                <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-primary btn-sm">Edit</a>

                <?php if ($post['status'] === 'pending'): ?>
                  <a href="manage_posts.php?action=approve&id=<?= $post['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this post?')">Approve</a>
                  <a href="manage_posts.php?action=reject&id=<?= $post['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject this post?')">Reject</a>
                <?php endif; ?>

                <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this post?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>

</div>
<!-- ./wrapper -->

<!-- AdminLTE JS -->
<script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>