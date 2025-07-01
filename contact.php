<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Us - Yellow Diary</title>
  <link rel="stylesheet" href="main.css" />

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
