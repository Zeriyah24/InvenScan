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

// Fetch the list of distributed equipment for the specific school
$schoolId = $_SESSION['school_id']; // Assume school ID is stored in session
$queryDistributed = "
    SELECT di.delivery_date, di.laptop_brand, di.details, ds.quantity, di.price_per_piece, ds.delivery_id
    FROM distribution ds
    LEFT JOIN delivery_items di ON ds.delivery_id = di.id
    WHERE ds.school_id = ?";


$stmtDistributed = $conn->prepare($queryDistributed);

if (!$stmtDistributed) {
    die("Query preparation failed: " . $conn->error);
}

$stmtDistributed->bind_param("i", $schoolId);
$stmtDistributed->execute();
$distributed_result = $stmtDistributed->get_result();

// Fetch the list of received equipment
$queryReceived = "
    SELECT r.receiving_date, di.laptop_brand, di.details, r.quantity_received, di.price_per_piece, r.receiver_name
    FROM received_equipment r
    LEFT JOIN delivery_items di ON r.delivery_item_id = di.id
    WHERE r.school_id = ?";

$stmtReceived = $conn->prepare($queryReceived);

if (!$stmtReceived) {
    die("Query preparation failed: " . $conn->error);
}

$stmtReceived->bind_param("i", $schoolId);
$stmtReceived->execute();
$received_result = $stmtReceived->get_result();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receive Equipment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            padding: 20px;
            border-radius: 5px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 30px;
            color: #343a40;
            text-align: center;
        }
        .table th {
            background-color: #BFD8AF;
            color: white;
            text-align: center;
        }
        .table td {
            vertical-align: middle;
            text-align: center;
        }
        .school-division h2 {
            margin: 0;
            font-size: 36px;
            color: green;
            text-align: center;
        }
        .input-group {
            width: 300px;
            margin: 0 auto;
        }
        .btn-sm {
            padding: 0.375rem 0.75rem;
        }
    </style>
</head>
<body>
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
    <div class="container">
      <button class="btn btn-secondary" onclick="goBack()">
          <i class="fas fa-arrow-left"></i> Back
      </button>
        <h1>Receive Equipment</h1>

        <!-- Table for received equipment -->
        <h3>Distributed Equipment</h3>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Delivery Date</th>
                    <th>Laptop Brand</th>
                    <th>Details</th>
                    <th>Quantity</th>
                    <th>Price Per Unit</th>
                    <th>Total Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($distributed_result && $distributed_result->num_rows > 0): ?>
                    <?php while ($delivery = $distributed_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($delivery['delivery_date'] ?? 'No Date Provided'); ?></td>
                            <td><?php echo htmlspecialchars($delivery['laptop_brand'] ?? 'No Brand Provided'); ?></td>
                            <td><?php echo htmlspecialchars($delivery['details'] ?? 'No Details Provided'); ?></td>
                            <td><?php echo htmlspecialchars($delivery['quantity'] ?? '0'); ?></td>
                            <td><?php echo htmlspecialchars(number_format($delivery['price_per_piece'] ?? 0.00, 2)); ?></td>
                            <td>
                                <?php
                                $quantity = $delivery['quantity'] ?? 0;
                                $pricePerPiece = $delivery['price_per_piece'] ?? 0.00;
                                $totalPrice = $quantity * $pricePerPiece;
                                echo htmlspecialchars(number_format($totalPrice, 2));
                                ?>
                            </td>
                            <td>
                                <form method="post" action="process_receive.php" style='display:inline;' onsubmit="return showConfirmModal(event, '<?php echo htmlspecialchars($delivery['delivery_id']); ?>');">
                                    <input type="hidden" name="delivery_id" value="<?php echo htmlspecialchars($delivery['delivery_id']); ?>">
                                    <div class="input-group">
                                        <input type="text" name="receiver_name" placeholder="Receiver Name" required class="form-control" style="max-width: 150px;">
                                        <input type="date" name="receiving_date" required class="form-control" style="max-width: 120px;">
                                        <div class="input-group-append">
                                            <button type="submit" name="confirm_received" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No equipment deliveries found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Table for received equipment -->
        <h3>Received Equipment</h3>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Receiving Date</th>
                    <th>Laptop Brand</th>
                    <th>Details</th>
                    <th>Quantity Received</th>
                    <th>Price Per Unit</th>
                    <th>Total Price</th>
                    <th>Receiver Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($received_result && $received_result->num_rows > 0): ?>
                    <?php while ($received = $received_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($received['receiving_date'] ?? 'No Date Provided'); ?></td>
                            <td><?php echo htmlspecialchars($received['laptop_brand'] ?? 'No Brand Provided'); ?></td>
                            <td><?php echo htmlspecialchars($received['details'] ?? 'No Details Provided'); ?></td>
                            <td><?php echo htmlspecialchars($received['quantity_received'] ?? '0'); ?></td>
                            <td><?php echo htmlspecialchars(number_format($received['price_per_piece'] ?? 0.00, 2)); ?></td>
                            <td>
                                <?php
                                $quantityReceived = $received['quantity_received'] ?? 0;
                                $pricePerPiece = $received['price_per_piece'] ?? 0.00;
                                $totalPrice = $quantityReceived * $pricePerPiece;
                                echo htmlspecialchars(number_format($totalPrice, 2));
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($received['receiver_name'] ?? 'No Receiver'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No received equipment found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function updatePST() {
            const now = new Date();
            const options = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const dateOptions = { timeZone: 'Asia/Manila', year: 'numeric', month: 'long', day: 'numeric' };

            const pstTime = now.toLocaleTimeString('en-US', options);
            const pstDate = now.toLocaleDateString('en-US', dateOptions);

            document.getElementById("pst-time").textContent = pstTime;
            document.getElementById("pst-date").textContent = pstDate;
        }

        setInterval(updatePST, 1000);
        
        function goBack() {
            window.history.back();
        }
    </script>
  </body>
  </html>
