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
  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /* ===== Reset & Base ===== */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Roboto', sans-serif;
  background-color: #f9f9f9;
  color: #333;
  line-height: 1.6;
}

/* ===== Links & Headings ===== */
a {
  color: #007bff;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

h1, h2, h3 {
  color: #222;
}

/* ===== Header ===== */
.header {
  background-color: #ffc107;
  padding: 1rem 2rem;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.navbar h1 a {
  font-size: 1.8rem;
  font-weight: bold;
  color: #4a3600;
}

.nav a {
  margin-left: 1.2rem;
  font-weight: 500;
  color: #333;
}

.nav a:hover {
  color: #000;
}

/* ===== Main Content ===== */
.main-content {
  padding: 2rem;
  max-width: 1200px;
  margin: auto;
}

.search-results h2 {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: #444;
  border-bottom: 2px solid #ffc107;
  display: inline-block;
}

/* ===== Results Grid ===== */
.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

/* ===== Business Card ===== */
.feature-card {
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-6px);
}

.card-image img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  display: block;
}

.card-content {
  padding: 1rem;
  flex-grow: 1;
}

.card-content h3 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
  color: #222;
}

.card-content p {
  margin-bottom: 0.4rem;
  color: #555;
  font-size: 0.95rem;
}

.description {
  color: #666;
  font-size: 0.9rem;
}

/* ===== Actions (Visit / Call) ===== */
.card-actions {
  margin-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.card-actions .btn {
  padding: 0.4rem 0.8rem;
  background-color: #007bff;
  color: #fff;
  border-radius: 5px;
  font-size: 0.9rem;
  transition: background 0.3s;
}

.card-actions .btn:hover {
  background-color: #0056b3;
}

/* ===== No Results ===== */
.no-results {
  margin-top: 2rem;
  font-style: italic;
  color: #777;
  text-align: center;
}

/* ===== Footer ===== */
.footer {
  background-color: #222;
  color: #eee;
  padding: 1rem;
  text-align: center;
  font-size: 0.9rem;
  margin-top: 2rem;
}

.footer a {
  color: #ffc107;
  margin: 0 0.5rem;
}

.footer a:hover {
  text-decoration: underline;
}

/* ===== Responsive Fixes ===== */
@media (max-width: 768px) {
  .navbar {
    flex-direction: column;
    align-items: flex-start;
  }

  .nav {
    margin-top: 0.5rem;
  }

  .nav a {
    margin: 0.4rem 0;
    display: block;
  }
}

  </style>
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
