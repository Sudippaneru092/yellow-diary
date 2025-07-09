<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

$success = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'gmail@gmail.com';         //  Gmail
            $mail->Password   = 'password';           // app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom($email, $name);
            $mail->addAddress('gmail@gmail.com');          //  receiving email

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = "
              <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.5;'>
                <p>Hello,</p>
                <p>A new form has been submitted on your website. Details below.</p>

                <br />

                <p style='margin-bottom: 4px; font-weight: bold;'>Name</p>
                <p style='margin-top: 0; margin-bottom: 15px; color: #555;'>{$name}</p>

                <p style='margin-bottom: 4px; font-weight: bold;'>Email</p>
                <p style='margin-top: 0; margin-bottom: 15px;'>
                  <a href='mailto:{$email}' style='color: #2a7ae2; text-decoration: none;'>{$email}</a>
                </p>

                <p style='margin-bottom: 4px; font-weight: bold;'>Message</p>
                <p style='margin-top: 0; white-space: pre-wrap; color: #555;'>{$message}</p>
              </div>
            ";

            $mail->send();
            $success = "Your message has been sent successfully!";
            $_POST = []; // Clear form values after success
        } catch (Exception $e) {
            $errors[] = "Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>


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
  <div class="alert-success" style="text-align:center;">
    <?= htmlspecialchars($success) ?>
  </div>
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
