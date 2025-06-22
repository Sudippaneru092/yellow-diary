<?php
include "admin/db.php";
session_start();

// Redirect if business not logged in
if(!isset($_SESSION['business_id'])) {
  header("Location: business_login.php");
  exit;
}

$business_id = $_SESSION['business_id'];

// Define allowed categories
$allowed_categories = ['Event', 'Job Vacancy', 'Announcement', 'Advertising', 'Others'];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $location = mysqli_real_escape_string($conn, $_POST['location']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);

  // Validate category
  if (!in_array($category, $allowed_categories)) {
    $error = "Invalid category selected.";
  } else {
    // Handle image upload
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

    // Insert post into database
    $query = "INSERT INTO posts (business_id, title, category, location, description, image, status) 
              VALUES ('$business_id', '$title', '$category', '$location', '$description', '$image', 'pending')";

    if(mysqli_query($conn, $query)) {
      $success = "Post submitted successfully! It will appear after approval.";
    } else {
      $error = "Error submitting post: " . mysqli_error($conn);
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Submit Business Post</title>
  <link rel="stylesheet" href="styles.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <style>
    /* ===== Submit Business Post ===== */
.submit-post-container {
  max-width: 800px;
  margin: 3rem auto;
  padding: 2rem;
  background: #ffffff;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-md);
  animation: fadeIn 0.5s ease;
}

.submit-post-container h1 {
  text-align: center;
  margin-bottom: 2rem;
  font-size: 2rem;
  color: var(--dark-bg);
}

.alert {
  padding: 1rem;
  border-radius: 6px;
  margin-bottom: 1.5rem;
  font-weight: 600;
}

.alert.success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert.error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.post-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--text-dark);
}

.form-group input[type="text"],
.form-group select,
.form-group textarea,
.form-group input[type="file"] {
  padding: 1rem;
  font-size: 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  background: #fafafa;
  transition: var(--transition);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: var(--primary-color);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
  outline: none;
}

.form-group small {
  font-size: 0.85rem;
  color: var(--text-muted);
  margin-top: 0.25rem;
}

.submit-btn {
  align-self: center;
  padding: 0.9rem 2rem;
  background: var(--primary-color);
  color: var(--text-dark);
  font-weight: 700;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.submit-btn:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(255, 193, 7, 0.4);
}

/* Mobile Responsive */
@media (max-width: 576px) {
  .submit-post-container {
    padding: 1.25rem;
    margin: 2rem 1rem;
  }

  .submit-post-container h1 {
    font-size: 1.5rem;
  }
}
  box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2);
  }
//   transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }
  .post-form textarea {
    height: 150px;
    resize: vertical;
  }
  .submit-btn {
    background: #fbbf24; /* bright yellow */
    color: #444; /* dark text */
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
  }
  .submit-btn:hover {
    background: #e0a800; /* darker yellow on hover */
    transform: translateY(-2px);
  }



  </style>
</head>
<body>

  <?php include 'header.php'; ?>

  <main class="submit-post-container">
    <h1>Submit Business Update</h1>

    <?php if(isset($success)): ?>
      <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
      <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="submit_post.php" method="POST" enctype="multipart/form-data" class="post-form">
      <div class="form-group">
        <label for="title">Post Title*</label>
        <input type="text" id="title" name="title" required>
      </div>

     
      <div class="form-group">
        <label for="category">Category*</label>
        <select id="category" name="category" required onchange="toggleCustomCategory(this.value)">
          <option value="">Select Category</option>
          <?php foreach($allowed_categories as $cat): ?>
            <option value="<?= $cat ?>"><?= $cat ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group hidden" id="custom-category-group">
        <label for="custom_category">Specify Other Category*</label>
        <input type="text" id="custom_category" name="custom_category">
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
