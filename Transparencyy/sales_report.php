<?php
session_start();

if(!isset($_SESSION["login"])){
    header("Location: login.php");
    exit;
}
require 'function.php';

$sales = getSalesReport();
$totalOmzet = getTotalOmzet();
$netProfit = getNetProfit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Sales Report</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity Sold</th>
                    <th>Sale Date</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($sales as $sale): ?>
                <tr>
                    <td><?= htmlspecialchars($sale["item_name"]) ?></td>
                    <td><?= htmlspecialchars($sale["quantity_sold"]) ?></td>
                    <td><?= htmlspecialchars($sale["sale_date"]) ?></td>
                    <td><?= htmlspecialchars($sale["price"]) ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <div class="mt-4">
            <h3>Total Omzet: <?= htmlspecialchars($totalOmzet) ?></h3>
            <h3>Net Profit: <?= htmlspecialchars($netProfit) ?></h3>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>