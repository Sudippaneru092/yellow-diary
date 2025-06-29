<?php
session_start();
include "admin/db.php";

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$error = '';
$login = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($login) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // Check login attempts (prevent brute force)
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts 
                              WHERE ip_address = ? AND last_attempt > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $attempts = $stmt->get_result()->fetch_assoc()['attempts'];
        $stmt->close();

        if ($attempts >= 5) {
            $error = "Too many login attempts. Please try again later.";
        } else {
            // Prepare statement to check both email and url fields
            $stmt = $conn->prepare("SELECT id, name, password, status, email, url FROM businesses 
                                   WHERE email = ? OR url = ?");
            if (!$stmt) {
                $error = "Database error. Please try again later.";
                error_log("Prepare failed: " . $conn->error);
            } else {
                $stmt->bind_param("ss", $login, $login);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $business = $result->fetch_assoc();
                    
                    // Verify password
                    if (password_verify($password, $business['password'])) {
                        // Check if business is approved
                        if ($business['status'] == 'approved') {
                            // Regenerate session ID for security
                            session_regenerate_id(true);
                            
                            // Set session variables
                            $_SESSION['business_id'] = $business['id'];
                            $_SESSION['business_name'] = $business['name'];
                            $_SESSION['business_email'] = $business['email'];
                            $_SESSION['loggedin'] = true;
                            
                            // Update last login time
                            $conn->query("UPDATE businesses SET last_login = NOW() WHERE id = {$business['id']}");
                            
                            // Clear any failed attempts
                            $conn->query("DELETE FROM login_attempts WHERE ip_address = '{$ip}'");
                            
                            // Redirect to dashboard
                            header("Location: index.php");
                            exit();
                        } else {
                            $error = "Your account is pending approval. Please contact support.";
                        }
                    } else {
                        $error = "Invalid login credentials";
                        // Log failed attempt
                        $conn->query("INSERT INTO login_attempts (ip_address, attempts, last_attempt) 
                                     VALUES ('{$ip}', 1, NOW()) 
                                     ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
                    }
                } else {
                    $error = "Invalid login credentials";
                    // Log failed attempt
                    $conn->query("INSERT INTO login_attempts (ip_address, attempts, last_attempt) 
                                 VALUES ('{$ip}', 1, NOW()) 
                                 ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Business Login - Yellow Diary</title>
 
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="main.css"/>
</head>
<body>

<header class="header">
  <div class="navbar">
    <h1>Yellow Diary</h1>
    <nav class="nav">
      <a href="/">Home</a>
      <a href="service.php">Services</a>
      <a href="submit_business.php">Submit Business</a>
      <a href="contact.php">Contact</a>
      <a href="business_login.php" class="active">Business Login</a>
    </nav>
  </div>
</header>

<main class="main-content">
  <div class="login-container">
    <div class="login-header">
      <h2>Business Login</h2>
      <p>Access your business dashboard to manage your listing</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="error-message">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form action="business_login.php" method="POST">
      <div class="form-group">
        <label for="login">Business Email or Website URL</label>
        <input type="text" id="login" name="login" required 
               placeholder="Enter your registered email or website URL"
               value="<?php echo htmlspecialchars($login); ?>">
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required 
               placeholder="Enter your password" minlength="8">
      </div>
      
      <button type="submit" class="login-btn">Login</button>
    </form>

    <div class="login-footer">
      <p>Don't have an account? <a href="submit_business.php">Register your business</a></p>
      <p><a href="forgot_password.php">Forgot password?</a></p>
    </div>
  </div>
</main>

<footer class="footer">
  <p>&copy; <?php echo date('Y'); ?> Yellow Diary. All rights reserved.</p>
  <p>
    <a href="privacy.php">Privacy Policy</a> | 
    <a href="terms.php">Terms of Service</a> |
    <a href="admin_login.php">Admin Login</a>
  </p>
</footer>

</body>
</html>