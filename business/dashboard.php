<?php
session_start();
include '../admin/db.php'; // Adjust path if needed

if (!isset($_SESSION['business_id'])) {
    header('Location: login.php');
    exit();
}

$business_id = $_SESSION['business_id'];

$sql = "SELECT * FROM businesses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $business = $result->fetch_assoc();
} else {
    echo "Business not found.";
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<body>
<div class="d-flex" id="wrapper">

  <?php include 'includes/sidebar.php'; ?>

  <div id="page-content-wrapper" class="flex-grow-1">

    <!-- Navbar / Header with toggle button -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
      <div class="container-fluid">
        <button class="btn btn-primary" id="sidebarToggle">
          <i class="fas fa-bars"></i>
        </button>
        <span class="navbar-brand ms-3">Welcome, <?= htmlspecialchars($business['name']) ?></span>
      </div>
    </nav>

    

    <div class="container-fluid px-4">
      <h2>Business Details</h2>

      <div class="row">

        <div class="col-md-8">
          <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
              Business Details
            </div>
            <div class="card-body">
              <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($business['email']) ?></p>
              <p><i class="fas fa-phone-alt"></i> <strong>Phone:</strong> <?= htmlspecialchars($business['phone']) ?></p>
              <p><i class="fas fa-layer-group"></i> <strong>Category:</strong> <?= htmlspecialchars($business['category']) ?></p>
              <p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?= htmlspecialchars($business['location']) ?></p>

              <p>
                <strong>Status:</strong>
                <?php if (!empty($business['status']) && strtolower($business['status']) === 'approved'): ?>
                  <span class="badge bg-success">Approved</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">Pending</span>
                <?php endif; ?>
              </p>

              <p><strong>Joined On:</strong> <?= !empty($business['created_at']) ? date('d M Y', strtotime($business['created_at'])) : 'N/A' ?></p>
              <p><strong>Last Login:</strong> <?= !empty($business['last_login']) ? date('d M Y H:i', strtotime($business['last_login'])) : 'N/A' ?></p>

              <p><strong>Website:</strong>
                <?php if (!empty($business['url'])): ?>
                  <a href="<?= htmlspecialchars($business['url']) ?>" target="_blank" rel="noopener noreferrer">
                    <?= htmlspecialchars($business['url']) ?>
                  </a>
                <?php else: ?>
                  N/A
                <?php endif; ?>
              </p>

              <p><strong>Description:</strong><br>
                <?= nl2br(htmlspecialchars($business['description'])) ?>
              </p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow mb-4">
            <div class="card-header bg-warning text-dark">
              Business Image
            </div>
            <div class="card-body text-center">
              <?php
                $imagePath = __DIR__ . "/../uploads/" . ($business['image'] ?? '');
                $imageUrl = "../uploads/" . ($business['image'] ?? '');
              ?>
              <?php if (!empty($business['image']) && file_exists($imagePath)): ?>
                <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid img-thumbnail" alt="Business Image" style="max-height: 200px;">
              <?php else: ?>
                <img src="../uploads/default.png" class="img-fluid img-thumbnail" alt="Default Image" style="max-height: 200px;">
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Sidebar toggle script -->
<script>
  const sidebarToggle = document.getElementById('sidebarToggle');
  const wrapper = document.getElementById('wrapper');

  sidebarToggle.addEventListener('click', () => {
    wrapper.classList.toggle('toggled');
  });
</script>

<style>
  /* Sidebar toggle styles */
  #wrapper {
    display: flex;
    flex-wrap: nowrap;
  }
  #sidebarMenu {
    width: 250px;
    transition: margin-left 0.3s ease;
  }
  #wrapper.toggled #sidebarMenu {
    margin-left: -250px;
  }
  #page-content-wrapper {
    width: 100%;
  }
  @media (max-width: 768px) {
    #sidebarMenu {
      margin-left: -250px;
      position: fixed;
      height: 100vh;
      z-index: 1030;
    }
    #wrapper.toggled #sidebarMenu {
      margin-left: 0;
      box-shadow: 2px 0 5px rgba(0,0,0,.5);
      background-color: #f8f9fa;
    }
  }
</style>

</body>
</html>
