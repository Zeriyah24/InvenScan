<?php
session_start();
$school_id = isset($_SESSION['school_id']) ? $_SESSION['school_id'] : null;

if (!$school_id) {
    die("Error: No school ID provided. Please log in.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$type1 = $_GET['type1'];
$options = '';

if ($type1 == 'generator') {
    $sql = "SELECT DISTINCT generator FROM equipment WHERE school_id = ?";
} elseif ($type1 == 'status') {
    $sql = "SELECT DISTINCT status FROM equipment WHERE school_id = ?";
} elseif ($type1 == 'delivery_date') {
    $sql = "SELECT DISTINCT delivery_date FROM equipment WHERE school_id = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $school_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $options .= '<option value="' . htmlspecialchars($row[$type1]) . '">' . htmlspecialchars($row[$type1]) . '</option>';
}

echo $options;
?>
