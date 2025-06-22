<?php
include "admin/db.php";

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: posts.php");
  exit;
}

$post_id = (int)$_GET['id'];

// Get the post
$query = "SELECT p.*, b.name as business_name, b.phone, b.email, b.url 
          FROM posts p
          JOIN businesses b ON p.business_id = b.id
          WHERE p.id = $post_id AND p.status = 'approved'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
  header("Location: posts.php");
  exit;
}

$post = mysqli_fetch_assoc($result);
$page_title = htmlspecialchars($post['title']);

// Increment view count
mysqli_query($conn, "UPDATE posts SET views = views + 1 WHERE id = $post_id");

// Get related posts
$related_query = "SELECT p.id, p.title, p.image, p.created_at 
                 FROM posts p
                 WHERE p.category = '{$post['category']}' 
                 AND p.id != $post_id
                 AND p.status = 'approved'
                 ORDER BY p.created_at DESC
                 LIMIT 3";
$related_result = mysqli_query($conn, $related_query);

// Format the created_at date
$post_date = date('F j, Y', strtotime($post['created_at']));
$postImage = !empty($post['image']) ? 'admin/uploads/posts/'.$post['image'] : 'images/default-post.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page_title ?> | Yellow Diary</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /* ===== Single Post Styles ===== */
.single-post .container {
  max-width: 900px;
}

.breadcrumb {
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  color: var(--text-muted);
}

.breadcrumb a {
  color: var(--secondary-color);
  text-decoration: none;
}

.post-full {
  background: #fff;
  padding: 2rem;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-sm);
}

.post-header {
  margin-bottom: 2rem;
  text-align: center;
}

.post-header h1 {
  font-size: 2rem;
  margin: 1rem 0;
}

.post-meta {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  flex-wrap: wrap;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
  color: var(--text-muted);
}

.post-business {
  font-weight: 600;
  color: var(--secondary-color);
}

.post-category-location {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 1rem;
}

.post-image-full {
  margin: 2rem 0;
}

.post-image-full img {
  width: 100%;
  max-height: 500px;
  object-fit: cover;
  border-radius: var(--border-radius);
}

.post-content-full {
  line-height: 1.8;
  font-size: 1.1rem;
}

.post-content-full p {
  margin-bottom: 1.5rem;
}

.business-contact {
  margin: 3rem 0;
  padding: 2rem;
  background: #f8f9fa;
  border-radius: var(--border-radius);
}

.business-contact h3 {
  margin-bottom: 1.5rem;
  color: var(--secondary-color);
}

.contact-details p {
  margin-bottom: 0.8rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.contact-details a {
  color: var(--text-dark);
  transition: var(--transition);
  text-decoration: none;
}

.contact-details a:hover {
  color: var(--secondary-color);
}

/* Related Posts */
.related-posts {
  margin-top: 4rem;
}

.related-posts h2 {
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  color: var(--secondary-color);
}

.related-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.related-card {
  transition: var(--transition);
}

.related-card:hover {
  transform: translateY(-5px);
}

.related-image {
  height: 150px;
  overflow: hidden;
  border-radius: var(--border-radius);
  margin-bottom: 0.5rem;
  aspect-ratio: 16/9;
}

.related-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: var(--transition);
}

.related-card:hover .related-image img {
  transform: scale(1.05);
}

.related-card h3 {
  font-size: 1.1rem;
  margin-bottom: 0.3rem;
}

.related-date {
  font-size: 0.8rem;
  color: var(--text-muted);
}
  </style>
</head>
<body>

  <?php include 'header.php'; ?>

  <main class="main-content single-post">
    <div class="container">
      <article class="post-full">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
          <a href="/">Home</a> &raquo;
          <a href="posts.php">Updates</a> &raquo;
          <span><?= htmlspecialchars($post['title']) ?></span>
        </nav>

        <header class="post-header">
          <div class="post-meta">
            <span class="post-business"><?= htmlspecialchars($post['business_name']) ?></span>
            <span class="post-date"><?= $post_date ?></span>
            <span class="post-views"><i class="far fa-eye"></i> <?= number_format($post['views']) ?> views</span>
          </div>
          <h1><?= htmlspecialchars($post['title']) ?></h1>
          <div class="post-category-location">
            <span class="post-badge"><?= htmlspecialchars($post['category']) ?></span>
            <span class="post-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($post['location']) ?></span>
          </div>
        </header>

        <div class="post-image-full">
          <img src="<?= $postImage ?>" alt="<?= htmlspecialchars($post['title']) ?>">
        </div>

        <div class="post-content-full">
          <?= nl2br(htmlspecialchars($post['description'])) ?>
        </div>

        <!-- Business Contact Info -->
        <div class="business-contact">
          <h3>About <?= htmlspecialchars($post['business_name']) ?></h3>
          <div class="contact-details">
            <?php if(!empty($post['phone'])): ?>
              <p><i class="fas fa-phone"></i> <a href="tel:<?= htmlspecialchars($post['phone']) ?>"><?= htmlspecialchars($post['phone']) ?></a></p>
            <?php endif; ?>
            <?php if(!empty($post['email'])): ?>
              <p><i class="fas fa-envelope"></i> <a href="mailto:<?= htmlspecialchars($post['email']) ?>"><?= htmlspecialchars($post['email']) ?></a></p>
            <?php endif; ?>
            <?php if(!empty($post['url'])): ?>
              <p><i class="fas fa-globe"></i> <a href="<?= htmlspecialchars($post['url']) ?>" target="_blank">Visit Website</a></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Related Posts -->
        <?php if(mysqli_num_rows($related_result) > 0): ?>
          <section class="related-posts">
            <h2>Related Updates</h2>
            <div class="related-grid">
              <?php while($related = mysqli_fetch_assoc($related_result)): 
                $relatedImage = !empty($related['image']) ? 'admin/uploads/posts/'.$related['image'] : 'images/default-post.jpg';
              ?>
                <article class="related-card">
                  <a href="post.php?id=<?= $related['id'] ?>">
                    <div class="related-image">
                      <img src="<?= $relatedImage ?>" alt="<?= htmlspecialchars($related['title']) ?>">
                    </div>
                    <h3><?= htmlspecialchars($related['title']) ?></h3>
                    <p class="related-date"><?= date('M d, Y', strtotime($related['created_at'])) ?></p>
                  </a>
                </article>
              <?php endwhile; ?>
            </div>
          </section>
        <?php endif; ?>
      </article>
    </div>
  </main>

  <?php include 'footer.php'; ?>

</body>
</html>