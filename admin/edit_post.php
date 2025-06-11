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
  $id = $_POST['id'];

  $update = $conn->prepare("UPDATE posts SET title=?, category=?, description=? WHERE id=?");

  if (!$update) {
      die("Prepare failed: " . $conn->error);
  }

  $update->bind_param("sssi", $title, $category, $content, $id);

  $update->execute();
  if ($update->error) {
      echo "No rows updated. Try changing the content to something new.";
  }else{
    header("Location: manage_posts.php");
      exit;
  }

 
  $update->close();

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
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
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
        <textarea name="content" class="form-control" rows="5"><?php echo htmlspecialchars($post['description']); ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
      <a href="manage_posts.php" class="btn btn-secondary">Cancel</a>
    </form>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
