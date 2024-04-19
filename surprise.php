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
            echo '<li><a href="surprise.html">Surprise Me</a></li>';
            echo '<li><a href="">Search</a></li>';
            if(!empty($_SESSION["id"]) && ($row['role'] == 2 || 3)){
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
    <div class="surpriseContainer">
        <button id="randomRecipeButton">Get me a random Recipe</button>
        <div id="recipeContainer"></div>
    </div>
    <button id="nextRecipeButton" class="hidden">Perhaps another?</button>

    <footer>
        <p style="border-top: none;">Tai nera komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>
    </footer>
</body>
<script>
document.getElementById('randomRecipeButton').addEventListener('click', function() {
    fetchRandomRecipe();
});

document.getElementById('nextRecipeButton').addEventListener('click', function() {
    fetchRandomRecipe();
});

function fetchRandomRecipe() {
    var recipeContainer = document.getElementById('recipeContainer');
    recipeContainer.innerHTML = '';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'getRandomRecipe.php', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var recipe = JSON.parse(xhr.responseText);
                displayRecipe(recipe);
            } else {
                console.error('Failed to fetch random recipe:', xhr.status);
            }
        }
    };
    xhr.send();
}

function displayRecipe(recipe) {
            var randomRecipeButton = document.getElementById('randomRecipeButton');
            var recipeContainer = document.getElementById('recipeContainer');
            var nextRecipeButton = document.getElementById('nextRecipeButton');

            var recipeCard = document.createElement('div');
            recipeCard.classList.add('random-card');
            randomRecipeButton.classList.add('hidden');
            nextRecipeButton.classList.remove('hidden');

            var recipeLink = document.createElement('a');
            if (recipe.category === 1) {
                recipeLink.href = 'cocktails.php?id=' + recipe.recipe_id;
            } else if (recipe.category === 2) {
                recipeLink.href = 'smoothies.php?id=' + recipe.recipe_id;
            } else {
                recipeLink.href = 'protein.php?id=' + recipe.recipe_id;
            }

            var recipeName = document.createElement('h2');
            recipeName.textContent = recipe.name;
            recipeLink.appendChild(recipeName);

            var recipeImage = document.createElement('img');
            recipeImage.src = "recipes/" + recipe.recipe_id + "." + recipe.picture;
            recipeImage.alt = recipe.name;
            recipeLink.appendChild(recipeImage);

            recipeLink.addEventListener('click', function(event) {
                event.preventDefault();
                window.location.href = recipeLink.href;
            });

            recipeCard.appendChild(recipeLink);
            recipeContainer.appendChild(recipeCard);
        }
</script>
</html>