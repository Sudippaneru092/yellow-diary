<?php
include "admin/db.php";
session_start();

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$radius = isset($_GET['radius']) ? (int)$_GET['radius'] : 0;
$lat = isset($_GET['lat']) ? (float)$_GET['lat'] : null;
$lng = isset($_GET['lng']) ? (float)$_GET['lng'] : null;

$conditions = ["status = 'approved'"];
$params = [];
$types = "";

// Keyword search
if (!empty($query)) {
  $conditions[] = "(name LIKE ? OR description LIKE ?)";
  $params[] = "%$query%";
  $params[] = "%$query%";
  $types .= "ss";
}

// Category filter
if (!empty($category)) {
  $conditions[] = "category = ?";
  $params[] = $category;
  $types .= "s";
}

if ($radius > 0 && $lat !== null && $lng !== null) {
  // Radius search with Haversine formula
  $haversine = "
    (6371 * acos(
      cos(radians(?)) *
      cos(radians(latitude)) *
      cos(radians(longitude) - radians(?)) +
      sin(radians(?)) *
      sin(radians(latitude))
    )) AS distance
  ";

  $sql = "SELECT *, $haversine FROM businesses WHERE " . implode(" AND ", $conditions) . " HAVING distance <= ? ORDER BY distance ASC";

  // Bind latitude and longitude first, twice because used twice in formula, then existing params, then radius
  array_unshift($params, $lat, $lng, $lat);
  $params[] = $radius;

  // Types: d = double(float), i = integer
  $types = "ddd" . $types . "i";
} else {
  // No radius or no lat/lng - normal search without distance calculation
  $sql = "SELECT * FROM businesses WHERE " . implode(" AND ", $conditions) . " ORDER BY created_at DESC";
}

$stmt = $conn->prepare($sql);
if ($params) {
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Search Results - Yellow Diary</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <style>/* Reset and base */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f9f9f9;
  color: #333;
  line-height: 1.6;
  padding: 20px;
}

/* Page title */
h1 {
  text-align: center;
  margin-bottom: 30px;
  color: #ffb600; /* Yellow Diary theme primary color */
  font-weight: 700;
  font-size: 2.5rem;
}

/* Grid container for cards */
.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 25px;
  max-width: 1200px;
  margin: 0 auto 40px auto;
}

/* Card styles */
.feature-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 3px 8px rgb(0 0 0 / 0.1);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transition: box-shadow 0.3s ease;
}

.feature-card:hover {
  box-shadow: 0 8px 20px rgb(0 0 0 / 0.15);
}

/* Image container */
.card-image img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  display: block;
}

/* Card content */
.card-content {
  padding: 15px 20px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

/* Headings */
.card-content h3 {
  font-size: 1.4rem;
  color: #222;
  margin-bottom: 8px;
  font-weight: 700;
}

/* Info paragraphs */
.card-content p {
  font-size: 0.9rem;
  margin-bottom: 6px;
  color: #555;
}

/* Distance special style */
.card-content p strong {
  color: #ffb600;
}

/* Description snippet */
.description {
  margin-top: 10px;
  font-style: italic;
  color: #666;
  flex-grow: 1;
}

/* Action buttons container */
.card-actions {
  margin-top: 15px;
  display: flex;
  gap: 10px;
}

/* Buttons */
.btn {
  flex: 1;
  text-align: center;
  background-color: #ffb600;
  color: #fff;
  text-decoration: none;
  padding: 8px 12px;
  border-radius: 5px;
  font-weight: 600;
  font-size: 0.9rem;
  transition: background-color 0.3s ease;
  user-select: none;
}

.btn:hover,
.btn:focus {
  background-color: #e6a800;
  outline: none;
}

/* Responsive tweaks */
@media (max-width: 480px) {
  body {
    padding: 10px;
  }
  .card-image img {
    height: 140px;
  }
  h1 {
    font-size: 2rem;
  }
}
</style>
</head>
<body>

<h1>Search Results</h1>

<?php if ($result->num_rows > 0): ?>
  <div class="features-grid">
    <?php while ($row = $result->fetch_assoc()): 
      $imagePath = !empty($row['image']) && file_exists('admin/' . $row['image']) ? 'admin/' . $row['image'] : 'images/default.jpg';
    ?>
      <article class="feature-card">
        <div class="card-image">
          <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($row['name']) ?>" loading="lazy" />
        </div>
        <div class="card-content">
          <h3><?= htmlspecialchars($row['name']) ?></h3>
          <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
          <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
          <?php if (isset($row['distance'])): ?>
            <p><strong>Distance:</strong> <?= round($row['distance'], 2) ?> km</p>
          <?php endif; ?>
          <p class="description"><?= htmlspecialchars(substr($row['description'], 0, 150)) ?>...</p>
          <div class="card-actions">
            <?php if (!empty($row['url'])): ?>
              <a href="<?= htmlspecialchars($row['url']) ?>" class="btn" target="_blank">Visit Website</a>
            <?php endif; ?>
            <?php if (!empty($row['phone'])): ?>
              <a href="tel:<?= htmlspecialchars($row['phone']) ?>" class="btn">Call Now</a>
            <?php endif; ?>
          </div>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
<?php else: ?>
  <p>No businesses found matching your search criteria.</p>
<?php endif; ?>

</body>
</html>
