<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff9db;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .form{
            display: flex;
            flex-direction: row;
        }
        button{
            margin-left: 5px;
        }
    </style>
</head>
<body>
<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user ID and role are set
    if (isset($_POST['id']) && isset($_POST['role'])) {
        // Sanitize user inputs to prevent SQL injection
        $user_id = mysqli_real_escape_string($conn, $_POST['id']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        
        // Fetch user details for confirmation message
        $user_sql = "SELECT * FROM users WHERE user_id = '$user_id'";
        $user_result = mysqli_query($conn, $user_sql);
        $user_row = mysqli_fetch_assoc($user_result);
        
        // Fetch role name from roles table
        $role_sql = "SELECT name FROM roles WHERE role_id = '$role'";
        $role_result = mysqli_query($conn, $role_sql);
        $role_row = mysqli_fetch_assoc($role_result);
        $role_name = $role_row['name'];
        
        // Check if confirmation is submitted
        if (isset($_POST['confirm'])) {
            // Update user's role in the database
            $update_sql = "UPDATE users SET role = '$role' WHERE user_id = '$user_id'";
            if (mysqli_query($conn, $update_sql)) {
                // If update is successful, redirect back to admin.php
                header("Location: admin.php");
                exit();
            } else {
                // If there is an error with the SQL query, display an error message
                echo "Error updating record: " . mysqli_error($conn);
            }
        } else {
            echo "<div class='container'>";
                echo "<h2>Confirmation</h2>";
                echo "<p>Are you sure you want to update the role of user '".$user_row['username']."' to '".$role_name."'?</p>";
                echo "<div class='form'>";
                    echo "<form method='post'>";
                        echo "<input type='hidden' name='id' value='".$user_id."'>";
                        echo "<input type='hidden' name='role' value='".$role."'>";
                        echo "<input type='submit' name='confirm' value='Confirm'>";
                    echo "</form>";
                    echo "<a href='admin.php'><button>Cancel</button></a>";
                echo "</div>";
            echo "</div>";
        }
    } else {
        // If user ID or role is not set, display an error message
        echo "Error: User ID or role not set.";
    }
} else {
    // If request method is not POST, redirect back to admin.php
    header("Location: admin.php");
    exit();
}
?>

</body>
</html>
