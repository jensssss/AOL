<?php
session_start();
include('function.php');

// // Redirect to login if not authenticated
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }

// // Example: Check user role and restrict access if needed
// if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'manager') {
//     // Redirect or show access denied message
//     echo "Access denied.";
//     exit;
// }

// Example usage: Fetch sales report
$salesReport = getSalesReport();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Management Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            color: #333;
        }
        h3 {
            color: #555;
        }
        .dashboard-link {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .dashboard-link:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <h3>Stock Management Dashboard</h3>
    
    <a class="dashboard-link" href="add_item_type.php">Add Item Type</a>
    <a class="dashboard-link" href="add_item.php">Add Item</a>
    <a class="dashboard-link" href="sell_item.php">Sell Item</a>
    <a class="dashboard-link" href="sales_report.php">View Sales Report</a>

    <h3>Sales Report</h3>
    <table>
        <tr>
            <th>Sale ID</th>
            <th>Customer Name</th>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Sale Date</th>
        </tr>
        <?php foreach ($salesReport as $sale) : ?>
        <tr>
            <td><?php echo $sale['sale_id']; ?></td>
            <td><?php echo $sale['customer_name']; ?></td>
            <td><?php echo $sale['item_name']; ?></td>
            <td><?php echo $sale['quantity']; ?></td>
            <td><?php echo $sale['total_price']; ?></td>
            <td><?php echo $sale['sale_date']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <br><a href="logout.php">Logout</a>
</body>
</html>

