<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_categories.php");
    exit;
}

$category_id = intval($_GET['id']);

// Fetch existing data
$stmt = $conn->prepare("SELECT * FROM category WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

if (!$category) {
    header("Location: manage_categories.php");
    exit;
}

// Handle Update
if (isset($_POST['update_category'])) {
    $name = trim($_POST['category_name']);
    $description = trim($_POST['description']);

    if ($name == '') {
        $error = "Category name cannot be empty.";
    } else {
        // Check if name exists (except for current record)
        $stmt = $conn->prepare("SELECT id FROM category WHERE name = ? AND id != ?");
        $stmt->bind_param("si", $name, $category_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Another category with this name already exists.";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("UPDATE category SET name = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $description, $category_id);
            if ($stmt->execute()) {
                $success = "Category updated successfully.";
                // Refresh data
                $category['name'] = $name;
                $category['description'] = $description;
            } else {
                $error = "Update failed.";
            }
        }
        $stmt->close();
    }
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Category</h1>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Update Category</h3>
                </div>
                <form method="POST">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" name="category_name" class="form-control" required value="<?= htmlspecialchars($category['name']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Description (optional)</label>
                            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($category['description']) ?></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" name="update_category" class="btn btn-warning">Update Category</button>
                        <a href="manage_categories.php" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
