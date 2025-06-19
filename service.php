<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Services - Yellow Diary</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      line-height: 1.6;
      background-color: #f9f9f9;
    }
    header {
      background-color: #fdd835;
      padding: 1rem;
      color: #333;
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    .navbar h1 {
      margin: 0;
    }
    .nav a {
      margin-left: 1rem;
      text-decoration: none;
      color: #333;
      font-weight: bold;
    }

    .services {
      padding: 2rem;
      max-width: 1000px;
      margin: auto;
      background-color: #fff;
    }
    .services h1 {
      text-align: center;
      margin-bottom: 2rem;
      color: #444;
    }
    .service-section {
      margin-bottom: 2rem;
    }
    .service-section h2 {
      color: #222;
      margin-bottom: 0.5rem;
    }
    .service-section p {
      margin-bottom: 1rem;
      color: #555;
    }
    .service-section ul {
      list-style-type: disc;
      padding-left: 1.5rem;
      color: #444;
    }
    .categories-list {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-top: 1rem;
    }
    .categories-list span {
      background-color: #ffeb3b;
      padding: 0.5rem 1rem;
      border-radius: 25px;
      font-size: 1rem;
    }

    footer {
      background-color: #333;
      color: #fff;
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
    }

    @media (max-width: 600px) {
      .navbar, .categories-list {
        flex-direction: column;
        align-items: center;
      }
      .nav a {
        margin: 0.5rem 0;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <?php include "header.html";?>

  <!-- Services Section -->
  <section class="services">
    <h1>Our Services</h1>

    <div class="service-section">
      <h2>📢 For Business Owners</h2>
      <p>Grow your local presence by showcasing your business in Yellow Diary.</p>
      <ul>
        <li>List your business for free</li>
        <li>Post job openings, announcements, or events</li>
        <li>Feature your listing on the homepage</li>
        <li>Easy dashboard for managing your profile (coming soon!)</li>
      </ul>
    </div>

    <div class="service-section">
      <h2>🔍 For Users</h2>
      <p>Quickly find trusted local services, businesses, and updates.</p>
      <ul>
        <li>Search by category, keyword, or location</li>
        <li>Find schools, restaurants, hospitals, hotels, and more</li>
        <li>See job openings and local event announcements</li>
        <li>Get directions and contact details instantly</li>
      </ul>
    </div>

    <div class="service-section">
      <h2>📂 Categories We Cover</h2>
      <div class="categories-list">
        <span>🏫 Schools</span>
        <span>🏥 Hospitals</span>
        <span>🍴 Restaurants</span>
        <span>🏨 Hotels</span>
        <span>💼 Jobs</span>
        <span>📢 Announcements</span>
        <span>📅 Events</span>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    &copy; 2025 Yellow Diary. All rights reserved.
  </footer>
</body>
</html>
