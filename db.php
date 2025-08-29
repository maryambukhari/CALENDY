<?php
$servername = "localhost"; // Change this to your actual database host if not localhost (e.g., for cloud DB)
$username = "uxhc7qjwxxfub";
$password = "g4t0vezqttq6";
$dbname = "db79ldgg1nkgda";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
