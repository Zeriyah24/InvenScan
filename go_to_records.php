<?php
// Start the session
session_start();

// Assume school_id and role are stored in the session after login
$school_id = isset($_SESSION['school_id']) ? $_SESSION['school_id'] : null;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null; // Get user role

// Check if school_id and role are set, if not show an error or redirect to login
if (!$school_id || !in_array($user_role, ['Admin'])) {
    // Destroy session to clear any stored values
    session_destroy();
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Update status in the database
if (isset($_POST['status']) && isset($_POST['id'])) {
    // Prepare and bind the statement
    $stmt = $conn->prepare("UPDATE deliveries SET status = ? WHERE id = ? AND school_id = ?");
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'SQL prepare error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("sii", $_POST['status'], $_POST['id'], $school_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Do not output anything on success
        http_response_code(204); // No content
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
    exit;
}

// Fetch unique delivery dates for the filter dropdown
$delivery_date_filter = isset($_GET['delivery_date']) ? $_GET['delivery_date'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Get distinct delivery dates for the dropdown
$sql_dates = "SELECT DISTINCT delivery_date FROM deliveries WHERE school_id = ? ORDER BY delivery_date DESC";
$result_dates = $conn->prepare($sql_dates);
$result_dates->bind_param("i", $school_id);
$result_dates->execute();
$result_dates = $result_dates->get_result();
$delivery_dates = [];
if ($result_dates && $result_dates->num_rows > 0) {
    while ($row = $result_dates->fetch_assoc()) {
        $delivery_dates[] = $row['delivery_date'];
    }
}

// Fetch deliveries with filters, restricted to the specific school
$sql = "SELECT id, delivery_date, details, laptop_brand, status, quantity, receiving_date, receiver_name FROM deliveries WHERE school_id = ?";
$params = [$school_id];
$conditions = [];

// Add filters to the query
if ($delivery_date_filter) {
    $conditions[] = "delivery_date = ?";
    $params[] = $delivery_date_filter;
}
if ($status_filter) {
    $conditions[] = "status = ?";
    $params[] = $status_filter;
}

// Apply conditions if any
if (count($conditions) > 0) {
    $sql .= " AND " . implode(' AND ', $conditions);
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'SQL prepare error: ' . $conn->error]);
    exit;
}

if (!empty($params)) {
    $types = str_repeat("s", count($params) - 1) . "i"; // "s" for string, "i" for integer (school_id)
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$equipment_items = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $equipment_items[] = $row;
    }
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .school-division {
            text-align: center;
        }

        .school-division h2 {
            color: green;
            font-size: 36px;
            margin: 0;
        }

        .dropdown-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        /* Table Styling */
        .table {
            margin-top: 60px;
            border-collapse: collapse;
            width: 100%;
        }

        .table th {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
        }

        .table td {
            vertical-align: middle;
            text-align: center;
            padding: 12px;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }

        /* Hover Effect */
        .table tr:hover {
            background-color: #f9f9f9;
        }

        /* Table Stripes for better readability */
        .table tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .table tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        /* Buttons Styling */
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9em;
            width: 35px;
            height: 35px;
            padding: 0;
            border-radius: 50%;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-icon i {
            font-size: 1.2em;
        }

        .btn-icon:hover {
            background-color: #e0e0e0;
            transform: scale(1.1);
        }

        .modal-body input[type="file"] {
            margin-top: 10px;
        }

        .modal-content {
            border-radius: 8px;
        }

        .modal-header {
            border-bottom: 1px solid #007bff;
        }

        .filter-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            justify-content: center;
        }

        .filter-container .form-control {
            width: 180px;
        }

        /* Header Enhancements */
        .table th, .table td {
            text-align: center;
            padding: 12px 15px;
        }

    </style>

</head>
<body>
    <?php include 'header.php'; ?>
    <br><br><br><br>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <?php include 'universal_backbutton.php'; ?>
            <h1 class="mx-auto text-center">Batch Record</h1>
        </div>

        <!-- Filter HTML -->
    <div class="filter-container">
        <select id="deliveryFilter" class="form-control" onchange="filterResults()">
            <option value="">Select Delivery Date</option>
            <?php foreach ($delivery_dates as $delivery_date): ?>
                <option value="<?php echo htmlspecialchars($delivery_date); ?>" <?php echo ($delivery_date_filter == $delivery_date) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($delivery_date); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="statusFilter" class="form-control" onchange="filterResults()">
            <option value="">Select Status</option>
            <option value="Working" <?php echo ($status_filter == 'Working') ? 'selected' : ''; ?>>Working</option>
            <option value="Defective" <?php echo ($status_filter == 'Defective') ? 'selected' : ''; ?>>Defective</option>
            <option value="Condemned" <?php echo ($status_filter == 'Condemned') ? 'selected' : ''; ?>>Condemned</option>
        </select>

        <button id="filterButton" class="btn btn-primary btn-icon" onclick="filterResults()" title="Filter">
            <i class="fas fa-filter"></i>
        </button>
    </div>

    <div class="container mt-5">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Delivery Date</th>
                <th>Details</th>
                <th>Laptop Brand</th>
                <th>Status</th>
                <th>Quantity</th>
                <th>Receiving Date</th>
                <th>Receiver Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($equipment_items)): ?>
                <tr>
                    <td colspan="9" class="text-center">No data found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($equipment_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td><?php echo htmlspecialchars($item['delivery_date']); ?></td>
                        <td><?php echo htmlspecialchars($item['details']); ?></td>
                        <td><?php echo htmlspecialchars($item['laptop_brand']); ?></td>
                        <td><?php echo htmlspecialchars($item['status']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['receiving_date']); ?></td>
                        <td><?php echo htmlspecialchars($item['receiver_name']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-primary" data-toggle="modal" data-target="#statusModal" onclick="setStatus(<?php echo $item['id']; ?>)">
                                <i class="fas fa-pencil-alt"></i> <!-- Font Awesome pencil icon -->
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal for status update -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Equipment Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="statusForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="statusId">
                    <label for="status">Select Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="Working">Working</option>
                        <option value="Defective">Defective</option>
                        <option value="Condemned">Condemned</option>
                    </select>

                    <!-- Reason Box -->
                    <div id="reasonBox" style="display: none;">
                        <label for="reason">Reason:</label>
                        <textarea class="form-control" name="reason" id="reason" rows="3"></textarea>
                    </div>

                    <!-- Attachment Box -->
                    <div id="attachmentBox" style="display: none;">
                        <label for="attachment">Add Attachment:</label>
                        <input type="file" class="form-control" name="attachment" id="attachment">
                    </div>
                </div>
                <div class="modal-footer">
                  <!-- Close Button with Icon -->
                  <button type="button" class="btn btn-secondary btn-icon" data-dismiss="modal">
                      <i class="fas fa-times"></i> <!-- Font Awesome "X" icon for close -->
                  </button>

                  <!-- Save Button with Icon -->
                  <button type="submit" class="btn btn-primary btn-icon">
                      <i class="fas fa-save"></i> <!-- Font Awesome "Save" icon -->
                  </button>
              </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Function to show the attachment and reason boxes if status is defective or condemned
        document.getElementById('status').addEventListener('change', function() {
            const reasonBox = document.getElementById('reasonBox');
            const attachmentBox = document.getElementById('attachmentBox');
            const statusValue = this.value;

            if (statusValue === 'Defective' || statusValue === 'Condemned') {
                reasonBox.style.display = 'block';  // Show reason textbox
                attachmentBox.style.display = 'block';  // Show file input
            } else {
                reasonBox.style.display = 'none';  // Hide reason textbox
                attachmentBox.style.display = 'none';  // Hide file input
            }
        });

        // When the modal is shown, set the equipment id to the hidden input
        function setStatus(id) {
            document.getElementById('statusId').value = id;
        }

        // Filter function (Optional if you have the proper filter data)
        function filterResults() {
            var deliveryFilter = document.getElementById('deliveryFilter').value;
            var statusFilter = document.getElementById('statusFilter').value;
            // You would need to reload the table data based on the filter values here
        }
    </script>
</body>
</html>
