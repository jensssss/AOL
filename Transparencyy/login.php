<?php
session_start(); // Start session to persist user login state

// Include database connection and functions
require_once('function.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate user credentials
    $user = validate_user($username, $password);

    if ($user) {
        // Authentication successful, store user details in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];

        // Redirect based on user role
        switch ($user['role_id']) {
            case 1: // Admin
                header('Location: admin_dashboard.php');
                exit();
            case 2: // Manager
                header('Location: manager_dashboard.php');
                exit();
            case 3: // Employee
                header('Location: employee_dashboard.php');
                exit();
            default:
                header('Location: index.php'); // Redirect to default page if role not recognized
                exit();
        }
    } else {
        // Authentication failed, show error message
        $error_message = "Invalid username or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        /* Your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding-top: 80px;
        }
        .login-box {
            width: 300px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-box input[type="text"], .login-box input[type="password"] {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-box input[type="submit"] {
            width: 100%;
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .login-box input[type="submit"]:hover {
            background: #45a049;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <?php
        // Display error message if authentication failed
        if (isset($error_message)) {
            echo '<p class="error-message">' . $error_message . '</p>';
        }
        ?>
        <form method="post" action="login.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
