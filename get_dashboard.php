<?php
// Include database connection and function
require_once 'db_connect.php'; // Adjust path as necessary

// Function to sanitize input data
function sanitizeInput($input) {
    return htmlspecialchars(trim($input));
}

// Establish database connection using PDO
$pdo = new PDO('mysql:host=localhost;dbname=userauthdb;charset=utf8mb4', 'username', 'password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exception

// Function to fetch all schools' dashboard content from database using PDO
function getAllSchoolsDashboardContent($pdo) {
    try {
        // Prepare SQL statement to fetch all schools' dashboard content
        $stmt = $pdo->prepare('SELECT name, dashboard_content FROM schools');
        $stmt->execute();

        // Fetch all schools' dashboard content
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            return $result;
        } else {
            return "No dashboard content available for any school.";
        }

    } catch (PDOException $e) {
        // Handle database connection and query errors
        return "Database error: ". $e->getMessage();
    }
}

// Get all schools' dashboard content
$allSchoolsDashboardContent = getAllSchoolsDashboardContent($pdo);

// Display all schools' dashboard content in admin dashboard
if ($allSchoolsDashboardContent) {
   ?>
    <h1>Admin Dashboard</h1>
    <table border="1">
        <tr>
            <th>School Name</th>
            <th>Dashboard Content</th>
        </tr>
        <?php foreach ($allSchoolsDashboardContent as $school) {?>
        <tr>
            <td><?php echo $school['name'];?></td>
            <td><?php echo $school['dashboard_content'];?></td>
        </tr>
        <?php }?>
    </table>
    <?php
} else {
    echo $allSchoolsDashboardContent;
}
?>
