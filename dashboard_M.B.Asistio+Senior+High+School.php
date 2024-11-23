<?php
session_start();

// Set school_id specifically for this page
$_SESSION['school_id'] = 60;

// Check if the user is not logged in
if (!isset($_SESSION['name']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once 'db_connection.php';

// Function to get batch IDs
function getBatchIds($pdo) {
    $stmt = $pdo->prepare("SELECT DISTINCT batch_id FROM items WHERE school_id = ?");
    $stmt->execute([$_SESSION['school_id']]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get items by batch ID and status
function getItemsByBatchIdAndStatus($pdo, $batch_id, $status) {
    $table = 'items';
    if ($status === 'Defective') {
        $table = 'defective_items';
    } elseif ($status === 'Condemn') {
        $table = 'condemn_items';
    }

    $sql = "SELECT item_id, item_name, quantity, uom, unit_price, total_price, stat, generator, created_at
            FROM $table
            WHERE batch_id = ? AND school_id = ?";

    if (!empty($status)) {
        $sql .= " AND stat = ?";
    }

    $stmt = $pdo->prepare($sql);

    if (!empty($status)) {
        $stmt->execute([$batch_id, $_SESSION['school_id'], $status]);
    } else {
        $stmt->execute([$batch_id, $_SESSION['school_id']]);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$batch_ids = getBatchIds($pdo);

$batch_id = isset($_GET['batch_id']) ? htmlspecialchars($_GET['batch_id']) : '';
$status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';

$items = [];
if (!empty($batch_id)) {
    $items = getItemsByBatchIdAndStatus($pdo, $batch_id, $status);
}

// Update item status if form submitted
if (isset($_POST['items_id']) && isset($_POST['new_status']) && isset($_POST['quantity'])) {
    $items_id = $_POST['items_id'];
    $new_status = $_POST['new_status'];
    $quantity = $_POST['quantity'];

    // Function to update item status (implement as needed)
    // if (updateItemStatus($pdo, $items_id, $new_status, $quantity)) {
    //     header("Location: " . $_SERVER['REQUEST_URI']);
    //     exit;
    // }
}

// Check if user role is 'Main' to show back to Main Dashboard link
$showMainDashboardLink = ($_SESSION['role'] === 'Main');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .navbar {
            position: fixed;
            top: 0;
            width: 96%;
            padding: 10px 20px;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand img {
            height: 75px;
            width: auto;
        }
        .school-division {
            flex-grow: 1;
            text-align: center;
        }
        .school-division h2 {
            color: green;
            font-size: 36px;
            margin: 0;
        }
        #pst-container {
            text-align: right;
            font-family: Arial, sans-serif;
        }
        .content {
            flex: 1;
            padding: 20px;
            background-color: #ecf0f1;
            margin-left: 250px;
            margin-top: 100px;
            overflow-y: auto;
        }
        .dropdown {
            margin-bottom: 20px;
        }
        .dropdown select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: none;
            border-radius: 4px;
        }
        .panel {
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 4px;
            color: #2c3e50;
        }
        .panel h3 {
            margin-top: 0;
        }
        .sidebar {
            position: fixed;
            top: 100px;
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            height: calc(100% - 100px);
            overflow-y: auto;
        }
        .nav-item {
            margin-bottom: 20px;
        }
        .nav-link {
            display: block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            background-color: #34495e;
            border-radius: 4px;
            text-align: center;
        }
        .nav-link:hover {
            background-color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #2c3e50;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #34495e;
            color: white;
        }
        form {
            display: inline;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .status-dropdown {
            width: 100%;
            padding: 5px;
            border: none;
            border-radius: 4px;
            background-color: #ecf0f1;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            max-width: 600px;
            border-radius: 8px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .nav-link.add-batch-btn {
            display: block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            background-color: #34495e;
            border-radius: 4px;
            text-align: center;
        }
        .nav-link.add-batch-btn:hover {
            background-color: #2c3e50;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <a class="navbar-brand" href="#">
        <img src="logo6.png" alt="School Logo">
    </a>
    <div class="school-division">
        <h2>SCHOOL DIVISION OFFICE OF CALOOCAN</h2>
    </div>
    <div id="pst-container">
        <div>Philippine Standard Time:</div>
        <div id="pst-time"></div>
        <div id="pst-date"></div>
    </div>
</nav>
<div class="container">
    <div class="sidebar">
        <h2>User Dashboard</h2>
        <div class="nav-item">
            <a class="nav-link" href="dashboard_M.B.Asistio+Senior+High+School.php">Batch Records</a>
        </div>
        <?php if ($showMainDashboardLink): ?>
            <div class="nav-item">
                <a class="nav-link" href="mainAdmin_dashboard.php">Back to Main Dashboard</a>
            </div>
        <?php endif; ?>
        <div class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
    <div class="content">
        <h1>M.B Asistio Senior High School Unit I</h1>
        <form method="GET" action="">
                <div class="dropdown">
                    <label for="batch_id">Select Batch ID:</label>
                    <select id="batch_id" name="batch_id">
                        <option value="">Select a batch ID</option>
                        <?php foreach ($batch_ids as $batch): ?>
                            <option value="<?= htmlspecialchars($batch['batch_id']) ?>" <?= isset($batch_id) && $batch_id == $batch['batch_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($batch['batch_id']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="dropdown">
                    <label for="status">Select Status:</label>
                    <select id="status" name="status">
                        <option value="Working" <?= isset($status) && $status == 'Working' ? 'selected' : '' ?>>Working</option>
                        <option value="Defective" <?= isset($status) && $status == 'Defective' ? 'selected' : '' ?>>Defective</option>
                        <option value="Condemn" <?= isset($status) && $status == 'Condemn' ? 'selected' : '' ?>>Condemned</option>
                    </select>
                </div>
                <button type="submit">Filter</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Items ID</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>UOM</th>
                        <th>Unit Price</th>
                        <th>Total Price</th>
                        <th>Generator</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr data-status="<?= htmlspecialchars($item['stat']) ?>">
                                <td><?= htmlspecialchars($item['item_id']) ?></td>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td><?= htmlspecialchars($item['uom']) ?></td>
                                <td><?= htmlspecialchars($item['unit_price']) ?></td>
                                <td><?= htmlspecialchars($item['total_price']) ?></td>
                                <td><?= htmlspecialchars($item['generator']) ?></td>
                                <td><?= htmlspecialchars($item['stat']) ?></td>
                                <td><?= htmlspecialchars($item['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">No items found for the selected batch ID.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
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

        $('#filter-select').on('change', function() {
            const filterValue = $(this).val().toLowerCase();
            $('tbody tr').each(function() {
                const status = $(this).data('status').toLowerCase();
                $(this).toggle(filterValue === 'all' || status === filterValue);
            });
        });
    });
</script>
</body>
</html>
