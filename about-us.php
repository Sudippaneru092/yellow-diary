<?php
session_start();

// about-us.php
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us - Your Company Name</title>
  <link rel="stylesheet" href="main.css" />
</head>
<body>

<main class="container" role="main">
  <h1>About Us</h1>
  <h1>Welcome to Yellow Diary –<span>Your Local Business Guide!</span> </h1>
  <p class="subtitle">Committed to Excellence and Innovation</p>

  <section class="content" aria-label="Company introduction">
    <p>At Yellow Diary, we believe in empowering communities by connecting people with the businesses, services, and opportunities that matter most. Whether you're looking for a trusted restaurant, a nearby hospital, a school, or even the latest job openings and local events – Yellow Diary is your go-to platform.

We are dedicated to creating a digital space where business owners can showcase their services, and users can easily discover everything they need – all in one place. Our mission is to make local discovery simple, smart, and accessible for everyone.</p>
    </section>

  <section class="team-section" aria-label="Meet the team">
    <h2>Meet Our Team</h2>
    <div class="team-members">
      <article class="member-card" tabindex="0">
        <img src="images/team1.jpg" alt="Photo of Jane Doe, CEO" class="member-photo" />
        <h3 class="member-name">Uttam Saud</h3>
        <p class="member-role">Chief Executive Officer</p>
        <p class="member-bio">With over 15 years of experience, Jane leads the company with a vision for sustainable growth and innovation.</p>
      </article>

      <article class="member-card" tabindex="0">
        <img src="images/team2.jpg" alt="Photo of John Smith, CTO" class="member-photo" />
        <h3 class="member-name">Rakesh Auji</h3>
        <p class="member-role">Chief Technology Officer</p>
        <p class="member-bio">John is responsible for driving technological advancement and ensuring we stay ahead in the digital landscape.</p>
      </article>

      <article class="member-card" tabindex="0">
        <img src="images/team3.jpg" alt="Photo of Mary Johnson, COO" class="member-photo" />
        <h3 class="member-name">Sudip paneru</h3>
        <p class="member-role">Chief Operating Officer</p>
        <p class="member-bio">Oversees the daily operations of the organization and ensure that all departments are aligned with the strategic goals. </p>
      </article>
        <article class="member-card" tabindex="0">
        <img src="images/team4.jpg" alt="Photo of Mary Johnson, COO" class="member-photo" />
        <h3 class="member-name">Samir Kusmi</h3>
        <p class="member-role">Chief Operating Officer</p>
        <p class="member-bio">Mary oversees daily operations, ensuring efficiency and excellent service delivery across all departments.</p>
      </article>
    </div>
  </section>
</main>

<?php
include 'footer.php';
?>
</body>
</html>
