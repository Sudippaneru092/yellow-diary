<?php
include 'db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Filter logic
$filter_category = $_GET['category'] ?? '';
$filter_start = $_GET['start_date'] ?? '';
$filter_end = $_GET['end_date'] ?? '';

$where = [];
$params = [];

if ($filter_category) {
    $where[] = "category = ?";
    $params[] = $filter_category;
}

if ($filter_start && $filter_end) {
    $where[] = "DATE(created_at) BETWEEN ? AND ?";
    $params[] = $filter_start;
    $params[] = $filter_end;
}

$where_clause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $conn->prepare("SELECT * FROM businesses $where_clause ORDER BY created_at DESC LIMIT 10");
if ($params) $stmt->bind_param(str_repeat('s', count($params)), ...$params);
$stmt->execute();
$recent = $stmt->get_result();

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM businesses"));
$by_category = mysqli_query($conn, "SELECT category, COUNT(*) AS count FROM businesses GROUP BY category");
$category_options = mysqli_query($conn, "SELECT name FROM category");
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1 class="mb-3">Business Reports</h1>
  </section>

  <section class="content">
    <!-- Filters -->
    <form class="row mb-4" method="GET">
      <div class="col-md-3">
        <select name="category" class="form-control">
          <option value="">All Categories</option>
          <?php while($cat = $category_options->fetch_assoc()): ?>
            <option value="<?= $cat['name'] ?>" <?= ($filter_category === $cat['name']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-3">
        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($filter_start) ?>">
      </div>
      
      <div class="col-md-3">
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        <a href="business-reports.php" class="btn btn-secondary"><i class="fas fa-sync"></i></a>
      </div>
    </form>

    <!-- Summary Cards -->
    <div class="row mb-3">
      <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3><?= $total['total'] ?></h3>
            <p>Total Businesses</p>
          </div>
          <div class="icon"><i class="fas fa-building"></i></div>
        </div>
      </div>
    </div>

    <!-- Category Summary -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Businesses by Category</h3>
        <a href="export_category_report.php" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export CSV</a>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr><th>Category</th><th>Total</th></tr></thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($by_category)): ?>
              <tr>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['category']) ?></span></td>
                <td><?= $row['count'] ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Business Listings -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Recent Business Listings</h3>
        <a href="export_recent_businesses.php" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Category</th>
              <th>Location</th>
              <th>Description</th>
              <th>Image</th>
              <th>URL</th>
              <th>Created At</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($recent)): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><span class="badge bg-primary"><?= htmlspecialchars($row['category']) ?></span></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= substr(strip_tags($row['description']), 0, 80) ?>...</td>
                <td>
                  <?php if (!empty($row['image'])): ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" class="img-thumbnail" style="width: 60px; height: 60px;">
                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($row['url']): ?>
                    <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank">Visit</a>
                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </td>
                <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
