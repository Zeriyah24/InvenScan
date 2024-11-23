<?php
session_start();

// Ensure the user is logged in and has the required role
$school_id = isset($_SESSION['school_id']) ? $_SESSION['school_id'] : null;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

if (!$school_id || $user_role !== 'Admin') {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'], $_POST['id'])) {
    $status = $_POST['status'];
    $id = intval($_POST['id']); // Ensure ID is an integer

    // Update status in the database
    $stmt = $conn->prepare("UPDATE deliveries SET status = ? WHERE id = ? AND school_id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("sii", $status, $id, $school_id); // Pass school_id to ensure only this school's records are updated

    if ($stmt->execute()) {
        // Handle additional actions for condemned status
        if ($status === 'Condemned') {
            // Example: Log the condemned item or update related tables
            // Add any specific logic here for handling condemned items
            // For example, you might want to log this in another table for tracking condemned items
            // Example:
            // $log_stmt = $conn->prepare("INSERT INTO condemned_items (delivery_id, reason) VALUES (?, ?)");
            // $log_stmt->bind_param("is", $id, $reason);
            // $log_stmt->execute();
        }

        echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }

    $stmt->close();
    exit;
}

// Fetch data for display (optional if only handling updates)
$sql = "SELECT id, delivery_date, details, laptop_brand, status, quantity, receiving_date, receiver_name FROM deliveries WHERE school_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $school_id); // Ensure only this school's deliveries are fetched
$stmt->execute();
$result = $stmt->get_result();
$equipment_items = [];
while ($row = $result->fetch_assoc()) {
    $equipment_items[] = $row;
}

$stmt->close();
$conn->close();

// Output the fetched data (for example purposes, you can modify this to display the data as needed)
echo json_encode(['success' => true, 'equipment_items' => $equipment_items]);
exit;
?>
