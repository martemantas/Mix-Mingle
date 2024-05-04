<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);
    
    if($row['role'] != 3){
        if(isset($_POST['suggestion'])) {
            $suggestion = $_POST['suggestion'];

            $sql = "INSERT INTO suggestions (suggestion) VALUES (?)";
            
            if($stmt = $conn->prepare($sql)){
                $stmt->bind_param("s", $suggestion);
                $stmt->execute();

                echo '<script>alert("Success"); window.history.back();</script>';
                exit();
            }
            else{
                echo '<script>alert("Error"); window.history.back();</script>';
            }
        }
    }
}
if($row['role'] != 3){
    echo '<script>window.history.back();</script>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suggestions</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        h3 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
                $delete_id = $_POST["delete_id"];
                $sql = "DELETE FROM suggestions WHERE id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $delete_id);
                    if ($stmt->execute()) {
                        echo "<script>alert('Suggestion deleted successfully')</script>";
                    } else {
                        echo "<script>alert('Error deleting suggestion')</script>";
                    }
                    $stmt->close();
                } else {
                    echo "<script>alert('Error preparing statement')</script>";
                }
            }

            $sql = "SELECT * FROM suggestions";
            if ($result = $conn->query($sql)) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['suggestion'] . "</td>";
                    echo "<td>
                            <form method='post'>
                                <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
                                <button type='submit'>Remove</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</body>
</html>