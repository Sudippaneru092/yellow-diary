<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['business_id'])) {
    header("Location: login.php");
    exit();
}

$business_id = $_SESSION['business_id'];
$message = "";

// Handle deletion (GET-based for now)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND business_id = ?");
    $stmt->bind_param("ii", $delete_id, $business_id);
    $stmt->execute();

    $message = ($stmt->affected_rows > 0) ? "✅ Post deleted successfully." : "❌ Unable to delete post.";
}

// Fetch posts
$stmt = $conn->prepare("SELECT * FROM posts WHERE business_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4 text-center">📋 Manage My Posts</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $row['status'] === 'published' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                            <td class="text-center">
                                <a href="post.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_post.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="manage_posts.php?delete=<?= $row['id'] ?>"
                                   onclick="return confirm('Are you sure you want to delete this post?');"
                                   class="btn btn-danger btn-sm" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            No posts found. <a href="edit_post.php" class="btn btn-sm btn-primary ms-2">Create One Now</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
