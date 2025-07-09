<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['business_id'])) {
    header("Location: login.php");
    exit();
}

$business_id = $_SESSION['business_id'];

// Handle deletion if requested via ?delete=ID
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    // Ensure post belongs to logged-in business
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND business_id = ?");
    $stmt->bind_param("ii", $delete_id, $business_id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $message = "✅ Post deleted successfully.";
    } else {
        $message = "❌ Failed to delete post or unauthorized access.";
    }
}

// Fetch all posts for this business
$stmt = $conn->prepare("SELECT * FROM posts WHERE business_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-3">My Posts</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($post = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($post['title']) ?></td>
                        <td>
                            <?php if ($post['status'] === 'published'): ?>
                                <span class="badge bg-success">Published</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y H:i', strtotime($post['created_at'])) ?></td>
                        <td>
                            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="my_posts.php?delete=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">You haven't added any posts yet.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
