<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check user role and fetch equipment accordingly
$role = $_SESSION['role'];
$school_id = $_SESSION['school_id'];

if ($role === 'school_admin') {
    // Fetch only the distributed equipment specific to the logged-in school (school_admin)
    $received_sql = "
        SELECT di.delivery_id, di.distributed_date, di.quantity AS distributed_quantity, di.serial_number, di.details,
               di.school_id, di.price_per_piece, di.laptop_brand, di.distributed_date, d.receiving_date, d.delivery_date, d.quantity AS received_quantity, d.receiver_name
        FROM distribution di
        INNER JOIN delivery_items d ON di.delivery_id = d.id
        WHERE di.school_id = ?";  // Filter by school_id for school admins

    // Prepare the statement
    $stmt = $conn->prepare($received_sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind school_id parameter for filtering
    $stmt->bind_param("i", $school_id);
} else {
    // Fetch both received equipment and distributed equipment for the main admin
    $received_sql = "
        SELECT d.id AS delivery_id, d.receiving_date, d.delivery_date, d.laptop_brand, d.details, d.quantity, d.receiver_name,
               GROUP_CONCAT(CONCAT(di.school_name, ' (', di.quantity, ')') SEPARATOR '<br>') AS distributed_schools
        FROM delivery_items d
        LEFT JOIN distribution di ON di.delivery_id = d.id
        WHERE d.status = 'RECEIVED'
        GROUP BY d.id";

    // Prepare the statement for the main admin
    $stmt = $conn->prepare($received_sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
}

// Execute the query
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$received_result = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .table th {
            background-color: #BFD8AF;
            color: white;
        }
        .school-division h2 {
            margin: 0;
            font-size: 36px;
            color: green;
            text-align: center;
        }
        .modal-title {
            color: #007bff;
        }
        .modal-body table {
            margin-top: 20px;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container mt-5">
  <div class="text-left mb-3">
<button class="btn btn-secondary" onclick="goBack()">
  <i class="fas fa-arrow-left"></i> Back
</button>
</div>
    <h1>Inventory of Equipment</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Delivery Date</th>
                <th>Receiving Date</th>
                <th>Laptop Brand</th>
                <th>Quantity</th>
                <th>Details</th>
                <th>Receiver Name</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($received_result && $received_result->num_rows > 0): ?>
                <?php while ($received = $received_result->fetch_assoc()): ?>
                    <tr data-toggle="modal" data-target="#distributionModal"
                        data-delivery-id="<?php echo htmlspecialchars($received['delivery_id']); ?>">
                        <td><?php echo htmlspecialchars((new DateTime($received['delivery_date']))->format('F j, Y')); ?></td>
                        <td><?php echo htmlspecialchars((new DateTime($received['receiving_date']))->format('F j, Y')); ?></td>
                        <td><?php echo htmlspecialchars($received['laptop_brand']); ?></td>
                        <td><?php echo htmlspecialchars($received['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($received['details']); ?></td>
                        <td><?php echo htmlspecialchars($received['receiver_name']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No received equipment found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-right mb-3">
        <button class="btn btn-primary" onclick="printInventory()">
            <i class="fas fa-print"></i> Print Inventory
        </button>
    </div>
</div>

<!-- Distribution Details Modal -->
<div class="modal fade" id="distributionModal" tabindex="-1" role="dialog" aria-labelledby="distributionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="distributionModalLabel">Distribution Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Distributed Date</th>
                            <th>Quantity</th>
                            <th>Laptop Brand</th>
                            <th>School Name</th>
                        </tr>
                    </thead>
                    <tbody id="distribution-details-body">
                        <!-- AJAX will populate distribution details here -->
                    </tbody>
                </table>
                <!-- Individual Equipment Details Modal -->
                <div class="modal fade" id="equipmentModal" tabindex="-1" role="dialog" aria-labelledby="equipmentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="equipmentModalLabel">Equipment Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Serial Number</th>
                                            <th>Details</th>
                                            <th>Price Per Piece</th>
                                        </tr>
                                    </thead>
                                    <tbody id="equipment-details-body">
                                        <!-- AJAX will populate equipment details here -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function () {
    // Fetch and display distribution details in the modal
    $('#distributionModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const deliveryId = button.data('delivery-id');
        $.ajax({
            url: 'fetch_distribution.php',
            type: 'GET',
            data: { delivery_id: deliveryId },
            success: function (data) {
                $('#distribution-details-body').html(data);

                // Add event listener for school name clicks
                $('#distribution-details-body').on('click', '.school-name', function () {
                    const schoolId = $(this).data('school-id');
                    fetchEquipmentDetails(schoolId);
                });
            }
        });
    });

    // Function to fetch and display individual equipment details
    function fetchEquipmentDetails(schoolId) {
        $.ajax({
            url: 'fetch_equipment_info.php',
            type: 'GET',
            data: { school_id: schoolId },
            success: function (data) {
                $('#equipment-details-body').html(data);
                $('#equipmentModal').modal('show');
            }
        });
    }

    // Print Inventory
    function printInventory() {
        const printContents = document.querySelector('.container').outerHTML;
        const newWindow = window.open('', '', 'width=900,height=600');
        newWindow.document.write(`<html><head><title>Print Inventory</title></head><body>${printContents}</body></html>`);
        newWindow.document.close();
        newWindow.print();
    }
});
    function goBack() {
        window.history.back();
    }

</script>

</body>
</html>
