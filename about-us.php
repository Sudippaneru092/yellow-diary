<?php
// about-us.php
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us - Your Company Name</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="main.css" />

  <style>
    /* Container & Typography */
    .container {
      max-width: 900px;
      margin: 3rem auto;
      padding: 0 1.5rem;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #222;
      line-height: 1.7;
    }

    .subtitle {
      text-align: center;
      font-size: 1.2rem;
      margin-bottom: 2rem;
      color: #666;
      font-style: italic;
    }
    section.content p {
      margin-bottom: 1.2rem;
      font-size: 1.1rem;
    }

    /* Team Section */
    .team-section {
      margin-top: 3rem;
    }
    .team-section h2 {
      font-size: 2rem;
      margin-bottom: 1.5rem;
      color: #007bff; /* secondary color */
      text-align: center;
    }
    .team-members {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      justify-content: center;
    }
    .member-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      padding: 1.5rem;
      max-width: 280px;
      flex: 1 1 280px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: default;
    }
    .member-card:focus,
    .member-card:hover {
      outline: none;
      transform: translateY(-6px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }
    .member-photo {
      width: 140px;
      height: 140px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 1rem;
      box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }
    .member-name {
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: 0.3rem;
      color: #222;
      text-align: center;
    }
    .member-role {
      font-size: 1rem;
      font-weight: 600;
      color: #007bff;
      margin-bottom: 0.75rem;
      text-align: center;
    }
    .member-bio {
      font-size: 0.95rem;
      color: #555;
      text-align: center;
      line-height: 1.4;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .team-members {
        flex-direction: column;
        align-items: center;
      }
      .member-card {
        max-width: 100%;
      }
    }
  </style>
</head>
<body>

<main class="container" role="main">
  <h1>About Us</h1>
  <p class="subtitle">Committed to Excellence and Innovation</p>

  <section class="content" aria-label="Company introduction">
    <p>Founded in 2010, <strong>Your Company Name</strong> has grown to become a leader in the [industry/sector]. Our mission is to deliver high-quality products and services that exceed customer expectations through innovation, dedication, and teamwork.</p>

    <p>We believe in building lasting relationships with our clients by providing tailored solutions that meet their unique needs. Our team of experts is passionate about what they do, continuously pushing boundaries to ensure we remain at the forefront of our field.</p>

    <p>At <strong>Your Company Name</strong>, we uphold our core values of integrity, transparency, and customer satisfaction. These principles guide everything we do and inspire us to constantly improve and evolve.</p>
  </section>

  <section class="team-section" aria-label="Meet the team">
    <h2>Meet Our Team</h2>
    <div class="team-members">
      <article class="member-card" tabindex="0">
        <img src="images/team1.jpg" alt="Photo of Jane Doe, CEO" class="member-photo" />
        <h3 class="member-name">Jane Doe</h3>
        <p class="member-role">Chief Executive Officer</p>
        <p class="member-bio">With over 15 years of experience, Jane leads the company with a vision for sustainable growth and innovation.</p>
      </article>

      <article class="member-card" tabindex="0">
        <img src="images/team2.jpg" alt="Photo of John Smith, CTO" class="member-photo" />
        <h3 class="member-name">John Smith</h3>
        <p class="member-role">Chief Technology Officer</p>
        <p class="member-bio">John is responsible for driving technological advancement and ensuring we stay ahead in the digital landscape.</p>
      </article>

      <article class="member-card" tabindex="0">
        <img src="images/team3.jpg" alt="Photo of Mary Johnson, COO" class="member-photo" />
        <h3 class="member-name">Mary Johnson</h3>
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
