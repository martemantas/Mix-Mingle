<?php
require 'config.php';
if (!empty($_SESSION["id"])) {
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);

    if ($row['role'] != 3) {
        header("Location: home.php");
        exit();
    }

    // Get product and unit arrays
    $query =    "SELECT product.name as name, units.name as unit, product.product_id as id
            FROM product
            INNER JOIN units ON product.unit = units.unit_id
            ORDER BY name";

    $query1 = "SELECT * FROM units";

    $result = $conn->query($query);

    $result1 = $conn->query($query1);

    if ($result && $result1) {
        $productArray = $result->fetch_all(MYSQLI_ASSOC);

        $unitsArray = $result1->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "Error: " . $conn->error;
    }

    // Confirmation for changing user role
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['role'])) {
        $user_id = mysqli_real_escape_string($conn, $_POST['id']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);

        $user_sql = "SELECT * FROM users WHERE user_id = '$user_id'";
        $user_result = mysqli_query($conn, $user_sql);
        $user_row = mysqli_fetch_assoc($user_result);

        $role_sql = "SELECT name FROM roles WHERE role_id = '$role'";
        $role_result = mysqli_query($conn, $role_sql);
        $role_row = mysqli_fetch_assoc($role_result);
        $role_name = $role_row['name'];

        if (isset($_POST['confirm'])) {
            $update_sql = "UPDATE users SET role = '$role' WHERE user_id = '$user_id'";
            if (mysqli_query($conn, $update_sql)) {
                header("Location: admin.php");
                exit();
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }
        } else {
            echo "<div class='container'>";
                echo "<h2>Confirmation</h2>";
                    echo "<p>Are you sure you want to update the role of user '" . $user_row['username'] . "' to '" . $role_name . "'?</p>";
                echo "<div class='form' style='padding-top: 10px;'>";
                echo "<form method='post'>";
                    echo "<input type='hidden' name='id' value='" . $user_id . "'>";
                    echo "<input type='hidden' name='role' value='" . $role . "'>";
                    echo "<input type='submit' name='confirm' value='Confirm'>";
                echo "</form>";
                    echo "<a href='admin.php' style='text-decoration: none;'><button style='margin-left: 3px;'>Cancel</button></a>";
                echo "</div>";
            echo "</div>";
        }
    }

    // Deleting
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] == "delete") {
        if (isset($_GET["type"]) && isset($_GET["id"])) {
            $type = $_GET["type"];
            $id = $_GET["id"];

            $deleteQuery = "";

            switch ($type) {
                case "user":
                    $deleteQuery = "DELETE FROM `users` WHERE `user_id` = ?";
                    break;
                case "ingredient":
                    $deleteQuery = "DELETE FROM `product` WHERE `product_id` = ?";
                    break;
                case "unit":
                    $deleteQuery = "DELETE FROM `units` WHERE `unit_id` = ?";
                    break;
            }

            if (!empty($deleteQuery)) {
                $stmt = $conn->prepare($deleteQuery);
                $stmt->bind_param("i", $id);

                try {
                    if ($stmt->execute()) {
                        echo "<script>alert('Deletion successful')</script>";
                        header("Location: admin.php");
                        exit();
                    }
                } catch (mysqli_sql_exception $e) {
                    echo "<script>alert('Ingredient or unit already used in a recipe. Cannot delete.')</script>";
                }
            }
        }
    }
} else {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
    <title>Admin panel</title>
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="home.js"></script>

<body>
    <nav>
        <div class="hamburger">
            <span class="line"></span>
            <span class="line"></span>
            <span class="line"></span>
        </div>
        <ul>
            <li><a href="home.php">Home</a></li>
        </ul>
        <?php
        if (empty($_SESSION["id"])) {
            echo '<li class="login"><a href="login.php">Login</a></li>';
        } else {
            echo '<li class="login"><a href="logout.php">Logout</a></li>';
        }
        ?>
    </nav>

    <!-- User table -->
    <?php
    $sql = "SELECT * FROM users where role != 3";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
            <th>Username</th>
            <th>Role</th>
            <th></th>
        </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>" . $row["username"] . "</td>
                <td>
                    <form method='post' action='admin.php'>
                        <input type='hidden' name='id' value='" . $row["user_id"] . "'>";
                        echo "<select name='role' onchange='this.form.submit()'>
                                <option value='1' " . ($row["role"] == 1 ? "selected" : "") . ">User</option>
                                <option value='2' " . ($row["role"] == 2 ? "selected" : "") . ">Editor</option>
                                <option value='3' " . ($row["role"] == 3 ? "selected" : "") . ">Admin</option>
                              </select>
                    </form>
                </td>
                <td>
                    <a class='delete-link' href='#' onclick='confirmDelete(\"user\", " . $row['user_id'] . ")' style='color: red;'>Delete</a>
                </td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "No non-admin users found.";
    }
    $conn->close();
    ?>

    <!-- Ingredient table -->
    <?php
        if (isset($productArray) && count($productArray) > 0) {
        ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Unit</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($productArray as $ingredient) {
                    ?>
                    <tr>
                        <td><?php echo $ingredient["name"]; ?></td>
                        <td><?php echo $ingredient["unit"]; ?></td>
                        <td><a class="delete-link" href="#" onclick="confirmDelete('ingredient', <?php echo $ingredient['id']; ?>)" style="color: red;">Delete</a></td>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        <?php
        } else {
            echo "No ingredients";
        }
    ?>

    <!-- Unit table -->
    <?php
        if (isset($unitsArray) && count($unitsArray) > 0) {
        ?>
            
            <table style="margin-bottom: 5%">
                <thead>
                    <tr>
                        <th>Unit</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($unitsArray as $unit) {
                    ?>
                    <tr>
                        <td><?php echo $unit["name"]; ?></td>
                        <td><a class="delete-link" href="#" onclick="confirmDelete('unit', <?php echo $unit['unit_id']; ?>)"  style="color: red;">Delete</a></td>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        <?php
        } else {
            echo "No units found.";
        }
    ?>

    <footer>
        <p style="border-top: none;">Tai nėra komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>
    </footer>

    <script>
        function confirmDelete(type, id) {
            var confirmed = window.confirm("Are you sure you want to delete this entry?");
            if (confirmed) {
                window.location.href = 'admin.php?action=delete&type=' + type + '&id=' + id;
            }
            return false;
        }
    </script>
</body>
</html>
