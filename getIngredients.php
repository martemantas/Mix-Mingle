<?php
require 'config.php';

if (isset($_GET['recipe_id'])) {
    $recipe_id = mysqli_real_escape_string($conn, $_GET['recipe_id']);

    $sql = "SELECT i.AMOUNT, u.name AS unit_name, p.name AS product_name 
            FROM `ingredient` i, `product` p, `units` u 
            WHERE i.fk_recipe_id = $recipe_id 
            AND p.product_id = i.fk_product_id 
            AND u.unit_id = p.unit 
            ORDER BY ingredient_id";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $ingredients = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $ingredients[] = $row;
        }

        echo json_encode($ingredients);
    } else {
        echo json_encode(array());
    }
}
?>
