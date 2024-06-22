<?php
session_start();

// Check if user is logged in and is an employee
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

// Include function.php for database functions and configurations
require_once('function.php');

// Handle recording new sales record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['record_sale'])) {
    $customer_name = $_POST['customer_name'];
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    
    // Example function for recording sales
    $result = recordSale($_SESSION['user_id'], $customer_name, $item_id, $quantity); // Define this function in function.php
    
    if ($result) {
        // Log action
        logAction($_SESSION['user_id'], "Recorded sale for Customer: $customer_name, Item ID: $item_id, Quantity: $quantity");
        
        // Redirect after successful recording
        header('Location: employee_dashboard.php');
        exit();
    } else {
        // Handle recording failure
        $error = "Failed to record sale.";
    }
}

// Handle updating sales record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_sale'])) {
    $sale_id = $_POST['sale_id'];
    $new_quantity = $_POST['new_quantity'];
    
    // Example function for updating sales record
    $result = updateSale($_SESSION['user_id'], $sale_id, $new_quantity); // Define this function in function.php
    
    if ($result) {
        // Log action
        logAction($_SESSION['user_id'], "Updated sale record ID: $sale_id, New Quantity: $new_quantity");
        
        // Redirect after successful update
        header('Location: employee_dashboard.php');
        exit();
    } else {
        // Handle update failure
        $error = "Failed to update sale record.";
    }
}

// Handle deleting sales record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_sale'])) {
    $sale_id = $_POST['sale_id'];
    
    // Example function for deleting sales record
    $result = deleteSale($_SESSION['user_id'], $sale_id); // Define this function in function.php
    
    if ($result) {
        // Log action
        logAction($_SESSION['user_id'], "Deleted sale record ID: $sale_id");
        
        // Redirect after successful deletion
        header('Location: employee_dashboard.php');
        exit();
    } else {
        // Handle deletion failure
        $error = "Failed to delete sale record.";
    }
}

// Fetch stock items assigned to this employee
$stockItems = getStockItemsByEmployee($_SESSION['user_id']); // Function defined in function.php

// Fetch sales records entered by this employee
$salesRecords = getSalesRecordsByEmployee($_SESSION['user_id']); // Function defined in function.php

// Fetch stock types for the dropdown
$stockTypes = getStockTypes(); // Function defined in function.php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <style>
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
    
    /* Form styling */
    form {
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

    a{
        adding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 4px;
    }
</style>

    </style>
</head>
<body>
    <div class="container">
        <h2>Employee Dashboard</h2>
        
        <!-- Display any errors here -->
        <?php if (isset($error)) { echo "<p class='error'>Error: $error</p>"; } ?>
        
        <!-- Record New Sale Form -->
        <h3>Record New Sale</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" required>
            
            <label for="type_id">Stock Type:</label>
                <select name="type_id" id="type_id" class="form-control" required>
                    <?php foreach($stockTypes as $type): ?>
                        <option value="<?= $type["type_id"] ?>"><?= $type["type_name"] ?></option>
                    <?php endforeach; ?>
                </select>

                
            
            <label for="item_id">Item:</label>
            <select id="item_id" name="item_id" required>
                <option value="">Select Item</option>
            </select>
            
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>
            <button type="submit" name="record_sale">Record Sale</button>
        </form>

        <script>
            function fetchItems(typeId) {
                if (typeId === "") {
                    document.getElementById("item_id").innerHTML = "<option value=''>Select Item</option>";
                    return;
                }
                
                const xhr = new XMLHttpRequest();
                xhr.open("GET", "fetch_items.php?type_id=" + typeId, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        document.getElementById("item_id").innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            }
        </script>

        
        <!-- Update Sale Record Form -->
        <h3>Update Sale Record</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="sale_id">Sale ID:</label>
            <input type="number" id="sale_id" name="sale_id" required>
            <label for="new_quantity">New Quantity:</label>
            <input type="number" id="new_quantity" name="new_quantity" required>
            <button type="submit" name="update_sale">Update Sale Record</button>
        </form>
        
        <!-- Delete Sale Record Form -->
        <h3>Delete Sale Record</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="sale_id">Sale ID:</label>
            <input type="number" id="sale_id" name="sale_id" required>
            <button type="submit" name="delete_sale">Delete Sale Record</button>
        </form>
        

        <a href="logout.php">Logout</a>
</body>
</html>
