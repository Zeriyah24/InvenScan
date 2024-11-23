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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    $brand = $_POST['brand'];
    $type = $_POST['type'];

    // Trim whitespace and escape special characters
    $serial_number = trim($_POST['serial_number']);
    $price = $_POST['price'];

    // Update item details using a prepared statement
    $update_stmt = $conn->prepare("UPDATE delivery_items SET brand = ?, type = ?, serial_number = ?, price = ? WHERE id = ?");
    $update_stmt->bind_param("ssssi", $brand, $type, $serial_number, $price, $item_id); // Note: Changed to "ssssi" to correctly match parameter types

    if ($update_stmt->execute()) {
        // Success message
        $_SESSION['message'] = "Item updated successfully!";
    } else {
        // Error message
        $_SESSION['message'] = "Error updating item: " . $conn->error;
    }

    // Redirect back to the delivery page
    header("Location: edit_delivery.php?delivery_id=" . $_POST['delivery_id']);
    exit();
}



$conn->close();
?>
