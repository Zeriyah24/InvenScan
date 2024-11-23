<?php
// search_emails.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search term from the AJAX request
$search = $_GET['term'];

// Fetch matching emails from the database
$sql = "SELECT email FROM users WHERE email LIKE '%$search%' LIMIT 10";
$result = $conn->query($sql);

$emails = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }
}

// Return the result as a JSON object
echo json_encode($emails);

$conn->close();
?>
