<?php

// Instructions to add sender email
// goto gmail setting and first turn-on 2 factor auth
// search for app password and create app and copy password



session_start();
$email = '';  // Initialize variable to avoid undefined warning

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;     // Use the PHPMailer class from the PHPMailer namespace
use PHPMailer\PHPMailer\SMTP; // Use the SMTP class for SMTP constants/debugging
use PHPMailer\PHPMailer\Exception; // Use the Exception class to handle errors

// Include the PHPMailer class files manually (alternative to autoload)
// NOTE: This is generally not needed if using Composer autoload
include 'PHPMailer.php';
include 'SMTP.php';
include 'Exception.php';
include 'code.php';
include '../admin/db.php'; // Assuming your DB connection is here


// Load Composer's autoloader (if you installed PHPMailer via Composer)
// This automatically loads all necessary classes
//require 'vendor/autoload.php';

// Create an instance of PHPMailer class
// Passing `true` enables exceptions to be thrown on errors (helps with error handling)
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 0;// diablesa verbose debug output (prints SMTP communication)
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Enable verbose debug output (prints SMTP communication)
    $mail->isSMTP();                        // Set mailer to use SMTP (instead of PHP mail function)
    $mail->Host       = 'smtp.gmail.com';  // Specify main and backup SMTP servers (Gmail SMTP server)
    $mail->SMTPAuth   = true;               // Enable SMTP authentication (required for Gmail)
    $mail->Username   = 'gmailexample@gmail.com';    // SMTP username (your Gmail email)            gmail goes here
    $mail->Password   = 'password';           // SMTP password (your Gmail password or app-specific password)    app password goes here without space
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption (STARTTLS)
    $mail->Port       = 587;                // TCP port to connect to (587 is default for STARTTLS)

    // Get email from POST or session 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
        $email = trim($_POST['email']);
    } elseif (isset($_SESSION['reset_email'])) {
        $email = $_SESSION['reset_email'];
    } else {
        throw new Exception("Email is not registered");
    }
        

    // Recipients
    $mail->setFrom('gmail.com', 'Yellow Diary');       // Set sender's email and name
    //    $mail->addAddress('joe@example.net', 'Joe User');   // Add a recipient with name
    $mail->addAddress($email);             // Add another recipient without a name
    // $mail->addReplyTo('', '');                   // Add a reply-to email address and name
    // $mail->addCC('cc@example.com');                      // Add a CC (carbon copy) recipient
    // $mail->addBCC('bcc@example.com');                    // Add a BCC (blind carbon copy) recipient
    // Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');        // Add a file attachment from this path
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');   // Add another attachment and rename it in the email
    $token = bin2hex(random_bytes(16)); // 32-character token
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store token in DB
    $stmt = $conn->prepare("UPDATE businesses SET reset_token = ?, token_expiry = ? WHERE email = ?");
    $stmt->bind_param("sss", $token, $expiry, $email);
    $stmt->execute();
    $reset_link = "https://localhost/yellow-diary/reset_password.php?token=$token";

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset';
    $mail->Body = "
        <h2>Password Reset</h2>
        <p>We received a request to reset your password. Please click the link below to reset it:</p>
        <p><a href=\"$reset_link\">$reset_link</a></p>
        <p>If you did not request this, please ignore this email.</p>
    ";

    $mail->AltBody = "We received a request to reset your password. Use this link to reset it:\n$reset_link\nIf you did not request this, ignore this email.";

    $mail->send();
    echo "Verification email sent successfully!";

exit;  // always call exit after header redirect
} catch (Exception $e) {
    // Handle exceptions/errors when sending email
        error_log("Mailer Error: {$mail->ErrorInfo}");  // Saved in server log
    echo "<div style='
        color: #fff;
        background-color: #e74c3c;
        padding: 15px;
        border-radius: 5px;
        font-family: Arial, sans-serif;
        margin: 10px 0;
        font-weight: bold;
    '>
        ❌ Something went wrong while sending the reset email. Please try again later.
    </div>";
}
