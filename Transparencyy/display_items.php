<?php
// display_items.php

// Include function.php for database functions and configurations
require_once('function.php');

// Fetch all stock items
$stockItems = getAllStockItems();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Display Items</title>
    <style>
        /* Your CSS styles */
    </style>
</head>
<body>
    <div class="container">
        <h2>Stock Items</h2>

        <!-- Display Stock Items -->
        <table>
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Type ID</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stockItems as $item): ?>
                    <tr>
                        <td><?php echo $item['item_id']; ?></td>
                        <td><?php echo $item['item_name']; ?></td>
                        <td><?php echo $item['type_id']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo $item['price']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Additional sections or features can be added here -->

        <br>
        <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
    </div>
</body>
</html>
