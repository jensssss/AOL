<?php
require_once('function.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Password should be hashed for security
    $role = $_POST['role']; // Role can be 'admin', 'manager', or 'employee'

    // Call register_user function from function.php
    $registrationResult = register_user($username, $password, $role);

    if ($registrationResult === true) {
        echo "New user registered successfully";
    } else {
        echo "Registration failed: " . $registrationResult;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <!-- Your HTML form for user registration -->
    <h2>User Registration Form</h2>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="manager">Manager</option>
            <option value="employee">Employee</option>
        </select><br><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
