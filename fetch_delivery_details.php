<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delivery_id'])) {
        $delivery_id = $_POST['delivery_id'];
        
        // Fetch delivery details
        $delivery_sql = "SELECT * FROM deliveries WHERE id = '$delivery_id'";
        $delivery_result = $conn->query($delivery_sql);
        $delivery = $delivery_result->fetch_assoc();

        // Fetch delivery items
        $items_sql = "SELECT * FROM delivery_items WHERE delivery_id = '$delivery_id'";
        $items_result = $conn->query($items_sql);

        echo "<h5>Details for Delivery ID: " . htmlspecialchars($delivery['id']) . "</h5>";
        echo "<p>Creator: " . htmlspecialchars($delivery['creator_name']) . "</p>";
        echo "<p>Details: " . htmlspecialchars($delivery['details']) . "</p>";
        echo "<p>Status: " . htmlspecialchars($delivery['status']) . "</p>";

        echo "<h6>Items in this delivery:</h6>";
        echo "<table class='table table-sm'><thead><tr><th>Brand</th><th>Type</th><th>Serial Number</th><th>Price</th><th>Generator</th></tr></thead><tbody>";
        if ($items_result->num_rows > 0) {
            while ($item = $items_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($item['brand']) . "</td>";
                echo "<td>" . htmlspecialchars($item['type']) . "</td>";
                echo "<td>" . htmlspecialchars($item['serial_number']) . "</td>";
                echo "<td>" . htmlspecialchars($item['price']) . "</td>";
                echo "<td>" . htmlspecialchars($item['generator']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No items found.</td></tr>";
        }
        echo "</tbody></table>";

        // Conditionally display the approve/deny buttons only if status is not APPROVED or DENIED
        if ($delivery['status'] !== 'APPROVED' && $delivery['status'] !== 'DENIED') {
            echo "<div class='modal-footer'>
                    <form method='POST' action='fetch_delivery_details.php'>
                        <input type='hidden' name='delivery_id' value='" . htmlspecialchars($delivery_id) . "'>
                        <button type='submit' name='approve' class='btn btn-success'>Approve</button>
                        <button type='submit' name='deny' class='btn btn-danger'>Deny</button>
                    </form>
                  </div>";
        }
    }

    // Handle approval or denial
    if (isset($_POST['approve']) || isset($_POST['deny'])) {
        $current_date = date('Y-m-d H:i:s'); // Get current date and time
        $delivery_id = $_POST['delivery_id']; // Ensure the delivery ID is captured

        if (isset($_POST['approve'])) {
            // Update delivery status to approved and set delivery_date
            $update_sql = "UPDATE deliveries SET status = 'APPROVED' WHERE id = '$delivery_id'";
            if ($conn->query($update_sql) === TRUE) {
                // Transfer items to equipment table
                $items_sql = "SELECT * FROM delivery_items WHERE delivery_id = '$delivery_id'";
                $items_result = $conn->query($items_sql);

                if ($items_result->num_rows > 0) {
                    while ($item = $items_result->fetch_assoc()) {
                        $insert_sql = "INSERT INTO equipment (delivery_id, school_id, brand, type, serial_number, price, generator, created_at, status, delivery_date)
                                       VALUES ('{$item['delivery_id']}', '{$item['school_id']}', '{$item['brand']}', '{$item['type']}', 
                                       '{$item['serial_number']}', '{$item['price']}', '{$item['generator']}', '{$item['created_at']}', 'Working', '$current_date')";
                        $conn->query($insert_sql);
                    }
                }
                header("Location: deliveries.php");
                exit();
            } else {
                echo "Error updating delivery: " . $conn->error;
            }
        } elseif (isset($_POST['deny'])) {
            // Update delivery status to denied and set delivery_date
            $update_sql = "UPDATE deliveries SET status = 'DENIED' WHERE id = '$delivery_id'";
            if ($conn->query($update_sql) === TRUE) {
                header("Location: deliveries.php");
                exit();
            } else {
                echo "Error updating delivery: " . $conn->error;
            }
        }
    }
}

$conn->close();
?>
