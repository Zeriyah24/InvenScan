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

// Fetch available schools
$school_sql = "SELECT school_id, school_name FROM schools";
$school_result = $conn->query($school_sql);
$schools = [];
if ($school_result->num_rows > 0) {
    while ($row = $school_result->fetch_assoc()) {
        $schools[] = $row;
    }
}

// Create a delivery
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['details'])) {
    $school_id = $_POST['school_id'];
    $creator_name = $_SESSION['name'];
    $details = $_POST['details'];
    $created_at = date('Y-m-d H:i:s'); // current datetime as created_at

    $sql = "INSERT INTO deliveries (school_id, creator_name, created_at, details, status)
            VALUES ('$school_id', '$creator_name', '$created_at', '$details', 'PENDING')";

    if ($conn->query($sql) === TRUE) {
        $delivery_id = $conn->insert_id; // Get the newly inserted delivery ID

        // Insert items
        foreach ($_POST['items'] as $item) {
            $brand = $item['brand'];
            $type = $item['type'];
            $serial_number = $item['serial_number'];
            $price = $item['price'];
            $generator = $_SESSION['name'];

            // Insert item into delivery_items table with correct delivery_id
            $item_sql = "INSERT INTO delivery_items (delivery_id, school_id, brand, type, serial_number, price, generator, created_at)
                         VALUES ('$delivery_id', '$school_id', '$brand', '$type', '$serial_number', '$price', '$generator', '$created_at')";

            if ($conn->query($item_sql) === FALSE) {
                echo "Error inserting item: " . $conn->error;
            }
        }

        $message = "Delivery created successfully with items!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .school-division h2 {
            margin: 0;
            font-size: 36px;
            color: green;
            text-align: center;
        }
        #pst-container {
            text-align: right;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-4"> <!-- Start the container with mt-4 -->
    <a href="delivery.php" class="btn btn-primary">Back</a>
    <h1>Create Delivery</h1>
    <form method="POST">
        <div class="form-group">
            <label for="school_id">School to Deliver To</label>
            <select class="form-control" name="school_id" required>
                <?php foreach ($schools as $school): ?>
                    <option value="<?php echo $school['school_id']; ?>"><?php echo $school['school_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="details">Details</label>
            <textarea class="form-control" name="details" required></textarea>
        </div>

        <!-- Item details will appear here in a 3-column grid -->
        <div id="item-list" class="row mb-3"></div>

        <!-- Add Item button triggers the modal -->
        <button type="button" class="btn btn-secondary mb-3" data-toggle="modal" data-target="#addItemModal">Add Item</button>

        <!-- Align buttons -->
        <div class="d-flex justify-content-between mt-3">
            <button type="submit" class="btn btn-primary">Create Delivery</button>
        </div>
    </form>

    <?php if (isset($message)): ?>
        <div class="alert alert-info mt-3"><?php echo $message; ?></div>
    <?php endif; ?>
</div>

<!-- Modal for adding item -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">Add Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="item-form">
                    <div class="form-group">
                        <label for="brand">Brand</label>
                        <input type="text" class="form-control" id="brand" name="brand" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Computer">Computer</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Mouse">Mouse</option>
                            <option value="Keyboard">Keyboard</option>
                            <option value="Printer">Printer</option>
                            <option value="Projector">Projector</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="serial_number">Serial Number</label>
                        <input type="text" class="form-control" id="serial_number" name="serial_number" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-item">Save Item</button>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    let itemCount = 0;
    document.getElementById('save-item').addEventListener('click', function() {
        // Get item details from the modal form
        const brand = document.getElementById('brand').value;
        const type = document.getElementById('type').value;
        const serial_number = document.getElementById('serial_number').value;
        const price = document.getElementById('price').value;

        // Append item details to the delivery form in a 3-column grid
        const itemList = document.getElementById('item-list');
        const itemHtml = `
            <div class="col-md-4 mb-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Item ${itemCount + 1}</h5>
                        <p><strong>Brand:</strong> ${brand}</p>
                        <p><strong>Type:</strong> ${type}</p>
                        <p><strong>Serial Number:</strong> ${serial_number}</p>
                        <p><strong>Price:</strong> $${price}</p>

                        <!-- Hidden inputs to send data via POST -->
                        <input type="hidden" name="items[${itemCount}][brand]" value="${brand}">
                        <input type="hidden" name="items[${itemCount}][type]" value="${type}">
                        <input type="hidden" name="items[${itemCount}][serial_number]" value="${serial_number}">
                        <input type="hidden" name="items[${itemCount}][price]" value="${price}">
                    </div>
                </div>
            </div>
        `;
        itemList.insertAdjacentHTML('beforeend', itemHtml);

        // Increment item count and reset the form
        itemCount++;
        document.getElementById('item-form').reset();
        $('#addItemModal').modal('hide');
    });
    function updateTime() {
            const now = new Date();
            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true };
            document.getElementById('pst-time').innerHTML = now.toLocaleString('en-US', options);
            document.getElementById('pst-date').innerHTML = now.toLocaleDateString('en-US', { weekday: 'long' });
        }
        setInterval(updateTime, 1000);
    </script>
</script>
</body>
</html>
