<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

include 'db.php';

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $category = $_POST['category'];
  $content = $_POST['content'];

  $update = $conn->prepare("UPDATE posts SET title=?, category=?, content=? WHERE id=?");
  $update->bind_param("sssi", $title, $category, $content, $id);
  $update->execute();

  header("Location: manage_posts.php");
  exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header"><h1>Edit Post</h1></section>
  <section class="content">
    <form method="POST">
      <div class="form-group">
        <label>Title</label>
        <input name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
      </div>
      <div class="form-group">
        <label>Category</label>
        <input name="category" class="form-control" value="<?php echo htmlspecialchars($post['category']); ?>" required>
      </div>
      <div class="form-group">
        <label>Content</label>
        <textarea name="content" class="form-control" rows="5"><?php echo htmlspecialchars($post['content']); ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
      <a href="manage_posts.php" class="btn btn-secondary">Cancel</a>
    </form>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
