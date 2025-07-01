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

// Get distinct locations for businesses
$locationsQuery = "SELECT DISTINCT location FROM businesses WHERE status = 'approved'";
$locationsResult = mysqli_query($conn, $locationsQuery);

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
  
  <link rel="stylesheet" href="index.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
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
    <form action="search_results.php" method="GET" class="search-form basic-search">
      <input type="text" name="query" id="searchInput" placeholder="Search businesses, categories, locations..." required
             list="searchSuggestions" autocomplete="off">
      <datalist id="searchSuggestions">
        <?php 
        // Combine business names, categories, and locations for suggestions
        $suggestionsQuery = "(SELECT name as suggestion FROM businesses WHERE status='approved' LIMIT 15)
                            UNION
                            (SELECT category as suggestion FROM businesses WHERE status='approved' LIMIT 15)
                            UNION
                            (SELECT location as suggestion FROM businesses WHERE status='approved' LIMIT 15)";
        $suggestionsResult = mysqli_query($conn, $suggestionsQuery);
        while($suggestion = mysqli_fetch_assoc($suggestionsResult)):
        ?>
          <option value="<?= htmlspecialchars($suggestion['suggestion']) ?>">
        <?php endwhile; ?>
      </datalist>
      <button type="submit">Search</button>
    </form>

    <div class="advanced-toggle">
      <button type="button" onclick="toggleAdvancedSearch()">⚙️ Advanced Search</button>
    </div>

    <form id="advancedSearchForm" action="search_results.php" method="GET" class="search-form advanced-search">
      <div class="form-row">
        <div class="form-group">
          <input type="text" name="name" id="nameInput" placeholder="Business name">
        </div>
        
        <div class="form-group">
          <input type="text" name="category" id="categoryInput" placeholder="Category (e.g. 'Edu')" 
                 list="categorySuggestions" autocomplete="off">
          <datalist id="categorySuggestions">
            <?php mysqli_data_seek($categoriesResult, 0); while($cat = mysqli_fetch_assoc($categoriesResult)): ?>
              <option value="<?= htmlspecialchars($cat['category']) ?>">
            <?php endwhile; ?>
          </datalist>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <input type="text" name="location" id="locationInput" placeholder="Location" 
                 list="locationSuggestions" autocomplete="off">
          <datalist id="locationSuggestions">
            <?php mysqli_data_seek($locationsResult, 0); while($loc = mysqli_fetch_assoc($locationsResult)): ?>
              <option value="<?= htmlspecialchars($loc['location']) ?>">
            <?php endwhile; ?>
          </datalist>
          <button type="button" id="useCurrentLocationBtn" class="location-btn">📍 Use My Location</button>
        </div>
        
        <div class="form-group">
          <select name="radius">
            <option value="">Any distance</option>
            <option value="1">Within 1 km</option>
            <option value="3">Within 3 km</option>
            <option value="5">Within 5 km</option>
            <option value="10">Within 10 km</option>
          </select>
        </div>
      </div>
      
      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lng" id="lng">
      <button type="submit" class="search-button">Advanced Search</button>
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
        $imagePath = (!empty($row['image'])) ? "admin/".$row['image'] : 'images/default-business.png';
        $categoryClass = strtolower($row['category']);
      ?>
        <article class="feature-card" data-category="<?= $categoryClass ?>">
          <div class="card-image">
            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['name']) ?>" onerror="this.src='images/default-business.png'">
          <?php echo $imagePath;?>
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
            $postImage = !empty($post['image']) ? 'admin/uploads/posts/' . $post['image'] : 'images/default-post.png';
            $postCategoryClass = strtolower($post['category']);
          ?>
            <article class="post-card" data-category="<?= $postCategoryClass ?>">
              <div class="post-image">
                <img src="<?= $postImage ?>" alt="<?= htmlspecialchars($post['title']) ?>" onerror="this.src='images/default-post.png'">
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
// Geolocation for advanced search
if (navigator.geolocation) {
  document.getElementById('useCurrentLocationBtn').addEventListener('click', function() {
    navigator.geolocation.getCurrentPosition(function(position) {
      document.getElementById('lat').value = position.coords.latitude;
      document.getElementById('lng').value = position.coords.longitude;
      document.getElementById('locationInput').value = "Near Me";
    }, function(error) {
      alert("Could not get your location. Please enable location services.");
    });
  });
}

// Toggle advanced search
function toggleAdvancedSearch() {
  const advForm = document.getElementById("advancedSearchForm");
  advForm.classList.toggle("show");
  const btn = document.querySelector('.advanced-toggle button');
  if (advForm.classList.contains('show')) {
    btn.textContent = '⚙️ Hide Advanced';
  } else {
    btn.textContent = '⚙️ Advanced Search';
  }
}

// Live search suggestions
document.getElementById('searchInput').addEventListener('input', function() {
  const input = this.value.trim();
  if (input.length > 1) {
    // In a real implementation, you would make an AJAX call here
    // to get dynamic suggestions based on the input
    console.log("Fetching suggestions for:", input);
  }
});

// Filter businesses by category
document.querySelector('.categories').addEventListener('click', (e) => {
  if (e.target.classList.contains('category')) {
    const category = e.target.getAttribute('data-category');
    filterBusinesses(category);
    document.querySelectorAll('.categories .category').forEach(btn => {
      btn.classList.remove('active');
    });
    e.target.classList.add('active');
  }
});

function filterBusinesses(category) {
  document.querySelectorAll('.feature-card').forEach(card => {
    const cardCategory = card.getAttribute('data-category');
    card.style.display = (category === "all" || cardCategory === category) ? "flex" : "none";
  });
}

// Filter posts by category
document.querySelector('.post-categories').addEventListener('click', (e) => {
  if (e.target.classList.contains('post-category')) {
    const category = e.target.getAttribute('data-category');
    filterPosts(category);
    document.querySelectorAll('.post-categories .post-category').forEach(btn => {
      btn.classList.remove('active');
    });
    e.target.classList.add('active');
  }
});

function filterPosts(category) {
  document.querySelectorAll('.post-card').forEach(post => {
    const postCategory = post.getAttribute('data-category');
    post.style.display = (category === "all" || postCategory === category) ? "flex" : "none";
  });
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', () => {
  filterBusinesses("all");
  filterPosts("all");
});
</script>
</body>
</html>