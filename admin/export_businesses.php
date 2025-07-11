<?php
include 'db.php';

$filter_start = $_GET['start_date'] ?? '';
$filter_end = $_GET['end_date'] ?? '';

$where = [];
$params = [];

if ($filter_start && $filter_end) {
    $where[] = "DATE(created_at) BETWEEN ? AND ?";
    $params[] = $filter_start;
    $params[] = $filter_end;
}

$where_clause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$types = str_repeat('s', count($params));

// Prepare SQL
$sql = "SELECT id, name, category, location, description, image, url, created_at FROM businesses $where_clause ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Set headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=businesses_export.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Output CSV column headers
fputcsv($output, ['ID', 'Name', 'Category', 'Location', 'Description', 'Image', 'URL', 'Created At']);

// Output each row
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['category'],
        $row['location'],
        strip_tags($row['description']),
        $row['image'],
        $row['url'],
        $row['created_at']
    ]);
}

fclose($output);
exit;
