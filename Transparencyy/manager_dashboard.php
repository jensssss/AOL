<?php
session_start();

// Check if user is logged in and is a manager
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();
}

// Include function.php for database functions and configurations
require_once('function.php');

// Handle adding new stock item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_stock'])) {
    $item_name = $_POST['item_name'];
    $type_id = $_POST['type_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    
    // Example function for adding new stock item
    $result = addStockItem($item_name, $type_id, $quantity, $price); // Define this function in function.php
    
    if ($result) {
        // Log action
        logAction($_SESSION['user_id'], "Added new stock item: $item_name");
        
        // Redirect after successful addition
        header('Location: manager_dashboard.php');
        exit();
    } else {
        // Handle addition failure
        $error = "Failed to add new stock item.";
    }
}

// Handle updating stock
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_item'])) {
    $item_id = $_POST['item_id'];
    $new_quantity = $_POST['new_quantity'];
    $new_price = $_POST['new_price'];
    
    // Example function for updating stock quantity
    $result = updateStock($item_id, $new_quantity, $new_price); // Define this function in function.php
    
    if ($result) {
        // Log action
        logAction($_SESSION['user_id'], "Updated stock item ID: $item_id");
        
        // Redirect after successful update
        header('Location: manager_dashboard.php');
        exit();
    } else {
        // Handle update failure
        $error = "Failed to update stock quantity.";
    }
}

// Handle deleting stock item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_item'])) {
    $item_id = $_POST['item_id'];
    
    // Example function for deleting stock item
    $result = deleteStockItem($item_id); // Define this function in function.php
    
    if ($result) {
        // Log action
        logAction($_SESSION['user_id'], "Deleted stock item ID: $item_id");
        
        // Redirect after successful deletion
        header('Location: manager_dashboard.php');
        exit();
    } else {
        // Handle deletion failure
        $error = "Failed to delete stock item.";
    }
}

// Fetching stock items managed by this manager
$stockItems = getStockItemsByManager($_SESSION['user_id']); // Function defined in function.php

// Fetching sales report for this manager's department
$salesReport = getSalesReport(); // Modify to get report by manager ID if needed

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Dashboard</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .logout {
            margin-top: 20px;
        }
    
        form a{
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        form label {
            display: block;
            margin-bottom: 8px;
        }
        form input[type="text"],
        form input[type="number"],
        form select {
            width: calc(100% - 10px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        form button[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        form button[type="submit"]:hover {
            background-color: #0056b3;
        }
</style>

    </style>
</head>
<body>
    <div class="container">
        <h2>Manager Dashboard</h2>
        
        <!-- Display any errors here -->
        <?php if (isset($error)) { echo "<p class='error'>Error: $error</p>"; } ?>
        
        <!-- Add New Stock Item Form -->
        <h3>Add New Stock Item</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="item_name">Item Name:</label>
            <input type="text" id="item_name" name="item_name" required>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>
            
            <!-- Select dropdown for type_id -->
            <label for="type_id">Stock Type:</label>
            <select id="type_id" name="type_id" required>
                <?php
                // Fetch stock types from database and populate options
                $stockTypes = getStockTypes(); // Implement getStockTypes() function to retrieve types
                foreach ($stockTypes as $type) {
                    echo "<option value=\"{$type['type_id']}\">{$type['type_name']}</option>";
                }
                ?>
            </select>
        
            <button type="submit" name="add_stock">Add Stock Item</button>
        </form>

        
        <!-- Update Stock Quantity Form -->
        <h3>Update Stock Quantity</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="item_id">Item ID:</label>
            <input type="number" id="item_id" name="item_id" required>
            <label for="new_quantity">New Quantity:</label>
            <input type="number" id="new_quantity" name="new_quantity" required>
            <label for="new_price">New Price:</label>
            <input type="number" id="new_price" name="new_price" required>
            <button type="submit" name="update_item" class="btn">Update Item</button>
        </form>
        
        <!-- Delete Stock Item Form -->
        <h3>Delete Stock Item</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="item_id">Item ID:</label>
            <input type="number" id="item_id" name="item_id" required>
            <button type="submit" name="delete_item" class="btn">Delete Stock Item</button>
        </form>

        <p class="logout"><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
