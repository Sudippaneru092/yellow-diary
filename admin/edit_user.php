<?php
include 'db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Check if ID is passed
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location.href='manage_users.php';</script>";
    exit;
}

$id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch user data
$query = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>window.location.href='manage_users.php';</script>";
    exit;
}

$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    // Simple validation
    if (empty($username) || empty($email) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Update query
        $update_query = "UPDATE users SET username=?, email=?, role=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $role, $id);
        if (mysqli_stmt_execute($stmt)) {
            $success = "User updated successfully.";
            // Refresh user data
            $user['username'] = $username;
            $user['email'] = $email;
            $user['role'] = $role;
        } else {
            $error = "Failed to update user.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Edit User</h1>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-body">
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
          </div>

          <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control" required>
              <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
              <option value="editor" <?= $user['role'] == 'editor' ? 'selected' : '' ?>>Editor</option>
              <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary">Update User</button>
          <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
