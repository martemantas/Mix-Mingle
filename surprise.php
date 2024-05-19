<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);
}
else{
    $sessionID = null;
}
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
            echo '<li><a href="surprise.php">Surprise Me</a></li>';
            echo '<li><a href="search.php">Search</a></li>';
            if(!empty($_SESSION["id"]) && ($row['role'] == 2 || 3)){
                echo '<li><a href="newRecipe.php">New recipe</a></li>';
                echo '<li><a href="newIngredient.php">New ingredient</a></li>';
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
        <button id="randomRecipeButton" onclick="fetchRandomRecipe()">Get me a random Recipe</button>
        <div id="recipeContainer"></div>
    </div>
    <button id="nextRecipeButton" class="hidden" onclick="fetchRandomRecipe()">Perhaps another?</button>

    <div id="newModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal(false)">&times;</span>
            <img id="modalImg">
            <div class="modalInfo">
                <h2 id="modalName"></h2>
                <p id="modalDescription"></p>
                <div class="modalRating">
                    <p id="modalRatingText"></p>
                    <div id="modalRatingStars" class="stars"></div>
                </div>
                <button id="flipButton" onclick="flipCard()">Show Ingredients</button>
            </div>
            <div class="left-side">
                <div class="ingredients">
                    <h2 class="backSideTitle">Ingredients</h2>
                    <div id="ingredients"></div>
                </div>
                <div class="leave-rating">
                    <p>Leave a review</p>
                    <div id="reviewStars" class="stars"></div>
                </div>
            </div> 
            <div class="right-side">
                <h2 class="backSideTitle">How to make it</h2>
                <div id="recipeSteps"></div>
            </div>
            <button id="flipButton" class="backBtn" onclick="flipCard()">Back</button>
        </div>
    </div>

    <footer>
        <p style="border-top: none;">Tai nėra komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>
    </footer>
</body>
<script src="modal.js"></script>
<script>
function fetchRandomRecipe() {
    var recipeContainer = document.getElementById('recipeContainer');
    recipeContainer.innerHTML = '';
    document.getElementById('nextRecipeButton').classList.add('hidden');

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
    var user = <?php echo json_encode($sessionID); ?>;

    var recipeCard = document.createElement('div');
    recipeCard.classList.add('random-card');
    randomRecipeButton.classList.add('hidden');
    nextRecipeButton.classList.remove('hidden');

    var recipeName = document.createElement('h2');
    recipeName.textContent = recipe.name;
    recipeCard.appendChild(recipeName);

    var recipeImage = document.createElement('img');
    recipeImage.src = "recipes/" + recipe.recipe_id + "." + recipe.picture;
    recipeImage.alt = recipe.name;
    recipeCard.appendChild(recipeImage);

    recipeCard.addEventListener('click', function(event) {
        event.preventDefault();
        openModal(recipe.recipe_id, recipeImage.src, recipe.name, recipe.description, recipe.total_rating, '<?php echo $sessionID; ?>');
    });
    closeModal(false);
    
    recipeContainer.appendChild(recipeCard);
}

</script>
</html>