<?php
// Get the current page filename (e.g., manage_business.php)
$current_page = basename($_SERVER['PHP_SELF']);

// List of submenu pages under "Reports"
$reports_pages = [
  'business-reports.php',
  'user-reports.php',
  'category-reports.php',
  'post-reports.php',
];

// Helper function to print "active" class
function isActive($page, $current) {
  return $page === $current ? 'active' : '';
}

// Check if current page is in reports submenu to open the parent
$reports_open = in_array($current_page, $reports_pages) ? 'menu-open' : '';
$reports_active = in_array($current_page, $reports_pages) ? 'active' : '';
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="position: fixed; height: 100vh; overflow-y: auto;">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link d-flex align-items-center">
    <i class="fas fa-book-reader fa-lg mx-2"></i>
    <span class="brand-text font-weight-light">Yellow Diary</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" 
          data-widget="treeview" role="menu" data-accordion="false">

        <!-- Dashboard -->
        <li class="nav-item">
          <a href="index.php" class="nav-link <?= isActive('index.php', $current_page) ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

    
        <!-- Manage Businesses -->
        <li class="nav-item">
          <a href="manage_business.php" class="nav-link <?= isActive('manage_business.php', $current_page) ?>">
            <i class="nav-icon fas fa-building"></i>
            <p>Manage Businesses</p>
          </a>
        </li>

        <!-- Manage Categories -->
        <li class="nav-item">
          <a href="manage_categories.php" class="nav-link <?= isActive('manage_categories.php', $current_page) ?>">
            <i class="nav-icon fas fa-th-list"></i>
            <p>Manage Categories</p>
          </a>
        </li>

        <!-- Manage Posts -->
        <li class="nav-item">
          <a href="manage_posts.php" class="nav-link <?= isActive('manage_posts.php', $current_page) ?>">
            <i class="nav-icon fas fa-newspaper"></i>
            <p>Manage Posts</p>
          </a>
        </li>

        <!-- Manage Cities -->
        <li class="nav-item">
          <a href="manage_cities.php" class="nav-link <?= isActive('manage_cities.php', $current_page) ?>">
            <i class="nav-icon fas fa-city"></i>
            <p>Manage Cities</p>
          </a>
        </li>

        <!-- Manage Areas -->
        <li class="nav-item">
          <a href="manage_areas.php" class="nav-link <?= isActive('manage_areas.php', $current_page) ?>">
            <i class="nav-icon fas fa-map-marker-alt"></i>
            <p>Manage Areas</p>
          </a>
        </li>

        <!-- Reports with submenu -->
        <li class="nav-item has-treeview <?= $reports_open ?>">
          <a href="#" class="nav-link <?= $reports_active ?>">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Reports
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview ps-3">
            <li class="nav-item">
              <a href="business-reports.php" class="nav-link <?= isActive('business-reports.php', $current_page) ?>">
                <i class="far fa-circle nav-icon text-info"></i>
                <p>Business Report</p>
              </a>
            </li>
         
            </li>
         
          <li class="nav-item">
              <a href="post-reports.php" class="nav-link <?= isActive('post-reports.php', $current_page) ?>">
                <i class="far fa-circle nav-icon text-secondary"></i>
                <p>Post Report</p>
              </a>
            </li>
          </ul>
        </li>


        <!-- Logout -->
        <li class="nav-item mt-auto">
          <a href="logout.php" class="nav-link text-danger">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
