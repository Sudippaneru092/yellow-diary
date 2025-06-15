<?php include('db.php'); ?>
<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <h1>Manage Cities</h1>
      <button id="showAddCityBtn" class="btn btn-primary">Add City</button>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <!-- Add City Form (hidden initially) -->
      <div id="addCityForm" class="card card-primary" style="display:none;">
        <div class="card-header">
          <h3 class="card-title">Add New City</h3>
        </div>
        <form method="POST" enctype="multipart/form-data">
          <div class="card-body">
            <div class="form-group">
              <label>City Name</label>
              <input type="text" name="city_name" class="form-control" required>
            </div>

            <div class="form-group">
              <label>City Image</label>
              <input type="file" name="image" class="form-control">
            </div>

            <div class="form-group">
              <label><input type="checkbox" name="is_popular"> Mark as Popular City</label>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" name="add_city" class="btn btn-primary">Add City</button>
          </div>
        </form>
      </div>

      <?php
      // Handle form submission here (same as your current code)
      if (isset($_POST['add_city'])) {
          $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);
          $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $city_name)));
          $is_popular = isset($_POST['is_popular']) ? 1 : 0;

          $image_path = '';
          if (!empty($_FILES['image']['name'])) {
              $target_dir = "uploads/cities/";
              $filename = time() . '_' . basename($_FILES["image"]["name"]);
              $image_path = $target_dir . $filename;
              move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
          }

          $sql = "INSERT INTO cities (name, slug, is_popular, image) VALUES ('$city_name', '$slug', '$is_popular', '$image_path')";
          if ($conn->query($sql)) {
              echo "<div class='alert alert-success m-3'>City added successfully!</div>";
              echo "<script>window.location.href='manage_cities.php';</script>";
          } else {
              echo "<div class='alert alert-danger m-3'>Error: " . $conn->error . "</div>";
          }
      }
      ?>

      <!-- Search box -->
      <div class="form-group mt-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Search cities...">
      </div>

      <!-- Cities List -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">City List</h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table id="citiesTable" class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th>ID</th>
                <th>City Name</th>
                <th>Slug</th>
                <th>Image</th>
                <th>Popular</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $result = $conn->query("SELECT * FROM cities ORDER BY id DESC");
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['slug']}</td>
                        <td>";
                if (!empty($row['image'])) {
                    echo "<img src='{$row['image']}' width='60' height='40'>";
                } else {
                    echo "N/A";
                }
                echo "</td>
                        <td>" . (isset($row['is_popular']) ? ($row['is_popular'] ? 'Yes' : 'No') : 'N/A') . "</td>
                        <td>" . (isset($row['created_at']) ? $row['created_at'] : 'N/A') . "</td>
                        <td>
                          <a href='edit_city.php?id={$row['id']}' class='btn btn-sm btn-info'>Edit</a>
                          <a href='?delete_id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this city?\")'>Delete</a>
                        </td>
                      </tr>";
              }
              // Handle Delete
              if (isset($_GET['delete_id'])) {
                  $id = intval($_GET['delete_id']);
                  $conn->query("DELETE FROM cities WHERE id = $id");
                  echo "<script>window.location.href='manage_cities.php';</script>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>
</div>

<script>
  // Toggle Add City form visibility
  document.getElementById('showAddCityBtn').addEventListener('click', function () {
    const form = document.getElementById('addCityForm');
    if (form.style.display === 'none' || form.style.display === '') {
      form.style.display = 'block';
      this.textContent = 'Hide Add City Form';
    } else {
      form.style.display = 'none';
      this.textContent = 'Add City';
    }
  });

  // Search/filter function for cities table
  document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#citiesTable tbody tr');
    rows.forEach(row => {
      const cityName = row.cells[1].textContent.toLowerCase();
      const slug = row.cells[2].textContent.toLowerCase();
      if (cityName.includes(filter) || slug.includes(filter)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
</script>

<?php include('includes/footer.php'); ?>
