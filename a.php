<?php
$host = "localhost:3306"; // Change to your database host
$username = "root";   
$password = ""; // Change to your database password
//$database = "your_database"; // Change to your database name

$conn = new mysqli($host, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully!";
?>
