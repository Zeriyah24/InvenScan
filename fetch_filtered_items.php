<?php

// Check if inputs are provided
if (!empty($type1) && !empty($type2)) {
    // Prepare the query based on filter options
    $query = "SELECT * FROM equipment WHERE $type1 = '$type2'";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if the query returns any results
    if ($result && mysqli_num_rows($result) > 0) {
        // Loop through the results and output rows
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['delivery_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['school_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['brand']) . "</td>";
            echo "<td>" . htmlspecialchars($row['type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['serial_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['price']) . "</td>";
            echo "<td>" . htmlspecialchars($row['generator']) . "</td>";
            echo "<td><img src='qr_codes/qr_" . htmlspecialchars($row['id']) . ".png' alt='QR Code' style='width: 100px; height: auto;' onclick='openQRCodeModal(\"qr_codes/qr_" . htmlspecialchars($row['id']) . ".png\")'></td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . htmlspecialchars($row['delivery_date']) . "</td>";
            echo "<td><a href='edit_item.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-primary btn-action'>Edit</a> ";
            echo "<a href='delete_item.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-danger btn-action' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a> ";
            echo "<button class='btn btn-secondary' onclick='printQRCode(\"qr_codes/qr_" . htmlspecialchars($row['id']) . ".png\")'>Print QR</button></td>";
            echo "</tr>";
        }
    } else {
        // No records found
        echo "<tr><td colspan='12'>No items found.</td></tr>";
    }
} else {
    // If no filter is applied, display a message
    echo "<tr><td colspan='12'>Please select valid filter options.</td></tr>";
}

// Close the database connection
mysqli_close($conn);
?>
