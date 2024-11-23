<?php
include 'db_connection.php'; // Ensure this file connects to your database and defines $pdo

$delivery_id = isset($_GET['delivery_id']) ? intval($_GET['delivery_id']) : 0;

if (!$pdo) {
    die("Database connection failed.");
}

try {
  $sql = "SELECT di.distributed_date, di.quantity, d.laptop_brand, s.school_name, s.school_id AS school_id
      FROM distribution di
      JOIN delivery_items d ON di.delivery_id = d.id
      JOIN schools s ON di.school_id = s.school_id
      WHERE di.delivery_id = :delivery_id";


    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':delivery_id', $delivery_id, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        foreach ($results as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['distributed_date']) . "</td>
                    <td>" . htmlspecialchars($row['quantity']) . "</td>
                    <td>" . htmlspecialchars($row['laptop_brand']) . "</td>
                    <td><a href='#' class='school-name' data-school-id='" . htmlspecialchars($row['school_id']) . "' data-delivery-id='" . htmlspecialchars($delivery_id) . "'>" . htmlspecialchars($row['school_name']) . "</a></td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No distribution data available.</td></tr>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
