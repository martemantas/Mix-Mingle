<?php
require 'config.php';

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipeId = $_POST["recipeId"];
    $userId = $_POST["userId"];
    if ($recipeId !== null && $userId !== null) {
        $sql = "SELECT value FROM rating WHERE fk_recipe_id = '$recipeId' AND fk_user_id = '$userId'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $rating = $row['value'];
            $response["status"] = "success";
            $response["rating"] = $rating;
        } else {
            $response["status"] = "success";
            $response["rating"] = 0;
        }
    } else {
        $response["status"] = "error";
        $response["message"] = "Missing parameters";
    }
} else {
    $response["status"] = "error";
    $response["message"] = "Invalid request method";
}

echo json_encode($response);
?>