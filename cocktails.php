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
            echo '<li><a href="surprise.php">Surprise Me</a></li>';
            echo '<li><a href="">Search</a></li>';
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
    <section>
        <div class="search-view">
            <h1>Alcoholic cocktails for your taste!</h1>
            <div class="search">
                <form class="searchBar"> <!-- form should have a name -->
                    <input type="text" placeholder="What are you looking for?" required>
                    <button type="submit">Search</button>
                </form>
                <div class="searchButtons">
                    <button id="favoriteButton">Favorite</button>
                </div>
            </div>
            <div class="drink-cards">
            <?php
                $result = mysqli_query($conn, "SELECT r.*
                FROM recipe r
                JOIN users u ON r.creator = u.user_id
                WHERE r.category = 1
                AND u.role IN (2, 3);");
                if (mysqli_num_rows($result) > 0) {
                    while ($resultRow = mysqli_fetch_assoc($result)) {
                        $recipeId = $resultRow['recipe_id'];

                        // Check if recipe has picture
                        if (empty($resultRow['picture'])) {
                            // Check if user is logged in
                            if(!empty($_SESSION["id"])){
                                // Check if user is admin or editor 
                                if($row['role'] > 1){
                                    echo '<form method="post" action="deleteRecipe.php">';
                                        echo '<div class="drink-card alcoholic" onclick="openModal('. $resultRow['recipe_id'] .', \''. $resultRow['picture'] .'\', \''. $resultRow['name'] .'\', \''. $resultRow['description'] .'\', \''. $resultRow['total_rating'] .'\', \''. $sessionID .'\')">' . $resultRow['name'] . '<button class="delete" value="'. $resultRow['recipe_id'] .'" id="confirmButton">&times;</button></div>';
                                    echo '</form>';
                                }
                                else {
                                    echo '<div class="drink-card alcoholic" onclick="openModal('. $resultRow['recipe_id'] .', \''. $resultRow['picture'] .'\', \''. $resultRow['name'] .'\', \''. $resultRow['description'] .'\', \''. $resultRow['total_rating'] .'\', \''. $sessionID .'\')">' . $resultRow['name'] . '</div>';
                                }
                            }
                            else{
                                echo '<div class="drink-card alcoholic" onclick="openModal('. $resultRow['recipe_id'] .', \''. $resultRow['picture'] .'\', \''. $resultRow['name'] .'\', \''. $resultRow['description'] .'\', \''. $resultRow['total_rating'] .'\')">' . $resultRow['name'] . '</div>';
                            }
                        }
                        else{
                            $resultRow['picture'] = "recipes/{$recipeId}.{$resultRow['picture']}";
                            // Check if user is logged in
                            if(!empty($_SESSION["id"])){
                                $isOwner = False;
                            
                                if($resultRow['creator'] == $_SESSION["id"])
                                {
                                    $isOwner = True;
                                }
                                // Check if user is admin or editor 
                                if($row['role'] == 2 && $isOwner || $row['role'] == 3){
                                    $currentPage = htmlspecialchars($_SERVER['REQUEST_URI']);
                                    echo '<form method="post" action="editRecipe.php">';
                                    echo '<input type="hidden" name="previous_page" value="'.$currentPage.'">';
                                    echo '<input type="hidden" name="recipe_id" value="'.$resultRow['recipe_id'].'">';
                                    echo '<div class="drink-card alcoholic" onclick="openModal('. $resultRow['recipe_id'] .', \''. $resultRow['picture'] .'\', \''. $resultRow['name'] .'\', \''. $resultRow['description'] .'\', \''. $resultRow['total_rating'] .'\', \''. $sessionID .'\')">';
                                    echo '<img src="'.$resultRow['picture'].'" alt="'.$resultRow['name'].'">';
                                    echo '<h1 class="recipe-name" style="text-align: center;">'. $resultRow['name'] .'</h1>';
                                    echo '<button class="delete" value="'. $resultRow['recipe_id'] .'" id="confirmButton">&times;</button>';
                                    echo '<button type="submit" class="edit" name="edit">E</button>';
                                    echo '</div>';
                                    echo '</form>';
                                }
                                else {
                                    echo '<div class="drink-card alcoholic" onclick="openModal('. $resultRow['recipe_id'] .', \''. $resultRow['picture'] .'\', \''. $resultRow['name'] .'\', \''. $resultRow['description'] .'\', \''. $resultRow['total_rating'] .'\', \''. $sessionID .'\')">';
                                    echo '<img src="'.$resultRow['picture'].'" alt="'.$resultRow['name'].'">';
                                    echo '<h1 class="recipe-name" style="text-align: center;">'. $resultRow['name'] .'</h1></div>';
                                }
                            }
                            else{
                                echo '<div class="drink-card alcoholic" onclick="openModal('. $resultRow['recipe_id'] .', \''. $resultRow['picture'] .'\', \''. $resultRow['name'] .'\', \''. $resultRow['description'] .'\', \''. $resultRow['total_rating'] .'\')">';
                                echo '<img src="'.$resultRow['picture'].'" alt="'.$resultRow['name'].'">';
                                echo '<h1 class="recipe-name" style="text-align: center;">'. $resultRow['name'] .'</h1></div>';
                            }
                        }
                    }
                } else {
                    echo "<h3 class='search-error'>No recipes found for the given category.</h3>";
                }
            ?>
            </div>
        </div>
    </section>

    <div id="newModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal(true)">&times;</span>
            <img id="modalImg">
            <div class="modalInfo">
                <div class="modalFavorite">
                    <h2 id="modalName"></h2>
                    <span class="heart-icon">&#10084;</span>
                </div>
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
                <div id="recipeSteps">
            </div>
            </div>
            <button id="flipButton" class="backBtn" onclick="flipCard()">Back</button>
        </div>
    </div>

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
        echo '<p>Tai nėra komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>';
        }
        else{
            echo '<p style="border-top:none">Tai nėra komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>';
        }
    ?>
    </footer>
</body>
<script src="modal.js"></script>
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

    function fetchRecipesByCategoryAndUser(categoryId, userId) {
        var xhr = new XMLHttpRequest();
        var url = 'fetchRecipes.php?category=' + categoryId + '&userId=' + userId;
        xhr.open('GET', url, true);

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var recipes = JSON.parse(xhr.responseText);
                    displayRecipes(recipes);
                } else {
                    console.error('Failed to fetch recipes:', xhr.status);
                }
            }
        };

        xhr.send();
    }

    function displayRecipes(recipes) {
    var recipesContainer = document.querySelector('.drink-cards');
    recipesContainer.innerHTML = ''; 

    if (recipes.length === 0) {
        recipesContainer.textContent = 'No recipes found.';
    } else {
        recipes.forEach(function(recipe) {
            var recipeCard = document.createElement('div');
            recipeCard.classList.add('drink-card', 'alcoholic');

            var imagePath = 'recipes/' + recipe.recipe_id + '.' + recipe.picture;
            var img = document.createElement('img');
            img.src = imagePath;
            img.alt = recipe.name;
            recipeCard.appendChild(img);

            var name = document.createElement('h1');
            name.classList.add('recipe-name');
            name.textContent = recipe.name;
            recipeCard.appendChild(name);

            recipesContainer.appendChild(recipeCard);
            recipeCard.addEventListener('click', function() {
                var formattedRating = parseFloat(recipe.total_rating).toFixed(2);
                openModal(recipe.recipe_id, imagePath, recipe.name, recipe.description, formattedRating, '<?php echo $sessionID; ?>');
            });
            closeModal(false);
        });
    }
}

    <?php if (!empty($_SESSION["id"])) { ?>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('favoriteButton').addEventListener('click', function() {
            var categoryId = 1;
            var userId = '<?php echo $sessionID; ?>';

            fetchRecipesByCategoryAndUser(categoryId, userId);
        });
    });
    <?php } else { ?>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('favoriteButton').addEventListener('click', function() {
            popUpDiv("User not found Please login",'loginSuggestion');
        });
    });
    <?php } ?>

</script>
</html>