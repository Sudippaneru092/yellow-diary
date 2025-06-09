<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

include 'db.php';

$id = (int) $_GET['id'];
$conn->query("DELETE FROM businesses WHERE id = $id");

header("Location: manage-business.php");
exit;
