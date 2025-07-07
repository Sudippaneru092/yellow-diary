<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include '../admin/db.php';

// Validate session
if (!isset($_SESSION['business_id']) || !is_numeric($_SESSION['business_id'])) {
    die("Invalid business session. Please login.");
}

$business_id = $_SESSION['business_id'];

// Fetch business data securely
$stmt = $conn->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Business not found or database error.");
}

$business = $result->fetch_assoc();
$stmt->close();

// Prepare image path
$imagePath = __DIR__ . "/../uploads/" . $business['image'];
$imageUrl = "../uploads/" . $business['image'];

// Include layout
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Welcome, <?= htmlspecialchars($business['name']) ?></h1>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-body">

          <p><strong>Email:</strong> <?= htmlspecialchars($business['email']) ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($business['phone']) ?></p>
          <p><strong>Category:</strong> <?= htmlspecialchars($business['category']) ?></p>
          <p><strong>Location:</strong> <?= htmlspecialchars($business['location']) ?></p>
          <p><strong>Status:</strong> 
            <?php if ($business['status'] === 'approved'): ?>
              <span class="badge badge-success">Approved</span>
            <?php else: ?>
              <span class="badge badge-warning">Pending</span>
            <?php endif; ?>
          </p>
          <p><strong>Joined On:</strong> <?= date('d M Y', strtotime($business['created_at'])) ?></p>
          <p><strong>Last Login:</strong> <?= $business['last_login'] ? date('d M Y H:i', strtotime($business['last_login'])) : 'N/A' ?></p>
          <p><strong>Website URL:</strong> 
            <a href="<?= htmlspecialchars($business['url']) ?>" target="_blank">
              <?= htmlspecialchars($business['url']) ?>
            </a>
          </p>

          <p><strong>Description:</strong><br>
            <?= nl2br(htmlspecialchars($business['description'])) ?>
          </p>

          <p><strong>Business Image:</strong><br>
            <?php if (!empty($business['image']) && file_exists($imagePath)): ?>
              <img src="<?= $imageUrl ?>" width="200" alt="Business Image" class="img-thumbnail">
            <?php else: ?>
              <em>No image available</em>
            <?php endif; ?>
          </p>

        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
