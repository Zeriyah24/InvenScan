<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caloocan City Map View</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet Routing Machine CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <style>
        #map {
            height: 80vh;
            width: 100%;
        }
        #controls {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: #f8f9fa;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            padding: 20px;
            width: 300px;
        }
        .control-section {
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 20px;
            padding: 10px 20px;
        }
        .btn {
            border-radius: 20px;
            padding: 10px;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        .btn-primary:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-back {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-back:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        .btn-back:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .back-button-container {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div id="controls">
        <div class="control-section">
            <h5 class="mb-3">Pick-Up Location</h5>
            <div class="form-group">
                <input type="text" id="pickup" class="form-control" placeholder="Enter pick-up location" oninput="checkFields()">
            </div>
        </div>

        <div class="control-section">
            <h5 class="mb-3">Drop-Off Location</h5>
            <div class="form-group">
                <input type="text" id="dropoff" class="form-control" placeholder="Enter drop-off location" oninput="checkFields()">
            </div>
        </div>

        <button id="submitBtn" class="btn btn-primary btn-block" onclick="submitLocations()" disabled>Submit Locations</button>
    </div>
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- Leaflet Routing Machine JS -->
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <!-- Leaflet Geocoder JS (for geocoding) -->
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Define the bounds for Metro Manila
        const bounds = L.latLngBounds(
            L.latLng(14.4325, 120.8800), // Southwest corner
            L.latLng(14.8275, 121.0800)  // Northeast corner
        );

        // Initialize the map and set its view to Caloocan City
        const map = L.map('map', {
            center: [14.6564, 120.9830],
            zoom: 13,
            maxBounds: bounds,
            maxBoundsViscosity: 1.0
        });

        // Load and display OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Ensure the map stays within the bounds
        map.on('moveend', function() {
            if (!map.getBounds().overlaps(bounds)) {
                map.fitBounds(bounds);
            }
        });

        let pickupMarker, dropoffMarker, routeControl, carIcon, carMarker;

        async function geocodeAddress(address) {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`);
            const data = await response.json();
            if (data.length > 0) {
                return { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon) };
            } else {
                alert(`Location '${address}' not found.`);
                return null;
            }
        }

        function moveCarAlongRoute(route) {
            let index = 0;
            const step = 100; // Number of steps for animation
            const interval = setInterval(() => {
                if (index >= route.length) {
                    clearInterval(interval);
                    return;
                }
                carMarker.setLatLng(route[index]);
                map.panTo(route[index]);
                index++;
            }, step);
        }

        async function submitLocations() {
            const pickupInput = document.getElementById('pickup').value;
            const dropoffInput = document.getElementById('dropoff').value;

            const pickupLocation = await geocodeAddress(pickupInput);
            const dropoffLocation = await geocodeAddress(dropoffInput);

            if (pickupLocation) {
                if (pickupMarker) {
                    pickupMarker.setLatLng(pickupLocation);
                } else {
                    pickupMarker = L.marker(pickupLocation).addTo(map);
                }
                pickupMarker.bindPopup(`<h4>Pick-Up Location: ${pickupInput}</h4>`).openPopup();
            }

            if (dropoffLocation) {
                if (dropoffMarker) {
                    dropoffMarker.setLatLng(dropoffLocation);
                } else {
                    dropoffMarker = L.marker(dropoffLocation).addTo(map);
                }
                dropoffMarker.bindPopup(`<h4>Drop-Off Location: ${dropoffInput}</h4>`).openPopup();
            }

            if (pickupLocation && dropoffLocation) {
                map.setView(pickupLocation, 15); // Focus on pick-up location

                // Clear previous route if exists
                if (routeControl) {
                    routeControl.remove();
                }

                // Add a route between pick-up and drop-off locations
                routeControl = L.Routing.control({
                    waypoints: [
                        L.latLng(pickupLocation.lat, pickupLocation.lng),
                        L.latLng(dropoffLocation.lat, dropoffLocation.lng)
                    ],
                    routeWhileDragging: true,
                    geocoder: L.Control.Geocoder.nominatim()
                }).addTo(map);

                // Add a car icon and animate it
                if (carMarker) {
                    carMarker.remove();
                }

                carIcon = L.icon({
                    iconUrl: 'https://example.com/car-icon.png', // Replace with the URL of your car icon
                    iconSize: [32, 32], // Size of the icon
                    iconAnchor: [16, 32], // Anchor point of the icon
                    popupAnchor: [0, -32] // Popup position relative to the icon
                });

                carMarker = L.marker(pickupLocation, { icon: carIcon }).addTo(map);

                const routePoints = routeControl.getPlan().getWaypoints().map(w => [w.latLng.lat, w.latLng.lng]);
                moveCarAlongRoute(routePoints);
            }
        }

        function checkFields() {
            const pickup = document.getElementById('pickup').value.trim();
            const dropoff = document.getElementById('dropoff').value.trim();
            const submitBtn = document.getElementById('submitBtn');

            if (pickup && dropoff) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }

        function goToDashboard() {
            // Replace with the actual URL of your dashboard
            window.location.href = 'dashboard.html';
        }
    </script>
</body>
</html>
