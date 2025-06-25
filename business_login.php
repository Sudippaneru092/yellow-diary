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
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="main.css" />

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
      color: #333;
    }
    .main-content {
      min-height: calc(100vh - 150px);
      display: flex;
      align-items: center;
    }
    .login-container {
      max-width: 500px;
      width: 100%;
      margin: 30px auto;
      padding: 40px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .login-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .login-header h2 {
      color: #333;
      margin-bottom: 10px;
      font-size: 28px;
    }
    .login-header p {
      color: #666;
    }
    .form-group {
      margin-bottom: 25px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #444;
    }
    .form-group input {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 16px;
      transition: border-color 0.3s;
    }
    .form-group input:focus {
      border-color: #FFD700;
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
    }
    .login-btn {
      width: 100%;
      padding: 14px;
      background-color: #FFD700;
      color: #333;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .login-btn:hover {
      background-color: #FFC800;
    }
    .error-message {
      color: #dc3545;
      margin-bottom: 20px;
      padding: 12px;
      background-color: #f8d7da;
      border-radius: 6px;
      text-align: center;
      border: 1px solid #f5c6cb;
    }
    .login-footer {
      text-align: center;
      margin-top: 25px;
      color: #666;
    }
    .login-footer a {
      color: #FFD700;
      text-decoration: none;
      font-weight: 500;
    }
    .login-footer a:hover {
      text-decoration: underline;
    }
    .footer {
      background-color: #333;
      color: #fff;
      text-align: center;
      padding: 20px 0;
      margin-top: auto;
    }
    .footer a {
      color: #FFD700;
      text-decoration: none;
      margin: 0 10px;
    }
    .footer a:hover {
      text-decoration: underline;
    }
  </style>
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