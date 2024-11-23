<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get all device locations
$sql = "SELECT device_name, latitude, longitude FROM device";
$result = $conn->query($sql);

$devices = [];

if ($result === false) {
    echo json_encode(['error' => $conn->error]);
} else {
    while ($row = $result->fetch_assoc()) {
        $devices[] = $row;
    }
    echo json_encode($devices);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Tracking</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 20px; }
        #map { height: 400px; width: 100%; }
    </style>
</head>
<body>
    <h1>Device Tracking</h1>
    <div id="map"></div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_ACTUAL_API_KEY"></script>
    <script>
        let map;
        let markers = [];

        function initMap() {
            // Initialize the map centered around Caloocan
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: { lat: 14.6534, lng: 120.9657 }  // Centered around Caloocan
            });

            // Fetch device locations every 5 seconds
            setInterval(fetchDeviceLocations, 5000);
        }

        function fetchDeviceLocations() {
            $.get("get_device_location.php", function(data) {
                try {
                    const devices = JSON.parse(data);
                    if (devices.error) {
                        console.error("Error fetching device data: ", devices.error);
                        return;
                    }

                    // Clear existing markers
                    markers.forEach(marker => marker.setMap(null));
                    markers = [];

                    devices.forEach(device => {
                        const position = { lat: parseFloat(device.latitude), lng: parseFloat(device.longitude) };
                        const marker = new google.maps.Marker({
                            position: position,
                            map: map,
                            title: device.device_name
                        });
                        markers.push(marker);
                    });

                    // Optionally center the map based on the first device
                    if (devices.length > 0) {
                        const firstDevice = devices[0];
                        const firstPosition = { lat: parseFloat(firstDevice.latitude), lng: parseFloat(firstDevice.longitude) };
                        map.setCenter(firstPosition);
                    }

                } catch (e) {
                    console.error("Error parsing device data: ", e);
                }
            });
        }

        window.onload = initMap;
    </script>
</body>
</html>
