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

// Radius search (Haversine formula)
if (!empty($lat) && !empty($lng) && !empty($radius)) {
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

// Auto-save search if any filter is applied
if (!empty($_GET)) {
    $queryVal = mysqli_real_escape_string($conn, $query);
    $nameVal = mysqli_real_escape_string($conn, $name);
    $categoryVal = mysqli_real_escape_string($conn, $category);
    $locationVal = mysqli_real_escape_string($conn, $location);
    $latVal = (float)$lat;
    $lngVal = (float)$lng;
    $radiusVal = (int)$radius;

    $search_hash = md5($queryVal . $nameVal . $categoryVal . $locationVal . $latVal . $lngVal . $radiusVal);

    if (isset($_SESSION['business_id'])) {
        $business_id = $_SESSION['business_id'];

        // Check for existing saved search using prepared statement
        $check_stmt = $conn->prepare("SELECT id FROM saved_searches 
            WHERE business_id = ? 
            AND MD5(CONCAT(query, name_filter, category_filter, location_filter, lat, lng, radius)) = ?");
        $check_stmt->bind_param("is", $business_id, $search_hash);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
            $autoName = "Auto Saved on " . date("M d, Y H:i");
            $stmt = $conn->prepare("INSERT INTO saved_searches 
                (business_id, name, query, name_filter, category_filter, location_filter, lat, lng, radius) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssdd", $business_id, $autoName, $queryVal, $nameVal, $categoryVal, $locationVal, $latVal, $lngVal, $radiusVal);
            $stmt->execute();
        }
    } else {
        // Not logged in — save to cookie
        $searchData = [
            'query' => $queryVal,
            'name' => $nameVal,
            'category' => $categoryVal,
            'location' => $locationVal,
            'lat' => $latVal,
            'lng' => $lngVal,
            'radius' => $radiusVal,
            'saved_at' => date('Y-m-d H:i:s')
        ];

        $existing = isset($_COOKIE['guest_searches']) ? json_decode($_COOKIE['guest_searches'], true) : [];

        $is_duplicate = false;
        foreach ($existing as $item) {
            $item_hash = md5(
                $item['query'] . $item['name'] . $item['category'] . $item['location'] .
                $item['lat'] . $item['lng'] . $item['radius']
            );
            if ($item_hash === $search_hash) {
                $is_duplicate = true;
                break;
            }
        }

        if (!$is_duplicate) {
            array_unshift($existing, $searchData); // Add new on top
            $existing = array_slice($existing, 0, 5); // Keep only latest 5
            setcookie("guest_searches", json_encode($existing), time() + (86400 * 30), "/"); // 30 days
        }
    }
}
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
