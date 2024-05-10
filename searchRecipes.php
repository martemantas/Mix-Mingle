<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["query"])) {
        $searchQuery = $_GET["query"];
        $category = $_GET["category"];

        if($category != 0){
            $stmt = $conn->prepare("SELECT * FROM recipe WHERE name LIKE ? and category = $category");
        }
        else{
            $stmt = $conn->prepare("SELECT * FROM recipe WHERE name LIKE ?");
        }
        $searchParam = "%" . $searchQuery . "%";
        $stmt->bind_param("s", $searchParam);
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
