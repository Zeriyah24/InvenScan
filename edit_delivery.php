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

// Fetch delivery details if delivery_id is set
if (isset($_GET['delivery_id'])) {
    $delivery_id = $_GET['delivery_id'];

    // Fetch delivery using a prepared statement
    $stmt = $conn->prepare("SELECT d.id, d.school_id, s.school_name, d.creator_name, d.details, d.status, d.created_at
                             FROM deliveries d
                             JOIN schools s ON d.school_id = s.school_id
                             WHERE d.id = ?");
    $stmt->bind_param("i", $delivery_id); // "i" indicates the parameter type is integer
    $stmt->execute();
    $delivery_result = $stmt->get_result();
    $delivery = $delivery_result->fetch_assoc();

    // Fetch delivery items using a prepared statement
    $item_stmt = $conn->prepare("SELECT * FROM delivery_items WHERE delivery_id = ?");
    $item_stmt->bind_param("i", $delivery_id);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();
}

// Handle form submission for updating delivery
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_delivery'])) {
    // After fetching the POST data
    $new_school_id = $_POST['school_id'];
    $new_creator_name = $_POST['creator_name'];
    $new_details = $_POST['details'];
    $new_status = $_POST['status'];

    // Update delivery details using a prepared statement
    $update_stmt = $conn->prepare("UPDATE deliveries SET school_id = ?, creator_name = ?, details = ?, status = ? WHERE id = ?");
    $update_stmt->bind_param("isssi", $new_school_id, $new_creator_name, $new_details, $new_status, $delivery_id);
    $update_stmt->execute();

    // Redirect back to the delivery page
    header("Location: delivery.php");
    exit();
}

// Handle deletion of delivery items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
    $item_id = $_POST['item_id'];

    // Delete the item using a prepared statement
    $delete_stmt = $conn->prepare("DELETE FROM delivery_items WHERE id = ?");
    $delete_stmt->bind_param("i", $item_id);
    $delete_stmt->execute();

    // Redirect back to the same page to refresh
    header("Location: edit_delivery.php?delivery_id=" . $delivery_id);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Delivery</title>
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
    
    <div class="container mt-5">
        <h1>Edit Delivery</h1>
        
        <?php if ($delivery): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="school_id">School</label>
                    <input type="text" id="school_id" class="form-control" value="<?php echo htmlspecialchars($delivery['school_name']); ?>" readonly>
                    <input type="hidden" name="school_id" value="<?php echo htmlspecialchars($delivery['school_id']); ?>">
                    <input type="hidden" name="creator_name" value="<?php echo htmlspecialchars($delivery['creator_name']); ?>">
                </div>
                <div class="form-group">
                    <label for="details">Details</label>
                    <textarea name="details" id="details" class="form-control" required><?php echo htmlspecialchars($delivery['details']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="PENDING" <?php if ($delivery['status'] == 'PENDING') echo 'selected'; ?>>PENDING</option>
                        <option value="DELIVERED" <?php if ($delivery['status'] == 'DELIVERED') echo 'selected'; ?>>DELIVERED</option>
                        <option value="CANCELLED" <?php if ($delivery['status'] == 'CANCELLED') echo 'selected'; ?>>CANCELLED</option>
                    </select>
                </div>
                <button type="submit" name="update_delivery" class="btn btn-primary">Update Delivery</button>
            </form>

            <h2 class="mt-4">Delivery Items</h2>
            <?php if ($item_result && $item_result->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Brand</th>
                            <th>Type</th>
                            <th>Serial Number</th>
                            <th>Price</th>
                            <th>Generator</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $item_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['id']); ?></td>
                                <td><?php echo htmlspecialchars($item['brand']); ?></td>
                                <td><?php echo htmlspecialchars($item['type']); ?></td>
                                <td><?php echo htmlspecialchars($item['serial_number']); ?></td>
                                <td><?php echo htmlspecialchars($item['price']); ?></td>
                                <td><?php echo htmlspecialchars($item['generator']); ?></td>
                                <td>
                                    <button class="btn btn-warning" data-toggle="modal" data-target="#editItemModal" 
                                            data-id="<?php echo $item['id']; ?>" 
                                            data-brand="<?php echo htmlspecialchars($item['brand']); ?>" 
                                            data-type="<?php echo htmlspecialchars($item['type']); ?>" 
                                            data-serial="<?php echo htmlspecialchars($item['serial_number']); ?>" 
                                            data-price="<?php echo htmlspecialchars($item['price']); ?>" 
                                            data-generator="<?php echo htmlspecialchars($item['generator']); ?>">
                                        Edit Item
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" name="delete_item" class="btn btn-danger">Delete Item</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No delivery items found.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Delivery not found.</p>
        <?php endif; ?>
    </div>
<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="update_item.php">
                    <input type="hidden" name="delivery_id" id="modal_delivery_id" value="<?php echo $delivery_id; ?>">
                    <input type="hidden" name="item_id" id="modal_item_id">
                    
                    <div class="form-group">
                        <label for="modal_brand">Brand</label>
                        <input type="text" name="brand" id="modal_brand" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_type">Type</label>
                        <input type="text" name="type" id="modal_type" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_serial">Serial Number</label>
                        <input type="text" name="serial_number" id="modal_serial" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_price">Price</label>
                        <input type="number" name="price" id="modal_price" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_generator">Generator</label>
                        <input type="text" name="generator" id="modal_generator" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Item</button>
                    
                </form>


            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // JavaScript to handle modal data
        $('#editItemModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var brand = button.data('brand');
            var type = button.data('type');
            var serial = button.data('serial');
            var price = button.data('price');
            var generator = button.data('generator');

            var modal = $(this);
            modal.find('#modal_item_id').val(id);
            modal.find('#modal_brand').val(brand);
            modal.find('#modal_type').val(type);
            modal.find('#modal_serial').val(serial);
            modal.find('#modal_price').val(price);
            modal.find('#modal_generator').val(generator);
        });

        // Set current date and time
        function updateTime() {
            const now = new Date();
            document.getElementById("pst-time").innerText = now.toLocaleTimeString('en-US', { timeZone: 'Asia/Manila' });
            document.getElementById("pst-date").innerText = now.toLocaleDateString('en-US', { timeZone: 'Asia/Manila' });
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>
</html>
