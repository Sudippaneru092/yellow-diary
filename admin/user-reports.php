<?php

require_once 'db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Summary
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"));

// Role-wise count
$users_by_role = mysqli_query($conn, "SELECT role, COUNT(*) AS count FROM users GROUP BY role");

// Recent users
$recent_users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC LIMIT 10");
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>User Reports</h1>
  </section>

  <section class="content">
    <div class="row">
      <!-- Total Users -->
      <div class="col-lg-4 col-6">
        <div class="small-box bg-primary">
          <div class="inner">
            <h3><?= $total_users['total'] ?></h3>
            <p>Total Users</p>
          </div>
          <div class="icon"><i class="fas fa-users"></i></div>
        </div>
      </div>
    </div>

    <!-- Users by Role -->
    <div class="card">
      <div class="card-header"><h3 class="card-title">Users by Role</h3></div>
      <div class="card-body">
        <table class="table table-bordered">
          <thead><tr><th>Role</th><th>Total</th></tr></thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($users_by_role)): ?>
              <tr>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td><?= $row['count'] ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Users -->
    <div class="card">
      <div class="card-header"><h3 class="card-title">Recent Users</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Joined On</th>
            </tr>
          </thead>
          <tbody>
            <?php while($user = mysqli_fetch_assoc($recent_users)): ?>
              <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= ucfirst($user['role']) ?></td>
                <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

  </section>
</div>

<?php require_once 'includes/footer.php'; ?>

