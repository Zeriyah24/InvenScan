<?php
session_start();

$school_id = 2;
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'User';
}

// Include database connection
require_once 'db_connection.php';

// Function to get batch IDs
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

$conn = new mysqli($servername, $username, $password, $dbname);

// Fetch unique Delivery IDs and Generators for school_id = 12
$delivery_ids_query = "SELECT DISTINCT delivery_id FROM equipment WHERE school_id = ?";
$generator_query = "SELECT DISTINCT generator FROM equipment WHERE school_id = ?";

$stmt = $conn->prepare($delivery_ids_query);
$stmt->bind_param("i", $school_id); // bind school_id
$stmt->execute();
$result = $stmt->get_result();
$delivery_ids = [];
while ($row = $result->fetch_assoc()) {
    $delivery_ids[] = $row['delivery_id'];
}
$stmt->close();

$stmt = $conn->prepare($generator_query);
$stmt->bind_param("i", $school_id); // bind school_id
$stmt->execute();
$result = $stmt->get_result();
$generators = [];
while ($row = $result->fetch_assoc()) {
    $generators[] = $row['generator'];
}
$stmt->close();

// Fetch filter values if available
$batch_id_filter = isset($_GET['delivery_id']) ? $_GET['delivery_id'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$generator_filter = isset($_GET['generator']) ? $_GET['generator'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';

// Prepare SQL query with filters
$sql = "SELECT id, delivery_id, school_id, brand, type, serial_number, price, generator, status, delivery_date
        FROM equipment
        WHERE school_id = ?";

$params = [$school_id];

// Add filters to the SQL query
if ($batch_id_filter) {
    $sql .= " AND delivery_id = ?";
    $params[] = $batch_id_filter;
}
if ($status_filter) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}
if ($generator_filter) {
    $sql .= " AND generator = ?";
    $params[] = $generator_filter;
}
if ($type_filter) {
    $sql .= " AND type = ?";
    $params[] = $type_filter;
}

// Prepare and execute statement
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("s", count($params)), ...$params); // Dynamic binding
$stmt->execute();
$result = $stmt->get_result();
$equipment_items = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $equipment_items[] = $row; // Store each row in the array
    }
}

// Close the connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Record</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
      /* Your existing styles */
      #qrCodeModal {
          display: none; /* Hide modal by default */
          position: fixed;
          z-index: 1000;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          overflow: auto;
          background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
      }
      .navbar {
          position: fixed;
          top: 0;
          width: 100%;
          padding: 10px 20px;
          z-index: 1000;
          display: flex;
          justify-content: space-between;
          align-items: center;
          background-color: #f8f9fa;
      }

      .navbar-brand img {
          height: 75px;
          width: auto;
      }

      .school-division {
          text-align: center;
      }

      .school-division h2 {
          color: green;
          font-size: 36px;
          margin: 0;
      }

      /* Dropdown styling */
      .dropdown-container {
          display: flex;
          align-items: center;
          margin-bottom: 10px; /* Space between dropdowns */
      }
      .add-button {
          margin-left: 10px; /* Space between dropdown and button */

      }
      .table td {
          vertical-align: middle; /* Aligns items in the middle of the cell */
      }
      .table th {
          text-align: center; /* Center text in header */
      }
      body {
      padding-top: 70px; /* Add top padding to clear the fixed navbar */
  }
  </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo6.png" alt="School Logo" style="height: 75px;">
        </a>
        <div class="school-division mx-auto">
            <h2>
                SCHOOL DIVISION
                <br>
                OFFICE OF CALOOCAN
            </h2>
        </div>
        <div id="pst-container" class="ml-auto text-right">
            <div>Philippine Standard Time:</div>
            <div id="pst-time"></div>
            <div id="pst-date"></div>
        </div>
    </nav>
    <br><br>

    <div class="container mt-4">
        <button class="btn btn-secondary mb-3" onclick="window.history.back()">Back</button>

        <div class="container mt-4">
            <h1>Batch Record</h1>

            <!-- Filters -->
            <select id="deliveryFilter" class="form-control" onchange="filterResults()">
                <option value="">Select Delivery ID</option>
                <?php foreach ($delivery_ids as $delivery_id): ?>
                    <option value="<?php echo htmlspecialchars($delivery_id); ?>" <?php echo ($batch_id_filter == $delivery_id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($delivery_id); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="dropdown-container">
                <select id="statusFilter" class="form-control" onchange="filterResults()">
                    <option value="">Select Status</option>
                    <option value="Working" <?php echo ($status_filter == 'Working') ? 'selected' : ''; ?>>Working</option>
                    <option value="Defective" <?php echo ($status_filter == 'Defective') ? 'selected' : ''; ?>>Defective</option>
                    <option value="Condemned" <?php echo ($status_filter == 'Condemned') ? 'selected' : ''; ?>>Condemned</option>
                </select>
                <select id="generatorFilter" class="form-control" onchange="filterResults()">
                    <option value="">Select Generator</option>
                    <?php foreach ($generators as $generator): ?>
                        <option value="<?php echo htmlspecialchars($generator); ?>" <?php echo ($generator_filter == $generator) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($generator); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select id="typeFilter" class="form-control" onchange="filterResults()">
                    <option value="">Select Type</option>
                    <option value="Laptop" <?php echo ($type_filter == 'Laptop') ? 'selected' : ''; ?>>Laptop</option>
                    <option value="Computer" <?php echo ($type_filter == 'Computer') ? 'selected' : ''; ?>>Computer</option>
                    <option value="Monitor" <?php echo ($type_filter == 'Monitor') ? 'selected' : ''; ?>>Monitor</option>
                    <option value="Mouse" <?php echo ($type_filter == 'Mouse') ? 'selected' : ''; ?>>Mouse</option>
                    <option value="Keyboard" <?php echo ($type_filter == 'Keyboard') ? 'selected' : ''; ?>>Keyboard</option>
                    <option value="Printer" <?php echo ($type_filter == 'Printer') ? 'selected' : ''; ?>>Printer</option>
                    <option value="Projector" <?php echo ($type_filter == 'Projector') ? 'selected' : ''; ?>>Projector</option>
                </select>
            </div>

            <!-- Table to display equipment items -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Delivery ID</th>
                        <th>School ID</th>
                        <th>Brand</th>
                        <th>Type</th>
                        <th>Serial Number</th>
                        <th>Price</th>
                        <th>Generator</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipment_items as $item): ?>
                        <tr id="row-<?php echo $item['id']; ?>">
                            <td><?php echo htmlspecialchars($item['id']); ?></td>
                            <td><?php echo htmlspecialchars($item['delivery_id']); ?></td>
                            <td><?php echo htmlspecialchars($item['school_id']); ?></td>
                            <td><?php echo htmlspecialchars($item['brand']); ?></td>
                            <td><?php echo htmlspecialchars($item['type']); ?></td>
                            <td><?php echo htmlspecialchars($item['serial_number']); ?></td>
                            <td><?php echo htmlspecialchars($item['price']); ?></td>
                            <td><?php echo htmlspecialchars($item['generator']); ?></td>
                            <td><?php echo htmlspecialchars($item['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Function to redirect with selected filters
        function filterResults() {
            const deliveryId = document.getElementById('deliveryFilter').value;
            const status = document.getElementById('statusFilter').value;
            const generator = document.getElementById('generatorFilter').value;
            const type = document.getElementById('typeFilter').value;

            // Redirect with query parameters
            const queryParams = new URLSearchParams({
                delivery_id: deliveryId,
                status: status,
                generator: generator,
                type: type
            });
            window.location.href = '?' + queryParams.toString();
        }
    </script>
</body>
</html>
