<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["query"])) {
        $searchQuery = $_GET["query"];
        $creatorQuery = $_GET["creatorQuery"];
        $category = $_GET["category"];
        $ingredientList = json_decode($_GET["ingredients"], true);

        if (count($ingredientList) != 0) {
            $ingredientListQuoted = array_map(function($item) {
                return "'" . $item . "'";
            }, $ingredientList);
            $ingredientIds = implode(",", $ingredientListQuoted);
        }
        
        if (count($ingredientList) > 0) {
            $sql = "SELECT r.*
            FROM recipe r";

            //  ingredients and creator
            if ($creatorQuery != '') {

                $sql .= " WHERE EXISTS (
                    SELECT 1
                    FROM ingredient i
                    JOIN product p ON i.fk_product_id = p.product_id
                    WHERE i.fk_recipe_id = r.recipe_id
                    AND p.name IN (" . "$ingredientIds" . ")
                            GROUP BY i.fk_recipe_id
                            HAVING COUNT(DISTINCT p.name) = " . count($ingredientList) . ")
                            AND (
                                SELECT 1
                                FROM users u
                                WHERE u.user_id = r.creator
                                AND u.username LIKE " . "'%$creatorQuery%'" . ")";
            } else {
                // just ingredients
                $sql .= " WHERE EXISTS (
                    SELECT 1
                    FROM ingredient i
                    JOIN product p ON i.fk_product_id = p.product_id
                    WHERE i.fk_recipe_id = r.recipe_id
                    AND p.name IN (" . "$ingredientIds" . ")
                            GROUP BY i.fk_recipe_id
                            HAVING COUNT(DISTINCT p.name) = " . count($ingredientList) . ")";
            }

            if ($category != 0) {
                $sql .= " AND r.category = $category";
            }

            if ($searchQuery != '') {
                $sql .= " AND r.name like " . "'%$searchQuery%'";
            }
        // no ingredients FOR SEARCH PAGE
        } else if($creatorQuery != ''){
            $sql = "SELECT * FROM recipe r WHERE name LIKE '%$searchQuery%'
                AND (
                    SELECT 1
                    FROM users u
                    WHERE u.user_id = r.creator
                    AND u.username LIKE " . "'%$creatorQuery%'" . ")";

            if ($category != 0) {
                $sql .= " AND r.category = $category";
            }
        }
        //simplest for individual pages where creator field doesnt exists
        else{
            $sql = "SELECT * FROM recipe r WHERE name LIKE " . "'%$searchQuery%'";

            if ($category != 0) {
                $sql .= " AND category = $category";
            }
        }
        $sql .= " order by r.name";
        // echo "<script>console.log($sql);</script>";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $row;
        }

        echo json_encode($recipes);
    }
}
