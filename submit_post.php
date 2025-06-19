<?php
include "admin/db.php";
session_start();

// Redirect if business not logged in
if(!isset($_SESSION['business_id'])) {
  header("Location: business_login.php");
  exit;
}

$business_id = $_SESSION['business_id'];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $location = mysqli_real_escape_string($conn, $_POST['location']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);
  
  // Image upload handling
  $image = null;
  if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "admin/uploads/posts/";
    $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    
    // Validate image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
      $image = uniqid() . '.' . $imageFileType;
      move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image);
    }
  }
  
  // Insert post
  $query = "INSERT INTO posts (business_id, title, category, location, description, image, status) 
            VALUES ('$business_id', '$title', '$category', '$location', '$description', '$image', 'pending')";
  
  if(mysqli_query($conn, $query)) {
    $success = "Post submitted successfully! It will appear after approval.";
  } else {
    $error = "Error submitting post: " . mysqli_error($conn);
  }
}

// Get categories for dropdown
$categoriesQuery = "SELECT DISTINCT category FROM businesses WHERE status = 'approved'";
$categoriesResult = mysqli_query($conn, $categoriesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Business Post</title>
  <link rel="stylesheet" href="styles.css">
  <!-- Include Font Awesome for icons if needed -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

  <?php include 'header.php'; ?>

  <main class="submit-post-container">
    <h1>Submit Business Update</h1>
    
    <?php if(isset($success)): ?>
      <div class="alert success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
      <div class="alert error"><?= $error ?></div>
    <?php endif; ?>
    
    <!-- Your form code goes here -->
    <form action="submit_post.php" method="POST" enctype="multipart/form-data" class="post-form">
      <div class="form-group">
        <label for="title">Post Title*</label>
        <input type="text" id="title" name="title" required>
      </div>
      
      <div class="form-group">
        <label for="category">Category*</label>
        <select id="category" name="category" required>
          <option value="">Select Category</option>
          <?php while($cat = mysqli_fetch_assoc($categoriesResult)): ?>
            <option value="<?= htmlspecialchars($cat['category']) ?>">
              <?= htmlspecialchars($cat['category']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      
      <div class="form-group">
        <label for="location">Location*</label>
        <input type="text" id="location" name="location" required>
      </div>
      
      <div class="form-group">
        <label for="description">Post Content*</label>
        <textarea id="description" name="description" rows="6" required></textarea>
      </div>
      
      <div class="form-group">
        <label for="image">Featured Image (Optional)</label>
        <input type="file" id="image" name="image" accept="image/*">
        <small>Max size: 2MB (JPEG, PNG, GIF)</small>
      </div>
      
      <button type="submit" class="submit-btn">Submit Post</button>
    </form>
  </main>

  <?php include 'footer.php'; ?>

</body>
</html>