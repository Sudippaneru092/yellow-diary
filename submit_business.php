<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "admin/db.php";

$errors = [];
$success = false;

// Initial values
$fields = [
  'name' => '',
  'category' => '',
  'city' => '',
  'area' => '',
  'description' => '',
  'url' => '',
  'latitude' => '',
  'longitude' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  foreach ($fields as $key => $val) {
    $fields[$key] = trim($_POST[$key] ?? '');
  }

  // Validate required fields
  if (empty($fields['name'])) $errors[] = "Business name is required.";
  if (empty($fields['category'])) $errors[] = "Category is required.";
  if (empty($fields['city'])) $errors[] = "City is required.";
  if (empty($fields['area'])) $errors[] = "Area is required.";

  if (!empty($fields['url']) && !filter_var($fields['url'], FILTER_VALIDATE_URL)) {
    $errors[] = "Invalid URL format.";
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
        $imagePath = $targetFile;
      } else {
        $errors[] = "Failed to upload image.";
      }
    }
  }

  if (empty($errors)) {
    $location = $fields['city'] . ", " . $fields['area'];
    $slug = strtolower(preg_replace("/[^a-z0-9]+/", "-", $fields['name']));

    $stmt = $conn->prepare("INSERT INTO businesses 
      (name, category, location, description, image, url, created_at, slug, status, latitude, longitude)
      VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, 'pending', ?, ?)");

    $stmt->bind_param("ssssssssdd",
      $fields['name'],
      $fields['category'],
      $location,
      $fields['description'],
      $imagePath,
      $fields['url'],
      $slug,
      $fields['latitude'],
      $fields['longitude']
    );

    if ($stmt->execute()) {
      $success = true;
      $fields = array_map(fn() => '', $fields);
    } else {
      $errors[] = "Database error: " . $stmt->error;
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Business</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h2>Submit Your Business</h2>

  <?php if ($success): ?>
    <div style="color: green;">Business submitted successfully. Pending approval.</div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <ul style="color: red;">
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Business Name*</label>
    <input type="text" name="name" value="<?= htmlspecialchars($fields['name']) ?>" required><br>

    <label>Category*</label>
    <select name="category" required>
      <option value="">Select Category</option>
      <?php
        $result = $conn->query("SELECT name FROM category ORDER BY name");
        while ($row = $result->fetch_assoc()):
      ?>
        <option value="<?= $row['name'] ?>" <?= $row['name'] == $fields['category'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
      <?php endwhile; ?>
    </select><br>

    <label>City*</label>
    <select name="city" id="city" required>
      <option value="">Select City</option>
      <?php
        $cities = $conn->query("SELECT id, name FROM cities ORDER BY name");
        while ($row = $cities->fetch_assoc()):
      ?>
        <option value="<?= $row['name'] ?>" <?= $row['name'] == $fields['city'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
      <?php endwhile; ?>
    </select><br>

    <label>Area*</label>
    <select name="area" id="area" required>
      <option value="">Select Area</option>
                <?php
               $areas = $conn->query("SELECT id, name FROM areas ORDER BY name");
                while ($row = $areas->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $location) ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['name']) ?>
            </option>
          <?php endwhile; ?>
          ?>
    </select><br>

    <label>Description</label>
    <textarea name="description"><?= htmlspecialchars($fields['description']) ?></textarea><br>

    <label>Website</label>
    <input type="url" name="url" value="<?= htmlspecialchars($fields['url']) ?>"><br>

    <label>Latitude</label>
    <input type="text" name="latitude" value="<?= htmlspecialchars($fields['latitude']) ?>"><br>

    <label>Longitude</label>
    <input type="text" name="longitude" value="<?= htmlspecialchars($fields['longitude']) ?>"><br>

    <label>Upload Image</label>
    <input type="file" name="image" accept="image/*"><br><br>

    <button type="submit">Submit</button>
  </form>

<script>
  document.getElementById('city').addEventListener('change', function () {
    const cityName = this.value;
    const areaSelect = document.getElementById('area');
    areaSelect.innerHTML = '<option value="">Loading...</option>';

    fetch('get_areas.php?city=' + encodeURIComponent(cityName))
      .then(response => response.json())
      .then(data => {
        areaSelect.innerHTML = '<option value="">Select Area</option>';
        data.forEach(area => {
          const opt = document.createElement('option');
          opt.value = area.name;
          opt.textContent = area.name;
          areaSelect.appendChild(opt);
        });
      });
  });
</script>
</body>
</html>
