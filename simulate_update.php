// simulate_update.php
<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

// Simulating a random location update
$device_id = 1; // Example device ID
$latitude = rand(-90, 90);
$longitude = rand(-180, 180);

// Update device location
$sql = "UPDATE devices SET latitude = $latitude, longitude = $longitude WHERE id = $device_id";
$conn->query($sql);

$conn->close();
?>
