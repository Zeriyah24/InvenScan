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

// Handle form submission for marking a delivery as received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_received'])) {
    $item_id = $_POST['item_id']; // Changed to item_id for delivery_items

    // Update the delivery status to 'RECEIVED' in delivery_items
    $update_sql = "UPDATE delivery_items SET status = 'RECEIVED', receiving_date = NOW() WHERE id = '$item_id'";
    if ($conn->query($update_sql) === TRUE) {
        $delivery_message = "Item marked as received!";
    } else {
        $delivery_message = "Error: " . $conn->error;
    }
}

// Handle form submission for adding a new supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_supplier'])) {
    $supplier_name = $_POST['supplier_name'];
    $supplier_email = $_POST['supplier_email'];
    $supplier_phone = $_POST['supplier_phone'];

    // Insert supplier into the database
    $insert_sql = "INSERT INTO suppliers (name, email, phone) VALUES ('$supplier_name', '$supplier_email', '$supplier_phone')";
    if ($conn->query($insert_sql) === TRUE) {
        $message = "Supplier added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle form submission for adding a new equipment delivery
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_delivery'])) {
    $supplier_id = $_POST['supplier_id'];
    $delivery_date = $_POST['delivery_date'];
    $equipment_details = $_POST['equipment_details'];
    $laptop_brand = isset($_POST['laptop_brand']) ? $_POST['laptop_brand'] : '';
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 0;
    $price_per_piece = isset($_POST['price_per_piece']) ? $_POST['price_per_piece'] : 0;

    // Calculate total cost
    $total_cost = $quantity * $price_per_piece;

    // Insert into delivery_items instead of deliveries
    $delivery_sql = "INSERT INTO delivery_items (supplier_id, delivery_date, details, laptop_brand, quantity, price_per_piece, total_cost, status)
                     VALUES ('$supplier_id', '$delivery_date', '$equipment_details', '$laptop_brand', '$quantity', '$price_per_piece', '$total_cost', 'PENDING')";

    if ($conn->query($delivery_sql) === TRUE) {
        $delivery_message = "Delivery item added successfully!";
    } else {
        $delivery_message = "Error: " . $conn->error;
    }
}

// Fetch existing suppliers for the delivery form
$sql = "SELECT * FROM suppliers";
$supplier_result = $conn->query($sql);

// Fetch existing delivery items with optional filters
$filter_sql = "SELECT di.id, di.delivery_date, s.name AS supplier_name, di.details, di.laptop_brand, di.quantity, di.price_per_piece, di.total_cost, di.status
               FROM delivery_items di JOIN suppliers s ON di.supplier_id = s.id";

if (isset($_POST['filter_deliveries'])) {
    $supplier_id = $_POST['filter_supplier_id'] ?? '';
    $from_date = $_POST['from_date'] ?? '';
    $to_date = $_POST['to_date'] ?? '';

    $filter_conditions = [];
    if ($supplier_id) {
        $filter_conditions[] = "di.supplier_id = '$supplier_id'";
    }
    if ($from_date) {
        $filter_conditions[] = "di.delivery_date >= '$from_date'";
    }
    if ($to_date) {
        $filter_conditions[] = "di.delivery_date <= '$to_date'";
    }

    if ($filter_conditions) {
        $filter_sql .= " WHERE " . implode(" AND ", $filter_conditions);
    }
}

$delivery_result = $conn->query($filter_sql);

// Check if the delivery query was successful
if (!$delivery_result) {
    $delivery_message = "Error fetching delivery items: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier and Delivery Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .form-section {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .table th {
            background-color: #BFD8AF;
            color: white;
        }
        .alert {
            margin-top: 20px;
        }
        .school-division h2 {
            margin: 0;
            font-size: 36px;
            color: green;
            text-align: center;
        }
        #pst-container {
            text-align: right;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-icon {
            padding: 0.5rem 0.75rem;
            margin: 0 0.2rem;
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

    <div class="container mt-5">
        <h1 class="text-center">Supplier and Delivery Management</h1>

        <!-- Supplier Form -->
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h2>Add New Supplier</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="supplier_name">Supplier Name:</label>
                    <input type="text" id="supplier_name" name="supplier_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="supplier_email">Supplier Email:</label>
                    <input type="email" id="supplier_email" name="supplier_email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="supplier_phone">Supplier Phone:</label>
                    <input type="text" id="supplier_phone" name="supplier_phone" class="form-control" required>
                </div>

                <div class="text-right">
                    <button type="submit" name="add_supplier" class="btn btn-success">Add Supplier</button>
                </div>
            </form>
        </div>

        <!-- Delivery Form -->
        <?php if (isset($delivery_message)): ?>
            <div class="alert alert-success"><?php echo $delivery_message; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h2 class="mt-5">Add New Delivery Item</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="supplier_id">Select Supplier:</label>
                    <select id="supplier_id" name="supplier_id" class="form-control" required>
                        <option value="">Select Supplier</option>
                        <?php while ($supplier = $supplier_result->fetch_assoc()): ?>
                            <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="delivery_date">Delivery Date:</label>
                    <input type="date" id="delivery_date" name="delivery_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="equipment_details">Equipment Details:</label>
                    <textarea id="equipment_details" name="equipment_details" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label for="laptop_brand">Laptop Brand:</label>
                    <select id="laptop_brand" name="laptop_brand" class="form-control" required>
                        <option value="">Select Brand</option>
                        <option value="Dell">Dell</option>
                        <option value="HP">HP</option>
                        <option value="Lenovo">Lenovo</option>
                        <option value="Acer">Acer</option>
                        <option value="Asus">Asus</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" required min="1">
                </div>

                <div class="form-group">
                    <label for="price_per_piece">Price per Unit:</label>
                    <input type="number" id="price_per_piece" name="price_per_piece" class="form-control" required step="0.01">
                </div>

                <div class="text-right">
                    <button type="submit" name="add_delivery" class="btn btn-success">Add Delivery Item</button>
                </div>
            </form>
        </div>

        <!-- Delivery Items Table -->
        <div class="form-section">
            <h2 class="mt-5">Delivery Items</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="filter_supplier_id">Filter by Supplier:</label>
                    <select id="filter_supplier_id" name="filter_supplier_id" class="form-control">
                        <option value="">All Suppliers</option>
                        <?php while ($supplier = $supplier_result->fetch_assoc()): ?>
                            <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="from_date">From Date:</label>
                    <input type="date" id="from_date" name="from_date" class="form-control">
                </div>

                <div class="form-group">
                    <label for="to_date">To Date:</label>
                    <input type="date" id="to_date" name="to_date" class="form-control">
                </div>

                <div class="text-right">
                    <button type="submit" name="filter_deliveries" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <table class="table table-hover mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Delivery Date</th>
                        <th>Supplier</th>
                        <th>Details</th>
                        <th>Laptop Brand</th>
                        <th>Quantity</th>
                        <th>Price per Piece</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($delivery_result->num_rows > 0): ?>
                        <?php while ($delivery_item = $delivery_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $delivery_item['id']; ?></td>
                                <td><?php echo $delivery_item['delivery_date']; ?></td>
                                <td><?php echo $delivery_item['supplier_name']; ?></td>
                                <td><?php echo $delivery_item['details']; ?></td>
                                <td><?php echo $delivery_item['laptop_brand']; ?></td>
                                <td><?php echo $delivery_item['quantity']; ?></td>
                                <td><?php echo number_format($delivery_item['price_per_piece'], 2); ?></td>
                                <td><?php echo number_format($delivery_item['total_cost'], 2); ?></td>
                                <td><?php echo $delivery_item['status']; ?></td>
                                <td>
                                    <?php if ($delivery_item['status'] == 'PENDING'): ?>
                                        <form method="post" action="" style="display:inline;">
                                            <input type="hidden" name="item_id" value="<?php echo $delivery_item['id']; ?>">
                                            <button type="submit" name="mark_received" class="btn btn-success btn-icon">
                                                <i class="fas fa-check"></i> Mark as Received
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">No delivery items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Display Philippine Standard Time
        function updatePST() {
            const pstDate = new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" });
            const [date, time] = pstDate.split(", ");
            document.getElementById("pst-time").innerText = time;
            document.getElementById("pst-date").innerText = date;
        }
        setInterval(updatePST, 1000);
        updatePST();
    </script>
</body>
</html>
