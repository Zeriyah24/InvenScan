<?php
session_start();
// Check if the user is not logged in
if (!isset($_SESSION['name']) || !isset($_SESSION['school_id']) || !isset($_SESSION['id'])) {
    // Redirect to the login page or another appropriate page
    header("Location: login.php"); // Adjust as per your application flow
    exit;
}
// Include database connection
require_once 'db_connection.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize variables
$name = isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Unknown User';
$school_id = isset($_SESSION['school_id']) ? $_SESSION['school_id'] : null;

// Fetch batch options for the dropdown
try {
    $stmt = $pdo->prepare("SELECT batch_id FROM batch WHERE school_id = ?");
    $stmt->execute([$school_id]);
    $batch_options = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo "Error fetching batches: " . $e->getMessage();
    exit;
}

    function determineDashboardLink() {
        // Check if session variables are set
        if (!isset($_SESSION['role']) || !isset($_SESSION['school_id'])) {
            return "login.php"; // Redirect to login if session variables are not set
        }

        // Assign variables from session
        $role = $_SESSION['role'];
        $school_id = $_SESSION['school_id'];

        // Determine the appropriate dashboard link
        if ($role == "Admin") {
            switch ($school_id) {
                case 1:
                    return 'admin_dashboard_A.+Mabini+Elementary+School.php';
                case 2:
                    return 'admin_dashboard_Amparo+Elementary+School.php';
                case 3:
                    return 'admin_dashboard_Amparo+High+School.php';
                case 4:
                    return 'admin_dashboard_Andres+Bonifacio+Elementary+School.php';
                case 5:
                    return 'admin_dashboard_Antonio+Luna+Elementary+School.php';
                case 6:
                    return 'admin_dashboard_Antonio+Luna+High+School.php';
                case 7:
                    return 'admin_dashboard_AntonioUy+Tan+Senior+High+School.php';
                case 8:
                    return 'admin_dashboard_Baesa+Elementary+School.php';
                case 9:
                    return 'admin_dashboard_Baesa+High+School.php';
                case 10:
                    return 'admin_dashboard_Bagbaguin+Elementary+School.php';
                case 11:
                    return 'admin_dashboard_Bagong+Barrio+Elementary+School.php';
                case 12:
                    return 'admin_dashboard_Bagong+Barrio+National+High+School.php';
                case 13:
                    return 'admin_dashboard_Bagong+Barrio+Senior+High+School.php';
                case 14:
                    return 'admin_dashboard_Bagong+Silang+Elementary+School.php';
                case 15:
                    return 'admin_dashboard_Bagong+Silang+Elementary+School+(4th Ave.).php';
                case 16:
                    return 'admin_dashboard_Bagong+Silang+High+School.php';
                case 17:
                    return 'admin_dashboard_Bagumbong+Elementary+School.php';
                case 18:
                    return 'admin_dashboard_Bagumbong+High+School.php';
                case 19:
                    return 'admin_dashboard_Benigno+Aquino+Junior+High+School.php';
                case 20:
                    return 'admin_dashboard_Brixton+Senior+High+School.php';
                case 21:
                    return 'admin_dashboard_Caloocan+Central+Elementary+School.php';
                case 22:
                    return 'admin_dashboard_Caloocan+City+Business+High+School.php';
                case 23:
                    return 'admin_dashboard_Caloocan+City+Science+High+School.php';
                case 24:
                    return 'admin_dashboard_Caloocan+High+School.php';
                case 25:
                    return 'admin_dashboard_Caloocan+Nat’L+Science+&+Technology+Hs.php';
                case 26:
                    return 'admin_dashboard_Caloocan+North+Elementary+School.php';
                case 27:
                    return 'admin_dashboard_Camarin+D+Elementary+School.php';
                case 28:
                    return 'admin_dashboard_Camarin+D+Elementary+School-Unit+II.php';
                case 29:
                    return 'admin_dashboard_Camarin+Elementary+School.php';
                case 30:
                    return 'admin_dashboard_Camarin+High+School.php';
                case 31:
                    return 'admin_dashboard_Caybiga+Elementary+School.php';
                case 32:
                    return 'admin_dashboard_Caybiga+High+School.php';
                case 33:
                    return 'admin_dashboard_Cayetano+Arellano+Elementary+School.php';
                case 34:
                    return 'admin_dashboard_Cecilio+Apostol+Elementary+School.php';
                case 35:
                    return 'admin_dashboard_Cielito+Zamora+Elementary+School.php';
                case 36:
                    return 'admin_dashboard_Cielito+Zamora+Junior+High+School.php';
                case 37:
                    return 'admin_dashboard_Cielito+Zamora+Senior+High+School.php';
                case 38:
                    return 'admin_dashboard_Congress+Elementary+School.php';
                case 39:
                    return 'admin_dashboard_Deparo+Elementary+School.php';
                case 40:
                    return 'admin_dashboard_Deparo+High+School.php';
                case 41:
                    return 'admin_dashboard_East+Bagong+Barrio+Elementary+School.php';
                case 42:
                    return 'admin_dashboard_Eulogio+Rodriguez+Elementary+School.php';
                case 43:
                    return 'admin_dashboard_Gabriela+Silang+Elementary+School.php';
                case 44:
                    return 'admin_dashboard_Gomburza+Elementary+School.php';
                case 45:
                    return 'admin_dashboard_Grace+Park+Elementary+School.php';
                case 46:
                    return 'admin_dashboard_Gregoria+De+Jesus+Elementary+School.php';
                case 47:
                    return 'admin_dashboard_Horacio+Dela+Costa+Elementary+School.php';
                case 48:
                    return 'admin_dashboard_Horacio+Dela+Costa+High+School.php';
                case 49:
                    return 'admin_dashboard_Kalayaan+Elementary+School.php';
                case 50:
                    return 'admin_dashboard_Kalayaan+National+High+School.php';
                case 51:
                    return 'admin_dashboard_Kasarinlan+Elementary+School.php';
                case 52:
                    return 'admin_dashboard_Kasarinlan+High+School.php';
                case 53:
                    return 'admin_dashboard_Kaunlaran+Elementary+School.php';
                case 54:
                    return 'admin_dashboard_Lerma+Elementary+School.php';
                case 55:
                    return 'admin_dashboard_Libis+Baesa+Elementary+School.php';
                case 56:
                    return 'admin_dashboard_Libis+Talisay+Elementary+School.php';
                case 57:
                    return 'admin_dashboard_Llano+Elementary+School.php';
                case 58:
                    return 'admin_dashboard_Llano+High+School.php';
                case 59:
                    return 'admin_dashboard_M.B.+Asistio+High+School-Unit+I.php';
                case 60:
                    return 'admin_dashboard_M.B.+Asistio+Senior+High+School.php';
                case 61:
                    return 'admin_dashboard_Ma.+Clara+High+School.php';
                case 62:
                    return 'admin_dashboard_Manuel+L.+Quezon+Elementary+School.php';
                case 63:
                    return 'admin_dashboard_Manuel+L.+Quezon+High+School.php';
                case 64:
                    return 'admin_dashboard_Marcelo+H.+Del+Pilar+Elementary+School.php';
                case 65:
                    return 'admin_dashboard_Maypajo+Elementary+School.php';
                case 66:
                    return 'admin_dashboard_Maypajo+High+School.php';
                case 67:
                    return 'admin_dashboard_Morning+Breeze+Elementary+School.php';
                case 68:
                    return 'admin_dashboard_Mountain+Heights+High+School.php';
                case 69:
                    return 'admin_dashboard_NHC+Elementary+School.php';
                case 70:
                    return 'admin_dashboard_NHC+High+School.php';
                case 71:
                    return 'admin_dashboard_Pag-Asa+Elementary+School.php';
                case 72:
                    return 'admin_dashboard_Pangarap+Elementary+School.php';
                case 73:
                    return 'admin_dashboard_Pangarap+High+School.php';
                case 74:
                    return 'admin_dashboard_Rene+Cayetano+Elementary+School.php';
                case 75:
                    return 'admin_dashboard_Samaria+Senior+High+School.php';
                case 76:
                    return 'admin_dashboard_Sampaguita+Elementary+School.php';
                case 77:
                    return 'admin_dashboard_Sampaguita+High+School.php';
                case 78:
                    return 'admin_dashboard_Sampalukan+Elementary+School.php';
                case 79:
                    return 'admin_dashboard_San+Jose+Elementary+School.php';
                case 80:
                    return 'admin_dashboard_Silanganan+Elementary+School.php';
                case 81:
                    return 'admin_dashboard_Sta.+Quiteria+Elementary+School.php';
                case 82:
                    return 'admin_dashboard_Sto.+Niño+Elementary+School.php';
                case 83:
                    return 'admin_dashboard_Tala+Elementary+School.php';
                case 84:
                    return 'admin_dashboard_Tala+High+School.php';
                case 85:
                    return 'admin_dashboard_Talipapa+Elementary+School.php';
                case 86:
                    return 'admin_dashboard_Talipapa+High+School.php';
                case 87:
                    return 'admin_dashboard_Tandang+Sora+Integrated+School.php';
                case 88:
                    return 'admin_dashboard_Urduja+Elementary+School.php';
                case 89:
                    return 'admin_dashboard_Vicente+Malapitan+Senior+High+School.php';
                default:
                    return "dashboard_generic.php"; // Default for unknown school IDs
            }
        }

     else {
            return "access_denied.php"; // Redirect if role is not Admin
        }
    }


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch form data
    $batch_id = htmlspecialchars($_POST['batch_id']);
    $item_name = htmlspecialchars($_POST['item_name']);
    $quantity = intval($_POST['quantity']);
    $uom = htmlspecialchars($_POST['uom']);
    $description = htmlspecialchars($_POST['descript']);
    $unit_price = floatval($_POST['unit_price']);
    $total_price = $quantity * $unit_price;
    $status = htmlspecialchars($_POST['stat']);
    $name = $_SESSION['name']; // Fetch the user's name from session

    try {
        // Prepare and execute the INSERT statement
        $stmt = $pdo->prepare("INSERT INTO items (school_id, batch_id, item_name, quantity, uom, descript, unit_price, total_price, stat, generator) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$school_id, $batch_id, $item_name, $quantity, $uom, $description, $unit_price, $total_price, $status, $name]);

        // Redirect to the appropriate dashboard
        header("Location: " . determineDashboardLink());
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add New Batch Package</title>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: #f4f4f4;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .form-group {
        margin-bottom: 20px;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Add New Batch Package</h1>
    <form id="addItemForm" action="add_item.php" method="POST">
        <div class="form-group">
            <label for="schoolDropdown">School</label>
            <!-- Echo the session school_id -->
            <select id="schoolDropdown" name="school" class="form-control" disabled>
                <option value="<?php echo $school_id; ?>"><?php echo $school_id; ?></option>
            </select>
            <!-- Use a hidden input to pass school_id in POST -->
            <input type="hidden" name="school" value="<?php echo $school_id; ?>">
        </div>
        <div class="form-group">
            <label for="batch_id">Batch ID</label>
            <select id="batch_id" name="batch_id" class="form-control" required>
                <option value="">- Select Batch ID -</option>
                <?php foreach ($batch_options as $option): ?>
                    <option value="<?php echo htmlspecialchars($option); ?>"><?php echo htmlspecialchars($option); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="item_name">Item Name</label>
            <input type="text" id="item_name" name="item_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="uom">Unit of Measurement (UOM)</label>
            <input type="text" id="uom" name="uom" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="descript">Description</label>
            <textarea id="descript" name="descript" class="form-control" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="unit_price">Unit Price</label>
            <input type="text" id="unit_price" name="unit_price" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="total_price">Total Price</label>
            <input type="text" id="total_price" name="total_price" class="form-control" readonly>
        </div>
        <div class="form-group">
            <label for="stat">Status</label>
            <select id="stat" name="stat" class="form-control" required>
                <option value="Working">Working</option>
                <option value="Defective">Defective</option>
                <option value="Condemn">Condemn</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Batch Package</button>
    </form>
    <p><a href="<?php echo determineDashboardLink(); ?>">⇐ Back to Dashboard</a></p>

    <script>
        // Calculate total price based on unit price and quantity
        document.getElementById('unit_price').addEventListener('input', function() {
            calculateTotalPrice();
        });

        document.getElementById('quantity').addEventListener('input', function() {
            calculateTotalPrice();
        });

        function calculateTotalPrice() {
            let unitPrice = parseFloat(document.getElementById('unit_price').value);
            let quantity = parseInt(document.getElementById('quantity').value);
            let totalPrice = unitPrice * quantity;
            document.getElementById('total_price').value = totalPrice.toFixed(2);
        }
    </script>

</div>
</body>
</html>
