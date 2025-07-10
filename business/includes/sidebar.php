<?php
// sidebar.php
?>

<!-- Sidebar -->
<nav id="sidebarMenu" class="d-md-block bg-light sidebar collapse">
  <div class="position-sticky pt-3">
    <ul class="nav flex-column">

      <li class="nav-item mb-2">
        <a class="nav-link active" href="dashboard.php">
          <i class="fas fa-tachometer-alt"></i>
          Dashboard
        </a>
      </li>

      <li class="nav-item mb-2">
        <a class="nav-link" href="business_profile.php">
          <i class="fas fa-user"></i>
          Profile
        </a>
      </li>



      <li class="nav-item mb-2">
  <a class="nav-link" href="manage_posts.php">
    <i class="fas fa-file-alt"></i> Manage posts
  </a>
</li>



      <li class="nav-item mb-2">
        <a class="nav-link" href="change_password.php">
          <i class="fas fa-key"></i>
          Change Password
        </a>
      </li>

     
      

      <li class="nav-item mt-4">
        <a class="nav-link text-danger" href="logout.php">
          <i class="fas fa-sign-out-alt"></i>
          Logout
        </a>
      </li>

    </ul>
  </div>
</nav>
