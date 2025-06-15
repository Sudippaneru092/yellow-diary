<?php
include 'db.php';

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filters and search
$filter_category = isset($_GET['category']) ? $_GET['category'] : '';
$filter_location = isset($_GET['location']) ? $_GET['location'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build WHERE clauses dynamically
$where = [];
if ($filter_category !== '') {
    $where[] = "category = '" . mysqli_real_escape_string($conn, $filter_category) . "'";
}
if ($filter_location !== '') {
    $where[] = "location = '" . mysqli_real_escape_string($conn, $filter_location) . "'";
}
if ($search !== '') {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $where[] = "(title LIKE '%$search_esc%' OR description LIKE '%$search_esc%')";
}
$where_sql = '';
if (count($where) > 0) {
    $where_sql = "WHERE " . implode(' AND ', $where);
}

// Get total records count
$count_query = "SELECT COUNT(*) AS total FROM posts $where_sql";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch filtered posts
$data_query = "SELECT * FROM posts $where_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$data_result = mysqli_query($conn, $data_query);

// Fetch distinct categories and locations for filters
$categories_result = mysqli_query($conn, "SELECT DISTINCT category FROM posts");
$locations_result = mysqli_query($conn, "SELECT DISTINCT location FROM posts");

// Handle export request
if (isset($_GET['export'])) {
    $export_type = $_GET['export'];
    $export_query = "SELECT * FROM posts $where_sql ORDER BY created_at DESC";
    $export_result = mysqli_query($conn, $export_query);

    if ($export_type === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=posts_export.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Business ID', 'Title', 'Category', 'Location', 'Description', 'Status', 'Created At']);
        while ($row = mysqli_fetch_assoc($export_result)) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    } elseif ($export_type === 'pdf') {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename=posts_export.pdf');
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
<title>Post Reports - Yellow Diary</title>
<!-- Bootstrap 5 CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  body {
    background-color: #f8f9fa;
  }
  .container {
    margin-top: 40px;
  }
  .table-responsive {
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
  }
  .truncate {
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>
</head>
<body>

<div class="container">
  <h2 class="mb-4 text-primary">Post Reports</h2>

  <form method="GET" class="row g-3 align-items-center mb-4">
    <div class="col-md-4">
      <input type="text" name="search" class="form-control" placeholder="Search title or description" value="<?= htmlspecialchars($search) ?>" />
    </div>
    <div class="col-md-3">
      <select name="category" class="form-select">
        <option value="">-- Select Category --</option>
        <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
          <option value="<?= htmlspecialchars($cat['category']) ?>" <?= $filter_category === $cat['category'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['category']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="location" class="form-select">
        <option value="">-- Select Location --</option>
        <?php while ($loc = mysqli_fetch_assoc($locations_result)): ?>
          <option value="<?= htmlspecialchars($loc['location']) ?>" <?= $filter_location === $loc['location'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($loc['location']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2 d-grid">
      <button type="submit" class="btn btn-primary">Filter</button>
    </div>
  </form>

  <div class="mb-3">
    <span>Export: </span>
    <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv', 'page' => 1])) ?>" class="btn btn-outline-success btn-sm me-2">CSV</a>
    <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'pdf', 'page' => 1])) ?>" class="btn btn-outline-danger btn-sm">PDF</a>
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

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php mysqli_close($conn); ?>
