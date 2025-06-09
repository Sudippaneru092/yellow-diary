<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid ID');
}

$id = (int) $_GET['id'];
$business = $conn->query("SELECT * FROM businesses WHERE id = $id")->fetch_assoc();

if (!$business) {
    die('Business not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);

    $conn->query("UPDATE businesses SET 
      name = '$name', 
      category = '$category', 
      location = '$location', 
      description = '$description'
      WHERE id = $id");

    header("Location: manage-business.php");
    exit;
}
?>

<?php include 'includes/header.php'; include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
  <section class="content-header"><h1>Edit Business</h1></section>
  <section class="content">
    <form method="POST">
      <div class="form-group">
        <label>Name:</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($business['name']) ?>" required>
      </div>
      <div class="form-group">
        <label>Category:</label>
        <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($business['category']) ?>" required>
      </div>
      <div class="form-group">
        <label>Location:</label>
        <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($business['location']) ?>" required>
      </div>
      <div class="form-group">
        <label>Description:</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($business['description']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-success">Update Business</button>
    </form>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
