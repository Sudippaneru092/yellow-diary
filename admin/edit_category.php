<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: manage_categories.php');
    exit;
}

$id = intval($_GET['id']);
$errors = [];

// Fetch category
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

if (!$category) {
    $_SESSION['error'] = "Category not found.";
    header('Location: manage_categories.php');
    exit;
}

$name = $category['name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    if ($name == '') {
        $errors[] = "Category name cannot be empty.";
    } else {
        // Check duplicate except current
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Category name already exists.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Category updated successfully.";
            header("Location: manage_categories.php");
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
    <h1>Edit Category</h1>
    <a href="manage_categories.php" class="btn btn-secondary mb-3">Back to Manage Categories</a>
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
      <div class="form-group">
        <label for="name">Category Name</label>
        <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
      </div>
      <button type="submit" class="btn btn-primary">Update Category</button>
    </form>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
