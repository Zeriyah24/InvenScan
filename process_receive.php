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
    $school_id = $_SESSION['school_id']; // Assume school ID is stored in session

    // Fetch data from delivery_items
    // Fetch data from delivery_items (updated query without school_id)
    $delivery_query = "SELECT laptop_brand, details, quantity, price_per_piece, delivery_date FROM delivery_items WHERE id = ?";
    $stmt = $conn->prepare($delivery_query);
    $stmt->bind_param("i", $delivery_id);
    $stmt->execute();
    $delivery_result = $stmt->get_result();

    if ($delivery_result->num_rows > 0) {
        $delivery_data = $delivery_result->fetch_assoc();

        // Insert data into deliveries
        $insert_received_query = "
            INSERT INTO deliveries (school_id, delivery_date, details, quantity, laptop_brand, receiver_name, receiving_date, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($insert_received_query);
        $status = 'working'; // Set the status as 'working' once received

        $stmt->bind_param(
            "issdssss", // Types for the parameters
            $school_id,  // Use session school_id for the school receiving the equipment
            $delivery_data['delivery_date'], // delivery_date from delivery_items
            $delivery_data['details'],       // details from delivery_items
            $delivery_data['quantity'],      // quantity from delivery_items
            $delivery_data['laptop_brand'],  // laptop_brand from delivery_items
            $receiver_name,                  // receiver_name from POST data
            $receiving_date,                 // receiving_date from POST data
            $status                          // status set to 'working' after receiving
        );

        if ($stmt->execute()) {
            $message = "Equipment received and marked as working!";

            // Update delivery_items status to WORKING
            $update_delivery_query = "UPDATE delivery_items SET status = 'WORKING' WHERE id = ?";
            $stmt = $conn->prepare($update_delivery_query);
            $stmt->bind_param("i", $delivery_id);
            $stmt->execute();

            // Delete item from distribution
            $delete_distribution_query = "DELETE FROM distribution WHERE delivery_id = ? AND school_id = ?";
            $stmt = $conn->prepare($delete_distribution_query);
            $stmt->bind_param("ii", $delivery_id, $school_id);
            $stmt->execute();
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "No delivery found for the specified ID.";
    }
} // <-- Close the if block here

// Redirect back with message
header("Location: deliveries.php?message=" . urlencode($message));
exit();
?>
