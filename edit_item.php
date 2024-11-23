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

// Fetch the item ID from the URL
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if the item ID is valid
if ($item_id <= 0) {
    die("Error: Invalid item ID.");
}

// Fetch the current item details from the database
$sql = "SELECT * FROM equipment WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    die("Error: Item not found.");
}

// Handle form submission for updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brand = $_POST['brand'];
    $type = $_POST['type'];
    $serial_number = $_POST['serial_number'];
    $price = $_POST['price'];

    // Update the item details in the database
    $update_sql = "UPDATE equipment SET brand = ?, type = ?, serial_number = ?, price = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $brand, $type, $serial_number, $price, $item_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Item updated successfully!'); window.location.href='inventory.php';</script>";
    } else {
        echo "<script>alert('Error updating item.');</script>";
    }

    $update_stmt->close();

    
    
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="inventory.php" class="btn btn-secondary">Back</a> <!-- Back button -->
        <h1 class="mx-auto text-center">Batch Record</h1>
    </div>
        <form method="post">
            <div class="form-group">
                <label for="delivery_id">Delivery ID</label>
                <input type="number" class="form-control" id="delivery_id" name="delivery_id" value="<?php echo htmlspecialchars($item['delivery_id']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="school_id">School ID</label>
                <input type="number" class="form-control" id="school_id" name="school_id" value="<?php echo htmlspecialchars($item['school_id']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" class="form-control" id="brand" name="brand" value="<?php echo htmlspecialchars($item['brand']); ?>" required>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">Select Type</option>
                    <option value="Laptop" <?php echo ($item['type'] == 'Laptop') ? 'selected' : ''; ?>>Laptop</option>
                    <option value="Computer" <?php echo ($item['type'] == 'Computer') ? 'selected' : ''; ?>>Computer</option>
                    <option value="Monitor" <?php echo ($item['type'] == 'Monitor') ? 'selected' : ''; ?>>Monitor</option>
                    <option value="Mouse" <?php echo ($item['type'] == 'Mouse') ? 'selected' : ''; ?>>Mouse</option>
                    <option value="Keyboard" <?php echo ($item['type'] == 'Keyboard') ? 'selected' : ''; ?>>Keyboard</option>
                    <option value="Printer" <?php echo ($item['type'] == 'Printer') ? 'selected' : ''; ?>>Printer</option>
                    <option value="Projector" <?php echo ($item['type'] == 'Projector') ? 'selected' : ''; ?>>Projector</option>
                </select>
            </div>
            <div class="form-group">
                <label for="serial_number">Serial Number</label>
                <textarea class="form-control" id="serial_number" name="serial_number" required><?php echo htmlspecialchars($item['serial_number']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="generator">Generator</label>
                <input type="text" class="form-control" id="generator" name="generator" value="<?php echo htmlspecialchars($item['generator']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" class="form-control" id="status" name="status" value="<?php echo htmlspecialchars($item['status']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="delivery_date">Delivery Date</label>
                <input type="text" class="form-control" id="delivery_date" name="delivery_date" value="<?php echo htmlspecialchars($item['delivery_date']); ?>" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Update Item</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
