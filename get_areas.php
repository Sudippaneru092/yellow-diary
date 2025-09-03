<?php
include "admin/db.php";

header('Content-Type: application/json');

if (!isset($_GET['city_id']) || empty($_GET['city_id'])) {
    echo json_encode([]);
    exit;
}

$city_id = intval($_GET['city_id']);

// Fetch all areas for this city
$areas = $conn->query("SELECT name FROM areas WHERE city_id = $city_id ORDER BY name");

$result = [];
while ($row = $areas->fetch_assoc()) {
    $result[] = $row['name']; // just return names, keeps it simple
}

echo json_encode($result);
