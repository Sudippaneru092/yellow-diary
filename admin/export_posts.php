<?php
include 'db.php';

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=posts_export_' . date('Y-m-d') . '.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Output the CSV column headings
fputcsv($output, ['ID', 'Business Name', 'Title', 'Category', 'Description', 'Created At']);

// SQL query (uses posts.category instead of posts.type)
$query = "
    SELECT 
        posts.id, 
        businesses.name AS business_name, 
        posts.title, 
        posts.category, 
        posts.description, 
        posts.created_at 
    FROM posts 
    LEFT JOIN businesses ON posts.business_id = businesses.id 
    ORDER BY posts.created_at DESC
";

$result = mysqli_query($conn, $query);

// Write each row to the CSV
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'],
        $row['business_name'],
        $row['title'],
        $row['category'],
        strip_tags($row['description']),
        $row['created_at']
    ]);
}

fclose($output);
exit();
