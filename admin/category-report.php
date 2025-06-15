<?php
include 'db.php';

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get selected category filter (optional)
$filter_category = isset($_GET['category']) ? $_GET['category'] : '';

// Get all distinct categories with post counts
$category_counts_query = "
    SELECT category, COUNT(*) as post_count
    FROM posts
    GROUP BY category
    ORDER BY category ASC
";
$category_counts_result = mysqli_query($conn, $category_counts_query);

// Build WHERE clause if filtering by category
$where_sql = '';
if ($filter_category !== '') {
    $filter_category_esc = mysqli_real_escape_string($conn, $filter_category);
    $where_sql = "WHERE category = '$filter_category_esc'";
}

// Get total posts count for pagination (filtered by category if any)
$count_query = "SELECT COUNT(*) AS total FROM posts $where_sql";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch posts filtered by category with pagination
$data_query = "SELECT * FROM posts $where_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$data_result = mysqli_query($conn, $data_query);

// Handle export
if (isset($_GET['export'])) {
    $export_type = $_GET['export'];
    $export_query = "SELECT * FROM posts $where_sql ORDER BY created_at DESC";
    $export_result = mysqli_query($conn, $export_query);

    if ($export_type === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=category_report.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Business ID', 'Title', 'Category', 'Location', 'Description', 'Status', 'Created At']);
        while ($row = mysqli_fetch_assoc($export_result)) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    } elseif ($export_type === 'pdf') {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename=category_report.pdf');
        echo "ID | Business ID | Title | Category | Location | Description | Status | Created At\n";
        while ($row = mysqli_fetch_assoc($export_result)) {
            echo implode(' | ', $row) . "\n";
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Category Report - Yellow Diary</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  .truncate {
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>
</head>
<body>
<div class="container mt-5">

  <h2 class="mb-4 text-primary">Category Report</h2>

  <div class="mb-4">
    <h5>Categories & Post Counts</h5>
    <ul class="list-group">
      <?php while ($cat = mysqli_fetch_assoc($category_counts_result)): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center <?= ($filter_category === $cat['category']) ? 'active text-white' : '' ?>">
          <a href="?category=<?= urlencode($cat['category']) ?>" class="<?= ($filter_category === $cat['category']) ? 'text-white' : '' ?>">
            <?= htmlspecialchars($cat['category']) ?>
          </a>
          <span class="badge bg-primary rounded-pill"><?= $cat['post_count'] ?></span>
        </li>
      <?php endwhile; ?>
      <li class="list-group-item <?= ($filter_category === '') ? 'active text-white' : '' ?>">
        <a href="?" class="<?= ($filter_category === '') ? 'text-white' : '' ?>">Show All</a>
      </li>
    </ul>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Posts <?= $filter_category ? "in '" . htmlspecialchars($filter_category) . "'" : '' ?></h5>
    <div>
      Export:
      <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv', 'page' => 1])) ?>" class="btn btn-outline-success btn-sm me-2">CSV</a>
      <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'pdf', 'page' => 1])) ?>" class="btn btn-outline-danger btn-sm">PDF</a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead class="table-primary">
        <tr>
          <th>ID</th>
          <th>Business ID</th>
          <th>Title</th>
          <th>Category</th>
          <th>Location</th>
          <th>Description</th>
          <th>Status</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($data_result && mysqli_num_rows($data_result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($data_result)): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['business_id']) ?></td>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= htmlspecialchars($row['category']) ?></td>
              <td><?= htmlspecialchars($row['location']) ?></td>
              <td class="truncate" title="<?= htmlspecialchars(strip_tags($row['description'])) ?>">
                <?= htmlspecialchars(substr(strip_tags($row['description']), 0, 80)) ?>...
              </td>
              <td>
                <?php if (strtolower($row['status']) === 'active'): ?>
                  <span class="badge bg-success"><?= htmlspecialchars($row['status']) ?></span>
                <?php else: ?>
                  <span class="badge bg-secondary"><?= htmlspecialchars($row['status']) ?></span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($row['created_at']))) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center">No posts found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-center">
        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
          <li class="page-item <?= $p === $page ? 'active' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>"><?= $p ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>
