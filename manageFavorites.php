<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add"])) {
        $recipeId = $_POST["add"];
        $userId = $_POST["userId"];
        $stmt = $conn->prepare("INSERT INTO favorite_recipes (fk_recipe_id, fk_user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $recipeId, $userId);
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Recipe added to favorites."));
        } else {
            echo json_encode(array("error" => "Failed to add recipe to favorites."));
        }
    } elseif (isset($_POST["remove"])) {
        $recipeId = $_POST["remove"];
        $userId = $_SESSION["id"];
        $stmt = $conn->prepare("DELETE FROM favorite_recipes WHERE fk_recipe_id = ? AND fk_user_id = ?");
        $stmt->bind_param("ii", $recipeId, $userId);
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Recipe removed from favorites."));
        } else {
            echo json_encode(array("error" => "Failed to remove recipe from favorites."));
        }
    } elseif (isset($_POST["check"])) {
        $recipeId = $_POST["check"];
        $userId = $_SESSION["id"];
        $sql = "SELECT COUNT(*) AS favorite_count FROM favorite_recipes WHERE fk_recipe_id = ? AND fk_user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $recipeId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['favorite_count'] > 0) {
            echo json_encode(array("isFavorite" => true));
        } else {
            echo json_encode(array("isFavorite" => false));
        }
    }
}
?>
