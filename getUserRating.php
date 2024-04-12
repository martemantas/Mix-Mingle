<?php
require 'config.php';

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipeId = $_POST["recipeId"];
    $userId = $_POST["userId"];
    if ($recipeId !== null && $userId !== null) {
        // Prepare SQL statement to retrieve user's rating for the recipe
        $sql = "SELECT value FROM rating WHERE fk_recipe_id = '$recipeId' AND fk_user_id = '$userId'";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Rating found, return the rating value
            $row = $result->fetch_assoc();
            $rating = $row['value'];
            $response["status"] = "success";
            $response["rating"] = $rating;
        } else {
            // Rating not found, return 0
            $response["status"] = "success";
            $response["rating"] = 0;
        }
    } else {
        // Handle missing parameters
        $response["status"] = "error";
        $response["message"] = "Missing parameters";
    }
} else {
    // Handle invalid request method
    $response["status"] = "error";
    $response["message"] = "Invalid request method";
}

echo json_encode($response);
?>
