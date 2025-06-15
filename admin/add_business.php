<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $areas = trim($_POST['areas']);
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
        $stmtCity = $conn->prepare("SELECT name FROM cities WHERE id = ?");
        $stmtCity->bind_param("i", $location);
        $stmtCity->execute();
        $resultCity = $stmtCity->get_result();
        $cityName = $resultCity->fetch_assoc()['name'] ?? '';
        $stmtCity->close();

        $fullLocation = $cityName . ", " . $areas;

        $stmt = $conn->prepare("INSERT INTO businesses (name, category, location, description, image, url, created_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')");
        $stmt->bind_param("ssssss", $name, $category, $fullLocation, $description, $imagePath, $url);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Business added successfully and is pending approval.";
            header("Location: manage_business.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
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
          <?php
               $areas = $conn->query("SELECT id, name FROM areas ORDER BY name");
                while ($row = $areas->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $location) ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['name']) ?>
            </option>
          <?php endwhile; ?>
          ?>
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
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
      </div>

      <button type="submit" class="btn btn-primary">Add Business</button>
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

  // Pre-load areas if city was already selected (e.g. on form validation failure)
  window.addEventListener('DOMContentLoaded', () => {
    const citySelect = document.getElementById('city');
    if (citySelect.value) {
      citySelect.dispatchEvent(new Event('change'));
    }
  });
</script>

<?php include 'includes/footer.php'; ?>
