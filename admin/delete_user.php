<?php
include 'db.php';

// Check if id is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prepare delete statement
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        // Redirect to manage users page with success message
        header("Location: manage_users.php?msg=deleted");
        exit;
    } else {
        mysqli_stmt_close($stmt);
        // Redirect with error message
        header("Location: manage_users.php?msg=error");
        exit;
    }
} else {
    // Redirect if id is not valid
    header("Location: manage_users.php");
    exit;
}
?>
