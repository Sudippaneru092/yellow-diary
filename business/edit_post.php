<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['business_id'])) {
    header("Location: business_login.php");
    exit();
}

$business_id = $_SESSION['business_id'];

$allowed_categories = ['Event', 'Job Vacancy', 'Announcement', 'Advertising', 'Others'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid post ID.";
    exit();
}

$post_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND business_id = ?");
$stmt->bind_param("ii", $post_id, $business_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Post not found or access denied.";
    exit();
}

$post = $result->fetch_assoc();

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);

    if (empty($title) || empty($category) || empty($location) || empty($description)) {
        $error = "Please fill in all required fields.";
    } elseif (!in_array($category, $allowed_categories)) {
        $error = "Invalid category selected.";
    } else {
        $image = $post['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = __DIR__ . "/../admin/uploads/posts/";
            $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($imageFileType, $allowed_types)) {
                $error = "Unsupported image format.";
            } else {
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check === false) {
                    $error = "Uploaded file is not a valid image.";
                } else {
                    $newImageName = uniqid('post_', true) . '.' . $imageFileType;
                    $target_file = $target_dir . $newImageName;

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        if (!empty($image) && file_exists($target_dir . $image)) {
                            unlink($target_dir . $image);
                        }
                        $image = $newImageName;
                    } else {
                        $error = "Failed to upload image.";
                    }
                }
            }
        }

        if (!$error) {
            // Update query without status
            $stmt = $conn->prepare("UPDATE posts SET title=?, category=?, location=?, description=?, image=? WHERE id=? AND business_id=?");
            $stmt->bind_param("sssssii", $title, $category, $location, $description, $image, $post_id, $business_id);

            if ($stmt->execute()) {
                $message = "Post updated successfully.";
                $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND business_id = ?");
                $stmt->bind_param("ii", $post_id, $business_id);
                $stmt->execute();
                $post = $stmt->get_result()->fetch_assoc();
            } else {
                $error = "Failed to update post. Please try again.";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4" style="max-width: 700px;">
    <h2>Edit Post</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-12">
            <label for="title" class="form-label">Title*</label>
            <input type="text" name="title" id="title" class="form-control" required value="<?= htmlspecialchars($post['title']) ?>">
        </div>

        <div class="col-12">
            <label for="category" class="form-label">Category*</label>
            <select name="category" id="category" class="form-select" required>
                <option value="">Select category</option>
                <?php foreach ($allowed_categories as $cat): ?>
                    <option value="<?= $cat ?>" <?= $post['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <label for="location" class="form-label">Location*</label>
            <input type="text" name="location" id="location" class="form-control" required value="<?= htmlspecialchars($post['location']) ?>">
        </div>

        <div class="col-12">
            <label for="description" class="form-label">Description*</label>
            <textarea name="description" id="description" rows="6" class="form-control" required><?= htmlspecialchars($post['description']) ?></textarea>
        </div>

        <div class="col-12">
            <label class="form-label">Current Image:</label><br>
            <?php if (!empty($post['image']) && file_exists(__DIR__ . '/../admin/uploads/posts/' . $post['image'])): ?>
                <img src="../admin/uploads/posts/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" style="max-width: 200px; margin-bottom: 10px;">
            <?php else: ?>
                <p><em>No image uploaded.</em></p>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <label for="image" class="form-label">Replace Image (Optional)</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            <small class="text-muted">Leave empty to keep current image.</small>
        </div>

        <div class="col-12 d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Update Post</button>
            <a href="manage_posts.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
