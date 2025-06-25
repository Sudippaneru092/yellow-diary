<?php
include "admin/db.php";

header('Content-Type: application/json');

$city_id = (int) ($_GET['city_id'] ?? 0);

$areas = [];
if ($city_id > 0) {
    $stmt = $conn->prepare("SELECT id, name FROM areas WHERE city_id = ? ORDER BY name");
    $stmt->bind_param("i", $city_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $areas[] = $row;
    }
    $stmt->close();
}

echo json_encode($areas);
