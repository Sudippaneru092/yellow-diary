<?php
session_start(); // Start session to save email

// Include DB connection
include 'admin/db.php'; // adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        echo "<div style='
        color: #fff;
        background-color: #e74c3c;
        padding: 15px;
        border-radius: 5px;
        font-family: Arial, sans-serif;
        margin: 10px 0;
        font-weight: bold;
        '>
            ⚠️ Email field is required.
        </div>";
        exit;
    }

    // Check if the email exists in businesses table
    $stmt = $conn->prepare("SELECT * FROM businesses WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Email not found
        echo "<div style='
        color: #fff;
        background-color: #e74c3c;
        padding: 15px;
        border-radius: 5px;
        font-family: Arial, sans-serif;
        margin: 10px 0;
        font-weight: bold;
        '>
            ❌ No account found with this email address.
        </div>";
        exit;
    }
    

    // Save email to session before redirecting
     $_SESSION['reset_email'] = $email;

    header("Location: phpmailer/send_reset_link.php");
    exit;
}
?>
<?php include "header.php"; ?>
<div class="container mt-5">
  <h2>Forgot Password</h2>
  <form action="" method="post">
    <label>Business Email:</label>
    <input type="email" name="email" required class="form-control mb-2">
    <button type="submit" name="submit" class="btn btn-warning">Send Reset Link</button>
  </form>
</div>
<?php include "footer.php"; ?>
