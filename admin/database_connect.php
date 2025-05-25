<?php
/* File Name: database_connect.php
    Authors : Sudip Paneru
    Date Of Creation: 2025/5/19
    Description : A program to connect to localhost and databased named yellow_diary
*/
try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "yellow_diary";  

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

}catch(Exception $e) {
    die("". $e->getMessage());
}
?>
