<?php
session_start();

if (!isset($_SESSION['name']) || !isset($_SESSION['school_id']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_connection.php';



function getSchoolName($pdo, $school_id) {
    $stmt = $pdo->prepare("SELECT school_name FROM schools WHERE school_id = ?");
    $stmt->execute([$school_id]);
    return $stmt->fetchColumn();
}



// Fetch the school name based on the school_id in the session
$school_name = getSchoolName($pdo, $_SESSION['school_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .breadcrumb-container {
            margin-top: 10px;
            padding-left: 20px;
        }
        .breadcrumb {
            background-color: transparent;
            margin-bottom: 0;
            font-size: 14px;
        }
        .dashboard-panel {
            margin-top: 100px;
            padding: 20px;
        }
        .card {
            text-align: center;
            background-color: #BFD8AF;
            border: none;
            box-shadow: 0px 6px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            transition: transform 0.2s ease-in-out;
            height: 75%;
            padding: 20px 20px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 12px rgba(0,0,0,0.2);
        }
        .card-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .card-description {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        .card-body a {
            color: #fff;
            background-color: #C0EBA6;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .card-body a:hover {
            background-color: #86D293;
        }
        .row .col-md-4 {
            margin-bottom: 20px;
        }
        .dashboard-panel .row {
            justify-content: center;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
    <div class="container dashboard-panel">
        <div class="content">
            <center><h1><?php echo htmlspecialchars($school_name); ?></h1></center>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-folder-open"></i>Batch Records</h5>
                            <p class="card-description">View and manage batch records.</p>
                            <a href="go_to_records.php" class="btn btn-primary">Go to Records</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-box"></i>Inventory</h5>
                            <p class="card-description">Manage your inventory.</p>
                            <a href="inventory.php" class="btn btn-primary">View Inventory</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-truck"></i> Delivery Management</h5>
                            <p class="card-description">Manage equipment deliveries.</p>
                            <a href="deliveries.php" class="btn btn-primary">View Deliveries</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-search"></i>Tracking Device</h5>
                            <p class="card-description">Track devices effectively.</p>
                            <a href="tracking.php" class="btn btn-primary">Track Devices</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-tools"></i>Ticketing System</h5>
                            <p class="card-description">Request a technician for support.</p>
                            <a href="ticketing_system.php" class="btn btn-primary">View Tickets</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-sign-out-alt"></i>Logout</h5>
                            <p class="card-description">Log out from the admin panel.</p>
                            <a href="logout.php" class="btn btn-danger">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Closing the content div -->
    </div> <!-- Closing the dashboard-panel div -->
    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
