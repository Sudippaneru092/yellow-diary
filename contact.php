<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Us - Yellow Diary</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f9f9f9;
      color: #333;
    }

    header {
      background-color: #fdd835;
      padding: 1rem;
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

    .contact-container {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 2rem;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .contact-container h1 {
      text-align: center;
      margin-bottom: 2rem;
    }

    .contact-details, .contact-form {
      margin-bottom: 2rem;
    }

    .contact-details p {
      line-height: 1.6;
      color: #555;
    }

    .contact-form form {
      display: flex;
      flex-direction: column;
    }

    .contact-form input, .contact-form textarea {
      padding: 0.8rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
    }

    .contact-form button {
      background-color: #fdd835;
      color: #333;
      padding: 0.8rem;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
    }

    .contact-form button:hover {
      background-color: #fbc02d;
    }

    .map {
      width: 100%;
      height: 300px;
      margin-top: 2rem;
      border: 0;
    }

    footer {
      background-color: #333;
      color: #fff;
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
    }

    @media (max-width: 600px) {
      .navbar, .nav {
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

  <!-- Contact Page Content -->
  <main class="contact-container">
    <h1>Contact Us</h1>

    <div class="contact-details">
      <p><strong>Email:</strong> support@yellowdiary.com</p>
      <p><strong>Phone:</strong> +977-9800000000</p>
      <p><strong>Address:</strong> Dhangadhi, Kailali, Nepal</p>
      <p>Feel free to contact us for business listings, partnerships, or feedback. We’re happy to hear from you!</p>
    </div>

    <div class="contact-form">
      <form action="#" method="POST">
        <input type="text" name="name" placeholder="Your Name" required />
        <input type="email" name="email" placeholder="Your Email" required />
        <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
        <button type="submit">Send Message</button>
      </form>
    </div>

    <!-- Optional Google Map -->
    <iframe class="map"
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28264.50978307976!2d80.57456885!3d28.70546035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3997b51bbf247369%3A0xc1b9b61a2b15305d!2sDhangadhi!5e0!3m2!1sen!2snp!4v1681904440914"
      allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </main>

  <!-- Footer -->
  <footer>
    &copy; 2025 Yellow Diary. All rights reserved.
  </footer>

</body>
</html>
