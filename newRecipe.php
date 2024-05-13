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

// Check if the query was successful
if ($result && $result1) {
    // Fetch all rows into an associative array
    $productArray = $result->fetch_all(MYSQLI_ASSOC);

    $unitsArray = $result1->fetch_all(MYSQLI_ASSOC);
} else {
    // Handle the error
    echo "Error: " . $conn->error;
}

if(isset($_POST["add"])){
    $name = $_POST["name"];
    $description = $_POST["description"];
    $category = $_POST["category_id"];

    $ingredients = $_POST["product"];
    $quantity = $_POST["product_amount"];
    $instructions = $_POST['instruction'];
   
    if (empty($name) || empty($description)) {
        echo "<script>alert('Please fill in all the fields.')</script>";
    }

    $filename = basename($_FILES["file"]["name"]);
    $target_file = $filename;
    $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Insert new recipe into the database
    $insertRecipe = "INSERT INTO `recipe` (`name`, `description`, `picture`, `creator`, `category`) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertRecipe);
    $stmt->bind_param("ssssi", $name, $description, $fileType, $user, $category);

    if ($stmt->execute()) {
        $recipeID = $stmt->insert_id;

        // Save uploaded picture in recipes folder
        $targetFolder = "recipes/";
        $targetFile = $targetFolder . $recipeID . "." . $fileType;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            echo "<script>alert('Recipe added.')</script>";
        } else {
            echo "<script>alert('Failed to save the picture.')</script>";
            $queryDelete = "DELETE FROM `recipe` WHERE `recipe`.`recipe_id` ='$recipeID'";
            $result = $conn->query($queryDelete);
        }
    } else {
        echo "<script>alert('Recipe already exists.')</script>";
    }

    $insertIngredients = "INSERT INTO `ingredient` (`amount`, `fk_product_id`, `fk_recipe_id`) VALUES (?, ?, ?)";
    $insertInstructions = "INSERT INTO `instruction` (`description`, `fk_recipe_id`) VALUES (?, ?)";

    $count = 0;
    $countInstruction = 0;

    foreach($quantity as $q)
    {  
        $q = trim($q);
        $q = floatval($q);
        $q = round($q, 1);

        $stmt = $conn->prepare($insertIngredients);
        $stmt->bind_param("iii", $q, $ingredients[$count], $recipeID);

        try {
            if ($stmt->execute()) {
                // Ingredient was inserted successfully
            }
        } catch (mysqli_sql_exception $e) {
            echo "<script>alert('One or more ingredients were not inserted due to duplicates.')</script>";
        }

        $count = $count + 1;
    }

    foreach($instructions as $ins)
    {
        $stmt = $conn->prepare($insertInstructions);
        $stmt->bind_param("si", $ins, $recipeID);

        try {
            if ($stmt->execute()) {
                // Ingredient was inserted successfully
            }
        } catch (mysqli_sql_exception $e) {
            echo "<script>alert('One or more instructions were not inserted.')</script>";
        }

        $countInstruction = $countInstruction + 1;
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
    <link rel="stylesheet" href="newRecipe.css">
    <title>New Recipe</title>
</head>
<script src="home.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="newRecipe.js"></script>
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
        <h1 class="title">New recipe</h1>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="field">
                <input type="text" name="name" placeholder="Recipe name" required>
            </div>
            <div class="field">
                <input type="text" name="description" placeholder="Recipe description" required>
            </div>

            <div class="gap" style="border-top: 2px solid #000; border-bottom: 2px solid #000; height:fit-content">
                <div class="tab">
                    <!-- Tabs -->
                    <ul class="tabs">
                        <li><a href="#">Ingredients</a></li>
                        <li><a href="#">Instructions</a></li>
                    </ul>

                    <!-- Ingredients -->
                    <div class="tab_content" id="filledTabs">
                        <div class="tabs_item" style="padding-bottom: 5px">
                            <!-- Product input fields -->
                            <div class="product-input" style="display: flex; padding-bottom: 5px;">
                                <input type="number" name="product_amount[]" placeholder="Amount" style="max-width: 30%; margin-right: 5px; padding-left: 5px;  border-radius: 5px">
                                <select name="product[]" required>
                                    <?php foreach ($productArray as $product) { ?>
                                        <option value="<?php echo $product['id']; ?>">
                                            <?php echo $product['name'] . ", " . $product['unit']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div style="display:flex;">
                                <div class="field btn">
                                    <div class="btn-layer"></div>
                                    <button type="button" id="addProduct">Add</button>     
                                </div>
                                <div class="field btn">
                                    <div class="btn-layer"></div>
                                    <button type="button" id="removeProduct" style = "background-color: red;">Remove</button>
                                </div>     
                            </div>         
                        </div>

                        <div class="tabs_item" style="padding-bottom: 5px">
                            <!-- Instruction input fields -->
                            <div class="instruction-input" style="display: flex; padding-bottom: 5px; width: 100%">
                                <textarea type="text" name="instruction[]" placeholder="Instruction" maxlength="200" style="width: 100%; height: 4em"></textarea>
                            </div>

                            <div style="display:flex;">
                                <div class="field btn">
                                    <div class="btn-layer"></div>
                                    <button type="button" class="addInstruction">Add</button>     
                                </div>
                                <div class="field btn">
                                    <div class="btn-layer"></div>
                                    <button type="button" class="removeInstruction" style="background-color: red;">Remove</button>
                                </div>     
                            </div>         
                        </div>
                    </div>
                </div>
            </div>

            <div class="gap">
                <label style="margin-left:10px;" for="file">Upload recipe photo:</label>
                    <input type="file" name="file" id="file" accept="image/png, image/jpeg, image/jpg" required>
            </div>

            <div class="field">
                <select name="category_id">
                    <?php
                        require 'config.php';
                        $categories = mysqli_query($conn, "SELECT * FROM categories");
                        while($c = mysqli_fetch_array($categories)){
                    ?>
                    <option value="<?php echo $c['category_id']?>"><?php echo $c['name']?></option>
                    <?php } ?>
                </select>
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
<script>
    // Adding and removing product fields
    $(document).ready(function(){
        $("#addProduct").click(function(){
            var lastAmountField = $(".product-input:last").find("input[name='product_amount[]']");
            var lastAmountValue = lastAmountField.val();
            
            // Check if the last added amount field is empty
            if (lastAmountValue.trim() === "") {
                alert("Please fill the amount before adding another ingredient.");
                return;
            }
            
            var productInputs = $(".product-input").length;
            if(productInputs < 6) {
                var newProductInput = `
                        <div class="product-input" style="display: flex; padding-bottom: 5px;">
                            <input type="number" name="product_amount[]" placeholder="Amount" style="max-width: 30%; margin-right: 5px; padding-left: 5px;  border-radius: 5px">
                            <select name="product[]" required>
                                <?php foreach ($productArray as $product) { ?>
                                    <option value="<?php echo $product['id']; ?>">
                                        <?php echo $product['name'] . ", " . $product['unit']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>`;
                    $(newProductInput).insertAfter(".product-input:last");
            } else {
                alert("You cannot add more than 6 ingredients.");
            }
        });
        
        $("#removeProduct").click(function(){
            var productInputs = $(".product-input").length;
            if(productInputs > 1) {
                $(".product-input:last").remove();
            } else {
                alert("You cannot delete the only product field.");
            }
        });

        $(".addInstruction").click(function(){
            var lastInstructionField = $(".instruction-input:last").find("textarea[name='instruction[]']");
            var lastInstructionValue = lastInstructionField.val();
            
            // Check if the last added instruction field is empty
            if (lastInstructionValue.trim() === "") {
                alert("Please fill the instruction before adding another one.");
                return;
            }
            
            var instructionInputs = $(".instruction-input").length;
            if(instructionInputs < 6) {
                var newInstructionInput = `
                        <div class="instruction-input" style="display: flex; padding-bottom: 5px;">
                            <textarea type="text" name="instruction[]" placeholder="Instruction" maxlength="200" style="width: 100%; height: 4em"></textarea>
                        </div>`;
                    $(newInstructionInput).insertAfter(".instruction-input:last");
            } else {
                alert("You cannot add more than 6 instructions.");
            }
        });
        
        $(".removeInstruction").click(function(){
            var instructionInputs = $(".instruction-input").length;
            if(instructionInputs > 1) {
                $(".instruction-input:last").remove();
            } else {
                alert("You cannot delete the only instruction field.");
            }
        });
    });

    // Validation
    function validateForm() {
        // Check recipe name
        var recipeName = $("input[name='name']").val().trim();
        if (recipeName === "") {
            alert("Please fill in the recipe name.");
            return false;
        }
        
        // Check each ingredient validity and duplicates
        var ingredientsValid = true;
        var noDuplicates = true; 
        var selectedIngredients = [];
        $(".product-input").each(function() {
            var amount = parseFloat($(this).find("input[name='product_amount[]']").val().trim());
            var product = $(this).find("select[name='product[]']").val().trim();
            if (amount === "" || isNaN(amount) || amount <= 0.1 || amount >= 10000 || product === "") {
                ingredientsValid = false;
                return false;
            }
            // Check for duplicate ingredients
            if (selectedIngredients.includes(product)) {
                noDuplicates = false;
                return false;
            }
            selectedIngredients.push(product);
        });
        if (!ingredientsValid) {
            alert("Please fill in all ingredient amounts. Amount must be between 0.1 and 10000.");
            return false;
        }
        if(!noDuplicates)
        {
            alert("Duplicate ingredients are not allowed.");
            return false;
        }
        
        // Check each instruction
        var instructionsValid = true;
        $(".instruction-input").each(function() {
            var instruction = $(this).find("textarea[name='instruction[]']").val().trim();
            if (instruction === "") {
                instructionsValid = false;
                return false;
            }
        });
        if (!instructionsValid) {
            alert("Please fill in all instruction fields.");
            return false;
        }

        // Check file upload
        var fileInput = document.getElementById('file');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

        if (!allowedExtensions.exec(filePath)) {
            alert('Incorrect file type. Please upload only PNG, JPEG or JPG type files.');
            fileInput.value = '';
            return false;
        }

        return true;
    }

</script>
</html>
