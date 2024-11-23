<?php
// Database connection
include 'db_connection.php';

// Get user ID from URL
$id = $_GET['id'];

// Delete user from database
$sql = "DELETE FROM users WHERE id = $id";
if ($conn->query($sql) === TRUE) {
    echo "User deleted successfully";
} else {
    echo "Error deleting user: " . $conn->error;
}

$conn->close();
header("Location: dashboard.php"); // Redirect back to the dashboard
exit();
?>
