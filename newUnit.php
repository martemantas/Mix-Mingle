<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);
    $user = $row["user_id"];   
}

// Get units from the database
$query = "SELECT * FROM units";

$result = $conn->query($query);

if ($result) {
    $unitsArray = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error: " . $conn->error;
}

if(isset($_POST["add"])){
    $unitName = $_POST['unitName'];

    // Validate form data
    if (empty($unitName)) {
        echo "<script>alert('Please fill in all the fields.')</script>";
    }

    // Insert new ingredient into the database
    $insertQuery = "INSERT INTO `units` (`name`) VALUES (?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("s", $unitName);

    try {
        if ($stmt->execute()) {
            echo "<script>alert('New unit was added successfully')</script>";

            echo "<script>window.location.href = '$_SERVER[PHP_SELF]';</script>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "<script>alert('Unit was not added. Duplicate name.')</script>";
    }

    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="newIngredient.css">
    <title>New unit</title>
</head>
<script src="home.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
            echo '</ul>';
        echo '</nav>';
    ?>
    <div class="container">
        <h1 class="title">New unit</h1>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">

            <?php
            if (isset($unitsArray) && count($unitsArray) > 0) {
            ?>
                
                <table>
                    <thead>
                        <tr>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($unitsArray as $unit) {
                        ?>
                        <tr>
                            <td><?php echo $unit["name"]; ?></td>
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

            <div style="display: block; padding-top: 20px">
                <label for="unitName">Name:</label>
                <input type="text" id="unitName" name="unitName" onkeydown="return /[a-z]/i.test(event.key)"  required>
            </div>
            <div class="field btn">
                <div class="btn-layer"></div>
                <button type="submit" name="add">Add</button>
            </div>
        </form>
    </div>
    
    <footer style="position:fixed; bottom:0;">
        <p style="border-top: none;">Tai nėra komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>
    </footer>
</body>
</html>
