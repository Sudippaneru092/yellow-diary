<?php
ini_set('display_errors', 0); // disable errors for JSON
error_reporting(0);
include "db.php"; // adjust path if needed

header('Content-Type: application/json');

$city_id = isset($_GET['city_id']) && is_numeric($_GET['city_id']) ? intval($_GET['city_id']) : 0;
if (!$city_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT name FROM areas WHERE city_id = ? ORDER BY name");
if (!$stmt) {
    echo json_encode([]);
    exit;
}

$stmt->bind_param("i", $city_id);
$stmt->execute();
$result = $stmt->get_result();

$areas = [];
while ($row = $result->fetch_assoc()) {
    $areas[] = $row['name'];
}

echo json_encode($areas);

$stmt->close();
$conn->close();
