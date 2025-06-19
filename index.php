<?php 
include "admin/db.php";
session_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get approved businesses with limit
$featuredQuery = "SELECT * FROM businesses WHERE status = 'approved' ORDER BY RAND() LIMIT 12";
$featuredResult = mysqli_query($conn, $featuredQuery);

// Get distinct categories
$categoriesQuery = "SELECT DISTINCT category FROM businesses WHERE status = 'approved'";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

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
  <meta name="description" content="Yellow Diary - Your go-to directory for local businesses in Nepal">
  <title>Yellow Diary - Local Business Directory</title>
<link rel="stylesheet" href="assets/css/styles.css">
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
 
</head>
<body>

<!-- Header -->
<header class="header">
  <div class="navbar">
    <h1>Yellow Diary</h1>
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

<!-- Hero Section -->
<section class="hero" aria-label="Welcome to Yellow Diary">
  <div class="hero-content">
    <h1>Welcome to Yellow Diary</h1>
    <p>Your go-to directory for local businesses in Nepal.</p>
  </div>
</section>

<!-- Search Section -->
<main class="main-content">
  <section class="search-container" aria-label="Business search">
    <h2>🔍 Find a Business</h2>

    <!-- Basic Search -->
    <form action="search.php" method="GET" class="search-form basic-search">
      <input type="text" name="query" placeholder="Search businesses..." required
             aria-label="Search businesses">
      <button type="submit">Search</button>
    </form>

    <!-- Advanced Search Toggle -->
    <div class="advanced-toggle">
      <button type="button" onclick="tog()" aria-expanded="false" aria-controls="advancedSearchForm">
        ⚙️ Advanced Search
      </button>
    </div>

    <!-- Advanced Search Form -->
<form id="advancedSearchForm" action="search.php" method="GET" class="search-form advanced-search">
      <input type="text" name="location" placeholder="City or Area" aria-label="Location">
      
      <select name="category" aria-label="Business category">
        <option value="">All Categories</option>
        <?php while($cat = mysqli_fetch_assoc($categoriesResult)): ?>
          <option value="<?= htmlspecialchars($cat['category']) ?>">
            <?= htmlspecialchars($cat['category']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      
      <select name="radius" aria-label="Search radius">
        <option value="">Nearby (km)</option>
        <option value="1">1 km</option>
        <option value="3">3 km</option>
        <option value="5">5 km</option>
        <option value="10">10 km</option>
        <option value="25">25 km</option>
      </select>
      
      <button type="submit">Advanced Search</button>
    </form>
  </section>

  <!-- Categories -->
  <div class="categories" role="tablist" aria-label="Business categories">
    <button class="category active" data-category="all" role="tab" aria-selected="true">All</button>
    <?php 
    mysqli_data_seek($categoriesResult, 0); // Reset pointer
    while ($cat = mysqli_fetch_assoc($categoriesResult)): 
      $catVal = strtolower($cat['category']);
    ?>
      <button class="category" data-category="<?= $catVal ?>" role="tab" aria-selected="false">
        <?= htmlspecialchars($cat['category']) ?>
      </button>
    <?php endwhile; ?>
  </div>

  <!-- Featured Businesses -->
  <section class="features-business" aria-label="Featured businesses">
    <h2>Featured Businesses</h2>
    
    <?php if(mysqli_num_rows($featuredResult) > 0): ?>
      <div class="features-grid">
        <?php while ($row = mysqli_fetch_assoc($featuredResult)): 
          $imagePath = !empty($row['image']) ? 'yellow-diary/admin/' . $row['image'] : 'images/default.jpg';
          $categoryClass = strtolower($row['category']);
        ?>
          <article class="feature-card" data-category="<?= $categoryClass ?>">
            <div class="card-image">
              <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['name']) ?>" loading="lazy">
            </div>
            <div class="card-content">
              <h3><?= htmlspecialchars($row['name']) ?></h3>
              <p><strong>Type:</strong> <?= htmlspecialchars($row['category']) ?></p>
              <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
              <p class="description"><?= htmlspecialchars($row['description']) ?></p>
              <div class="card-actions">
                <?php if(!empty($row['url'])): ?>
                  <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn">
                    Visit Website
                  </a>
                <?php endif; ?>
                <?php if(!empty($row['phone'])): ?>
                  <a href="tel:<?= htmlspecialchars($row['phone']) ?>" class="btn">
                    Call Now
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="no-results">No featured businesses found. Check back later!</p>
    <?php endif; ?>
  </section>

  <!-- Business Posts Section -->
  <section class="business-posts" aria-label="Business news and updates">
    <div class="container">
      <h2>Business News & Updates</h2>
      
      <div class="posts-grid">
        <?php if(mysqli_num_rows($postsResult) > 0): ?>
          <?php while($post = mysqli_fetch_assoc($postsResult)): 
            $postImage = !empty($post['image']) ? 'admin/uploads/posts/'.$post['image'] : 'images/default-post.jpg';
          ?>
            <article class="post-card">
              <div class="post-image">
                <img src="<?= $postImage ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                <div class="post-badge">
                  <?= htmlspecialchars($post['category']) ?>
                </div>
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
          <p class="no-posts">No business updates available yet. Check back soon!</p>
        <?php endif; ?>
      </div>
      
      <div class="view-all-container">
        <a href="posts.php" class="view-all-btn">View All Updates</a>
      </div>
    </div>
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

<!-- JavaScript -->
<script>
  // Toggle advanced search
function tog() {
  const advForm = document.getElementById("advancedSearchForm");
  const toggleButton = document.querySelector('.advanced-toggle button');
  const isExpanded = advForm.classList.toggle("show");

  toggleButton.setAttribute('aria-expanded', isExpanded);
}


  // Category filtering for businesses
  document.querySelector('.categories').addEventListener('click', (e) => {
    if (e.target.classList.contains('category')) {
      const category = e.target.getAttribute('data-category');
      filterBusinesses(category);
      
      // Update active state and ARIA attributes
      document.querySelectorAll('.categories .category').forEach(btn => {
        btn.classList.remove('active');
        btn.setAttribute('aria-selected', 'false');
      });
      
      e.target.classList.add('active');
      e.target.setAttribute('aria-selected', 'true');
    }
  });

  function filterBusinesses(category) {
    const cards = document.querySelectorAll('.feature-card');
    cards.forEach(card => {
      const cardCategory = card.getAttribute('data-category');
      if (category === "all" || cardCategory === category) {
        card.style.display = "flex";
        card.setAttribute('aria-hidden', 'false');
      } else {
        card.style.display = "none";
        card.setAttribute('aria-hidden', 'true');
      }
    });
  }

  // Initialize
  document.addEventListener('DOMContentLoaded', () => {
    filterBusinesses("all");
  });
</script>

</body>
</html>