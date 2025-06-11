<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid business ID.");
}

$id = intval($_GET['id']);

// Fetch business info
$result = $conn->query("SELECT * FROM businesses WHERE id = $id");
if ($result->num_rows == 0) {
    die("Business not found.");
}

$business = $result->fetch_assoc();
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Edit Business</h1>
  </section>

  <section class="content">
    <form action="update_business.php" method="post">
      <input type="hidden" name="id" value="<?= $business['id'] ?>">

      <div class="form-group">
        <label for="name">Business Name</label>
        <input type="text" name="name" id="name" class="form-control" required
               value="<?= htmlspecialchars($business['name']) ?>">
      </div>

      <div class="form-group">
        <label for="category">Category</label>
        <input type="text" name="category" id="category" class="form-control" required
               value="<?= htmlspecialchars($business['category']) ?>">
      </div>

      <div class="form-group">
        <label for="location">Location</label>
        <input type="text" name="location" id="location" class="form-control" required
               value="<?= htmlspecialchars($business['location']) ?>">
      </div>

      <div class="form-group">
        <label for="url">Website URL</label>
        <input type="url" name="url" id="url" class="form-control"
               value="<?= htmlspecialchars($business['url']) ?>">
      </div>

      <button type="submit" class="btn btn-primary">Update Business</button>
      <a href="manage_business.php" class="btn btn-secondary">Cancel</a>
    </form>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
