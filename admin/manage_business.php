<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM businesses WHERE id = $id");
    $_SESSION['success'] = "Business deleted successfully.";
    header("Location: manage_business.php");
    exit;
}

// Handle Approval or Rejection
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve') {
        $conn->query("UPDATE businesses SET status = 'approved' WHERE id = $id");
        $_SESSION['success'] = "Business approved successfully.";
    } elseif ($action === 'reject') {
        $conn->query("UPDATE businesses SET status = 'rejected' WHERE id = $id");
        $_SESSION['success'] = "Business rejected successfully.";
    }

    header("Location: manage_business.php");
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';

// Pagination and Search Setup
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$results_per_page = 10; // You can change this number

$where = [];

if ($search !== '') {
    $where[] = "(name LIKE '%$search%' OR category LIKE '%$search%' OR location LIKE '%$search%')";
}

if ($status_filter !== '' && in_array($status_filter, ['pending', 'approved', 'rejected'])) {
    $where[] = "status = '$status_filter'";
}

$where_sql = '';
if (count($where) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where);
}

// Get total number of matching rows (for pagination)
$count_sql = "SELECT COUNT(*) as total FROM businesses $where_sql";
$count_result = $conn->query($count_sql);
$row_count = $count_result->fetch_assoc();
$total_results = $row_count['total'];
$total_pages = ceil($total_results / $results_per_page);

// Calculate the offset for the query
$offset = ($page - 1) * $results_per_page;

// Fetch paginated results
$sql = "SELECT * FROM businesses $where_sql ORDER BY id DESC LIMIT $offset, $results_per_page";
$result = $conn->query($sql);
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage Businesses</h1>
    <a href="add_business.php" class="btn btn-primary mb-3">+ Add New Business</a>
  </section>

  <section class="content">

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Search Form -->
    <form method="GET" action="manage_business.php" class="mb-3 form-inline">
      <input type="text" name="search" class="form-control mr-2" placeholder="Search businesses..." 
             value="<?= htmlspecialchars($search) ?>">
      <select name="status" class="form-control mr-2">
        <option value="">All Statuses</option>
        <option value="pending" <?= ($status_filter === 'pending') ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= ($status_filter === 'approved') ? 'selected' : '' ?>>Approved</option>
        <option value="rejected" <?= ($status_filter === 'rejected') ? 'selected' : '' ?>>Rejected</option>
      </select>
      <button type="submit" class="btn btn-primary">Search</button>
      <a href="manage_business.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-dark">
            <tr>
              <th>#</th>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Location</th>
              <th>Status</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td>
                    <?php if (!empty($row['image'])): ?>
                      <img src="<?= htmlspecialchars($row['image']) ?>" width="60" height="40" alt="Business Image">
                    <?php else: ?>
                      N/A
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($row['name']) ?></td>
                  <td><?= htmlspecialchars($row['category']) ?></td>
                  <td><?= htmlspecialchars($row['location']) ?></td>
                  <td>
                    <span class="badge badge-<?= $row['status'] == 'approved' ? 'success' : ($row['status'] == 'rejected' ? 'danger' : 'secondary') ?>">
                      <?= ucfirst($row['status']) ?>
                    </span>
                  </td>
                  <td><?= $row['created_at'] ?></td>
                  <td>
                    <a href="edit_business.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>

                    <?php if ($row['status'] === 'pending'): ?>
                      <a href="manage_business.php?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this business?')">Approve</a>
                      <a href="manage_business.php?action=reject&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject this business?')">Reject</a>
                    <?php else: ?>
                      <span class="badge badge-info">Reviewed</span>
                    <?php endif; ?>

                    <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this business?')">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="8">No businesses found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
        <nav>
          <ul class="pagination justify-content-center">
            <!-- Previous -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" tabindex="-1">Previous</a>
            </li>

            <?php
            // Show pagination numbers with a window of 5 pages max
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);

            for ($i = $start_page; $i <= $end_page; $i++): ?>
              <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>

    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
