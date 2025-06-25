<?php 
include "admin/db.php";
session_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get approved businesses with limit
$featuredQuery = "SELECT * FROM businesses WHERE status = 'approved' ORDER BY RAND() LIMIT 12";
$featuredResult = mysqli_query($conn, $featuredQuery);

// Get distinct categories for businesses
$categoriesQuery = "SELECT DISTINCT category FROM businesses WHERE status = 'approved'";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

// Get distinct categories for posts
$postCategoriesQuery = "SELECT DISTINCT category FROM posts WHERE status = 'approved'";
$postCategoriesResult = mysqli_query($conn, $postCategoriesQuery);

// Get approved business posts
$postsQuery = "SELECT p.*, b.name as business_name 
              FROM posts p
              JOIN businesses b ON p.business_id = b.id
              WHERE p.status = 'approved'
              ORDER BY p.created_at DESC
              LIMIT 6";
$postsResult = mysqli_query($conn, $postsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Yellow Diary - Local Business Directory</title>
  

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="main.css" />


  </style>
</head>
<body>

<?php include "header.php"; ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">
    <h1>Welcome to Yellow Diary</h1>
    <p>Your go-to directory for local businesses in Nepal.</p>
  </div>
</section>

<main class="main-content">
  <!-- Search Section -->
  <section class="search-container">
    <h2>🔍 Find a Business</h2>
    <form action="search.php" method="GET" class="search-form basic-search">
      <input type="text" name="query" placeholder="Search businesses..." required>
      <button type="submit">Search</button>
    </form>

    <div class="advanced-toggle">
      <button type="button" onclick="tog()">⚙️ Advanced Search</button>
    </div>

    <form id="advancedSearchForm" action="advanced_search.php" method="GET" class="search-form advanced-search">
      <input type="text" name="location" placeholder="City or Area">
      <select name="category">
        <option value="">All Categories</option>
        <?php mysqli_data_seek($categoriesResult, 0); while($cat = mysqli_fetch_assoc($categoriesResult)): ?>
          <option value="<?= htmlspecialchars($cat['category']) ?>"><?= htmlspecialchars($cat['category']) ?></option>
        <?php endwhile; ?>
      </select>
      <select name="radius">
        <option value="">Nearby (km)</option>
        <option value="1">1 km</option>
        <option value="3">3 km</option>
        <option value="5">5 km</option>
        <option value="10">10 km</option>
        <option value="25">25 km</option>
      </select>
      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lng" id="lng">
      <button type="submit">Advanced Search</button>
    </form>
  </section>

  <!-- Categories -->
  <div class="categories">
    <button class="category active" data-category="all">All</button>
    <?php mysqli_data_seek($categoriesResult, 0); while ($cat = mysqli_fetch_assoc($categoriesResult)): ?>
      <button class="category" data-category="<?= strtolower($cat['category']) ?>"><?= htmlspecialchars($cat['category']) ?></button>
    <?php endwhile; ?>
  </div>

  <!-- Featured Businesses -->
  <section class="features-business">
    <h2>Featured Businesses</h2>
    <?php if(mysqli_num_rows($featuredResult) > 0): ?>
    <div class="features-grid">
      <?php while ($row = mysqli_fetch_assoc($featuredResult)):

          $image = $row['image'];

          // Try multiple path formats
          $possiblePaths = [
              'admin/uploads/business_images/' . $image,
              'admin/' . $image,
              $image, // already full relative path
          ];

          $imagePath = 'images/default.jpg'; // fallback

          foreach ($possiblePaths as $path) {
              if (!empty($image) && file_exists($path)) {
                  $imagePath = $path;
                  break;
              }
          }

        $categoryClass = strtolower($row['category']);
      ?>
        <article class="feature-card" data-category="<?= $categoryClass ?>">
          <div class="card-image">
            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['name']) ?>">
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
      <p>No featured businesses found.</p>
    <?php endif; ?>
  </section>

  <!-- Business Posts -->
  <section class="business-posts">
    <div class="container">
      <h2>Business News & Updates</h2>

      <div class="post-categories">
        <button class="post-category active" data-category="all">All</button>
        <?php mysqli_data_seek($postCategoriesResult, 0); while ($postCat = mysqli_fetch_assoc($postCategoriesResult)):
          $postCatVal = strtolower($postCat['category']);
        ?>
          <button class="post-category" data-category="<?= $postCatVal ?>"><?= htmlspecialchars($postCat['category']) ?></button>
        <?php endwhile; ?>
      </div>

      <div class="posts-grid">
        <?php if(mysqli_num_rows($postsResult) > 0): ?>
          <?php while($post = mysqli_fetch_assoc($postsResult)):
            $postImage = !empty($post['image']) ? 'admin/uploads/posts/'.$post['image'] : 'images/default-post.jpg';
            $postCategoryClass = strtolower($post['category']);
          ?>
            <article class="post-card" data-category="<?= $postCategoryClass ?>">
              <div class="post-image">
                <img src="<?= $postImage ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                <div class="post-badge"><?= htmlspecialchars($post['category']) ?></div>
              </div>
              <div class="post-content">
                <div class="post-meta">
                  <span class="post-business"><?= htmlspecialchars($post['business_name']) ?></span>
                  <span class="post-date"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                </div>
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p class="post-excerpt"><?= htmlspecialchars(substr($post['description'], 0, 150)) ?>...</p>
                <div class="post-location">
                  <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($post['location']) ?>
                </div>
                <a href="post.php?id=<?= $post['id'] ?>" class="read-more">Read More</a>
              </div>
            </article>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No business updates available yet.</p>
        <?php endif; ?>
      </div>

      <div class="view-all-container">
        <a href="posts.php" class="view-all-btn">View All Updates</a>
      </div>
    </div>
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

<script>
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(function(position) {
    document.getElementById("lat").value = position.coords.latitude;
    document.getElementById("lng").value = position.coords.longitude;
  });
}
function tog() {
  const advForm = document.getElementById("advancedSearchForm");
  advForm.classList.toggle("show");
}

// Filter businesses
document.querySelector('.categories').addEventListener('click', (e) => {
  if (e.target.classList.contains('category')) {
    const category = e.target.getAttribute('data-category');
    filterBusinesses(category);
    document.querySelectorAll('.categories .category').forEach(btn => {
      btn.classList.remove('active');
      btn.setAttribute('aria-selected', 'false');
    });
    e.target.classList.add('active');
    e.target.setAttribute('aria-selected', 'true');
  }
});

function filterBusinesses(category) {
  document.querySelectorAll('.feature-card').forEach(card => {
    const cardCategory = card.getAttribute('data-category');
    card.style.display = (category === "all" || cardCategory === category) ? "flex" : "none";
  });
}

// Filter posts
document.querySelector('.post-categories').addEventListener('click', (e) => {
  if (e.target.classList.contains('post-category')) {
    const category = e.target.getAttribute('data-category');
    filterPosts(category);
    document.querySelectorAll('.post-categories .post-category').forEach(btn => {
      btn.classList.remove('active');
      btn.setAttribute('aria-selected', 'false');
    });
    e.target.classList.add('active');
    e.target.setAttribute('aria-selected', 'true');
  }
});

function filterPosts(category) {
  document.querySelectorAll('.post-card').forEach(post => {
    const postCategory = post.getAttribute('data-category');
    post.style.display = (category === "all" || postCategory === category) ? "flex" : "none";
  });
}

document.addEventListener('DOMContentLoaded', () => {
  filterBusinesses("all");
  filterPosts("all");
});
</script>
</body>
</html>
