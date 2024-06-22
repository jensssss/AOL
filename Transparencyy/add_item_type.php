<?php
session_start();
include('function.php');

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Example: Check user role and restrict access if needed
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'manager') {
    // Redirect or show access denied message
    echo "Access denied.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $typeName = $_POST['type_name'];

    if (addStockType($typeName)) {
        // Log action
        logAction($_SESSION['user_id'], "Added new stock type: $typeName");

        // Redirect to index or success page
        header('Location: index.php');
        exit;
    } else {
        $error = "Failed to add stock type.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Stock Type</title>
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
        input[type="text"] {
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
    <h2>Add Stock Type</h2>
    <form method="post">
        <label for="type_name">Stock Type Name:</label>
        <input type="text" id="type_name" name="type_name" required><br><br>
        <button type="submit">Add Stock Type</button>
    </form>
    <br><a href="index.php">Back to Dashboard</a>
</body>
</html>

