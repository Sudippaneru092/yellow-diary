<?php
include "admin/db.php";
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$searchQuery = trim($_GET['query'] ?? '');

if (empty($searchQuery)) {
    // Redirect back or show message if no search query provided
    header("Location: index.php");
    exit;
}

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM businesses WHERE status = 'approved' AND (name LIKE ? OR category LIKE ? OR location LIKE ? OR description LIKE ?) ORDER BY name ASC");
$likeQuery = '%' . $searchQuery . '%';
$stmt->bind_param("ssss", $likeQuery, $likeQuery, $likeQuery, $likeQuery);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Search results for "<?= htmlspecialchars($searchQuery) ?>" - Yellow Diary</title>
  <link rel="stylesheet" href="index.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
</head>
<body>

<?php include "header.php"; ?>

<section class="hero">
  <div class="hero-content">
    <h1>Search results for "<?= htmlspecialchars($searchQuery) ?>"</h1>
    <p><a href="index.php">← Back to Home</a></p>
  </div>
</section>

<main class="main-content">
  <section class="features-business">
    <?php if ($result && $result->num_rows > 0): ?>
      <div class="features-grid">
        <?php while ($row = $result->fetch_assoc()):
          $imagePath = !empty($row['image']) ? $row['image'] : 'images/default-business.png';
          $categoryClass = strtolower($row['category']);
        ?>
          <article class="feature-card" data-category="<?= $categoryClass ?>">
            <div class="card-image">
              <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['name']) ?>" onerror="this.src='images/default-business.png'">
            </div>
            <div class="card-content">
              <h3><?= htmlspecialchars($row['name']) ?></h3>
              <p><strong>Type:</strong> <?= htmlspecialchars($row['category']) ?></p>
              <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
              <p class="description"><?= htmlspecialchars($row['description']) ?></p>
              <div class="card-actions">
                <?php if(!empty($row['url'])): ?>
                <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" class="btn">Visit Website</a>
                <?php endif; ?>
                <?php if(!empty($row['phone'])): ?>
                <a href="tel:<?= htmlspecialchars($row['phone']) ?>" class="btn">Call Now</a>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p>No businesses found matching your search.</p>
    <?php endif; ?>
  </section>
</main>

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
<?php
$stmt->close();
$conn->close();
?>
