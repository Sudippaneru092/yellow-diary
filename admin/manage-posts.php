<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

$result = $conn->query("SELECT * FROM posts ORDER BY id DESC");
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage Posts</h1>
  </section>

  <section class="content">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Category</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td>
              <a href="/yellow_diary_admin_full/admin/edit-post.php?id=<?php echo $row['id']; ?>">Edit</a> |
              <a href="/yellow_diary_admin_full/admin/delete-post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this post?');">Delete</a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
