<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["category"]) && isset($_GET["userId"])) {
        $categoryId = $_GET["category"];
        $userId = $_GET["userId"];

        if($categoryId != 0){
            $stmt = $conn->prepare("SELECT * FROM recipe r JOIN favorite_recipes fr ON r.recipe_id = fr.fk_recipe_id WHERE r.category = $categoryId AND fr.fk_user_id = $userId order by r.name");
        }
        else{
            $stmt = $conn->prepare("SELECT * FROM recipe r JOIN favorite_recipes fr ON r.recipe_id = fr.fk_recipe_id WHERE fr.fk_user_id = $userId order by r.name");
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $row;
        }

        echo json_encode($recipes);
    }
}