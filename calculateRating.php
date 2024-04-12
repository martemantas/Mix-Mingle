<?php
require 'config.php';

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filledStars = $_POST["filledStars"];
    $recipeId = $_POST["recipeId"];
    $userId = $_POST["userId"];

    $checkQuery = "SELECT * FROM rating WHERE fk_recipe_id = $recipeId AND fk_user_id = $userId";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $updateQuery = "UPDATE rating SET value = $filledStars WHERE fk_recipe_id = $recipeId AND fk_user_id = $userId";
        $result = mysqli_query($conn, $updateQuery);
        if (!$result) {
            $response["status"] = "error";
            $response["message"] = "Failed to update rating: " . mysqli_error($conn);
        } else {
            $response["status"] = "success";
            $response["message"] = "Thank you for your changed opinion!";
        }
    } else {
        $insertQuery = "INSERT INTO rating (value, fk_recipe_id, fk_user_id) VALUES ($filledStars, $recipeId, $userId)";
        $result = mysqli_query($conn, $insertQuery);
        if (!$result) {
            $response["status"] = "error";
            $response["message"] = "Failed to insert rating: " . mysqli_error($conn);
        } else {
            $response["status"] = "success";
            $response["message"] = "Thank you for your opinion!";
        }
    }
} else {
    // Handle invalid request method
    $response["status"] = "error";
    $response["message"] = "Invalid request method";
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
