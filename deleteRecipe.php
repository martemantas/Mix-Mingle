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

        $deleteQuery = "DELETE from recipe where recipe_id = '$recipeId'";
        if ($conn->query($deleteQuery) === FALSE) { 
            echo "Error deleting record: " . $conn->error;
        }
    }
?>