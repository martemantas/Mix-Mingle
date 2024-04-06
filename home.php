<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);
}
// else{
//     header("Location: login.php");
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Mix&Mingle</title>
</head>
<script src="home.js"></script>
<body>
<?php
    echo '<nav>';
        echo '<div class="hamburger">';
            echo '<span class="line"></span>';
            echo '<span class="line"></span>';
            echo '<span class="line"></span>';
        echo '</div>';
        echo '<ul>';
            echo '<li><a href="home.php">Home</a></li>';
            echo '<li><a href="">Mix</a></li>';
            echo '<li><a href="">Surprise Me</a></li>';
            echo '<li><a href="">Search</a></li>';
            if(!empty($_SESSION["id"]) && $row['role'] == 2 || 3){
                echo '<li><a href="newRecipe.php">New recipe</a></li>';
            }   
            if(!empty($_SESSION["id"]) && $row['role'] == 3){
                echo '<li><a href="admin.php">Admin</a></li>';
            } 
        echo '</ul>';
        if(empty($_SESSION["id"])){
            echo '<li class="login"><a href="login.php">Login</a></li>';
        }
        else{
            echo '<li class="login"><a href="logout.php">Logout</a></li>';
        }
    echo '</nav>';
?>
    <div class="cards">
        <div class="card">
            <a href="cocktails.php">
                <img src="images/aa.png" alt="minimalistic-cocktail-image">
                <h1>Cocktails</h1>
            </a>
        </div>
        <div class="card">
            <a href="protein.php">
                <img src="images/bb.png" alt="minimalistic-protein-shake-image">
                <h1>Protein</h1>
            </a>
        </div>
        <div class="card">
            <a href="smoothies.php">
                <img src="images/cc.png" alt="minimalistic-smoothie-image">
                <h1>Smoothies</h1>
            </a>
        </div>
    </div>
    <footer>
        <p style="border-top: none;">Tai nera komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>
    </footer>
</body>
</html>