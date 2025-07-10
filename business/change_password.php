<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['business_id'])) {
    header("Location: login.php");
    exit();
}

$business_id = $_SESSION['business_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    $message = "";
    if($current === "" || $new === "" || $confirm === "") {
        $message = "❌ All fields are required.";
    } elseif (strlen($new) < 8) {
        $message = "❌ New password must be at least 8 characters long.";
    } elseif (strlen($current) < 8) {
        $message = "❌ Current password must be at least 8 characters long.";
    }elseif ($new !== $confirm) {
        $message = "❌ New password and confirmation do not match.";
    } else {
        // Fetch current hashed password
        $stmt = $conn->prepare("SELECT password FROM businesses WHERE id = ?");
        $stmt->bind_param("i", $business_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        // Verify current password
        if (!password_verify($current, $hashed_password)) {
            $message = "❌ Current password is incorrect.";
        } else {
            // Hash new password
            $new_hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE businesses SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hashed, $business_id);

            if ($stmt->execute()) {
                $message = "✅ Password updated successfully.";
            } else {
                $message = "❌ Failed to update password.";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Change Password</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3" novalidate>
        <div class="col-md-12">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" required minlength="6">
        </div>

        <div class="col-md-6">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required minlength="6">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Change Password</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
