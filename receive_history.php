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

// Fetch received equipment history with price per unit
$received_sql = "
    SELECT d.id AS delivery_id, d.receiving_date, d.delivery_date, d.laptop_brand, d.details, d.quantity, d.receiver_name, d.price_per_piece
    FROM delivery_items d
    WHERE d.status = 'RECEIVED'";
$received_result = $conn->query($received_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Received Equipment History</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th {
            background-color: #BFD8AF;
            color: white;
        }
        .school-division h2 {
            margin: 0;
            font-size: 36px;
            color: green;
            text-align: center;
        }
        #pst-container {
            text-align: right;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">
        <img src="logo6.png" alt="School Logo" style="height: 75px;">
    </a>
    <div class="school-division mx-auto">
        <h2>
            SCHOOL DIVISION
            <br>
            OFFICE OF CALOOCAN
        </h2>
    </div>
    <div id="pst-container" class="ml-auto text-right">
        <div>Philippine Standard Time:</div>
        <div id="pst-time"></div>
        <div id="pst-date"></div>
    </div>
</nav>

<div class="container mt-5">
    <h1>Received Equipment History</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Delivery Date</th>
                <th>Receiving Date</th>
                <th>Laptop Brand</th>
                <th>Quantity</th>
                <th>Price per Unit</th>
                <th>Total Price</th>
                <th>Details</th>
                <th>Receiver Name</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($received_result && $received_result->num_rows > 0): ?>
                <?php while ($received = $received_result->fetch_assoc()): ?>
                    <?php
                        // Calculate total price
                        $total_price = $received['quantity'] * $received['price_per_piece'];
                    ?>
                    <tr data-toggle="modal" data-target="#detailsModal" data-delivery-id="<?php echo htmlspecialchars($received['delivery_id']); ?>"
                        data-delivery-date="<?php echo htmlspecialchars($received['delivery_date']); ?>"
                        data-receiving-date="<?php echo htmlspecialchars($received['receiving_date']); ?>"
                        data-laptop-brand="<?php echo htmlspecialchars($received['laptop_brand']); ?>"
                        data-quantity="<?php echo htmlspecialchars($received['quantity']); ?>"
                        data-price-per-unit="<?php echo htmlspecialchars($received['price_per_piece']); ?>"
                        data-total-price="<?php echo htmlspecialchars($total_price); ?>"
                        data-details="<?php echo htmlspecialchars($received['details']); ?>"
                        data-receiver-name="<?php echo htmlspecialchars($received['receiver_name']); ?>">
                        <td><?php
                            $delivery_date = new DateTime($received['delivery_date']);
                            echo htmlspecialchars($delivery_date->format('F j, Y'));
                        ?></td>
                        <td><?php
                            $receiving_date = new DateTime($received['receiving_date']);
                            echo htmlspecialchars($receiving_date->format('F j, Y'));
                        ?></td>
                        <td><?php echo htmlspecialchars($received['laptop_brand']); ?></td>
                        <td><?php echo htmlspecialchars($received['quantity']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($received['price_per_piece'], 2)); ?></td>
                        <td><?php echo htmlspecialchars(number_format($total_price, 2)); ?></td>
                        <td><?php echo htmlspecialchars($received['details']); ?></td>
                        <td><?php echo htmlspecialchars($received['receiver_name']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No received equipment found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Button to return to the main page -->
    <div class="text-right mt-5">
         <button class="btn btn-info" onclick="window.history.back();">Back to Previous Page</button>
     </div>
</div>

<!-- Modal for displaying details -->
<!-- Existing modal code unchanged -->

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        // Time and date update function
        function updateTimeAndDate() {
            const optionsTime = { timeZone: 'Asia/Manila', hour12: true, hour: 'numeric', minute: 'numeric', second: 'numeric' };
            const optionsDate = { timeZone: 'Asia/Manila', year: 'numeric', month: 'long', day: 'numeric' };

            const formatterTime = new Intl.DateTimeFormat('en-US', optionsTime);
            const formatterDate = new Intl.DateTimeFormat('en-US', optionsDate);

            $('#pst-time').text(formatterTime.format(new Date()));
            $('#pst-date').text(formatterDate.format(new Date()));
        }

        updateTimeAndDate();
        setInterval(updateTimeAndDate, 1000);

        // Populate modal with row data
        $('#detailsModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            // Existing modal population code unchanged
        });
    });
</script>
</body>
</html>
