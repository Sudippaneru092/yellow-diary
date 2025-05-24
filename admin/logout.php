<?php
session_start();
session_unset();           // Optional but good practice before destroying
session_destroy();         // Destroys all session data

// Redirect to login page or homepage
header("Location: http://localhost/project/yellow-diary/admin/admin.php");

exit(); // Always add exit() after header redirect
?>
