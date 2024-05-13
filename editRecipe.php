<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);
    $user = $row["user_id"];   
}

// Store the previous page address
//$_SESSION['previous_page'] = $_POST['previous_page'];

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recipe_id'])) {
    $recipeID = $_POST['recipe_id'];

    $findName = "SELECT name FROM recipe WHERE recipe_id = '$recipeID'";
    $result = $conn->query($findName);

    $row = $result->fetch_assoc();
    $recipeName = $row['name'];
  

    $findDescription = "SELECT description FROM recipe WHERE recipe_id = '$recipeID'";
    $result = $conn->query($findDescription);

    $row = $result->fetch_assoc();
    $recipeDescription = $row['description'];

    
    $findCategory = "SELECT category FROM recipe WHERE recipe_id = '$recipeID'";
    $result = $conn->query($findCategory);

    $row = $result->fetch_assoc();
    $recipeCategory = $row['category'];


    // Get ingredients and instructions
    $getIngredients = "SELECT * FROM ingredient WHERE fk_recipe_id = '$recipeID'";
    $getInstructions = "SELECT * FROM instruction WHERE fk_recipe_id = '$recipeID'";

    $resultIngredients = $conn->query($getIngredients);
    $resultInstructions = $conn->query($getInstructions);

    // Check if the query was successful
    if ($resultIngredients && $resultInstructions) {
        // Fetch all rows into an associative array
        $ingredientArray = $resultIngredients->fetch_all(MYSQLI_ASSOC);

        $instructionArray = $resultInstructions->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "<script>alert(''Failed to fetch ingredients.'.')</script>";
    }
}

if(isset($_POST["editRecipe"])){
    $recipeID = $_POST["recipe_id"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $category = $_POST["category_id"];

    $ingredients = $_POST["product"];
    $quantity = $_POST["product_amount"];
    $instructions = $_POST['instruction'];

    //echo("<script>console.log('ingredients " . json_encode($ingredients) . "');</script>");
    //echo("<script>console.log('Quantity " . json_encode($quantity) . "');</script>");
    //echo("<script>console.log('instructions " . json_encode($instructions) . "');</script>");

    // Update name, description and category fields
    $updateName = "UPDATE recipe SET recipe.name = '$name' WHERE recipe_id = '$recipeID'";
    $updateDescription = "UPDATE recipe SET recipe.description = '$description' WHERE recipe_id = '$recipeID'";
    $updateCategory = "UPDATE recipe SET recipe.category = '$category' WHERE recipe_id = '$recipeID'";

    $result = $conn->query($updateName);
    $result1 = $conn->query($updateDescription);
    $result2 = $conn->query($updateCategory);

    // Check if the query was successful
    if (!$result || !$result1 || !$result2) {
        echo "<script>alert(''Failed to update.'.')</script>";
    } 

    // When ingredient number is the same, update the ingredients
    if(count($ingredients, 0) == count($ingredientArray, 0))
    {
        for($i = 0; $i < count($ingredients, 0); $i++)
        {
            $q = number_format($quantity[$i], 0);

            $updateIngredient = "UPDATE ingredient SET ingredient.amount = '$q', ingredient.fk_product_id = '$ingredients[$i]' WHERE ingredient_id = '{$ingredientArray[$i]['ingredient_id']}'";

            $result = $conn->query($updateIngredient);

            if(!$result) {
                echo "<script>alert('Failed to update ingredient.')</script>";
            }

            //echo("<script>console.log('ingredients " . json_encode($ingredients[$i]) . json_encode($q) . "');</script>");
        }
    } else if(count($ingredients, 0) < count($ingredientArray, 0)){
        for($i = 0; $i < count($ingredientArray, 0); $i++)
        {
            if($i < count($ingredients, 0))
            {
                $q = number_format($quantity[$i], 0);

                $updateIngredient = "UPDATE ingredient SET ingredient.amount = '$q', ingredient.fk_product_id = '$ingredients[$i]' WHERE ingredient_id = '{$ingredientArray[$i]['ingredient_id']}'";

                $resultUpdate = $conn->query($updateIngredient);

                if(!$resultUpdate) {
                    echo "<script>alert('Failed to update ingredient.')</script>";
                }
            }  else{
                $deleteIngredient = "DELETE FROM ingredient WHERE ingredient_id = '{$ingredientArray[$i]['ingredient_id']}'";

                $resultDelete = $conn->query($deleteIngredient);

                if(!$resultDelete) {
                    echo "<script>alert('Failed to delete ingredient.')</script>";
                }
            }
        }
    } else if(count($ingredients, 0) > count($ingredientArray, 0)){
        for($i = 0; $i < count($ingredients, 0); $i++)
        {
            $q = number_format($quantity[$i], 0);

            if($i < count($ingredientArray, 0))
            {
                $updateIngredient = "UPDATE ingredient SET ingredient.amount = '$q', ingredient.fk_product_id = '$ingredients[$i]' WHERE ingredient_id = '{$ingredientArray[$i]['ingredient_id']}'";

                $resultUpdate = $conn->query($updateIngredient);

                if(!$resultUpdate) {
                    echo "<script>alert('Failed to update ingredient.')</script>";
                }
            }  else {
                $insertIngredient = "INSERT INTO ingredient (amount, fk_product_id, fk_recipe_id) VALUES($q, $ingredients[$i], $recipeID)";

                $resultInsert = $conn->query($insertIngredient);

                if(!$resultInsert) {
                    echo "<script>alert('Failed to insert ingredient.')</script>";
                }
            }
        }
    }

    // When instruction number is the same, update the instructions
    if(count($instructions, 0) == count($instructionArray, 0))
    {
        for($i = 0; $i < count($instructions, 0); $i++)
        {
            $updateInstruction = "UPDATE instruction SET instruction.description = '$instructions[$i]' WHERE step_id = '{$instructionArray[$i]['step_id']}'";

            $result = $conn->query($updateInstruction);

            if(!$result) {
                echo "<script>alert('Failed to update instructions.')</script>";
            }

            //echo("<script>console.log('instructions " . json_encode($instructions[$i]) . json_encode($q) . "');</script>");
        }
    } else if(count($instructions, 0) < count($instructionArray, 0)){
        for($i = 0; $i < count($instructionArray, 0); $i++)
        {
            if($i < count($instructions, 0))
            {
                $updateInstruction = "UPDATE instruction SET instruction.description = '$instructions[$i]' WHERE step_id = '{$instructionArray[$i]['step_id']}'";

                $resultUpdate = $conn->query($updateInstruction);

                if(!$resultUpdate) {
                    echo "<script>alert('Failed to update instructions.')</script>";
                }
            }  else{
                $deleteInstruction = "DELETE FROM instruction WHERE step_id = '{$instructionArray[$i]['step_id']}'";

                $resultDelete = $conn->query($deleteInstruction);

                if(!$resultDelete) {
                    echo "<script>alert('Failed to delete instructions.')</script>";
                }
            }
        }
    } else if(count($instructions, 0) > count($instructionArray, 0)){
        for($i = 0; $i < count($instructions, 0); $i++)
        {
            if($i < count($instructionArray, 0))
            {
                $updateInstruction = "UPDATE instruction SET instruction.description = '$instructions[$i]' WHERE step_id = '{$instructionArray[$i]['step_id']}'";

                $resultUpdate = $conn->query($updateInstruction);

                if(!$resultUpdate) {
                    echo "<script>alert('Failed to update instructions.')</script>";
                }
            }  else {
                $insertInstruction = "INSERT INTO instruction (instruction.description, fk_recipe_id) VALUES ('$instructions[$i]', $recipeID)";

                $resultInsert = $conn->query($insertInstruction);

                if(!$resultInsert) {
                    echo "<script>alert('Failed to insert instructions.')</script>";
                }
            }
        }
    }

    // Update picture
    if(!empty($_FILES['file']['name'])) {
        // Delete the old picture
        $findFormat = "SELECT recipe.picture FROM recipe WHERE recipe_id = '$recipeID'";
        $result = $conn->query($findFormat);

        $row = $result->fetch_assoc();
        $pictureFormat = $row['picture'];

        $picture = "recipes/{$recipeID}.{$pictureFormat}";
        unlink("{$picture}");
        
        // Save newly uploaded picture in recipes folder
        $filename = basename($_FILES["file"]["name"]);
        $target_file = $filename;
        $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        $targetFolder = "recipes/";
        $targetFile = $targetFolder . $recipeID . "." . $fileType;

        // Save uploaded picture in recipes folder
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            // Update the format in the database
            $updatePicture = "UPDATE recipe SET picture = '$fileType' WHERE recipe_id = '$recipeID'";
            $resultUpdate = $conn->query($updatePicture);

            if (!$resultUpdate) {
                echo "<script>alert('Failed to update the picture in the database.')</script>";
            }     
        } else {
            echo "<script>alert('Failed to save the picture.')</script>";
        }
    }

    echo "<script>alert('Recipe was edited successfully.')</script>";
    header("Location: " . 'home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="newRecipe.css">
    <title>Edit Recipe</title>
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
                echo '<li><a href="#" onclick="goBack()">Back</a></li>';
            echo '</ul>';
        echo '</nav>';
    ?>
    <div class="container">
        <h1 class="title">Edit recipe</h1>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="field">
                <input type="text" name="name" placeholder="Recipe name"  value="<?php echo $recipeName; ?>" required>
            </div>
            <div class="field">
                <input type="text" name="description" placeholder="Recipe description" value="<?php echo $recipeDescription; ?>" required>
            </div>
            <input type="hidden" name="recipe_id" value="<?php echo $recipeID; ?>">
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
                        <?php foreach ($ingredientArray as $ingredient) { ?>
                            <div class="product-input" style="display: flex; padding-bottom: 5px;">
                                <input type="number" name="product_amount[]" placeholder="Amount" style="max-width: 30%; margin-right: 5px; padding-left: 5px;  border-radius: 5px" value="<?php echo $ingredient['amount']; ?>">
                                <select name="product[]" required>
                                    <?php foreach ($productArray as $product) { ?>
                                        <option value="<?php echo $product['id']; ?>" <?php if ($product['id'] == $ingredient['fk_product_id']) echo "selected"; ?>>
                                            <?php echo $product['name'] . ", " . $product['unit']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>

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
                        <?php foreach ($instructionArray as $instruction) { ?>
                            <div class="instruction-input" style="display: flex; padding-bottom: 5px; width: 100%">
                                <textarea type="text" name="instruction[]" placeholder="Instruction" maxlength="200" style="width: 100%; height: 4em"><?php echo $instruction['description']; ?></textarea>
                            </div>
                        <?php } ?>

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
                <label style="margin-left:10px;" for="file">Upload new recipe photo to change:</label>
                    <input type="file" name="file" id="file" accept="image/png, image/jpeg, image/jpg">
            </div>

            <div class="field">
                <select name="category_id">
                    <?php
                        require 'config.php';
                        $categories = mysqli_query($conn, "SELECT * FROM categories");
                        while($c = mysqli_fetch_array($categories)){
                            echo '<option value="'.$c['category_id'].'"';
                            
                            if($c['category_id'] == $recipeCategory) {
                                echo ' selected="selected"';
                            }
                            echo '>'.$c['name'].'</option>';
                        }
                    ?>
                </select>
            </div>

            <div class="field btn">
                <div class="btn-layer"></div>
                <button type="submit" name="editRecipe">Edit Recipe</button>
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

        // Check if a file is uploaded
        if (filePath !== "") {
            var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
            
            // Check if the file type is allowed
            if (!allowedExtensions.exec(filePath)) {
                alert('Incorrect file type. Please upload only PNG, JPEG or JPG type files.');
                fileInput.value = '';
                return false;
            }
        }

        return true;
    }

    function goBack() {
        window.history.back();
    }
</script>
</html>
