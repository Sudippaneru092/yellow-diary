<?php
// File: user_management.php
require 'includes/database_connect.php';
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    die('Access denied. Admins only.');
}

// Fetch all users
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
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
            /* Style for the user management table */
    .user-table-container {
    background: white;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-top: 20px;
    overflow-x: auto;
    }

    .user-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 16px;
    color: #2f3542;
    }

    .user-table th,
    .user-table td {
    padding: 12px 15px;
    border: 1px solid #dfe4ea;
    text-align: left;
    }

    .user-table th {
    background-color: #ffa502;
    color: white;
    font-weight: 600;
    }

    .user-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
    }

    .user-table a {
    margin-right: 8px;
    text-decoration: none;
    color: #007bff;
    font-weight: 500;
    transition: color 0.2s;
    }

    .user-table a:hover {
    color: #0056b3;
    }


    </style>
</head>
<body>
  <div class="container">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main">
      <?php include 'includes/header.php'; ?>

   
       <div class="user-table-container">
        <h1>User Management</h1>
        <a href="user_create.php" class="btn btn-success mb-3">+ Add New User</a>

        <table class="user-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Active</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['user_type'] ?></td>
                <td><?= $user['is_active'] ? 'Yes' : 'No' ?></td>
                <td><?= $user['created_at'] ?></td>
                <td>
                    <a href="user_edit.php?id=<?= $user['id'] ?>">Edit</a>
                    <a href="user_toggle.php?id=<?= $user['id'] ?>">
                    <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                    </a>
                    <a href="user_delete.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>

      </div>
    </div> <!-- End main -->
  </div> <!-- End container -->
</body>
</html>