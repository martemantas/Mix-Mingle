<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);

    if($row['role'] != 3){
        header("Location: home.php");
    }
}
else{
    header("Location: home.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<script src="home.js"></script>
<body>
<?php
    echo '<nav>';
        echo '<div class="hamburger">';
            echo '<span class="line"></span>';
            echo '<span class="line"></span>';
            echo '<span class="line"></span>';
        echo '</div>';
        echo '<ul>';
            echo '<li><a href="home.php">Home</a></li>';
            echo '<li><a href="newRecipe.php">New recipe</a></li>';
        echo '</ul>';
        if(empty($_SESSION["id"])){
            echo '<li class="login"><a href="login.php">Login</a></li>';
        }
        else{
            echo '<li class="login"><a href="logout.php">Logout</a></li>';
        }
    echo '</nav>';
?>

<?php
    $sql = "SELECT * FROM users where role != 3";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
            <th>Username</th>
            <th>Role</th>
        </tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>".$row["username"]."</td>
                <td>
                    <form method='post' action='updateRole.php'>
                        <input type='hidden' name='id' value='".$row["user_id"]."'>
                        <select name='role' onchange='this.form.submit()'>
                            <option value='1' ".($row["role"] == 1 ? "selected" : "").">User</option>
                            <option value='2' ".($row["role"] == 2 ? "selected" : "").">Editor</option>
                            <option value='3' ".($row["role"] == 3 ? "selected" : "").">Admin</option>
                        </select>
                    </form>
                </td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }
    $conn->close();
?>
</body>
</html>