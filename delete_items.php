<?php
// Start the session
session_start();

// Assume school_id is stored in the session after login
$school_id = isset($_SESSION['school_id']) ? $_SESSION['school_id'] : null;

// Check if school_id is set, if not show an error or redirect to login
if (!$school_id) {
    die("Error: No school ID provided. Please log in.");
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the ID of the item to delete
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0) {
    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM equipment WHERE id = ? AND school_id = ?");
    $stmt->bind_param("ii", $item_id, $school_id);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to the inventory page with success message
        header("Location: inventory.php?message=Item deleted successfully");
    } else {
        // Redirect back with an error message
        header("Location: inventory.php?error=Error deleting item");
    }
    
    // Close the statement
    $stmt->close();
} else {
    // Redirect back with an error message if ID is invalid
    header("Location: inventory.php?error=Invalid item ID");
}

// Close the connection
$conn->close();
?>
