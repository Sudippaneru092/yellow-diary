<?php
ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid business ID.";
    header("Location: manage_business.php");
    exit;
}

$businessId = (int)$_GET['id'];

// Fetch business
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

// Parse location
$cityName = $areaName = "";
if (strpos($business['location'], ',') !== false) {
    list($cityName, $areaName) = array_map('trim', explode(',', $business['location'], 2));
} else {
    $cityName = trim($business['location']);
}

$cityId = null;
$stmtCityId = $conn->prepare("SELECT id FROM cities WHERE name = ?");
$stmtCityId->bind_param("s", $cityName);
$stmtCityId->execute();
$resCityId = $stmtCityId->get_result();
if ($rowCityId = $resCityId->fetch_assoc()) {
    $cityId = $rowCityId['id'];
}
$stmtCityId->close();

// Initialize form fields
$name = $business['name'];
$category = $business['category'];
$location = $cityId;
$areas = $areaName;
$description = $business['description'];
$url = $business['url'];
$imagePath = $business['image'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $areas = trim($_POST['areas']);
    $description = trim($_POST['description']);
    $url = trim($_POST['url']);

    if (!$name) $errors[] = "Business name is required.";
    if (!$category) $errors[] = "Category is required.";
    if (!$location) $errors[] = "City is required.";
    if (!$areas) $errors[] = "Area is required.";
    if ($url && !filter_var($url, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid website URL.";
    }

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
        // Get city name from ID
        $stmtCity = $conn->prepare("SELECT name FROM cities WHERE id = ?");
        $stmtCity->bind_param("i", $location);
        $stmtCity->execute();
        $resultCity = $stmtCity->get_result();
        $cityName = $resultCity->fetch_assoc()['name'] ?? '';
        $stmtCity->close();

        $fullLocation = $cityName . ", " . $areas;

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

include 'includes/header.php';
include 'includes/sidebar.php';
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
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
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
          while ($row = $categories->fetch_assoc()):
              $isSelected = (trim(strtolower($row['name'])) == trim(strtolower($category))) ? 'selected' : '';
          ?>
            <option value="<?= htmlspecialchars($row['name']) ?>" <?= $isSelected ?>>
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
          while ($row = $cities->fetch_assoc()):
              $selected = ($row['id'] == $location) ? 'selected' : '';
          ?>
            <option value="<?= $row['id'] ?>" <?= $selected ?>><?= htmlspecialchars($row['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="areas" class="form-label">Area <span class="text-danger">*</span></label>
        <select name="areas" id="areas" class="form-select">
          <option value="">Select Area</option>
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
            <img src="<?= htmlspecialchars($imagePath) ?>" alt="Business Image" style="max-width: 200px; object-fit: contain;">
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
document.addEventListener('DOMContentLoaded', function () {
  const citySelect = document.getElementById('city');
  const areaSelect = document.getElementById('areas');
  const selectedArea = "<?= addslashes($areas) ?>";

  function loadAreas(cityId) {
    areaSelect.innerHTML = '<option value="">Loading...</option>';
    fetch('get_areas.php?city_id=' + cityId)
      .then(res => res.json())
      .then(data => {
        let options = '<option value="">Select Area</option>';
        data.forEach(area => {
          const isSelected = area.name.trim().toLowerCase() === selectedArea.trim().toLowerCase() ? 'selected' : '';
          options += `<option value="${area.name}" ${isSelected}>${area.name}</option>`;
        });
        areaSelect.innerHTML = options;
      })
      .catch(() => {
        areaSelect.innerHTML = '<option value="">Error loading areas</option>';
      });
  }

  if (citySelect.value) {
    loadAreas(citySelect.value);
  }

  citySelect.addEventListener('change', function () {
    if (this.value) {
      loadAreas(this.value);
    } else {
      areaSelect.innerHTML = '<option value="">Select Area</option>';
    }
  });
});
</script>

<?php include 'includes/footer.php'; ?>
<?php ob_end_flush(); ?>
