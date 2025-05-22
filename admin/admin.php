
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <title>Admin Login / Signup - Yellow Diary</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      display: flex;
      justify-content: center;
      padding: 2rem;
    }
    .container {
      width: 400px;
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 10px #ccc;
    }
    .tab-buttons {
      display: flex;
      justify-content: space-around;
      margin-bottom: 1rem;
    }
    .tab-buttons button {
      flex: 1;
      padding: 0.8rem;
      cursor: pointer;
      border: none;
      background: #fdd835;
      font-weight: bold;
    }
    .tab-buttons button.active {
      background: #fbc02d;
    }
    form {
      display: none;
      flex-direction: column;
    }
    form.active {
      display: flex;
    }
    input {
      margin-bottom: 1rem;
      padding: 0.7rem;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 100%;
    }
    button.submit-btn {
      background: #fdd835;
      border: none;
      padding: 0.8rem;
      font-weight: bold;
      cursor: pointer;
      border-radius: 5px;
    }
  </style>
 
  
</head>
<body class="hold-transition login-page">


<div class="container">
  <h3 style="text-align:center;">Admin Login / Signup</h3>
  <div class="tab-buttons">
    <button id="loginTab" class="active" onclick="switchForm('login')">Login</button>
    <button id="signupTab" onclick="switchForm('signup')">Signup</button>
  </div>

  <!-- Admin Login Form -->
  <form id="loginForm" class="active" method="POST" action="admin_login.php">
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit" class="submit-btn">Login</button>
  </form>

  <!-- Admin Signup Form -->
  <form id="signupForm" method="POST" action="admin_signup.php">
    <input type="text" name="fullName" placeholder="Full Name" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <input type="password" name="confirmPassword" placeholder="Confirm Password" required />
    <button type="submit" class="submit-btn">Signup</button>
  </form>
</div>

<script>
  function switchForm(form) {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');

    if (form === 'login') {
      loginForm.classList.add('active');
      signupForm.classList.remove('active');
      loginTab.classList.add('active');
      signupTab.classList.remove('active');
    } else {
      signupForm.classList.add('active');
      loginForm.classList.remove('active');
      signupTab.classList.add('active');
      loginTab.classList.remove('active');
    }
  }
</script>

<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>
