<?php

session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'db.php'; // only DB needed before logic

// Initialize variables
$name = $category = $location = $areas = $description = $url = "";
$imagePath = "";
$errors = [];
$exists = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $location = trim($_POST['city_id'] ?? ''); // matches <select name="city_id">
    $areas    = trim($_POST['area'] ?? '');    // matches <select name="area">

    $description = trim($_POST['description']);
    $url = trim($_POST['url']);

    if (empty($name)) $errors[] = "Business name is required.";
    if (empty($category)) $errors[] = "Category is required.";
    if (empty($location)) $errors[] = "City is required.";
    if (empty($areas)) $errors[] = "Area is required.";

    if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid URL format.";
    }

    // Handle image
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        } else {
            $uploadDir = "uploads/business_images/";
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
        // Get city name
        $stmtCity = $conn->prepare("SELECT name FROM cities WHERE id = ?");
        $stmtCity->bind_param("i", $location);
        $stmtCity->execute();
        $resultCity = $stmtCity->get_result();
        $cityName = $resultCity->fetch_assoc()['name'] ?? '';
        $stmtCity->close();

        $fullLocation = $cityName . ", " . $areas;
        $slug = strtolower(preg_replace("/[^a-z0-9]+/", "-", $name));

        // Duplicate check (name + location)
        $checkStmt = $conn->prepare("SELECT id FROM businesses WHERE name = ? AND location = ?");
        $checkStmt->bind_param("ss", $name, $fullLocation);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $exists = true;
            $errors[] = "A business with this name already exists in the same location.";
        } else {
            $stmt = $conn->prepare("INSERT INTO businesses 
                (name, category, location, description, image, url, created_at, slug, status) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, 'pending')");
            $stmt->bind_param("sssssss", $name, $category, $fullLocation, $description, $imagePath, $url, $slug);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Business added successfully and is pending approval.";
                header("Location: manage_business.php");
                exit;
            } else {
                // Handle duplicate key errors gracefully
                if ($stmt->errno == 1062) {
                    if (strpos($stmt->error, 'url') !== false) {
                        $errors[] = "This website URL is already registered.";
                    } elseif (strpos($stmt->error, 'slug') !== false) {
                        $errors[] = "A business with a similar name already exists.";
                    } else {
                        $errors[] = "Duplicate entry detected. Please change your data.";
                    }
                } else {
                    $errors[] = "Could not add business. Please try again." ;
                }
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
}


// ✅ Only output HTML after all processing is done:
include 'includes/header.php';
include 'includes/sidebar.php';
?>


<div class="content-wrapper">
  <section class="content-header py-3 bg-light border-bottom mb-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <h1 class="h3 mb-0">Add New Business</h1>
      <a href="manage_business.php" class="btn btn-outline-secondary">← Back to Manage Businesses</a>
    </div>
  </section>

  <section class="content container-fluid">
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" novalidate>
      <div class="mb-3">
        <label for="name" class="form-label">Business Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
      </div>

      <div class="mb-3">
        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
        <select name="category" id="category" class="form-select" required>
          <option value="">Select Category</option>
          <?php
          $categories = $conn->query("SELECT name FROM category ORDER BY name");
          while ($row = $categories->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['name']) ?>" <?= ($row['name'] == $category) ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <label>City*</label>
      <select name="city_id" id="citySelect" required>
          <option value="">Select City</option>
          <?php
          $cities = $conn->query("SELECT id, name FROM cities ORDER BY name");
          while ($row = $cities->fetch_assoc()):
          ?>
              <option value="<?= $row['id'] ?>" <?= ($row['id'] == ($fields['city_id'] ?? '')) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($row['name']) ?>
              </option>
          <?php endwhile; ?>
      </select>
      <input type="hidden" name="city" id="cityNameHidden" value="<?= htmlspecialchars($fields['city'] ?? '') ?>">

      <label>Area*</label>
      <select name="area" id="areaSelect" required>
          <option value="">Select Area</option>
      </select>

      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($description) ?></textarea>
      </div>

      <div class="mb-3">
        <label for="url" class="form-label">Website URL</label>
        <input type="url" name="url" id="url" class="form-control" value="<?= htmlspecialchars($url) ?>">
      </div>

      <div class="mb-3">
        <label for="image" class="form-label">Upload Image</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
      </div>

      <button type="submit" class="btn btn-primary">Add Business</button>
  
  </section>
</div>
  </form>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const citySelect = document.getElementById('citySelect');
    const cityNameHidden = document.getElementById('cityNameHidden');
    const areaSelect = document.getElementById('areaSelect');

    function loadAreas(cityId, selectedArea = null) {
        if (!cityId) {
            areaSelect.innerHTML = '<option value="">Select Area</option>';
            return;
        }

        areaSelect.innerHTML = '<option value="">Loading...</option>';

       fetch('get_areas.php?city_id=' + cityId)
        .then(response => response.text())  // get raw text first
        .then(text => {
            let data = [];
            try {
                data = JSON.parse(text);
            } catch (err) {
                console.error('Invalid JSON', text, err);
            }
            let options = '<option value="">Select Area</option>';
            data.forEach(areaName => {
                options += `<option value="${areaName}">${areaName}</option>`;
            });
            areaSelect.innerHTML = options;
        })
        .catch(err => {
            console.error(err);
            areaSelect.innerHTML = '<option value="">Error loading areas</option>';
        });

    }

    citySelect.addEventListener('change', function () {
        const cityId = this.value;
        const cityName = this.options[this.selectedIndex].text;
        cityNameHidden.value = cityName;

        loadAreas(cityId); // reload areas on city change
    });

    // On page load, preload areas if editing a business
    if (citySelect.value) {
        const selectedArea = '<?= isset($fields["area"]) ? htmlspecialchars($fields["area"]) : "" ?>';
        loadAreas(citySelect.value, selectedArea);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
