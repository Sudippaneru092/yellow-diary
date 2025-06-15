<?php
// edit_business.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid business ID.";
    header("Location: manage_business.php");
    exit;
}

$businessId = (int)$_GET['id'];

// Initialize variables
$name = $category = $location = $areas = $description = $url = "";
$imagePath = "";
$errors = [];

// Fetch existing business data
$stmt = $conn->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->bind_param("i", $businessId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Business not found.";
    header("Location: manage_business.php");
    exit;
}

$business = $result->fetch_assoc();
$stmt->close();

// Parse location into city and area if possible (location stored as "City, Area")
if (strpos($business['location'], ',') !== false) {
    list($cityName, $areaName) = array_map('trim', explode(',', $business['location'], 2));
} else {
    $cityName = $business['location'];
    $areaName = "";
}

// To get city id from cityName, fetch from cities table
$cityId = null;
$stmtCityId = $conn->prepare("SELECT id FROM cities WHERE name = ?");
$stmtCityId->bind_param("s", $cityName);
$stmtCityId->execute();
$resCityId = $stmtCityId->get_result();
if ($rowCityId = $resCityId->fetch_assoc()) {
    $cityId = $rowCityId['id'];
}
$stmtCityId->close();

// Initialize form variables with existing data (will be overridden if POST)
$name = $business['name'];
$category = $business['category'];
$location = $cityId;
$areas = $areaName;
$description = $business['description'];
$url = $business['url'];
$imagePath = $business['image'];  // Existing image path

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']); // city id
    $areas = trim($_POST['areas']);       // area name
    $description = trim($_POST['description']);
    $url = trim($_POST['url']);

    if (empty($name)) $errors[] = "Business name is required.";
    if (empty($category)) $errors[] = "Category is required.";
    if (empty($location)) $errors[] = "City is required.";
    if (empty($areas)) $errors[] = "Area is required.";

    if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid URL format.";
    }

    // Handle image upload if a new image is selected
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
                // Optionally delete old image if exists and different from new
                if (!empty($imagePath) && file_exists($imagePath) && $imagePath != $targetFile) {
                    unlink($imagePath);
                }
                $imagePath = $targetFile;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    if (empty($errors)) {
        // Get city name from city id
        $stmtCity = $conn->prepare("SELECT name FROM cities WHERE id = ?");
        $stmtCity->bind_param("i", $location);
        $stmtCity->execute();
        $resultCity = $stmtCity->get_result();
        $cityName = $resultCity->fetch_assoc()['name'] ?? '';
        $stmtCity->close();

        $fullLocation = $cityName . ", " . $areas;

        // Update query (status unchanged)
        $stmtUpdate = $conn->prepare("UPDATE businesses SET name = ?, category = ?, location = ?, description = ?, image = ?, url = ? WHERE id = ?");
        $stmtUpdate->bind_param("ssssssi", $name, $category, $fullLocation, $description, $imagePath, $url, $businessId);

        if ($stmtUpdate->execute()) {
            $_SESSION['success'] = "Business updated successfully.";
            header("Location: manage_business.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmtUpdate->error;
        }
        $stmtUpdate->close();
    }
}
?>

<div class="content-wrapper">
  <section class="content-header py-3 bg-light border-bottom mb-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <h1 class="h3 mb-0">Edit Business</h1>
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

      <div class="mb-3">
        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
        <select name="location" id="city" class="form-select" required>
          <option value="">Select City</option>
          <?php
          $cities = $conn->query("SELECT id, name FROM cities ORDER BY name");
          while ($row = $cities->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $location) ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="areas" class="form-label">Area <span class="text-danger">*</span></label>
        <select name="areas" id="areas" class="form-select" required>
          <option value="">Select Area</option>
          <!-- Will be populated dynamically -->
        </select>
      </div>

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
        <?php if (!empty($imagePath) && file_exists($imagePath)): ?>
          <div class="mb-2">
            <img src="<?= htmlspecialchars($imagePath) ?>" alt="Business Image" style="max-width: 200px; max-height: 150px; object-fit: contain;">
          </div>
        <?php endif; ?>
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
        <small class="form-text text-muted">Leave empty to keep existing image.</small>
      </div>

      <button type="submit" class="btn btn-primary">Update Business</button>
    </form>
  </section>
</div>

<script>
  // Load areas dynamically when city changes
  document.getElementById('city').addEventListener('change', function () {
    const cityId = this.value;
    const areaSelect = document.getElementById('areas');
    areaSelect.innerHTML = '<option value="">Loading...</option>';

    if (cityId) {
      fetch('get_areas.php?city_id=' + cityId)
        .then(response => response.json())
        .then(data => {
          areaSelect.innerHTML = '<option value="">Select Area</option>';
          data.forEach(area => {
            const option = document.createElement('option');
            option.value = area.name;  // assuming get_areas.php returns array of {id, name}
            option.textContent = area.name;
            areaSelect.appendChild(option);
          });

          // If previously selected area exists, reselect it
          const selectedArea = "<?= addslashes($areas) ?>";
          if(selectedArea) {
            areaSelect.value = selectedArea;
          }
        })
        .catch(err => {
          areaSelect.innerHTML = '<option value="">Failed to load areas</option>';
          console.error(err);
        });
    } else {
      areaSelect.innerHTML = '<option value="">Select Area</option>';
    }
  });

  // Pre-load areas if city was already selected (e.g. on page load)
  window.addEventListener('DOMContentLoaded', () => {
    const citySelect = document.getElementById('city');
    if (citySelect.value) {
      citySelect.dispatchEvent(new Event('change'));
    }
  });
</script>

<?php include 'includes/footer.php'; ?>
