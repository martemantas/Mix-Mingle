<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Mix&Migle</title>
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
        echo '</ul>';
        if(empty($_SESSION["id"])){
            echo '<li class="login"><a href="login.php">Login</a></li>';
        }
        else{
            echo '<li class="login"><a href="logout.php">Logout</a></li>';
        }
    echo '</nav>';
?>
    <section>
        <div class="search-view">
            <h1>Protein cocktails for your taste!</h1>
            <form class="searchBar">
                <input type="text" placeholder="What are you looking for?" required>
                <button type="submit">Search</button>
            </form>
            <div class="drink-cards">
            <?php
                $result = mysqli_query($conn, "SELECT * FROM recipe WHERE category = 3");
                if (mysqli_num_rows($result) > 0) {
                    while ($resultRow = mysqli_fetch_assoc($result)) {
                        $recipeId = $resultRow['recipe_id'];
                        if (empty($resultRow['picture'])) {
                            if($row['role'] > 1){
                                echo '<form method="post" action="deleteRecipe.php">';
                                echo '<div class="drink-card alcoholic">' . $resultRow['name'] . '<button class="delete" value="'. $resultRow['recipe_id'] .'" id="confirmButton">X</button></div>';
                                echo '</form>';
                            }
                            else{
                                echo '<div class="drink-card alcoholic">' . $resultRow['name'] . '</div>';
                            }
                        }
                        else{
                            //to do picture instead of name
                        }
                    }
                } else {
                    echo "<h3 class='search-error'>No recipes found for the given category.</h3>";
                }
            ?>
            </div>
        </div>
    </section>
    <footer>
    <?php
        if(!empty($_SESSION["id"])){
        echo '<div class="contact">';
            echo '<div class="info">';
                echo "<h2>Didn't find what you were looking for?</h2>";
                echo '<h2><i>Let us know!</i></h2>';
            echo '</div>';
            echo '<div class="submit">';
                echo '<input type="text">';
                echo '<button>Send</button> ';
            echo '</div>';
        echo '</div>';
        echo '<p>Tai nera komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>';
        }
        else{
            echo '<p style="border-top:none">Tai nera komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>';
        }
    ?>
    </footer>
</body>
<script>
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("delete")) {
            var result = confirm("Are you sure you want to delete?");
            if (!result) {
                event.preventDefault();
            } else {
                var recipeId = event.target.value;
                console.log(recipeId);

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "deleteRecipe.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        location.reload();
                    }
                };
                xhr.send("recipe_id=" + recipeId);
            }
        }
    });
</script>
</html>