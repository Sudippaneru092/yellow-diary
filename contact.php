<?php
session_start();
include "admin/db.php";

$errors = [];
$success = false;

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate inputs
    if (!$name) $errors[] = "Name is required.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!$subject) $errors[] = "Subject is required.";
    if (!$message) $errors[] = "Message is required.";

    if (empty($errors)) {
        // You can add here code to save to DB or send email
        // For example: mail(...) or insert into contact_messages table

        $success = "Thank you for contacting us! We will get back to you shortly.";
        // Clear form values
        $_POST = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Us - Yellow Diary</title>
  <link rel="stylesheet" href="main.css" />
  <style>
    /* Contact Us Page Styling */
    .contact-container {
      max-width: 600px;
      margin: 3rem auto;
      padding: 2rem;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      animation: fadeIn 0.3s ease-in-out;
    }
    .contact-container h1 {
      text-align: center;
      color: var(--primary-color, #ffc107);
      margin-bottom: 1.5rem;
      font-weight: 700;
    }
    form.contact-form {
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }
    label {
      font-weight: 600;
      color: #333;
    }
    input[type="text"], input[type="email"], textarea {
      width: 100%;
      padding: 0.9rem 1rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      background: #f9f9f9;
      transition: border-color 0.3s ease;
    }
    input[type="text"]:focus, input[type="email"]:focus, textarea:focus {
      border-color: var(--primary-color, #ffc107);
      background: #fff;
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
    }
    textarea {
      min-height: 120px;
      resize: vertical;
    }
    button[type="submit"] {
      background: var(--primary-color, #ffc107);
      color: #222;
      border: none;
      padding: 0.9rem;
      font-weight: 700;
      font-size: 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }
    button[type="submit"]:hover {
      background: var(--primary-dark, #e0a800);
      transform: translateY(-2px);
    }
    .alert-success, .alert-error {
      max-width: 600px;
      margin: 0 auto 1.5rem;
      padding: 1rem;
      border-radius: 8px;
      font-weight: 600;
    }
    .alert-success {
      background: #d4edda;
      border: 1px solid #c3e6cb;
      color: #155724;
    }
    .alert-error {
      background: #f8d7da;
      border: 1px solid #f5c6cb;
      color: #721c24;
    }
    .alert-error ul {
      margin-left: 1.5rem;
      list-style: disc;
    }

    /* Responsive */
    @media (max-width: 640px) {
      .contact-container {
        margin: 2rem 1rem;
        padding: 1.5rem;
      }
      button[type="submit"] {
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>

<?php include "header.php"; ?>

<div class="contact-container">
  <h1>Contact Us</h1>

  <?php if ($success): ?>
    <div class="alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="alert-error">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form class="contact-form" method="POST" action="">
    <label for="name">Name*</label>
    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />

    <label for="email">Email*</label>
    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />

    <label for="subject">Subject*</label>
    <input type="text" id="subject" name="subject" required value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" />

    <label for="message">Message*</label>
    <textarea id="message" name="message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>

    <button type="submit">Send Message</button>
  </form>
</div>

<?php include "footer.php"; ?>

</body>
</html>
