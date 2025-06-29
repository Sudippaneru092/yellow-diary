<?php
include 'db.php';

// Validate city_id parameter
if (!isset($_GET['city_id']) || !is_numeric($_GET['city_id'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$city_id = (int)$_GET['city_id'];

// Prepare and execute query to fetch areas for the city
$stmt = $conn->prepare("SELECT id, name FROM areas WHERE city_id = ? ORDER BY name");
$stmt->bind_param("i", $city_id);
$stmt->execute();
$result = $stmt->get_result();

$areas = [];
while ($row = $result->fetch_assoc()) {
    $areas[] = $row;
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($areas);
exit;
?>
