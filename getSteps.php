<?php
require 'config.php';

if (isset($_GET['recipe_id'])) {
    $recipeId = mysqli_real_escape_string($conn, $_GET['recipe_id']);

    $sql = "SELECT description FROM `instruction` WHERE fk_recipe_id = $recipeId ORDER BY step_id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $recipeStep = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $recipeStep[] = $row;
        }

        echo json_encode($recipeStep);
    } else {
        echo json_encode(array());
    }
}
?>
