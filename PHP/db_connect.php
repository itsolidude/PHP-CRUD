<?php
// MySQL server connection credentials
$servername = "127.0.0.1:3307";
$username = "root";
$password = "";
$dbname = "assignment02";

// Creating a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
