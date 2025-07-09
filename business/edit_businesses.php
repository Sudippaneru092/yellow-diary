<?php
session_start();
include '../admin/db.php'; // DB connection

if (!isset($_SESSION['business_id'])) {
    header('Location: login.php');
    exit();
}

$business_id = $_SESSION['business_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $url = trim($_POST['url']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("UPDATE businesses SET name=?, category=?, location=?, url=?, description=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $category, $location, $url, $description, $business_id);

    if ($stmt->execute()) {
        $message = "Business details updated successfully.";
    } else {
        $message = "Error updating business details.";
    }
}

// Fetch current business info
$stmt = $conn->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();
$business = $result->fetch_assoc();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Edit Business Details</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Business Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($business['name']) ?>" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Category</label>
            <input type="text" name="category" value="<?= htmlspecialchars($business['category']) ?>" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($business['location']) ?>" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Website URL</label>
            <input type="url" name="url" value="<?= htmlspecialchars($business['url']) ?>" class="form-control">
        </div>

        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($business['description']) ?></textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Update Business</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
