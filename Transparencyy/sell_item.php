<?php
session_start();
include('function.php');

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerName = $_POST['customer_name'];
    $itemId = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Fetch item details
    $conn = db_connect();
    $query = "SELECT * FROM stock_items WHERE item_id = $itemId";
    $result = $conn->query($query);
    if ($result->num_rows == 1) {
        $item = $result->fetch_assoc();
        
        if ($item['quantity'] >= $quantity) {
            // Calculate total price
            $totalPrice = $item['price'] * $quantity;

            // Record sale
            if (recordSale($customerName, $itemId, $quantity, $totalPrice)) {
                // Update stock quantity
                $newQuantity = $item['quantity'] - $quantity;
                if (updateStockQuantity($itemId, $newQuantity)) {
                    // Log action
                    logAction($_SESSION['user_id'], "Sold $quantity units of {$item['item_name']} to $customerName");

                    // Redirect to success page or index
                    header('Location: sales_report.php');
                    exit;
                } else {
                    $error = "Failed to update stock quantity.";
                }
            } else {
                $error = "Failed to record sale.";
            }
        } else {
            $error = "Not enough stock available.";
        }
    } else {
        $error = "Item not found.";
    }
    $conn->close();
}

// Fetch stock items for dropdown
$conn = db_connect();
$query = "SELECT * FROM stock_items";
$result = $conn->query($query);
$stockItems = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stockItems[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sell Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            color: #333;
        }
        form {
            max-width: 400px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Sell Item</h2>
    <form method="post">
        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" required><br><br>
        <label for="item_id">Item:</label>
        <select id="item_id" name="item_id" required>
            <?php foreach ($stockItems as $item) : ?>
                <option value="<?php echo $item['item_id']; ?>"><?php echo $item['item_name']; ?> (Quantity: <?php echo $item['quantity']; ?>)</option>
            <?php endforeach; ?>
        </select><br><br>
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br><br>
        <button type="submit">Sell Item</button>
    </form>
    <br><a href="index.php">Back to Dashboard</a>
</body>
</html>

