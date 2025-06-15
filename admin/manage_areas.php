<?php 
include('db.php'); 

// Handle delete area at the top before output
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM areas WHERE id = $id");
    header('Location: manage_areas.php');
    exit;
}

// Handle add area form submission at the top before output
if (isset($_POST['add_area'])) {
    $area_name = mysqli_real_escape_string($conn, $_POST['area_name']);
    $city_id = intval($_POST['city_id']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $area_name), '-'));

    $sql = "INSERT INTO areas (name, city_id, slug) VALUES ('$area_name', $city_id, '$slug')";
    if ($conn->query($sql)) {
        header('Location: manage_areas.php?success=1');
        exit;
    } else {
        $error_msg = "Error: " . $conn->error;
    }
}

include('includes/header.php'); 
include('includes/sidebar.php'); 
?>

<div class="content-wrapper">
  <section class="content-header py-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <h1 class="h3 mb-0 text-dark">Manage Areas</h1>
      <button id="showAddAreaBtn" class="btn btn-primary">Add Area</button>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Area added successfully!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($error_msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <!-- Add Area Form (hidden initially) -->
      <div id="addAreaForm" class="card shadow-sm mb-4" style="display:none;">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title mb-0">Add New Area</h5>
        </div>
        <form method="POST" class="needs-validation" novalidate>
          <div class="card-body">
            <div class="mb-3">
              <label for="area_name" class="form-label">Area Name <span class="text-danger">*</span></label>
              <input type="text" name="area_name" id="area_name" class="form-control" required>
              <div class="invalid-feedback">Please enter area name.</div>
            </div>
            <div class="mb-3">
              <label for="city_id" class="form-label">Select City <span class="text-danger">*</span></label>
              <select name="city_id" id="city_id" class="form-select" required>
                <option value="">-- Select City --</option>
                <?php
                $cities = $conn->query("SELECT id, name FROM cities ORDER BY name ASC");
                while ($city = $cities->fetch_assoc()) {
                  echo "<option value='{$city['id']}'>" . htmlspecialchars($city['name']) . "</option>";
                }
                ?>
              </select>
              <div class="invalid-feedback">Please select a city.</div>
            </div>
          </div>
          <div class="card-footer text-end">
            <button type="submit" name="add_area" class="btn btn-primary">Add Area</button>
          </div>
        </form>
      </div>

      <!-- Search box -->
      <div class="mb-3">
        <input type="search" id="searchAreaInput" class="form-control" placeholder="Search areas by area or city...">
      </div>

      <!-- Areas List -->
      <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
          <h5 class="card-title mb-0">Area List</h5>
        </div>
        <div class="card-body table-responsive p-0">
          <table id="areasTable" class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Area Name</th>
                <th>City</th>
                <th>Slug</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT areas.*, cities.name AS city_name 
                      FROM areas 
                      LEFT JOIN cities ON areas.city_id = cities.id 
                      ORDER BY areas.id DESC";
              $result = $conn->query($sql);
              while ($row = $result->fetch_assoc()):
              ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['city_name']) ?></td>
                <td><?= htmlspecialchars($row['slug']) ?></td>
                <td><?= isset($row['created_at']) ? $row['created_at'] : 'N/A' ?></td>
                <td>
                  <a href="edit_area.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info me-1">Edit</a>
                  <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this area?')">Delete</a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>
</div>

<script>
  // Bootstrap form validation
  (() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
  })()

  // Toggle Add Area form visibility
  document.getElementById('showAddAreaBtn').addEventListener('click', function () {
    const form = document.getElementById('addAreaForm');
    if (form.style.display === 'none' || form.style.display === '') {
      form.style.display = 'block';
      this.textContent = 'Hide Add Area Form';
    } else {
      form.style.display = 'none';
      this.textContent = 'Add Area';
    }
  });

  // Search/filter function for areas table
  document.getElementById('searchAreaInput').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#areasTable tbody tr');
    rows.forEach(row => {
      const areaName = row.cells[1].textContent.toLowerCase();
      const cityName = row.cells[2].textContent.toLowerCase();
      if (areaName.includes(filter) || cityName.includes(filter)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
</script>

<?php include('includes/footer.php'); ?>
