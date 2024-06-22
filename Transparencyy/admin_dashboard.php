<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php');
    exit();
}

// Include function.php for database functions and configurations
require_once('function.php');

// Handle updating user role
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role_id = $_POST['new_role_id'];
    
    // Example function for updating user role
    $result = updateUserRole($user_id, $new_role_id);
    
    if ($result) {
        // Log action
        logAction($_SESSION['user_id'], "Updated role of User ID $user_id to Role ID $new_role_id");
        
        // Redirect after successful update
        header('Location: admin_dashboard.php');
        exit();
    } else {
        // Handle update failure
        $error = "Failed to update user role.";
    }
}

// Handle deleting user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    
    // Example function for deleting user
    $result = deleteUser($user_id);
    
    if ($result) {
        // Log action
        logAction($_SESSION['user_id'], "Deleted user with User ID $user_id");
        
        // Redirect after successful deletion
        header('Location: admin_dashboard.php');
        exit();
    } else {
        // Handle deletion failure
        $error = "Failed to delete user.";
    }
}

// Fetch all users
$users = getAllUsers();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
</style>

    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        
        <!-- Link to Audit Log page -->
        <a href="audit_log.php" class="btn">View Audit Log</a>
        
        <!-- Display any errors -->
        <?php if (isset($error)) { echo "<p class='error'>Error: $error</p>"; } ?>
        
        <!-- Update User Role Form -->
        <h3>Update User Role</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="user_id">User ID:</label>
            <input type="number" id="user_id" name="user_id" required>
            <label for="new_role_id">New Role ID:</label>
            <input type="number" id="new_role_id" name="new_role_id" required>
            <button type="submit" name="update_role" class="btn">Update Role</button>
        </form>
        
        <!-- Delete User Form -->
        <h3>Delete User</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="user_id">User ID:</label>
            <input type="number" id="user_id" name="user_id" required>
            <button type="submit" name="delete_user" class="btn">Delete User</button>
        </form>
        
        <!-- Display Users -->
        <h3>Users</h3>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Role ID</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['role_id']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Additional sections or features can be added here -->
        
        <br>
        <p class="logout"><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
