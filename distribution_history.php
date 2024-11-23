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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $school_id = $_POST['school_id'];
    $delivery_id = $_POST['delivery_id'];
    $distributed_date = $_POST['distributed_date'];
    $quantity = $_POST['quantity'];
    $price_per_piece = $_POST['price_per_piece'];
    $total_price = $quantity * $price_per_piece; // Calculate total price

    // Fetch laptop brand from the delivery_items table
    $delivery_sql = "SELECT laptop_brand FROM delivery_items WHERE id = ?";
    $stmt = $conn->prepare($delivery_sql);
    $stmt->bind_param("i", $delivery_id);
    $stmt->execute();
    $stmt->bind_result($laptop_brand);
    $stmt->fetch();
    $stmt->close();

    // Insert distribution record into distribution table
    $insert_sql = "INSERT INTO distribution (school_id, delivery_id, distributed_date, quantity, laptop_brand, total_price)
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iisssi", $school_id, $delivery_id, $distributed_date, $quantity, $laptop_brand, $total_cost);
    $stmt->execute();
    $stmt->close();
}

// Query to fetch distributed equipment history
$distributed_sql = "
    SELECT ds.id AS distribution_id, ds.distributed_date, d.laptop_brand, ds.quantity, ds.total_cost, s.school_name
    FROM distribution ds
    LEFT JOIN delivery_items d ON ds.delivery_id = d.id
    LEFT JOIN schools s ON ds.school_id = s.school_id";

$distributed_result = $conn->query($distributed_sql);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distributed Equipment History</title>
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
      <h1>Distributed Equipment History</h1>

      <table class="table table-striped">
          <thead>
              <tr>
                  <th>Distribution ID</th>
                  <th>Distributed Date</th>
                  <th>Laptop Brand</th>
                  <th>Quantity</th>
                  <th>School Name</th>
                  <th>Total Price (₱)</th> <!-- Added Total Price Column -->
              </tr>
          </thead>
          <tbody>
              <?php if ($distributed_result && $distributed_result->num_rows > 0): ?>
                  <?php while ($distribution = $distributed_result->fetch_assoc()): ?>
                      <tr>
                          <td><?php echo htmlspecialchars($distribution['distribution_id']); ?></td>
                          <td><?php
                              // Format distributed_date
                              $distributed_date = new DateTime($distribution['distributed_date']);
                              echo htmlspecialchars($distributed_date->format('F j, Y'));
                          ?></td>
                          <td><?php echo htmlspecialchars($distribution['laptop_brand']); ?></td>
                          <td><?php echo htmlspecialchars($distribution['quantity']); ?></td>
                          <td><?php echo htmlspecialchars($distribution['school_name']); ?></td>
                          <td><?php echo htmlspecialchars($distribution['total_cost']); ?></td> <!-- Display Total Price -->
                      </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                      <td colspan="6" class="text-center">No distributed equipment found.</td>
                  </tr>
              <?php endif; ?>
          </tbody>
      </table>

      <div class="text-right mt-5">
          <a href="delivery.php" class="btn btn-info">Back to Main Page</a>
      </div>
  </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Time and date update function
        $(document).ready(function() {
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
        });
    </script>
</body>
</html>
