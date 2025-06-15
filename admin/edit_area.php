<?php include('db.php'); ?>
<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>

<?php
if (!isset($_GET['id'])) {
    echo "<script>window.location.href='manage_areas.php';</script>";
    exit;
}

$area_id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM areas WHERE id = $area_id");
$area = $result->fetch_assoc();

if (!$area) {
    echo "<div class='alert alert-danger m-3'>Area not found.</div>";
    exit;
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Edit Area</h1>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title">Edit Area: <?= htmlspecialchars($area['name']) ?></h3>
        </div>

        <form method="POST">
          <div class="card-body">

            <div class="form-group">
              <label>Select City</label>
              <select name="city_id" class="form-control" required>
                <option value="">-- Select City --</option>
                <?php
                $cities = $conn->query("SELECT id, name FROM cities ORDER BY name ASC");
                while ($city = $cities->fetch_assoc()) {
                    $selected = ($area['city_id'] == $city['id']) ? "selected" : "";
                    echo "<option value='{$city['id']}' $selected>{$city['name']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label>Area Name</label>
              <input type="text" name="area_name" class="form-control" value="<?= htmlspecialchars($area['name']) ?>" required>
            </div>

          </div>
          <div class="card-footer">
            <button type="submit" name="update_area" class="btn btn-info">Update Area</button>
            <a href="manage_areas.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>

        <?php
        if (isset($_POST['update_area'])) {
            $city_id = intval($_POST['city_id']);
            $area_name = mysqli_real_escape_string($conn, $_POST['area_name']);
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $area_name)));

            $update_sql = "UPDATE areas 
                           SET name = '$area_name', slug = '$slug', city_id = $city_id 
                           WHERE id = $area_id";

            if ($conn->query($update_sql)) {
                echo "<div class='alert alert-success m-3'>Area updated successfully!</div>";
                echo "<script>setTimeout(() => window.location.href='manage_areas.php', 1000);</script>";
            } else {
                echo "<div class='alert alert-danger m-3'>Error: " . $conn->error . "</div>";
            }
        }
        ?>

      </div>
    </div>
  </section>
</div>

<?php include('includes/footer.php'); ?>
