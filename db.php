<?php
$servername = "localhost";
$username = "root";
$password = ""; // or your MySQL root password
$dbname = "ecommerce_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
