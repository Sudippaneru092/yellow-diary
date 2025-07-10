<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['business_id'])) {
    header("Location: business_login.php");
    exit();
}

$business_id = $_SESSION['business_id'];

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
?>

<?php include 'includes/header.php'; ?>

<style>
  .post-container {
    max-width: 700px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    text-align: center;
  }
  .post-image {
    max-width: 100%;
    height: auto;
    margin-bottom: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  .badge-custom {
    font-size: 1rem;
    padding: 0.5em 1.1em;
  }
  .btn-back {
    margin-top: 30px;
  }
</style>

<div class="post-container">
  <h2 class="mb-3"><?= htmlspecialchars($post['title']) ?></h2>

  <p class="text-muted mb-1">
    <strong>Category:</strong> <?= htmlspecialchars($post['category']) ?> |
    <strong>Location:</strong> <?= htmlspecialchars($post['location']) ?>
  </p>

  <p>
    <strong>Status:</strong>
    <span class="badge <?= $post['status'] === 'approved' ? 'bg-success' : 'bg-warning' ?> badge-custom">
      <?= ucfirst($post['status']) ?>
    </span>
  </p>

  <p class="text-muted"><strong>Created On:</strong> <?= date('d M Y, H:i', strtotime($post['created_at'])) ?></p>

  <?php if (!empty($post['image']) && file_exists(__DIR__ . "/../admin/uploads/posts/" . $post['image'])): ?>
    <img src="../admin/uploads/posts/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" class="post-image">
  <?php else: ?>
    <p><em>No image available.</em></p>
  <?php endif; ?>

  <p class="text-start"><?= nl2br(htmlspecialchars($post['description'])) ?></p>

  <a href="manage_posts.php" class="btn btn-outline-primary btn-back">
    <i class="fas fa-arrow-left"></i> Back to Posts
  </a>
</div>

<?php include 'includes/footer.php'; ?>
