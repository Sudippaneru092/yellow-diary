<?php
session_start();
session_destroy();
unset($_SESSION['email']);
header("location:http://localhost/Questionbank/admin/admin.php");
?>