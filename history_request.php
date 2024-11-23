<?php
// Include database connection
include('db_connection.php');

// Fetch history of requests from the database
$sql = "SELECT * FROM history_of_requests"; // Adjust the table name accordingly
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History of Requests</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="font-weight-bold mb-4">History of Requests</h1>
        <table class="table table-striped table-bordered">
            <thead class="table-header">
                <tr>
                    <th>School ID</th>
                    <th>School Name</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Approved Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['school_id']}</td>
                                <td>{$row['school_name']}</td>
                                <td>{$row['subject']}</td>
                                <td>{$row['status']}</td>
                                <td>{$row['description']}</td>
                                <td>{$row['approved_date']}</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No requests found</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <button class="btn btn-secondary" onclick="window.location.href='mainAdmin_dashboard.php';">Back to Dashboard</button>
    </div>
</body>
</html>

<?php
$conn->close();
?>
