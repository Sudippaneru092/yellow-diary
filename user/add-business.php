<?php
session_start();
include "../admin/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

$userId = $_SESSION['user_id'];
$success = $error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $city = mysqli_real_escape_string($conn, $_POST['city']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $website = mysqli_real_escape_string($conn, $_POST['website']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);

  // Optional image upload
  $imagePath = '';
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageName = time() . '_' . basename($_FILES['image']['name']);
    $targetDir = "../admin/uploads/";
    $targetFile = $targetDir . $imageName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
      $imagePath = "uploads/" . $imageName;
    }
  }

  // Insert into DB
  $sql = "INSERT INTO businesses (user_id, name, category, city, address, phone, email, website, description, image, status)
          VALUES ('$userId', '$name', '$category', '$city', '$address', '$phone', '$email', '$website', '$description', '$imagePath', 'pending')";

  if (mysqli_query($conn, $sql)) {
    $success = "Business submitted successfully! Awaiting admin approval.";
  } else {
    $error = "Error: " . mysqli_error($conn);
  }
}
?>

<?php include "includes/user_header.php"; ?>
<?php include "includes/user_sidebar.php"; ?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Add New Business</h1>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
      <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>

      <div class="card">
        <div class="card-body">
          <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <label>Business Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Category</label>
              <input type="text" name="category" class="form-control" required>
            </div>

            <div class="form-group">
              <label>City</label>
              <input type="text" name="city" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Full Address</label>
              <textarea name="address" class="form-control" required></textarea>
            </div>

            <div class="form-group">
              <label>Phone</label>
              <input type="text" name="phone" class="form-control">
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" class="form-control">
            </div>

            <div class="form-group">
              <label>Website</label>
              <input type="url" name="website" class="form-control">
            </div>

            <div class="form-group">
              <label>Description</label>
              <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-group">
              <label>Upload Image (optional)</label>
              <input type="file" name="image" class="form-control-file">
            </div>

            <button type="submit" class="btn btn-primary">Submit Business</button>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include "includes/user_footer.php"; ?>
