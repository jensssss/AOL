<?php

// add_stock_item.php

session_start();
require_once('function.php');

// Check if user is logged in and authorized (you can adjust role check as needed)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php');
    exit();
}

// Example function to add a new stock item
function addStockItem($itemName, $typeId, $quantity, $price) {
    $conn = db_connect();
    $itemName = mysqli_real_escape_string($conn, $itemName);
    $typeId = (int) $typeId;
    $quantity = (int) $quantity;
    $price = (float) $price;

    // Check if the typeId exists in stock_types table
    $checkQuery = "SELECT type_id FROM stock_types WHERE type_id = $typeId";
    $checkResult = $conn->query($checkQuery);
    
    if ($checkResult->num_rows > 0) {
        // typeId exists, proceed with insertion
        $query = "INSERT INTO stock_items (item_name, type_id, quantity, price) VALUES ('$itemName', $typeId, $quantity, $price)";
        
        if ($conn->query($query) === TRUE) {
            // Log the action
            logAction($_SESSION['user_id'], "Added new stock item: $itemName, type_id: $typeId, quantity: $quantity, price: $price");
            return true;
        } else {
            return false;
        }
    } else {
        // typeId does not exist in stock_types table
        return false;
    }

    $conn->close();
}

// Example usage:
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemName = $_POST['item_name'];
    $typeId = $_POST['type_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    if (addStockItem($itemName, $typeId, $quantity, $price)) {
        // Redirect or display success message
        header('Location: stock_dashboard.php');
        exit();
    } else {
        // Handle error
        echo "Error adding stock item.";
    }
}

?>