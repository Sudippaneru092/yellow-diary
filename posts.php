<?php
include "admin/db.php";
$page_title = "All Business Updates";

// Pagination setup
$per_page = 9;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $per_page;

// Get total count for pagination
$total_query = "SELECT COUNT(*) FROM posts WHERE status = 'approved'";
$total_result = mysqli_query($conn, $total_query);
$total_rows = mysqli_fetch_row($total_result)[0];
$total_pages = ceil($total_rows / $per_page);

// Get paginated posts
$query = "SELECT p.*, b.name as business_name 
          FROM posts p
          JOIN businesses b ON p.business_id = b.id
          WHERE p.status = 'approved'
          ORDER BY p.created_at DESC
          LIMIT $per_page OFFSET $offset";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?> | Yellow Diary</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /* ===== posts.php Specific Styles ===== */
.main-content h1 {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 2.5rem;
  color: var(--dark-bg);
  position: relative;
}

.main-content h1::after {
  content: '';
  display: block;
  width: 80px;
  height: 4px;
  background: var(--primary-color);
  margin: 1rem auto 0;
  border-radius: 2px;
}

/* Posts Grid */
.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

/* Post Card */
.post-card {
  background: #fff;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: var(--shadow-md);
  transition: var(--transition);
  display: flex;
  flex-direction: column;
  position: relative;
  border: 1px solid #ececec;
}

.post-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
  border-color: rgba(255, 193, 7, 0.3);
}

/* Image */
.post-image {
  position: relative;
  height: 220px;
  overflow: hidden;
  aspect-ratio: 16/9;
}

.post-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.post-card:hover .post-image img {
  transform: scale(1.05);
}

/* Badge */
.post-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: var(--primary-color);
  color: var(--text-dark);
  padding: 0.3rem 0.9rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  box-shadow: var(--shadow-sm);
  z-index: 2;
}

/* Content */
.post-content {
  padding: 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.post-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
  font-size: 0.85rem;
  color: var(--text-muted);
  flex-wrap: wrap;
  gap: 0.5rem;
}

.post-content h2 {
  font-size: 1.3rem;
  margin-bottom: 0.75rem;
  color: var(--dark-bg);
  line-height: 1.4;
}

.post-excerpt {
  color: #555;
  margin-bottom: 1rem;
  line-height: 1.6;
}

.post-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-muted);
  font-size: 0.9rem;
  margin-bottom: 1rem;
}

.post-location i {
  color: var(--secondary-color);
}

/* Read More Link */
.read-more {
  display: inline-block;
  color: var(--primary-color);
  font-weight: 600;
  transition: var(--transition);
  text-decoration: none;
  margin-top: auto;
}

.read-more:hover {
  color: var(--primary-dark);
  text-decoration: underline;
}

/* No Posts */
.no-posts {
  text-align: center;
  padding: 3rem;
  background: #fff;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-sm);
  color: var(--text-muted);
  font-size: 1.2rem;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin: 2rem 0;
  flex-wrap: wrap;
}

.page-link {
  padding: 0.5rem 1.25rem;
  background: #f0f0f0;
  color: var(--text-dark);
  border-radius: 4px;
  text-decoration: none;
  font-weight: 600;
  transition: var(--transition);
}

.page-link:hover,
.page-link.active {
  background: var(--primary-color);
  color: var(--text-dark);
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .posts-grid {
    grid-template-columns: 1fr;
  }

  .main-content h1 {
    font-size: 2rem;
  }

  .post-content h2 {
    font-size: 1.2rem;
  }
}


  </style>
</head>
<body>

  <?php include 'header.php'; ?>

  <main class="main-content">
    <div class="container">
      <h1>Business News & Updates</h1>
      
      <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="posts-grid">
          <?php while($post = mysqli_fetch_assoc($result)): 
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
                <h2><?= htmlspecialchars($post['title']) ?></h2>
                <p class="post-excerpt"><?= htmlspecialchars(substr($post['description'], 0, 150)) ?>...</p>
                <div class="post-location">
                  <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($post['location']) ?>
                </div>
                <a href="post.php?id=<?= $post['id'] ?>" class="read-more">Read More</a>
              </div>
            </article>
          <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
          <?php if($current_page > 1): ?>
            <a href="posts.php?page=<?= $current_page - 1 ?>" class="page-link">&laquo; Previous</a>
          <?php endif; ?>
          
          <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="posts.php?page=<?= $i ?>" class="page-link <?= $i == $current_page ? 'active' : '' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
          
          <?php if($current_page < $total_pages): ?>
            <a href="posts.php?page=<?= $current_page + 1 ?>" class="page-link">Next &raquo;</a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <p class="no-posts">No business updates available yet. Check back soon!</p>
      <?php endif; ?>
    </div>
  </main>

  <?php include 'footer.php'; ?>

</body>
</html>