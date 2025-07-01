<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "admin/db.php";

$errors = [];
$success = false;
$location = '';
$exists = false;

$fields = [
  'name' => '',
  'category' => '',
  'city' => '',
  'area' => '',
  'description' => '',
  'url' => '',
  'email' => '',
  'phone' => '',
  'latitude' => '',
  'longitude' => ''
];

$password = '';
$confirmPassword = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  foreach ($fields as $key => $val) {
    $fields[$key] = trim($_POST[$key] ?? '');
  }

  $password = trim($_POST['password'] ?? '');
  $confirmPassword = trim($_POST['confirm_password'] ?? '');

  if (empty($fields['name'])) $errors[] = "Business name is required.";
  if (empty($fields['category'])) $errors[] = "Category is required.";
  if (empty($fields['city'])) $errors[] = "City is required.";
  if (empty($fields['area'])) $errors[] = "Area is required.";
  if (empty($fields['phone'])) $errors[] = "Phone number is required.";
  if (empty($fields['email']) || !filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email address is required.";
  }
  if (!empty($fields['url']) && !filter_var($fields['url'], FILTER_VALIDATE_URL)) {
    $errors[] = "Invalid URL format.";
  }

  if (empty($password) || empty($confirmPassword)) {
    $errors[] = "Password and Confirm Password are required.";
  } elseif ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
  } elseif (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters long.";
  }

  // Handle image upload
  $imagePath = "";
  if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
      $errors[] = "Only JPG, PNG, and GIF images are allowed.";
    } else {
      $uploadDir = "admin/uploads/business_images/";
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
      $imageName = time() . "_" . basename($_FILES["image"]["name"]);
      $targetFile = $uploadDir . $imageName;
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $imagePath = "uploads/business_images/" . $imageName;
      } else {
        $errors[] = "Failed to upload image.";
      }
    }
  }

  if (empty($errors)) {
    $location = $fields['city'] . ", " . $fields['area'];
    $slug = strtolower(preg_replace("/[^a-z0-9]+/", "-", $fields['name']));
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkStmt = $conn->prepare("SELECT id FROM businesses WHERE name = ? AND location = ?");
    $checkStmt->bind_param("ss", $fields['name'], $location);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
      $exists = true;
    } else {
      $stmt = $conn->prepare("INSERT INTO businesses 
        (name, category, location, description, image, url, email, phone, latitude, longitude, created_at, slug, status, password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'pending', ?)");

      $stmt->bind_param("ssssssssssss", 
        $fields['name'],
        $fields['category'],
        $location,
        $fields['description'],
        $imagePath,
        $fields['url'],
        $fields['email'],
        $fields['phone'],
        $fields['latitude'],
        $fields['longitude'],
        $slug,
        $hashedPassword
      );

      if ($stmt->execute()) {
        $success = true;
        $fields = array_map(fn() => '', $fields);
        $password = $confirmPassword = '';
      } else {
        $errors[] = "Database error: " . $stmt->error;
      }
      $stmt->close();
    }
    $checkStmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Submit Business</title>
  <link rel="stylesheet" href="main.css">
</head>
<body>
<?php include "header.php"; ?>

<h2>Submit Your Business</h2>

<?php if ($success): ?>
  <div class="success">Business submitted successfully. Pending approval.</div>
<?php endif; ?>

<?php if ($exists): ?>
  <script>alert("This business already exists in our directory.");</script>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <div class="error">
    <ul>
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
  <label>Business Name*</label>
  <input type="text" name="name" value="<?= htmlspecialchars($fields['name']) ?>" required>

  <label>Category*</label>
  <select name="category" required>
    <option value="">Select Category</option>
    <?php
      $result = $conn->query("SELECT name FROM category ORDER BY name");
      while ($row = $result->fetch_assoc()):
    ?>
      <option value="<?= $row['name'] ?>" <?= $row['name'] == $fields['category'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
    <?php endwhile; ?>
  </select>

  <label>City*</label>
  <select name="city_id" id="citySelect" required>
    <option value="">Select City</option>
    <?php
      $cities = $conn->query("SELECT id, name FROM cities ORDER BY name");
      while ($row = $cities->fetch_assoc()):
    ?>
      <option value="<?= $row['id'] ?>" <?= $row['name'] == $fields['city'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
    <?php endwhile; ?>
  </select>
  <input type="hidden" name="city" id="cityNameHidden" value="<?= htmlspecialchars($fields['city']) ?>">

  <label>Area*</label>
  <select name="area" id="areaSelect" required>
    <option value="">Select Area</option>
  </select>

  <label>Email*</label>
  <input type="email" name="email" value="<?= htmlspecialchars($fields['email']) ?>" required>

  <label>Phone Number*</label>
  <input type="text" name="phone" value="<?= htmlspecialchars($fields['phone']) ?>" required>

  <label>Password*</label>
  <input type="password" name="password" required>

  <label>Confirm Password*</label>
  <input type="password" name="confirm_password" required>

  <label>Description</label>
  <textarea name="description"><?= htmlspecialchars($fields['description']) ?></textarea>

  <label>Website (Optional)</label>
  <input type="url" name="url" value="<?= htmlspecialchars($fields['url']) ?>">

  <label>Latitude</label>
  <input type="text" name="latitude" value="<?= htmlspecialchars($fields['latitude']) ?>">

  <label>Longitude</label>
  <input type="text" name="longitude" value="<?= htmlspecialchars($fields['longitude']) ?>">

  <label>Upload Image</label>
  <input type="file" name="image" accept="image/*">

  <button type="submit">Submit</button>
</form>

<script>
  document.getElementById('citySelect').addEventListener('change', function () {
    const cityId = this.value;
    const cityName = this.options[this.selectedIndex].text;
    document.getElementById('cityNameHidden').value = cityName;

    const areaSelect = document.getElementById('areaSelect');
    areaSelect.innerHTML = '<option value="">Loading...</option>';

    fetch('get_areas.php?city_id=' + cityId)
      .then(response => response.json())
      .then(data => {
        let options = '<option value="">Select Area</option>';
        data.forEach(area => {
          options += `<option value="${area.name}">${area.name}</option>`;
        });
        areaSelect.innerHTML = options;
      })
      .catch(err => {
        areaSelect.innerHTML = '<option value="">Error loading areas</option>';
        console.error(err);
      });
  });

  window.addEventListener('DOMContentLoaded', function () {
    const citySelect = document.getElementById('citySelect');
    if (citySelect.value) {
      citySelect.dispatchEvent(new Event('change'));
    }
  });
</script>

<footer>
  &copy; 2025 Yellow Diary. All rights reserved.
</footer>
</body>
</html>
<?php include "footer.php"; ?>