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
  <link rel="stylesheet" href="main.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
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