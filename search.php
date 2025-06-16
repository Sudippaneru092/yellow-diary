<?php
include "admin/db.php";

$query = mysqli_real_escape_string($conn, $_GET['query'] ?? '');
$keyword = mysqli_real_escape_string($conn, $_GET['keyword'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Search Results for "<?php echo htmlspecialchars($query); ?>"</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <?php include "header.html"; ?>

  <section class="search-results">
    <h2>Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
    <div class="features-grid">
      <?php
        $sql = "SELECT * FROM businesses 
                WHERE (
                  name LIKE '%$query%' 
                  OR location LIKE '%$query%'
                  OR category IN (
                    SELECT name FROM category WHERE name LIKE '%$query%'
                  )
                )
                AND (
                  description LIKE '%$keyword%' 
                  OR name LIKE '%$keyword%' 
                  OR location LIKE '%$keyword%'
                )
                AND status = 'active'";

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $name = htmlspecialchars($row['name']);
            $category = htmlspecialchars($row['category']);
            $location = htmlspecialchars($row['location']);
            $desc = htmlspecialchars($row['description']);
            $image = htmlspecialchars($row['image']);
            $url = htmlspecialchars($row['url']);
            echo "
              <a href='$url' target='_blank' class='feature'>
                <img src='images/$image' alt='$name' />
                <h3>$name</h3>
                <p><strong>Type:</strong> $category</p>
                <p><strong>Location:</strong> $location</p>
                <p>$desc</p>
              </a>
            ";
          }
        } else {
          echo "<p>No results found for <strong>" . htmlspecialchars($query) . "</strong>.</p>";
        }
      ?>
    </div>
  </section>
</body>
</html>
