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
            $response["message"] = "Thank you for leaving a review!";
        }
    }

    // Recalculate the average rating for the recipe
    $averageQuery = "SELECT COALESCE(AVG(value), 0) AS avgRating FROM rating WHERE fk_recipe_id = '$recipeId'";
    $averageResult = $conn->query($averageQuery);
    if ($averageResult->num_rows > 0) {
        $row = $averageResult->fetch_assoc();
        $avgRating = $row['avgRating'];

        // Update the recipe table with the new total rating
        $updateQuery = "UPDATE recipe SET total_rating = '$avgRating' WHERE recipe_id = '$recipeId'";
        if ($conn->query($updateQuery) === TRUE) {
            // Include the updated total rating in the response
            $response["updatedTotalRating"] = $avgRating;
        } else {
            // Handle error if the update fails
            $response["status"] = "error";
            $response["message"] = "Error updating total rating: " . $conn->error;
        }
    } else {
        // Handle case where no ratings are found for the recipe
        $response["status"] = "error";
        $response["message"] = "No ratings found for the recipe";
    }
} else {
    // Handle invalid request method
    $response["status"] = "error";
    $response["message"] = "Invalid request method";
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
