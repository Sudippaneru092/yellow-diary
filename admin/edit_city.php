<?php
include('db.php');
include('includes/header.php');
include('includes/sidebar.php');

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='manage_cities.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Fetch city details
$sql = "SELECT * FROM cities WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<script>window.location.href='manage_cities.php';</script>";
    exit;
}

$city = $result->fetch_assoc();

?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Edit City: <?php echo htmlspecialchars($city['name']); ?></h1>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">Edit City Details</h3>
        </div>

        <form method="POST" enctype="multipart/form-data">
          <div class="card-body">

            <div class="form-group">
              <label>City Name</label>
              <input type="text" name="city_name" class="form-control" required value="<?php echo htmlspecialchars($city['name']); ?>">
            </div>

            <div class="form-group">
              <label>District</label>
              <input type="text" name="district" class="form-control" required value="<?php echo htmlspecialchars($city['district']); ?>">
            </div>

            <div class="form-group">
              <label>Province</label>
              <input type="text" name="province" class="form-control" required value="<?php echo htmlspecialchars($city['province']); ?>">
            </div>

            <div class="form-group">
              <label>Type</label>
              <select name="type" class="form-control" required>
                <?php
                $types = ['Municipality', 'Metropolitan', 'Sub-Metropolitan', 'Rural Municipality'];
                foreach ($types as $type) {
                    $selected = ($city['type'] == $type) ? 'selected' : '';
                    echo "<option value='$type' $selected>$type</option>";
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label>Current Image</label><br>
              <?php
              if (!empty($city['image']) && file_exists($city['image'])) {
                  echo "<img src='{$city['image']}' width='120' height='80' style='margin-bottom:10px;'><br>";
              } else {
                  echo "No image uploaded.";
              }
              ?>
            </div>

            <div class="form-group">
              <label>Change Image (optional)</label>
              <input type="file" name="image" class="form-control">
            </div>

            <div class="form-group">
              <label><input type="checkbox" name="is_popular" <?php echo ($city['is_popular']) ? 'checked' : ''; ?>> Mark as Popular City</label>
            </div>

          </div>

          <div class="card-footer">
            <button type="submit" name="update_city" class="btn btn-primary">Update City</button>
            <a href="manage_cities.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>

        <?php
        if (isset($_POST['update_city'])) {
            $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);
            $district = mysqli_real_escape_string($conn, $_POST['district']);
            $province = mysqli_real_escape_string($conn, $_POST['province']);
            $type = mysqli_real_escape_string($conn, $_POST['type']);
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $city_name)));

            // Properly check if checkbox is set
            $is_popular = isset($_POST['is_popular']) ? 1 : 0;

            // Handle Image Upload if new image uploaded
            $image_path = $city['image']; // keep existing image by default
            if (!empty($_FILES['image']['name'])) {
                $target_dir = "uploads/cities/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }

                // Delete old image file if exists
                if (!empty($city['image']) && file_exists($city['image'])) {
                    unlink($city['image']);
                }

                $filename = time() . '_' . basename($_FILES["image"]["name"]);
                $image_path = $target_dir . $filename;
                move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
            }

            $update_sql = "UPDATE cities SET
                name = '$city_name',
                district = '$district',
                province = '$province',
                type = '$type',
                slug = '$slug',
                is_popular = $is_popular,
                image = '$image_path'
                WHERE id = $id";

            if ($conn->query($update_sql)) {
                echo "<div class='alert alert-success m-3'>City updated successfully!</div>";
                echo "<script>setTimeout(() => { window.location.href='manage_cities.php'; }, 1500);</script>";
            } else {
                echo "<div class='alert alert-danger m-3'>Error updating city: " . $conn->error . "</div>";
            }
        }
        ?>

      </div>

    </div>
  </section>
</div>

<?php include('includes/footer.php'); ?>
