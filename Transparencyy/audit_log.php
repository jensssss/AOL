<?php
session_start();

// Check if user is logged in and is an administrator (role_id = 1 assuming admin role)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php'); // Redirect if not logged in as admin
    exit();
}

// Include function.php for database functions and configurations
require_once('function.php');

// Check if the form to log an action was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $userId = $_SESSION['user_id']; // Get current user ID from session
    $action = $_POST['action']; // Get action description from form

    // Call logAction function to record the action in the audit trail
    $logSuccess = logAction($userId, $action);

    // Optionally, you can handle success or failure here
    if ($logSuccess) {
        echo "Action logged successfully.";
    } else {
        echo "Failed to log action.";
    }
}

// Retrieve audit trail logs
$auditLogs = getAuditTrail();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Trail - Admin Only</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Audit Trail - Admin Only</h2>

        <!-- Display Audit Trail Logs -->
        <table>
            <thead>
                <tr>
                    <th>Audit ID</th>
                    <th>User ID</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($auditLogs as $log) {
                    echo "<tr>
                            <td>{$log['audit_id']}</td>
                            <td>{$log['user_id']}</td>
                            <td>{$log['action']}</td>
                            <td>{$log['action_date']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        
        <br>
        <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
    </div>
</body>
</html>
