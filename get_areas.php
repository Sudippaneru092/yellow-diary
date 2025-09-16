<?php
include "admin/db.php";

$city_id = intval($_GET['city_id'] ?? 0);

$areas = [];
if ($city_id > 0) {
    $result = $conn->query("SELECT id, name FROM areas WHERE city_id = $city_id ORDER BY name");
    while ($row = $result->fetch_assoc()) {
        // Always return "name"
        $areas[] = ["id" => $row['id'], "name" => $row['name']];
    }
}

header('Content-Type: application/json');
echo json_encode($areas);