<?php
include "admin/db.php";
session_start();

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$radius = isset($_GET['radius']) ? (int) $_GET['radius'] : 0;

$sql = "SELECT * FROM businesses WHERE status = 'approved'";
$conditions = [];

if (!empty($query)) {
  $escapedQuery = mysqli_real_escape_string($conn, $query);
  $conditions[] = "(name LIKE '%$escapedQuery%' OR description LIKE '%$escapedQuery%')";
}

if (!empty($location)) {
  $escapedLocation = mysqli_real_escape_string($conn, $location);
  $conditions[] = "(location LIKE '%$escapedLocation%')";
}

if (!empty($category)) {
  $escapedCategory = mysqli_real_escape_string($conn, $category);
  $conditions[] = "category = '$escapedCategory'";
}

if (!empty($conditions)) {
  $sql .= ' AND ' . implode(' AND ', $conditions);
}

$sql .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search Results - Yellow Diary</title>
  <meta name="description" content="Search results from Yellow Diary - Local Business Directory">
  <meta name="keywords" content="business directory, local businesses, search results, Yellow Diary">
  <meta name="author" content="Yellow Diary Team">
  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="stylesheet" href="styles.css">

  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
 



</head>
<body>
 


<!-- Header -->
<header class="header">
  <div class="navbar">
    <h1><a href="index.php">Yellow Diary</a></h1>
    <nav class="nav">
      <a href="home.php">Home</a>
      <a href="service.php">Services</a>
      <a href="submit_business.php">Submit Business</a>
      <?php if(isset($_SESSION['business_id'])): ?>
        <a href="submit_post.php">Submit Post</a>
      <?php endif; ?>
      <a href="contact.php">Contact</a>
      <?php if(isset($_SESSION['business_id'])): ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="business_login.php">Business Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Search Results -->
<main class="main-content">
  <section class="search-results" aria-label="Search results">
    <h2>🔍 Search Results</h2>
    <?php if(mysqli_num_rows($result) > 0): ?>
      <div class="features-grid">
        <?php while($row = mysqli_fetch_assoc($result)): 
          $imagePath = !empty($row['image']) && file_exists('admin/' . $row['image']) 
                      ? 'admin/' . $row['image'] 
                      : 'images/default.jpg';
        ?>
          <article class="feature-card">
            <div class="card-image">
              <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['name']) ?>" loading="lazy">
            </div>
            <div class="card-content">
              <h3><?= htmlspecialchars($row['name']) ?></h3>
              <p><strong>Type:</strong> <?= htmlspecialchars($row['category']) ?></p>
              <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
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
      <p class="no-results">No businesses found matching your search. Try different keywords or filters.</p>
    <?php endif; ?>
  </section>
</main>

<!-- Footer -->
<footer class="footer">
  <p>&copy; <?= date('Y') ?> Yellow Diary. All rights reserved.</p>
  <p>
    <a href="privacy.php">Privacy Policy</a> | 
    <a href="terms.php">Terms of Service</a> |
    <a href="admin_login.php">Admin Login</a>
  </p>
</footer>

</body>
</html>
