<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $school_id = $_POST['school_id'];
    $technician_needed = $_POST['technician_needed'];
    $notes = $_POST['notes'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'data_base_school');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO technician (school_id, technician_needed, notes) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $school_id, $technician_needed, $notes);

    if ($stmt->execute()) {
        // Redirect back to the form after successful submission
        header("Location: technician_request.php?success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
