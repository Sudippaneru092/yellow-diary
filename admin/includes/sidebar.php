<aside class="sidebar">
  <?php
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }

  $username = $_SESSION['username'] ?? '';
  $email = $_SESSION['email'] ?? 'Unknown User';
  $avatar = !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : 'https://via.placeholder.com/80';

  $displayName = !empty($username) ? $username : $email;
  ?>

  <div class="user-info">
    <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="User Avatar" style="border-radius: 50%; width: 80px; height: 80px;" />
    <h2><?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></h2>
  </div>

<ul>
  <li><button id="btnDashboard" onclick="location.href='../admin/dashboard.php'">
    <i class="fas fa-chart-pie"></i> Dashboard
  </button></li>

  <li><button id="btnUser" onclick="location.href='../admin/user_management.php'">
    <i class="fas fa-user"></i> User Management
  </button></li>

  <li><button id="btnService" onclick="location.href='../admin/service.php'">
    <i class="fas fa-database"></i> Service
  </button></li>

  <li><button id="btnReport" onclick="location.href='../admin/report.php'">
    <i class="fas fa-file-alt"></i> Report
  </button></li>

  <li><button id="btnMap" onclick="location.href='../admin/map.php'">
    <i class="fas fa-map"></i> Map
  </button></li>

  <li><button id="btnCharts" onclick="location.href='../admin/charts.php'">
    <i class="fas fa-chart-line"></i> Charts
  </button></li>

  <li><button id="btnSettings" onclick="location.href='../admin/settings.php'">
    <i class="fas fa-cog"></i> Settings
  </button></li>

  <li><button id="btnLogout" onclick="location.href='../logout.php'">
    <i class="fas fa-sign-out-alt"></i> Logout
  </button></li>
</ul>


</aside>
