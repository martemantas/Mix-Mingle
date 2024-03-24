<?php
require 'config.php';

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
} 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recipe_id'])) {
    $recipeId = $_POST['recipe_id'];
    $deleteQuery = "DELETE from recipe where recipe_id = '$recipeId'";
    if ($conn->query($deleteQuery) === FALSE) { 
        echo "Error deleting record: " . $conn->error;
    }
}
?>