<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);

    if ($name && $category && $location) {
        $sql = "INSERT INTO businesses (name, category, location, description) 
                VALUES ('$name', '$category', '$location', '$description')";
        if ($conn->query($sql)) {
            $success = "Business added successfully!";
        } else {
            $error = "Failed to add business.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Add New Business</h1>
  </section>

  <section class="content">
    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Business Name *</label>
        <input type="text" name="name" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Category *</label>
        <input type="text" name="category" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Location *</label>
        <input type="text" name="location" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="4" class="form-control"></textarea>
      </div>

      <button type="submit" class="btn btn-success">Add Business</button>
      <a href="manage-business.php" class="btn btn-secondary">Back</a>
    </form>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
