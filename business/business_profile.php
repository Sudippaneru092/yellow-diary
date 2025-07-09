<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['business_id'])) {
    header('Location: login.php');
    exit();
}

$business_id = $_SESSION['business_id'];

// Fetch business info
$stmt = $conn->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();
$business = $result->fetch_assoc();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-3">Business Profile</h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?= htmlspecialchars($business['name']) ?> <small class="text-light">(ID: <?= $business['id'] ?>)</small></h5>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong> <?= htmlspecialchars($business['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($business['phone']) ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($business['category']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($business['location']) ?></p>
                    <p><strong>Website:</strong> 
                        <?php if (!empty($business['url'])): ?>
                            <a href="<?= htmlspecialchars($business['url']) ?>" target="_blank"><?= htmlspecialchars($business['url']) ?></a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </p>
                    <p><strong>Status:</strong> 
                        <?php if ($business['status'] === 'approved'): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Joined:</strong> <?= date('d M Y', strtotime($business['created_at'])) ?></p>
                    <p><strong>Last Login:</strong> 
                        <?= !empty($business['last_login']) ? date('d M Y H:i', strtotime($business['last_login'])) : 'N/A' ?>
                    </p>
                    <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($business['description'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Profile Image -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <strong>Business Image</strong>
                </div>
                <div class="card-body text-center">
                    <?php
                        $imagePath = "../uploads/" . ($business['image'] ?? '');
                        if (!empty($business['image']) && file_exists(__DIR__ . "/../uploads/" . $business['image'])):
                    ?>
                        <img src="<?= $imagePath ?>" class="img-fluid img-thumbnail" alt="Business Image" style="max-height: 200px;">
                    <?php else: ?>
                        <img src="../uploads/default.png" class="img-fluid img-thumbnail" alt="Default Image" style="max-height: 200px;">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
