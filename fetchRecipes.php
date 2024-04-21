<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["category"]) && isset($_GET["userId"])) {
        $categoryId = $_GET["category"];
        $userId = $_GET["userId"];

        $stmt = $conn->prepare("SELECT * FROM recipe r JOIN favorite_recipes fr ON r.recipe_id = fr.fk_recipe_id WHERE r.category = ? AND fr.fk_user_id = ?");
        $stmt->bind_param("ii", $categoryId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $row;
        }

        echo json_encode($recipes);
    }
}
?>
