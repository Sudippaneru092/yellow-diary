<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid business ID.";
    header('Location: manage_business.php');
    exit;
}

$id = intval($_GET['id']);

// First, fetch the current image path to delete the file if exists
$stmt = $conn->prepare("SELECT image FROM businesses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$business = $result->fetch_assoc();
$stmt->close();

if (!$business) {
    $_SESSION['error'] = "Business not found.";
    header('Location: manage_business.php');
    exit;
}

// Delete the business record
$stmt = $conn->prepare("DELETE FROM businesses WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Delete the image file if exists
    if ($business['image'] && file_exists($business['image'])) {
        unlink($business['image']);
    }
    $_SESSION['success'] = "Business deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete business.";
}

$stmt->close();

header('Location: manage_business.php');
exit;
