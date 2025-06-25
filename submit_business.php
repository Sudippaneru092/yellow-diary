<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "admin/db.php";

$errors = [];
$success = false;
$location = '';
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
      $uploadDir = "yellow-diary/admin/uploads/business_images/";
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
      (name, category, location, description, image, url, created_at, slug, status)
      VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, 'pending')");

    $stmt->bind_param("sssssss",
      $fields['name'],
      $fields['category'],
      $location,
      $fields['description'],
      $imagePath,
      $fields['url'],
      $slug,
      
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
  <style>
    /* Base Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', sans-serif;
  background: #f8f9fa;
  color: #333;
  padding: 30px;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #444;
}

form {
  max-width: 600px;
  margin: 0 auto;
  padding: 25px 30px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
}

label {
  display: block;
  margin-top: 15px;
  font-weight: 600;
}

input[type="text"],
input[type="url"],
input[type="file"],
select,
textarea {
  width: 100%;
  padding: 10px 12px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 15px;
  background: #fff;
}

textarea {
  resize: vertical;
}

button[type="submit"] {
  margin-top: 20px;
  width: 100%;
  padding: 12px;
  background: #ffc107;
  border: none;
  font-size: 16px;
  font-weight: bold;
  color: #222;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.3s ease;
}

button[type="submit"]:hover {
  background: #e0a800;
}

.success,
.error {
  max-width: 600px;
  margin: 0 auto 20px;
  padding: 10px 15px;
  border-radius: 5px;
  font-weight: 500;
}

.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

ul {
  list-style: disc;
  margin-left: 40px;
  margin-top: 10px;
}

/* Responsive Design */
@media (max-width: 640px) {
  body {
    padding: 15px;
  }

  form {
    padding: 20px;
  }

  button[type="submit"] {
    font-size: 15px;
  }
}

  </style>

</head>
<body>
 <h2>Submit Your Business</h2>

  <?php if ($success): ?>
    <div class="success">Business submitted successfully. Pending approval.</div>
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
    <select name="city" id="citySelect" required>
      <option value="">Select City</option>
      <?php
        $cities = $conn->query("SELECT id, name FROM cities ORDER BY name");
        while ($row = $cities->fetch_assoc()):
      ?>
        <option value="<?= $row['id'] ?>" <?= $row['name'] == $fields['city'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
      <?php endwhile; ?>
    </select>

    <label>Area*</label>
    <select name="area" id="areaSelect" required>
      <option value="">Select Area</option>
    </select>

    <label>Description</label>
    <textarea name="description"><?= htmlspecialchars($fields['description']) ?></textarea>

    <label>Website</label>
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
    document.getElementById('citySelect').addEventListener('change', function() {
      const cityId = this.value;
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

    window.addEventListener('DOMContentLoaded', function() {
      if (document.getElementById('citySelect').value) {
        document.getElementById('citySelect').dispatchEvent(new Event('change'));
      }
    });
  </script>
</body>
</html>
