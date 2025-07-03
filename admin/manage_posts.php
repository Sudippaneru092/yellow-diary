<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

// Filtering and pagination setup
$where = "1=1";
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

if ($status !== '') {
    $safeStatus = $conn->real_escape_string($status);
    $where .= " AND p.status = '$safeStatus'";
}

if ($search !== '') {
    $safeSearch = $conn->real_escape_string($search);
    $where .= " AND p.title LIKE '%$safeSearch%'";
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$countSql = "SELECT COUNT(*) FROM posts p LEFT JOIN businesses b ON p.business_id = b.id WHERE $where";
$total = $conn->query($countSql)->fetch_row()[0];
$totalPages = ceil($total / $limit);

// Fetch filtered and paginated posts
$sql = "SELECT p.*, b.name AS business_name 
        FROM posts p 
        LEFT JOIN businesses b ON p.business_id = b.id 
        WHERE $where
        ORDER BY p.created_at DESC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Handle status update (Approve/Reject)
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if (in_array($action, ['approve', 'reject'])) {
        $newStatus = $action === 'approve' ? 'approved' : 'rejected';
        $stmt = $conn->prepare("UPDATE posts SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $newStatus, $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Post has been $newStatus.";
        header("Location: manage_posts.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Posts - Yellow Diary</title>
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css" />
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
  <section class="content-header"><h1>Manage Posts</h1></section>

  <section class="content">

    <!-- Flash Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Filter Form -->
    <form method="GET" class="mb-3 d-flex flex-wrap gap-2">
      <select name="status" class="form-control mr-2" style="max-width: 200px;">
        <option value="">All Statuses</option>
        <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>

      <input type="text" name="search" class="form-control mr-2" placeholder="Search title..." value="<?= htmlspecialchars($search) ?>" style="max-width: 300px;">

      <button type="submit" class="btn btn-primary mr-2">Filter</button>
      <a href="manage_posts.php" class="btn btn-secondary">Reset</a>
    </form>

    <!-- Posts Table -->
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Title</th>
          <th>Business</th>
          <th>Category</th>
          <th>Location</th>
          <th>Status</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($post = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $post['id'] ?></td>
              <td>
                <?php if ($post['image']): ?>
                  <img src="uploads/posts/<?= htmlspecialchars($post['image']) ?>" width="70" height="50" style="object-fit: cover;">
                <?php else: ?>
                  <span class="text-muted">N/A</span>
                <?php endif; ?>
              </td>
              <td title="<?= htmlspecialchars($post['title']) ?>">
                <?= strlen($post['title']) > 40 ? substr($post['title'], 0, 40) . '...' : $post['title'] ?>
              </td>
              <td><?= htmlspecialchars($post['business_name'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($post['category']) ?></td>
              <td><?= htmlspecialchars($post['location']) ?></td>
              <td>
                <?php
                  $statusClass = 'badge-secondary';
                  if ($post['status'] === 'approved') $statusClass = 'badge-success';
                  elseif ($post['status'] === 'rejected') $statusClass = 'badge-danger';
                  elseif ($post['status'] === 'pending') $statusClass = 'badge-warning';
                ?>
                <span class="badge <?= $statusClass ?>"><?= ucfirst($post['status']) ?></span>
              </td>
              <td><?= date("Y-m-d", strtotime($post['created_at'])) ?></td>
              <td>
                <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-info mb-1">Edit</a>

                <?php if ($post['status'] === 'pending'): ?>
                  <a href="manage_posts.php?action=approve&id=<?= $post['id'] ?>" class="btn btn-sm btn-success mb-1" onclick="return confirm('Approve this post?')">Approve</a>
                  <a href="manage_posts.php?action=reject&id=<?= $post['id'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Reject this post?')">Reject</a>
                <?php endif; ?>

                <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="9" class="text-center text-muted">No posts found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <nav>
        <ul class="pagination mt-3">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>

  </section>
</div>

<?php include 'includes/footer.php'; ?>
</div>

<script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>
