<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Services - Yellow Diary</title>
  <link rel="stylesheet" href="assets/css/styles.css" />
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
  <style>
    /* ===== Services Page Styles ===== */
.services {
  max-width: 960px;
  margin: 3rem auto;
  padding: 0 1.25rem;
  animation: fadeIn 0.6s ease;
}

.services h1 {
  text-align: center;
  font-size: 2.5rem;
  color: var(--primary-dark);
  margin-bottom: 2.5rem;
  position: relative;
}

.services h1::after {
  content: "";
  display: block;
  width: 60px;
  height: 4px;
  background: var(--primary-color);
  margin: 0.75rem auto 0;
  border-radius: 2px;
}

.service-section {
  background: #fff;
  padding: 1.5rem;
  border-left: 5px solid var(--primary-color);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-sm);
  margin-bottom: 2rem;
  transition: var(--transition);
}

.service-section:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-md);
}

.service-section h2 {
  font-size: 1.6rem;
  color: var(--primary-dark);
  margin-bottom: 0.75rem;
}

.service-section p {
  font-size: 1rem;
  color: var(--text-muted);
  margin-bottom: 0.75rem;
  line-height: 1.7;
}

.service-section ul {
  list-style: none;
  padding-left: 1rem;
  color: var(--text-dark);
}

.service-section ul li {
  margin-bottom: 0.5rem;
  position: relative;
  padding-left: 1.25rem;
}

.service-section ul li::before {
  content: "✔";
  color: var(--primary-color);
  font-weight: bold;
  position: absolute;
  left: 0;
  top: 0;
}

/* ===== Categories List Styling ===== */
.categories-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-top: 1rem;
}

.categories-list span {
  background: #f1f1f1;
  padding: 0.6rem 1.2rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.95rem;
  color: var(--text-dark);
  box-shadow: var(--shadow-sm);
  transition: var(--transition);
}

.categories-list span:hover {
  background: var(--primary-color);
  color: var(--dark-bg);
  transform: scale(1.05);
}

/* ===== Footer for Services Page ===== */
footer {
  background: var(--dark-bg);
  color: var(--text-light);
  text-align: center;
  padding: 1rem 0;
  font-size: 0.95rem;
  margin-top: 3rem;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.05);
}

/* ===== Responsive for Services ===== */
@media (max-width: 576px) {
  .services h1 {
    font-size: 2rem;
  }

  .service-section {
    padding: 1rem;
  }

  .service-section h2 {
    font-size: 1.3rem;
  }

  .categories-list {
    justify-content: center;
  }
}

  </style>
 
</head>
<body>
  <!-- Header -->
  <?php include "header.php";?>

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
