<?php
require 'config.php';

$query = "SELECT * FROM recipe ORDER BY RAND() LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Failed to fetch random recipe: " . mysqli_error($conn));
}

$randomRecipe = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode($randomRecipe);
?>
