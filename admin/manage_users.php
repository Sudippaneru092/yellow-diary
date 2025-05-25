<?php
try {
session_start();
require 'database_connect.php';

if (!isset($_SESSION['admin'])) {
    echo "Access denied.";
    exit();
}

$type_filter = $_GET['type'] ?? ''; // admin, general, guest or empty for all
$sql = "SELECT * FROM users";
if ($type_filter !== '') {
    $sql .= " WHERE user_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $type_filter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

echo "<h2>User Management</h2>";
echo "<form method='GET'><select name='type'>
        <option value=''>All</option>
        <option value='admin'>Admin</option>
        <option value='general'>General</option>
        <option value='guest'>Guest</option>
      </select>
      <button type='submit'>Filter</button>
    </form>";

echo "<table border='1'>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Actions</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['email']}</td>
        <td>{$row['user_type']}</td>
        <td>
            <a href='edit_user.php?id={$row['id']}'>Edit</a> |
            <a href='delete_user.php?id={$row['id']}'>Delete</a> |
            <a href='change_role.php?id={$row['id']}'>Change Role</a> |
            <a href='reset_password.php?id={$row['id']}'>Reset Password</a>
        </td>
    </tr>";
}
echo "</table>";
}catch (PDOException $e) {

    echo "". $e->getMessage(); 
    $conn->close();
}

?>
