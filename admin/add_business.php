<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

$name = $category = $location = $description = $url = "";
$imagePath = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $url = trim($_POST['url']);

    // Validate required fields
    if (empty($name)) {
        $errors[] = "Business name is required.";
    }
    if (empty($category)) {
        $errors[] = "Category is required.";
    }
    if (empty($location)) {
        $errors[] = "Location is required.";
    }

    // Handle image upload if any
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        } else {
            $uploadDir = "uploads/business_images/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $imageName = time() . "_" . basename($_FILES["image"]["name"]);
            $targetFile = $uploadDir . $imageName;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // If no errors, insert into DB
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO businesses (name, category, location, description, image, url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $category, $location, $description, $imagePath, $url);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Business added successfully.";
            header("Location: manage_business.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Add New Business</h1>
    <a href="manage_business.php" class="btn btn-secondary mb-3">Back to Manage Businesses</a>
  </section>

  <section class="content">
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="add_business.php" method="POST" enctype="multipart/form-data" class="form">
      <div class="form-group mb-3">
        <label for="name">Business Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
      </div>

    <div class="form-group mb-3">
        <label for="category">Category <span class="text-danger">*</span></label>
        <select name="category" id="category" class="form-control" required>
          <option value="">Select Category</option>
          <?php
          $categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
          while ($row = $categories->fetch_assoc()):
          ?>
            <option value="<?= htmlspecialchars($row['name']) ?>" <?= ($row['name'] == $category) ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
    </div>


      <div class="form-group mb-3">
        <label for="location">Location <span class="text-danger">*</span></label>
        <input type="text" name="location" id="location" class="form-control" value="<?= htmlspecialchars($location) ?>" required>
      </div>

      <div class="form-group mb-3">
        <label for="description">Description</label>
        <textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($description) ?></textarea>
      </div>

      <div class="form-group mb-3">
        <label for="url">Website URL</label>
        <input type="url" name="url" id="url" class="form-control" value="<?= htmlspecialchars($url) ?>" placeholder="https://example.com">
      </div>

      <div class="form-group mb-3">
        <label for="image">Upload Image</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
      </div>

      <button type="submit" class="btn btn-primary">Add Business</button>
    </form>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
