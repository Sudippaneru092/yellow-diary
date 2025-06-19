<!-- Header -->
<header class="header">
  <div class="navbar">
    <h1>Yellow Diary</h1>
    <nav class="nav">
      <a href="index.php">Home</a>
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