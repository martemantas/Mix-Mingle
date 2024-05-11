<?php
    require 'config.php';

    if (!isset($_SESSION["id"])) {
        header("Location: login.php");
        exit();
    }
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recipe_id'])) {
        $recipeId = $_POST['recipe_id'];

        $findRecipe = mysqli_query($conn, "SELECT * FROM recipe WHERE recipe_id = '$recipeId'");
        if (mysqli_num_rows($findRecipe) > 0)
        {
            $recipe = mysqli_fetch_assoc($findRecipe);
            $recipe['picture'] = "recipes/{$recipeId}.{$recipe['picture']}";
            unlink("{$recipe['picture']}");
        }

        $deleteIngredients = "DELETE FROM ingredient WHERE fk_recipe_id = '$recipeId'";
        if ($conn->query($deleteIngredients) === FALSE) { 
            echo "Error deleting record: " . $conn->error;
        }

        $deleteInstructions = "DELETE FROM rating WHERE fk_recipe_id = '$recipeId'";
        if ($conn->query($deleteInstructions) === FALSE) { 
            echo "Error deleting record: " . $conn->error;
        }

        $deleteRatings = "DELETE FROM favorite_recipes WHERE fk_recipe_id = '$recipeId'";
        if ($conn->query($deleteRatings) === FALSE) { 
            echo "Error deleting record: " . $conn->error;
        }
        
        $deleteFavorites = "DELETE FROM favorite_recipes WHERE fk_recipe_id = '$recipeId'";
        if ($conn->query($deleteFavorites) === FALSE) { 
            echo "Error deleting record: " . $conn->error;
        }

        $deleteQuery = "DELETE from recipe where recipe_id = '$recipeId'";
        if ($conn->query($deleteQuery) === FALSE) { 
            echo "Error deleting record: " . $conn->error;
        }
    }
?>