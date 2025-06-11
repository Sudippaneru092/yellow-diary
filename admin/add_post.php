<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

$errors = [];
$title = '';
$category_id = '';
$content = '';
$status = 'draft';

// Fetch categories for dropdown
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $category_id = intval($_POST['category_id']);
    $content = trim($_POST['content']);
    $status = in_array($_POST['status'], ['draft', 'published']) ? $_POST['status'] : 'draft';

    if ($title == '') $errors[] = "Title is required.";
    if ($category_id == 0) $errors[] = "Category is required.";
    if ($content == '') $errors[] = "Content cannot be empty.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO posts (title, category_id, content, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $title, $category_id, $content, $status);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Post added successfully.";
            header("Location: manage_posts.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Add New Post</h1>
    <a href="manage_posts.php" class="btn btn-secondary mb-3">Back to Manage Posts</a>
  </section>

  <section class="content">
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" class="form">
      <div class="form-group mb-3">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
      </div>

      <div class="form-group mb-3">
        <label for="category_id">Category</label>
        <select name="category_id" id="category_id" class="form-control" required>
          <option value="">-- Select Category --</option>
          <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $category_id) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group mb-3">
        <label for="content">Content</label>
        <textarea name="content" id="content" rows="6" class="form-control" required><?= htmlspecialchars($content) ?></textarea>
      </div>

      <div class="form-group mb-3">
        <label for="status">Status</label>
        <select name="status" id="status" class="form-control">
          <option value="draft" <?= ($status == 'draft') ? 'selected' : '' ?>>Draft</option>
          <option value="published" <?= ($status == 'published') ? 'selected' : '' ?>>Published</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Add Post</button>
    </form>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
