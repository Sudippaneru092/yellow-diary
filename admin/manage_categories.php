<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

// Handle Add Category
if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if ($category_name == '') {
        $error = "Category name cannot be empty.";
    } else {
        // Check if category already exists
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Category already exists.";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $category_name);
            if ($stmt->execute()) {
                $success = "Category added successfully.";
            } else {
                $error = "Database error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_categories.php?deleted=1');
    exit;
}

// Fetch categories
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage Categories</h1>
  </section>

  <section class="content">
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="alert alert-success">Category deleted successfully.</div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
      <div class="input-group">
        <input type="text" name="category_name" class="form-control" placeholder="New category name" required>
        <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
      </div>
    </form>

    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Category Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td>
                <a href="edit_category.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="manage_categories.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this category?');">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="3" class="text-center">No categories found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
