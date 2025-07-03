<?php
include "admin/db.php";
session_start();

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$name = $_GET['name'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$radius = $_GET['radius'] ?? '';
$lat = $_GET['lat'] ?? '';
$lng = $_GET['lng'] ?? '';

$where = "WHERE status IN ('approved', 'active')";

$conditions = [];

// Exclude invalid coordinates always

// Basic Search
if (!empty($query)) {
    $safe = mysqli_real_escape_string($conn, $query);
    $conditions[] = "(name LIKE '%$safe%' OR category LIKE '%$safe%' OR location LIKE '%$safe%')";
}

// Advanced Search
if (!empty($name)) {
    $safe = mysqli_real_escape_string($conn, $name);
    $conditions[] = "name LIKE '%$safe%'";
}
if (!empty($category)) {
    $safe = mysqli_real_escape_string($conn, $category);
    $conditions[] = "category LIKE '%$safe%'";
}
if (!empty($location) && $location !== 'Near Me') {
    $safe = mysqli_real_escape_string($conn, $location);
    $conditions[] = "location LIKE '%$safe%'";
}

// Radius (Haversine formula for nearby businesses)
if (!empty($lat) && !empty($lng) && !empty($radius)) {
  echo "<pre>Using radius search with lat: $lat, lng: $lng, radius: $radius</pre>";
    $radius = (float)$radius;
    $lat = (float)$lat;
    $lng = (float)$lng;

    $conditions[] = "(
        latitude != 0 AND longitude != 0 AND
        (6371 * acos(
            cos(radians($lat)) * cos(radians(latitude)) *
            cos(radians(longitude) - radians($lng)) +
            sin(radians($lat)) * sin(radians(latitude))
        )) <= $radius
    )";
}

if (!empty($conditions)) {
    $where .= ' AND ' . implode(' AND ', $conditions);
}


$sql = "SELECT * FROM businesses $where ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
echo "<pre>$sql</pre>";

?>

<!-- Display Results -->
<!DOCTYPE html>
<html>
<head>
    <title>Search Results - Yellow Diary</title>
    <link rel="stylesheet" href="index.css" />
</head>
<body>

<?php include "header.php"; ?>

<main class="main-content">
  <section class="search-results">
    <h2>Search Results</h2>
    <div class="features-grid">
      <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)){
          $imagePath = !empty($row['image']) ? "admin/".$row['image'] : 'images/default-business.png';
        ?>
        <article class="feature-card">
          <div class="card-image">
            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
        
          </div>
          <div class="card-content">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p><strong>Type:</strong> <?= htmlspecialchars($row['category']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
            <p class="description"><?= htmlspecialchars($row['description']) ?></p>
            <div class="card-actions">
              <?php if (!empty($row['url'])): ?>
              <a href="<?= htmlspecialchars($row['url']) ?>" class="btn" target="_blank">Visit</a>
              <?php endif; ?>
              <?php if (!empty($row['phone'])): ?>
              <a href="tel:<?= htmlspecialchars($row['phone']) ?>" class="btn">Call</a>
              <?php endif; ?>
            </div>
          </div>
        </article>
        <?php } ?>
      <?php else: ?>
        <p>No businesses found for your search.</p>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include "footer.php"; ?>

</body>
</html>
