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

// Initialize message variable
$message = '';

// Handle receiving confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_received'])) {
    $delivery_id = $_POST['delivery_id'];
    $receiving_date = $_POST['receiving_date'];
    $receiver_name = $_POST['receiver_name'];

    // Check if the delivery has already been received
    $check_received_sql = "SELECT * FROM delivery_items WHERE id = '$delivery_id' AND status = 'RECEIVED'";
    $check_received_result = $conn->query($check_received_sql  );

    if ($check_received_result->num_rows > 0) {
        $message = "This delivery has already been received.";
    } else {
        // Update delivery status, receiving date, and receiver name
        $update_delivery_sql = "
            UPDATE delivery_items
            SET status = 'RECEIVED', receiving_date = '$receiving_date', receiver_name = '$receiver_name'
            WHERE id = '$delivery_id'";

        if ($conn->query($update_delivery_sql) === TRUE) {
            $message = "Equipment received successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Handle distributing equipment to schools
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['distribute_equipment'])) {
    $school_id = $_POST['school_id'];
    $delivery_id = $_POST['delivery_id'];
    $distributed_date = $_POST['distributed_date'];
    $quantity = $_POST['quantity'];
    $price_per_piece = $_POST['price_per_piece'];

    // Ensure 'serial_number' and 'details' are arrays and set them to empty arrays if not
    $serial_numbers = isset($_POST['serial_number']) && is_array($_POST['serial_number']) ? $_POST['serial_number'] : [];
    $details = isset($_POST['details']) && is_array($_POST['details']) ? $_POST['details'] : [];

    // Fetch the available quantity in the received delivery
    $fetch_received_quantity_sql = "SELECT quantity FROM delivery_items WHERE id = '$delivery_id' AND status = 'RECEIVED'";
    $received_quantity_result = $conn->query($fetch_received_quantity_sql);

    if ($received_quantity_result->num_rows > 0) {
        $received_row = $received_quantity_result->fetch_assoc();
        $received_quantity = $received_row['quantity'];

        // Check if enough quantity is available
        if ($quantity > $received_quantity) {
            $message = "Not enough quantity available in stock.";
        } else {
            // Loop through each serial number and detail, and insert them individually
            for ($i = 0; $i < count($serial_numbers); $i++) {
                $serial_number = $conn->real_escape_string($serial_numbers[$i]);
                $details = isset($details[$i]) ? $conn->real_escape_string($details[$i]) : ''; // Default to empty if not set

                $insert_distribution_sql = "
                    INSERT INTO distribution (school_id, delivery_id, distributed_date, quantity, price_per_piece, serial_number, details)
                    VALUES ('$school_id', '$delivery_id', '$distributed_date', '$quantity', '$price_per_piece', '$serial_number', '$details')";

                if (!$conn->query($insert_distribution_sql)) {
                    $message = "Error inserting distribution record: " . $conn->error;
                    break;
                }
            }

            // Reduce the quantity in the delivery_items table after successful insertions
            if ($conn->affected_rows > 0) {
                $reduce_quantity_sql = "
                    UPDATE delivery_items
                    SET quantity = quantity - '$quantity'
                    WHERE id = '$delivery_id' AND status = 'RECEIVED' AND quantity >= '$quantity'";

                if ($conn->query($reduce_quantity_sql) === TRUE) {
                    $message = "Equipment distributed successfully!";
                } else {
                    $message = "Error updating quantity: " . $conn->error;
                }
            }
        }
    } else {
        $message = "No received equipment found for this delivery!";
    }
}

// Fetch deliveries for receiving (only those that are pending)
$pending_deliveries_sql = "SELECT id, delivery_date, laptop_brand, details, quantity, price_per_piece FROM delivery_items WHERE status = 'PENDING'";
$pending_delivery_result = $conn->query($pending_deliveries_sql);

// Fetch received deliveries for distribution dropdown
$received_deliveries_sql = "SELECT id, delivery_date, laptop_brand, details, quantity, price_per_piece FROM delivery_items WHERE status = 'RECEIVED'";
$received_delivery_result = $conn->query($received_deliveries_sql);

// Fetch schools for distribution
$school_sql = "SELECT school_id, school_name FROM schools";
$school_result = $conn->query($school_sql);

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receive and Distribute Equipment</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .table th {
            background-color: #5DA14E;
            color: white;
            text-align: center;
        }
        .table td {
            vertical-align: middle;
            text-align: center;
        }
        .school-division h2 {
            margin: 0;
            font-size: 24px;
            color: green;
            text-align: center;
        }
        #pst-container {
            text-align: right;
        }
        .distribute-section {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .container h1 {
            margin-top: 30px;
            color: #4a4a4a;
        }
        .form-group label {
            font-weight: bold;
        }

        .input-group input[type="text"], .input-group input[type="date"], .input-group input[type="number"] {
            width: auto;
        }
        .btn {
            font-size: 0.9rem;
        }
        .table-responsive {
            padding: 10px;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

    <div class="container mt-5">
        <h1>Receive Equipment from Supplier</h1>
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="text-left mb-3">
    <button class="btn btn-secondary" onclick="goBack()">
        <i class="fas fa-arrow-left"></i> Back
    </button>
</div>

        <!-- Table for pending deliveries -->
        <div class="table-responsive">
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Delivery Date</th>
                        <th>Laptop Brand</th>
                        <th>Details</th>
                        <th>Quantity</th>
                        <th>Price per Unit</th>
                        <th>Total Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($delivery = $pending_delivery_result->fetch_assoc()): ?>
                        <?php $total_price = $delivery['quantity'] * $delivery['price_per_piece']; ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('m/d/Y', strtotime($delivery['delivery_date']))); ?></td>
                            <td><?php echo htmlspecialchars($delivery['laptop_brand']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['details']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['quantity']); ?></td>
                            <td>₱<?php echo number_format($delivery['price_per_piece'], 2); ?></td>
                            <td>₱<?php echo number_format($total_price, 2); ?></td>
                            <td>
                                <form method="post" action="" class="d-inline">
                                    <input type="hidden" name="delivery_id" value="<?php echo htmlspecialchars($delivery['id']); ?>">
                                    <div class="input-group">
                                        <input type="text" name="receiver_name" placeholder="Receiver Name" required class="form-control">
                                        <input type="date" name="receiving_date" required class="form-control">
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
                </tbody>
            </table>
        </div>

        <div class="text-right mt-3">
            <a href="receive_history.php" class="btn btn-secondary">
                <i class="fas fa-history"></i> Received Equipment History
            </a>
        </div>
    </div>

    <div class="container mt-5">
        <h1 class="mt-5">Distribute Equipment to Schools</h1>

        <form method="post" action="" class="border p-4">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="school_id">School:</label>
                    <select id="school_id" name="school_id" class="form-control" required>
                        <option value="">Select School</option>
                        <?php while ($school = $school_result->fetch_assoc()): ?>
                            <option value="<?php echo $school['school_id']; ?>"><?php echo $school['school_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="delivery_id">Delivery ID:</label>
                    <select id="delivery_id" name="delivery_id" class="form-control" required>
                        <option value="">Select Delivery</option>
                        <?php while ($row = $received_delivery_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo "Delivery ID: " . $row['id'] . " - " . $row['laptop_brand'] . " - " . $row['details']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="distributed_date">Distributed Date:</label>
                    <input type="date" id="distributed_date" name="distributed_date" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" required min="1" oninput="generateEquipmentRows()">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="price_per_piece">Price per Unit (₱):</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">₱</span>
                        </div>
                        <input type="number" id="price_per_piece" name="price_per_piece" class="form-control" placeholder="Price" required min="0" oninput="updateTotalPrice()">
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label>Total Price (₱):</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">₱</span>
                        </div>
                        <input type="text" id="total_price" class="form-control" readonly>
                    </div>
                </div>
            </div>

            <!-- Table for Batch Equipment Details -->
            <div id="equipmentDetailsContainer" style="display: none;">
                <h5>Equipment Details</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Serial Number/Barcode</th>
                            <th>Equipment Details</th>
                            <th>Barcode</th>
                        </tr>
                    </thead>
                    <tbody id="equipmentDetailsTable">
                        <!-- Rows will be generated here -->
                    </tbody>
                </table>
            </div>

            <button type="submit" name="distribute_equipment" class="btn btn-primary">Distribute Equipment</button>
            <a href="distribution_history.php" class="btn btn-secondary">
                <i class="fas fa-history"></i> Distributed Equipment History
            </a>
        </form>

        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
        <script>
            function updateTotalPrice() {
                const quantity = parseFloat(document.getElementById('quantity').value) || 0;
                const pricePerUnit = parseFloat(document.getElementById('price_per_piece').value) || 0;
                const totalPrice = quantity * pricePerUnit;
                document.getElementById('total_price').value = totalPrice.toFixed(2);
            }

            function generateEquipmentRows() {
                const quantity = document.getElementById("quantity").value;
                const equipmentDetailsTable = document.getElementById("equipmentDetailsTable");
                equipmentDetailsTable.innerHTML = ""; // Clear existing rows

                for (let i = 0; i < quantity; i++) {
                    const row = document.createElement("tr");

                    // Serial Number / Barcode Input
                    const serialCell = document.createElement("td");
                    const serialInput = document.createElement("input");
                    serialInput.type = "text";
                    serialInput.name = "serial_number[]";
                    serialInput.classList.add("form-control");
                    serialInput.oninput = () => generateBarcode(serialInput, i); // Attach event to generate barcode on input
                    serialCell.appendChild(serialInput);
                    row.appendChild(serialCell);

                    // Equipment Detail Input
                    const detailCell = document.createElement("td");
                    const detailInput = document.createElement("input");
                    detailInput.type = "text";
                    detailInput.name = "equipment_detail[]";
                    detailInput.classList.add("form-control");
                    detailCell.appendChild(detailInput);
                    row.appendChild(detailCell);

                    // Barcode Preview Cell
                    const barcodeCell = document.createElement("td");
                    const barcodeImg = document.createElement("img");
                    barcodeImg.id = "barcode" + i;
                    barcodeImg.style.maxWidth = "100px";
                    barcodeCell.appendChild(barcodeImg);
                    row.appendChild(barcodeCell);

                    equipmentDetailsTable.appendChild(row);
                }

                document.getElementById("equipmentDetailsContainer").style.display = "block";
            }

            function generateBarcode(inputElement, index) {
                const barcodeValue = inputElement.value;
                const barcodeImg = document.getElementById("barcode" + index);
                if (barcodeValue) {
                    JsBarcode(barcodeImg, barcodeValue, {
                        format: "CODE128",
                        lineColor: "#000",
                        width: 2,
                        height: 40,
                        displayValue: true
                    });
                } else {
                    barcodeImg.src = ""; // Clear the barcode if no value
                }
            }
            function goBack() {
                window.history.back();
            }
        </script>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
