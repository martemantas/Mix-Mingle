<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["category"]) && isset($_GET["orderBy"]) && isset($_GET["orderDirection"])) {
        $categoryId = $_GET["category"];
        $orderBy = $_GET["orderBy"];
        $orderDirection = $_GET["orderDirection"];

        $validColumns = array("name", "total_rating");
        if (!in_array($orderBy, $validColumns)) {
            exit("Invalid orderBy parameter");
        }

        $validDirections = array("ASC", "DESC");
        if (!in_array($orderDirection, $validDirections)) {
            exit("Invalid orderDirection parameter");
        }

        $sql = "SELECT * FROM recipe WHERE category = ? ORDER BY $orderBy $orderDirection";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
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
