<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);
    $user = $row["user_id"];   
}

// Get products from the database
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

if(isset($_POST["add"])){
    $ingredientName = $_POST['ingredientName'];
    $unitId = $_POST['unitId'];

    // Validate form data
    if (empty($ingredientName) || empty($unitId)) {
        echo "<script>alert('Please fill in all the fields.')</script>";
    }

    // Insert new ingredient into the database
    $insertQuery = "INSERT INTO `product` (`name`, `unit`) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("si", $ingredientName, $unitId);

    try {
        if ($stmt->execute()) {
            echo "<script>alert('New ingredient added successfully')</script>";

            // Fetch the updated ingredient list
            $result = $conn->query($query);
            $ingredientArray = $result->fetch_all(MYSQLI_ASSOC);
        }
    } catch (mysqli_sql_exception $e) {
        echo "<script>alert('Ingredient was not added. Duplicate name.')</script>";
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
    <title>New ingredient</title>
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
        <h1 class="title">New ingredient</h1>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">

            <?php
            if (isset($productArray) && count($productArray) > 0) {
            ?>
                
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($productArray as $ingredient) {
                        ?>
                        <tr>
                            <td><?php echo $ingredient["name"]; ?></td>
                            <td><?php echo $ingredient["unit"]; ?></td>
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

            <div style="display: block; padding-top: 20px">
                <label for="ingredientName">Name:</label>
                <input type="text" id="ingredientName" name="ingredientName" onkeydown="return /[a-z]/i.test(event.key)"  required>

                <label for="unitId">Unit:</label>
                <select id="unitId" name="unitId" required>
                    <?php
                        foreach ($unitsArray as $unit) {
                        ?>
                            <option value="<?php echo $unit['unit_id']; ?>">
                            <?php echo $unit['name']; ?></option>
                        <?php
                        }
                    ?>
                </select>
            </div>
            <div class="field btn">
                <div class="btn-layer"></div>
                <button type="submit" name="add">Add</button>
            </div>
        </form>
    </div>
    
    <footer style="position:fixed; bottom:0;">
        <p style="border-top: none;">Tai nera komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>
    </footer>
</body>
</html>
