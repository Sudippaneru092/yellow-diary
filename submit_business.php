<?php
session_start();
include "admin/db.php";

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors = [];
$success = false;

// Form fields with initial values
$fields = [
    'name' => '',
    'category' => '',
    'location' => '',
    'description' => '',
    'url' => '',
    'email' => '',
    'phone' => '',
    'latitude' => '',
    'longitude' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    foreach ($fields as $key => $value) {
        $fields[$key] = trim($_POST[$key] ?? '');
    }
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate inputs
    if (empty($fields['name'])) $errors['name'] = "Business name is required";
    if (empty($fields['category'])) $errors['category'] = "Category is required";
    if (empty($fields['location'])) $errors['location'] = "Location is required";
    if (empty($password)) $errors['password'] = "Password is required";
    if ($password !== $confirm_password) $errors['confirm_password'] = "Passwords don't match";
    if (strlen($password) < 8) $errors['password'] = "Password must be at least 8 characters";
    
    // Check if business URL or email already exists
    $check_stmt = $conn->prepare("SELECT id FROM businesses WHERE url = ? OR email = ?");
    $check_stmt->bind_param("ss", $fields['url'], $fields['email']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $errors['url'] = "A business with this website or email already exists";
    }
    $check_stmt->close();

    // Handle file upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors['image'] = "Only JPG, PNG, and GIF files are allowed";
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) { // 5MB
            $errors['image'] = "File size must be less than 5MB";
        } else {
            $upload_dir = 'admin/uploads/business_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = time() . '_' . uniqid() . '.' . $file_ext;
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = $target_path;
            } else {
                $errors['image'] = "Error uploading file";
            }
        }
    }

    // If no errors, insert into database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $slug = strtolower(str_replace(' ', '-', $fields['name']));
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        
        $stmt = $conn->prepare("INSERT INTO businesses 
                              (name, category, location, description, image, url, email, phone, password, slug, status, created_at, latitude, longitude) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), ?, ?)");
        
        $stmt->bind_param("ssssssssssss", 
            $fields['name'],
            $fields['category'],
            $fields['location'],
            $fields['description'],
            $image_path,
            $fields['url'],
            $fields['email'],
            $fields['phone'],
            $hashed_password,
            $slug,
            $fields['latitude'],
            $fields['longitude']
        );
        
        if ($stmt->execute()) {
            $success = true;
            $fields = array_fill_keys(array_keys($fields), ''); // Clear form
        } else {
            $errors['database'] = "Error saving business: " . $conn->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Your Business - Yellow Diary</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .registration-container {
      max-width: 800px;
      margin: 30px auto;
      padding: 30px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .registration-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .registration-header h2 {
      color: #333;
      margin-bottom: 10px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
    }
    .form-group input, 
    .form-group select, 
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }
    .form-group textarea {
      min-height: 100px;
    }
    .form-row {
      display: flex;
      gap: 20px;
    }
    .form-row .form-group {
      flex: 1;
    }
    .submit-btn {
      width: 100%;
      padding: 12px;
      background-color: #FFD700;
      color: #333;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .submit-btn:hover {
      background-color: #FFC800;
    }
    .error-message {
      color: #dc3545;
      font-size: 14px;
      margin-top: 5px;
    }
    .success-message {
      color: #28a745;
      text-align: center;
      margin-bottom: 20px;
      padding: 10px;
      background-color: #d4edda;
      border-radius: 4px;
    }
    .file-upload {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .file-upload-preview {
      width: 100px;
      height: 100px;
      border: 1px dashed #ddd;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .file-upload-preview img {
      max-width: 100%;
      max-height: 100%;
    }
  </style>
</head>
<body>

<!-- Reuse your header from index.php -->
<header class="header">
  <div class="navbar">
    <h1>Yellow Diary</h1>
    <nav class="nav">
      <a href="/">Home</a>
      <a href="service.php">Services</a>
      <a href="submit_business.php" class="active">Submit Business</a>
      <a href="contact.php">Contact</a>
      <a href="business_login.php">Business Login</a>
    </nav>
  </div>
</header>

<main class="main-content">
  <div class="registration-container">
    <div class="registration-header">
      <h2>Register Your Business</h2>
      <p>List your business in our directory to reach more customers</p>
    </div>

    <?php if ($success): ?>
      <div class="success-message">
        <i class="fas fa-check-circle"></i> Your business has been submitted for approval. You'll be notified once approved.
      </div>
    <?php endif; ?>

    <?php if (isset($errors['database'])): ?>
      <div class="error-message" style="text-align: center; margin-bottom: 20px;">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errors['database']); ?>
      </div>
    <?php endif; ?>

    <form action="submit_business.php" method="POST" enctype="multipart/form-data">
      <div class="form-row">
        <div class="form-group">
          <label for="name">Business Name *</label>
          <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($fields['name']); ?>" required>
          <?php if (isset($errors['name'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['name']); ?></span>
          <?php endif; ?>
        </div>
        
        <div class="form-group">
          <label for="category">Category *</label>
          <select id="category" name="category" required>
            <option value="">Select Category</option>
            <option value="Restaurants" <?php if ($fields['category'] == 'Restaurants') echo 'selected'; ?>>Restaurants</option>
            <option value="Hotels" <?php if ($fields['category'] == 'Hotels') echo 'selected'; ?>>Hotels</option>
            <option value="Education" <?php if ($fields['category'] == 'Education') echo 'selected'; ?>>Education</option>
            <option value="Healthcare" <?php if ($fields['category'] == 'Healthcare') echo 'selected'; ?>>Healthcare</option>
            <option value="Retail" <?php if ($fields['category'] == 'Retail') echo 'selected'; ?>>Retail</option>
            <option value="Services" <?php if ($fields['category'] == 'Services') echo 'selected'; ?>>Services</option>
          </select>
          <?php if (isset($errors['category'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['category']); ?></span>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="location">Location *</label>
          <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($fields['location']); ?>" required>
          <?php if (isset($errors['location'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['location']); ?></span>
          <?php endif; ?>
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($fields['email']); ?>">
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($fields['phone']); ?>">
        </div>
        
        <div class="form-group">
          <label for="url">Website URL</label>
          <input type="url" id="url" name="url" value="<?php echo htmlspecialchars($fields['url']); ?>">
          <?php if (isset($errors['url'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['url']); ?></span>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="form-group">
        <label for="description">Business Description</label>
        <textarea id="description" name="description"><?php echo htmlspecialchars($fields['description']); ?></textarea>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="latitude">Latitude (optional)</label>
          <input type="text" id="latitude" name="latitude" value="<?php echo htmlspecialchars($fields['latitude']); ?>">
        </div>
        
        <div class="form-group">
          <label for="longitude">Longitude (optional)</label>
          <input type="text" id="longitude" name="longitude" value="<?php echo htmlspecialchars($fields['longitude']); ?>">
        </div>
      </div>
      
      <div class="form-group">
        <label>Business Image</label>
        <div class="file-upload">
          <div class="file-upload-preview">
            <?php if (isset($image_path)): ?>
              <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Preview">
            <?php else: ?>
              <i class="fas fa-image" style="font-size: 24px; color: #ccc;"></i>
            <?php endif; ?>
          </div>
          <input type="file" name="image" accept="image/*">
        </div>
        <?php if (isset($errors['image'])): ?>
          <span class="error-message"><?php echo htmlspecialchars($errors['image']); ?></span>
        <?php endif; ?>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="password">Password *</label>
          <input type="password" id="password" name="password" required>
          <?php if (isset($errors['password'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['password']); ?></span>
          <?php endif; ?>
        </div>
        
        <div class="form-group">
          <label for="confirm_password">Confirm Password *</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
          <?php if (isset($errors['confirm_password'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['confirm_password']); ?></span>
          <?php endif; ?>
        </div>
      </div>
      
      <button type="submit" class="submit-btn">Submit Business</button>
    </form>
    
    <div class="login-footer" style="text-align: center; margin-top: 20px;">
      <p>Already have an account? <a href="business_login.php">Login here</a></p>
    </div>
  </div>
</main>

<!-- Reuse your footer from index.php -->
<footer class="footer">
  <p>&copy; <?php echo date('Y'); ?> Yellow Diary. All rights reserved.</p>
  <p>
    <a href="privacy.php">Privacy Policy</a> | 
    <a href="terms.php">Terms of Service</a> |
    <a href="admin_login.php">Admin Login</a>
  </p>
</footer>

<script>
  // Image preview functionality
  document.querySelector('input[name="image"]').addEventListener('change', function(e) {
    const preview = document.querySelector('.file-upload-preview');
    const file = e.target.files[0];
    
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
      }
      reader.readAsDataURL(file);
    } else {
      preview.innerHTML = '<i class="fas fa-image" style="font-size: 24px; color: #ccc;"></i>';
    }
  });

  // Simple location autocomplete (would need proper implementation with Google Maps API)
  document.getElementById('location').addEventListener('input', function(e) {
    // In a real implementation, you would fetch suggestions from an API here
  });
</script>

</body>
</html>