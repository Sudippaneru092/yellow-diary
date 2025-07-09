<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['business_id'])) {
    header("Location: login.php");
    exit();
}

$business_id = $_SESSION['business_id'];
$message = "";

// Get post ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid post ID.";
    exit();
}
$post_id = intval($_GET['id']);

// Fetch post to verify ownership
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND business_id = ?");
$stmt->bind_param("ii", $post_id, $business_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Post not found or access denied.";
    exit();
}
$post = $result->fetch_assoc();

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $tags = trim($_POST['tags']);
    $status = trim($_POST['status']);

    $stmt = $conn->prepare("UPDATE posts SET title=?, content=?, tags=?, status=? WHERE id=? AND business_id=?");
    $stmt->bind_param("ssssii", $title, $content, $tags, $status, $post_id, $business_id);

    if ($stmt->execute()) {
        $message = "Post updated successfully.";
        // Refresh the post data
        $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND business_id = ?");
        $stmt->bind_param("ii", $post_id, $business_id);
        $stmt->execute();
        $post = $stmt->get_result()->fetch_assoc();
    } else {
        $message = "Failed to update post.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Edit Post</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-12">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($post['title']) ?>">
        </div>

        <div class="col-md-12">
            <label class="form-label">Content</label>
            <textarea name="content" rows="6" class="form-control" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Tags (comma-separated)</label>
            <input type="text" name="tags" class="form-control" value="<?= htmlspecialchars($post['tags']) ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Update Post</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
