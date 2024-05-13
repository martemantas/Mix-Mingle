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
    <link rel="stylesheet" href="search.css">
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
    <section>
        <div class="search-view">
            <h1>Any cocktails for your taste!</h1>
            <div class="search">
                <form class="searchBar" id="searchForm">
                    <input type="text" id="searchInput" placeholder="What are you looking for?">
                    <input type="text" id="searchInputCreator" placeholder="Do you know the author?">
                    <button type="submit">Search</button>
                </form>
                <div class="searchButtons" style="justify-content: flex-start;">
                    <div class="ingredient-dropdown">
                        <label for="">Or choose by ingredients</label>
                        <button class="add-ingredient">Add</button>
                    </div>
                </div>
            </div>
            <div class="drink-cards">
            <?php
                $result = mysqli_query($conn, "SELECT r.*
                FROM recipe r
                JOIN users u ON r.creator = u.user_id
                WHERE u.role IN (2, 3) order by name;");
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
                                // Check if user is admin or editor 
                                if($row['role'] > 1){
                                    echo '<form method="post" action="deleteRecipe.php">';
                                        echo '<div class="drink-card alcoholic" onclick="openModal('. $resultRow['recipe_id'] .', \''. $resultRow['picture'] .'\', \''. $resultRow['name'] .'\', \''. $resultRow['description'] .'\', \''. $resultRow['total_rating'] .'\', \''. $sessionID .'\')">';
                                        echo '<img src="'.$resultRow['picture'].'" alt="'.$resultRow['name'].'">';
                                        echo '<h1 class="recipe-name" style="text-align: center;">'. $resultRow['name'] .'</h1>';
                                        echo '<button class="delete" value="'. $resultRow['recipe_id'] .'" id="confirmButton">&times;</button></div>';
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
                <div id="recipeSteps"></div>
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
                echo '<form method="post" action="suggestions.php">';
                    echo '<input type="text" name="suggestion" placeholder="Enter your suggestion">';
                    echo '<button type="submit">Send</button>';
                echo '</form>';
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
<script src="modal.js"></script>
<script src="common.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var userId = '<?php echo $sessionID; ?>';
        
        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            var searchQuery = document.getElementById('searchInput').value;
            var creatorQuery = document.getElementById('searchInputCreator').value;
            var selectedIngredients = [];
            var ingredientSelects = document.querySelectorAll('.ingredient-select');
            ingredientSelects.forEach(function(select) {
                selectedIngredients.push(select.value);
            });

            fetchRecipesBySearchQuery(searchQuery, creatorQuery, 0, userId, selectedIngredients);
        });
    });

    // function fetchRecipesByIngredients(searchQuery, category, userId, ingredients) {
    //     var xhr = new XMLHttpRequest();
    //     var url = 'searchRecipes.php?query=' + encodeURIComponent(searchQuery) + '&category=' + category  + '&ingredients=' + JSON.stringify(ingredients);
    //     xhr.open('GET', url, true);
    
    //     xhr.onreadystatechange = function() {
    //         if (xhr.readyState === XMLHttpRequest.DONE) {
    //             if (xhr.status === 200) {
    //                 var recipes = JSON.parse(xhr.responseText);
    //                 displayRecipes(recipes, userId);
    //             } else {
    //                 console.error('Failed to fetch recipes:', xhr.status);
    //             }
    //         }
    //     };
    
    //     xhr.send();
    // }
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

            // Check if user is logged in
                if (<?php echo !empty($_SESSION["id"]) ? 'true' : 'false'; ?>) {
                    var canEdit = <?php echo ($row['role'] == 2 && $isOwner) || $row['role'] == 3 ? 'true' : 'false'; ?>;

                    // If user is admin or editor and creator of the recipe
                    if (canEdit) {
                        var editForm = document.createElement('form');
                        editForm.method = 'post';
                        editForm.action = 'editRecipe.php';

                        var hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'recipe_id';
                        hiddenInput.value = recipe.recipe_id;
                        editForm.appendChild(hiddenInput);

                        var editButton = document.createElement('button');
                        editButton.type = 'submit';
                        editButton.classList.add('edit');
                        editButton.name = 'edit';
                        editButton.textContent = 'E';
                        editForm.appendChild(editButton);

                        var deleteButton = document.createElement('button');
                        deleteButton.type = 'button';
                        deleteButton.classList.add('delete');
                        deleteButton.value = recipe.recipe_id;
                        deleteButton.textContent = '×';
                        deleteButton.id = 'confirmButton';
                        deleteButton.addEventListener('click', function() {
                        var result = confirm("Are you sure you want to delete?");
                        if (result) {
                            deleteRecipe(recipe.recipe_id);
                        }
                    });
                    editForm.appendChild(deleteButton);

                    recipeCard.appendChild(editForm);
                }
            }
                recipesContainer.appendChild(recipeCard);
                recipeCard.addEventListener('click', function() {
                    var formattedRating = parseFloat(recipe.total_rating).toFixed(2);
                    openModal(recipe.recipe_id, imagePath, recipe.name, recipe.description, formattedRating, '<?php echo $sessionID; ?>');
                });
                closeModal(false);
            });
        }
    }
</script>
<script src="search.js"></script>
</html>