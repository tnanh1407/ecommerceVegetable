<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "shop";
$port = 3306;

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";