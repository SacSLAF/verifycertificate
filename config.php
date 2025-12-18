<?php

$servername = "localhost";
$username = "root";
$password = "Sac@123";
$dbname = "dlqr";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>