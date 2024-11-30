<?php
$servername = "localhost:3306";
$username = "root";
$password = "Qq2370174994";
$dbname = "recipe_website";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
