<?php
include 'db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage Users</h1>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">User List</h3>
        <a href="add_user.php" class="btn btn-primary float-right">Add New User</a>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Email</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
              $i = 1;
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$i}</td>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['role']) . "</td>
                        <td>
                          <a href='edit_user.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                          <a href='delete_user.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure to delete this user?')\">Delete</a>
                        </td>
                      </tr>";
                $i++;
              }
            } else {
              echo "<tr><td colspan='5' class='text-center'>No users found.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
