<?php
// Database connection function
function db_connect() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "Transparency";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// function.php

// Function to fetch all roles from database
function getRoles() {
    $conn = db_connect();


    $roles = array();

    // SQL query to fetch all roles
    $sql = "SELECT * FROM roles";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
    }

    return $roles;
}


// Function to get role ID from role name
function getRoleIdFromName($conn, $roleName) {
    // Sanitize the role name to prevent SQL injection
    $roleName = $conn->real_escape_string($roleName);

    // Query to fetch role ID based on role name
    $query = "SELECT role_id FROM roles WHERE role_name = '$roleName'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['role_id'];
    } else {
        return null; // Return null if role name not found
    }
}

// Function to fetch all stock items
function getAllStockItems() {
    $conn = db_connect();
    $query = "SELECT * FROM stock_items";
    $result = $conn->query($query);
    $stockItems = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stockItems[] = $row;
        }
    }

    $conn->close();
    return $stockItems;
}


function getAllUsers() {
    $conn = db_connect();
    $query = "SELECT * FROM users";
    $result = $conn->query($query);
    $users = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    $conn->close();
    return $users;
}

function updateUserRole($userId, $newRoleId) {
    $conn = db_connect();
    $userId = (int) $userId;
    $newRoleId = (int) $newRoleId;

    $query = "UPDATE users SET role_id = $newRoleId WHERE user_id = $userId";

    if ($conn->query($query) === TRUE) {
        return true;
    } else {
        return false;
    }

    $conn->close();
}

// Function to register a new user with role assignment
function register_user($username, $password, $role) {
    $conn = db_connect();

    // Hash the password (you should use password_hash() function for better security)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into users table
    // Role is assigned based on the provided role name
    $query = "INSERT INTO users (username, password, role_id) 
              VALUES ('$username', '$hashedPassword', 
                      (SELECT role_id FROM roles WHERE role_name = '$role'))";

    if ($conn->query($query) === TRUE) {
        return true; // Registration successful
    } else {
        return "Error: " . $query . "<br>" . $conn->error;
    }

    $conn->close();
}

function deleteUser($userId) {
    $conn = db_connect();
    $userId = (int) $userId;

    $query = "DELETE FROM users WHERE user_id = $userId";

    if ($conn->query($query) === TRUE) {
        return true;
    } else {
        return false;
    }

    $conn->close();
}

// Function to validate user credentials
function validate_user($username, $password) {
    $conn = db_connect();

    // Sanitize inputs
    $username = mysqli_real_escape_string($conn, $username);

    // Query to fetch user details including role
    $query = "SELECT u.user_id, u.username, u.password, u.role_id, r.role_name 
              FROM users u
              INNER JOIN roles r ON u.role_id = r.role_id
              WHERE username = '$username'";
    
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        // User found, verify password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password correct, return user details
            return $user;
        } else {
            // Password incorrect
            return false;
        }
    } else {
        // User not found
        return false;
    }

    $conn->close();
}

// Function to retrieve stock types
function getStockTypes() {
    $conn = db_connect();
    $query = "SELECT type_id, type_name FROM stock_types";
    $result = $conn->query($query);
    $stockTypes = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stockTypes[] = $row;
        }
    }
    $conn->close();
    return $stockTypes;
}

// Function to add a new stock item
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


// Function to update stock quantity
function updateStock($itemId, $newQuantity, $newPrice) {
    $conn = db_connect();
    $itemId = (int) $itemId;
    $newQuantity = (int) $newQuantity;

    $query = "UPDATE stock_items 
                SET quantity = $newQuantity,
                price = $newPrice
                WHERE item_id = $itemId";
    
    if ($conn->query($query) === TRUE) {
        return true;
    } else {
        return false;
    }

    $conn->close();
}


function deleteStockItem($itemId) {
    $conn = db_connect();
    $itemId = (int) $itemId;

    $query = "DELETE FROM stock_items WHERE item_id = $itemId";

    if ($conn->query($query) === TRUE) {
        return true;
    } else {
        return false;
    }

    $conn->close();
}

// Function to record a new sale
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
            return true;
        } else {
            return false;
        }
    } else {
        return false; // Item not found or multiple items found (should not happen with correct item_id)
    }

    $conn->close();
}


function updateSale($employeeId, $saleId, $newQuantity) {
    $conn = db_connect();
    $employeeId = (int) $employeeId;
    $saleId = (int) $saleId;
    $newQuantity = (int) $newQuantity;

    // Fetch current item price and quantity
    $saleQuery = "SELECT item_id, quantity FROM sales WHERE sale_id = $saleId AND employee_id = $employeeId";
    $saleResult = $conn->query($saleQuery);

    if ($saleResult->num_rows == 1) {
        $saleData = $saleResult->fetch_assoc();
        $itemId = $saleData['item_id'];
        $currentQuantity = $saleData['quantity'];

        // Fetch current item price
        $itemQuery = "SELECT price FROM stock_items WHERE item_id = $itemId";
        $itemResult = $conn->query($itemQuery);

        if ($itemResult->num_rows == 1) {
            $itemData = $itemResult->fetch_assoc();
            $itemPrice = $itemData['price'];
            $totalPrice = $itemPrice * $newQuantity;

            $query = "UPDATE sales 
                      SET quantity = $newQuantity, total_price = $totalPrice
                      WHERE sale_id = $saleId AND employee_id = $employeeId";

            if ($conn->query($query) === TRUE) {
                return true;
            } else {
                return false;
            }
        } else {
            return false; // Item price not found (should not happen with correct item_id)
        }
    } else {
        return false; // Sale record not found or multiple records found (should not happen with correct sale_id and employee_id)
    }

    $conn->close();
}


function deleteSale($employeeId, $saleId) {
    $conn = db_connect();
    $employeeId = (int) $employeeId;
    $saleId = (int) $saleId;

    $query = "DELETE FROM sales WHERE sale_id = $saleId AND employee_id = $employeeId";

    if ($conn->query($query) === TRUE) {
        return true;
    } else {
        return false;
    }

    $conn->close();
}


function getStockItemsByEmployee($employeeId) {
    $conn = db_connect();
    $employeeId = (int) $employeeId;
    $query = "SELECT * FROM stock_items WHERE employee_id = $employeeId";
    $result = $conn->query($query);
    $stockItems = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stockItems[] = $row;
        }
    }

    $conn->close();
    return $stockItems;
}

function getSalesRecordsByEmployee($employeeId) {
    $conn = db_connect();
    $employeeId = (int) $employeeId;
    $query = "SELECT * FROM sales WHERE employee_id = $employeeId";
    $result = $conn->query($query);
    $salesRecords = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $salesRecords[] = $row;
        }
    }

    $conn->close();
    return $salesRecords;
}


// Function to fetch sales report
function getSalesReport() {
    $conn = db_connect();
    $query = "SELECT s.sale_id, s.customer_name, s.quantity, s.total_price, s.sale_date, i.item_name 
              FROM sales s 
              JOIN stock_items i ON s.item_id = i.item_id 
              ORDER BY s.sale_date DESC";
    
    $result = $conn->query($query);
    $salesReport = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $salesReport[] = $row;
        }
    }

    $conn->close();
    return $salesReport;
}


// Function to get stock items managed by a manager
function getStockItemsByManager($managerId) {
    $conn = db_connect();
    $managerId = (int) $managerId;

    $query = "SELECT si.item_id, si.item_name, si.quantity, si.price, st.type_name 
              FROM stock_items si
              JOIN stock_types st ON si.type_id = st.type_id
              WHERE si.manager_id = $managerId";

    $result = $conn->query($query);
    $stockItems = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stockItems[] = $row;
        }
    }

    $conn->close();
    return $stockItems;
}

function logAction($userId, $action) {
    $conn = db_connect();
    $userId = (int) $userId;
    $action = mysqli_real_escape_string($conn, $action);

    $query = "INSERT INTO audit_trail (user_id, action, action_date) VALUES ($userId, '$action', NOW())";
    
    if ($conn->query($query) === TRUE) {
        return true; // Logging successful
    } else {
        return false; // Logging failed
    }

    $conn->close();
}

// Function to get audit trail logs
function getAuditTrail() {
    $conn = db_connect();

    // Prepare SQL statement
    $query = "SELECT * FROM audit_trail ORDER BY action_date DESC";
    $result = $conn->query($query);

    // Check if results were returned
    $auditLogs = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $auditLogs[] = $row;
        }
    }

    // Close connection
    $conn->close();

    return $auditLogs;
}


?>
