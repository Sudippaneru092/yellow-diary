<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

  session_start();

  if (!isset($_SESSION['admin']) || !isset($_SESSION['user_id'])) {
      header("Location: http://localhost/project/yellow-diary/admin/admin.php");
      exit();
  }

  require_once 'database_connect.php'; // make sure this creates $conn

  $user_id = $_SESSION['user_id'];
  $_SESSION['login_time'] = $_SESSION['login_time'] ?? time(); // if not set yet, set now

  // Update last_login_time for user in DB
  $stmt = $conn->prepare("UPDATE users SET last_login_time = NOW() WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();

  include "admin_user_summary.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modern Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f9;
    }
    .container {
      display: flex;
      height: 100vh;
      overflow: hidden;
    }
    .sidebar {
      width: 260px;
      background: #2f3542;
      color: white;
      display: flex;
      flex-direction: column;
      padding: 20px;
    }
    .sidebar .user-info {
      text-align: center;
      margin-bottom: 30px;
    }
    .sidebar .user-info img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 10px;
      border: 2px solid #ffa502;
    }
    .sidebar .user-info h2 {
      font-size: 20px;
    }
    .sidebar ul {
      list-style: none;
      padding-left: 0;
    }
    .sidebar ul li {
      margin: 15px 0;
      font-size: 18px;
      display: block;
    }
    .sidebar ul li button {
      background: transparent;
      border: none;
      color: white;
      width: 100%;
      text-align: left;
      font-size: 18px;
      cursor: pointer;
      padding: 8px 12px;
      display: flex;
      align-items: center;
      border-radius: 6px;
      transition: background-color 0.3s, color 0.3s;
    }
    .sidebar ul li button i {
      margin-right: 10px;
      min-width: 20px;
      text-align: center;
    }
    .sidebar ul li button:hover {
      background-color: #ffa502;
      color: #2f3542;
    }
    .main {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .topbar {
      background: #ffffff;
      padding: 15px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .topbar .logo {
      font-weight: 600;
      font-size: 20px;
      color: #2f3542;
    }
    .topbar .actions {
      display: flex;
      align-items: center;
    }
    .topbar .actions i {
      margin-left: 20px;
      font-size: 18px;
      cursor: pointer;
      color: #2f3542;
    }
    .dashboard {
      padding: 30px;
      overflow-y: auto;
      flex: 1;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }
    .card {
      background: #ffffff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .card i {
      font-size: 30px;
      margin-bottom: 10px;
      color: #ffa502;
    }
    .card h3 {
      font-size: 22px;
    }
    .card p {
      color: #57606f;
    }
    canvas {
      max-width: 100%;
      margin-top: 40px;
    }

    /* FORM STYLES */
    form {
      background: white;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      max-width: 600px;
      margin: 0 auto;
      color: #2f3542;
    }
    form h2 {
      margin-bottom: 20px;
      font-weight: 600;
      font-size: 24px;
      text-align: center;
      color: #ffa502;
    }
    form label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
    }
    form input[type="text"],
    form textarea,
    form select {
      width: 100%;
      padding: 10px 12px;
      margin-bottom: 20px;
      border: 1.5px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
      font-family: 'Poppins', sans-serif;
      resize: vertical;
      transition: border-color 0.3s ease;
    }
    form input[type="text"]:focus,
    form textarea:focus,
    form select:focus {
      border-color: #ffa502;
      outline: none;
    }
    form textarea {
      min-height: 100px;
    }
    form button.submit-btn {
      background-color: #ffa502;
      color: #2f3542;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 18px;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }
    form button.submit-btn:hover {
      background-color: #cc8400;
    }

    /* Google map container */
    #map-container {
      width: 100%;
      height: 300px;
      margin-bottom: 20px;
      border-radius: 8px;
      overflow: hidden;
      border: 1.5px solid #ccc;
    }

  </style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="user-info">
        <img src="https://via.placeholder.com/80" alt="User Avatar" />
        <h2>User Account</h2>
      </div>
      <ul>
        <li>
          <button id="btnDashboard"><i class="fas fa-chart-pie"></i> Dashboard</button>
        </li>
        <li>
          <button id="btnUser"><i class="fas fa-user"></i> User</button>
        </li>
        <li>
          <button id="btnService"><i class="fas fa-database"></i> Service</button>
        </li>
        <li>
          <button id="btnReport"><i class="fas fa-file-alt"></i> Report</button>
        </li>
        <li>
          <button id="btnMap"><i class="fas fa-map"></i> Map</button>
        </li>
        <li>
          <button id="btnCharts"><i class="fas fa-chart-line"></i> Charts</button>
        </li>
        <li>
          <button id="btnSettings"><i class="fas fa-cog"></i> Settings</button>
        </li>
        <li>
          <button id="btnLogout"><i class="fas fa-cog"></i> Logout</button>
        </li>
      </ul>
    </aside>

    <main class="main">
      <div class="topbar">
        <div class="logo">Dashboard</div>
        <div class="actions">
          <i class="fas fa-bell"></i>
          <i class="fas fa-envelope"></i>
          <i class="fas fa-user-circle"></i>
        </div>
      </div>

      <section class="dashboard" id="dashboardContent">
        <!-- Default cards content -->
        <div class="cards" id="defaultCards">
          <div class="card">
            <i class="fas fa-user"></i>
            <h3><?php echo $total_users; ?></></h3>
            <p>Total Users</p>
          </div>
          <div class="card">
            <i class="fas fa-clock"></i>
            <h3><?php echo $avg_time_spent; ?> hours</h3>
            <p>Average Time Spent</p>
          </div>
          <div class="card">
            <i class="fas fa-download"></i>
            <h3>0</h3>
            <p>Collections</p>
          </div>
          <div class="card">
            <i class="fas fa-file-alt"></i>
            <h3>0</h3>
            <p>Reports</p>
          </div>
        </div>

        <canvas id="barChart"></canvas>
      </section>

      <!-- Hidden Service form container -->
      <section class="dashboard" id="serviceFormSection" style="display:none;">
        <form id="serviceForm">
          <h2>Service Data Entry</h2>

          <label for="title">Title</label>
          <input type="text" id="title" name="title" placeholder="Enter title" required />

          <label for="description">Description</label>
          <textarea id="description" name="description" placeholder="Enter description" required></textarea>

          <label for="location">Location (Google Map)</label>
          <input type="text" id="location" name="location" placeholder="Enter location address" required />
          <div id="map-container">
            <!-- Google Map iframe will be embedded dynamically -->
            <iframe
              id="mapIframe"
              src="https://maps.google.com/maps?q=New York&t=&z=13&ie=UTF8&iwloc=&output=embed"
              width="100%"
              height="100%"
              style="border:0;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>

          <label for="category">Category</label>
          <select id="category" name="category" required>
            <option value="">Select a category</option>
            <option value="School">School</option>
            <option value="College">College</option>
            <option value="Hotel">Hotel</option>
            <option value="Organization">Organization</option>
            <option value="Tution">Tution</option>
          </select>

          <button type="submit" class="submit-btn">Submit</button>
        </form>
      </section>

    </main>
  </div>
  
  <script>
    document.getElementById('btnLogout').addEventListener('click', function () {
    window.location.href = 'logout.php'; // adjust path if needed
    });
  </script>

  

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Bar Chart initialization
    const ctx = document.getElementById('barChart').getContext('2d');
    let barChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Facebook', 'Twitter', 'LinkedIn', 'Google+'],
        datasets: [{
          label: 'Followers',
          data: [35000, 584000, 758, 450],
          backgroundColor: [
            '#3b5998', '#00acee', '#0077b5', '#db4437'
          ]
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Elements
    const btnService = document.getElementById('btnService');
    const btnDashboard = document.getElementById('btnDashboard');
    const dashboardContent = document.getElementById('dashboardContent');
    const serviceFormSection = document.getElementById('serviceFormSection');
    const defaultCards = document.getElementById('defaultCards');
    const barChartCanvas = document.getElementById('barChart');

    // Show default dashboard view
    function showDashboard() {
      dashboardContent.style.display = 'block';
      defaultCards.style.display = 'grid';
      barChartCanvas.style.display = 'block';
      serviceFormSection.style.display = 'none';
      document.querySelector('.topbar .logo').textContent = 'Dashboard';
    }

    // Show service form view
    function showServiceForm() {
      dashboardContent.style.display = 'none';
      serviceFormSection.style.display = 'block';
      document.querySelector('.topbar .logo').textContent = 'Service Form';
    }

    // Event listeners for buttons
    btnService.addEventListener('click', () => {
      showServiceForm();
    });

    btnDashboard.addEventListener('click', () => {
      showDashboard();
    });

    // Add more buttons as needed, for now only Service and Dashboard have behavior

    // Handle location input change to update Google Map iframe dynamically
    const locationInput = document.getElementById('location');
    const mapIframe = document.getElementById('mapIframe');

    locationInput.addEventListener('change', () => {
      const query = encodeURIComponent(locationInput.value.trim());
      if (query) {
        mapIframe.src = https://maps.google.com/maps?q=${query}&t=&z=13&ie=UTF8&iwloc=&output=embed;
      }
    });

    // Form submission handling
    const serviceForm = document.getElementById('serviceForm');
    serviceForm.addEventListener('submit', (e) => {
      e.preventDefault();

      // Gather form data
      const formData = {
        title: serviceForm.title.value.trim(),
        description: serviceForm.description.value.trim(),
        location: serviceForm.location.value.trim(),
        category: serviceForm.category.value,
      };

      // Simple validation (all required by HTML but let's confirm)
      if (!formData.title || !formData.description || !formData.location || !formData.category) {
        alert('Please fill out all fields.');
        return;
      }

      // For demonstration, just alert the data
      alert(`Form submitted!\n
Title: ${formData.title}
Description: ${formData.description}
Location: ${formData.location}
Category: ${formData.category}`);

      // Reset form
      serviceForm.reset();

      // Reset map to default or blank
      mapIframe.src = 'https://maps.google.com/maps?q=New York&t=&z=13&ie=UTF8&iwloc=&output=embed';
    });

    // Initially show dashboard view
    showDashboard();
  </script>
</body>
</html>
