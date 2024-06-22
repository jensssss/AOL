<?php

// record_sale.php

session_start();
require_once('function.php');

// Check if user is logged in and authorized (you can adjust role check as needed)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php');
    exit();
}

// Example function to record a new sale
function recordSale($employeeId, $customerName, $itemId, $quantity) {
    $conn = db_connect();
    $employeeId = (int) $employeeId;
    $customerName = $conn->real_escape_string($customerName);
    $itemId = (int) $itemId;
    $quantity = (int) $quantity;

    // Fetch item price from stock_items table
    $itemQuery = "SELECT price FROM stock_items WHERE item_id = $itemId";
    $itemResult = $conn->query($itemQuery);

    if ($itemResult->num_rows == 1) {
        $itemData = $itemResult->fetch_assoc();
        $itemPrice = $itemData['price'];
        $totalPrice = $itemPrice * $quantity;

        $query = "INSERT INTO sales (employee_id, customer_name, item_id, quantity, total_price, sale_date) 
                  VALUES ($employeeId, '$customerName', $itemId, $quantity, $totalPrice, NOW())";

        if ($conn->query($query) === TRUE) {
            // Log the action
            logAction($employeeId, "Recorded sale for customer: $customerName, item_id: $itemId, quantity: $quantity");
            return true;
        } else {
            return false;
        }
    } else {
        return false; // Item not found or multiple items found (should not happen with correct item_id)
    }

    $conn->close();
}

// Example usage:
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeId = $_SESSION['user_id']; // Assuming the logged-in user is recording the sale
    $customerName = $_POST['customer_name'];
    $itemId = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    if (recordSale($employeeId, $customerName, $itemId, $quantity)) {
        // Redirect or display success message
        header('Location: sales_dashboard.php');
        exit();
    } else {
        // Handle error
        echo "Error recording sale.";
    }
}


?>