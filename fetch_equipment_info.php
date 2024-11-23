<?php
// Database connection
include 'db_connection.php';

if (isset($_GET['school_id'])) {
    $schoolId = intval($_GET['school_id']); // Sanitize input

    // Query to fetch equipment details specific to the school, grouped by distribution date and quantity
    $detailsQuery = "SELECT distributed_date, serial_number, details, price_per_piece, quantity
                     FROM distribution
                     WHERE school_id = :school_id
                     ORDER BY distributed_date ASC, quantity DESC"; // Order by date and quantity

    try {
        $detailsStmt = $pdo->prepare($detailsQuery);
        $detailsStmt->bindParam(':school_id', $schoolId, PDO::PARAM_INT);
        $detailsStmt->execute();

        // Check if records exist for this school
        if ($detailsStmt->rowCount() > 0) {
            $currentDate = null;
            $currentQuantity = null;

            while ($row = $detailsStmt->fetch(PDO::FETCH_ASSOC)) {
                // Check if the distribution date or quantity has changed
                if ($currentDate !== $row['distributed_date'] || $currentQuantity !== $row['quantity']) {
                    $currentDate = $row['distributed_date'];
                    $currentQuantity = $row['quantity'];

                    // Display a new row for a different distribution date and/or quantity
                    echo "<tr><td colspan='3'><strong>Distribution Date: {$currentDate}</strong> | Quantity: {$currentQuantity}</td></tr>";
                }

                // Format price_per_piece in PHP peso format
                $formattedPrice = "₱" . number_format($row['price_per_piece'], 2);

                echo "<tr>
                        <td>{$row['serial_number']}</td>
                        <td>{$row['details']}</td>
                        <td>{$formattedPrice}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No distribution records found for this school.</td></tr>";
        }
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
    }
} else {
    echo "<tr><td colspan='3'>Invalid or missing school ID parameter.</td></tr>";
}
?>
