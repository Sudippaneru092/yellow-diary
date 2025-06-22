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

// Predefined categories
$predefinedCategories = ['Event', 'Job Vacancy', 'Announcement', 'Advertising'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $category = $_POST['category'];
  $custom_category = $_POST['custom_category'] ?? '';
  $final_category = ($category === 'Others' && !empty($custom_category)) ? $custom_category : $category;

  $content = $_POST['content'];
  $id = $_POST['id'];

  $update = $conn->prepare("UPDATE posts SET title=?, category=?, description=? WHERE id=?");

  if (!$update) {
    die("Prepare failed: " . $conn->error);
  }

  $update->bind_param("sssi", $title, $final_category, $content, $id);
  $update->execute();

  if ($update->error) {
    echo "No rows updated. Try changing the content to something new.";
  } else {
    header("Location: manage_posts.php");
    exit;
  }

  $update->close();
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header"><h1>Edit Post</h1></section>
  <section class="content">
    <form method="POST">
      <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">

      <div class="form-group">
        <label>Title</label>
        <input name="title" class="form-control" value="<?= htmlspecialchars($post['title']); ?>" required>
      </div>

      <div class="form-group">
        <label>Category</label>
        <select name="category" id="category" class="form-control" onchange="toggleCustomCategory(this.value)">
          <?php
            $isCustom = !in_array($post['category'], $predefinedCategories);
            foreach ($predefinedCategories as $cat) {
              $selected = ($post['category'] === $cat) ? 'selected' : '';
              echo "<option value='$cat' $selected>$cat</option>";
            }
          ?>
          <option value="Others" <?= $isCustom ? 'selected' : '' ?>>Others</option>
        </select>
      </div>

      <div class="form-group" id="custom-category-group" style="display: <?= $isCustom ? 'block' : 'none' ?>;">
        <label>Custom Category</label>
        <input type="text" name="custom_category" id="custom_category" class="form-control"
               value="<?= $isCustom ? htmlspecialchars($post['category']) : '' ?>">
      </div>

      <div class="form-group">
        <label>Content</label>
        <textarea name="content" class="form-control" rows="5"><?= htmlspecialchars($post['description']); ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Update</button>
      <a href="manage_posts.php" class="btn btn-secondary">Cancel</a>
    </form>
  </section>
</div>

<script>
  function toggleCustomCategory(value) {
    const customInput = document.getElementById('custom-category-group');
    if (value === 'Others') {
      customInput.style.display = 'block';
    } else {
      customInput.style.display = 'none';
      document.getElementById('custom_category').value = '';
    }
  }
</script>

<?php include 'includes/footer.php'; ?>
