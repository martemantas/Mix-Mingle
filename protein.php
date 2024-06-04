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
$isOwner = False;
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
            echo '<li><a href="surprise.php">Surprise Me</a></li>';
            echo '<li><a href="search.php">Search</a></li>';
            if(!empty($_SESSION["id"]) && ($row['role'] > 1)){
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
            <h1>Delicious protein shakes!</h1>
            <div class="search">
                <form class="searchBar" id="searchForm">
                    <input type="text" id="searchInput" placeholder="What are you looking for?">
                    <button type="submit">Search</button>
                </form>
                <div class="searchButtons">
                    <button class="clearSearch" id="clearSearch">Clear</button>
                    <div class="dropdown">
                        <button id="sort">Sort</button>
                        <div class="dropdown-content">
                            <a id="ascendingName">By name (a)</a>
                            <a id="descendingName">By name (z)</a>
                            <a id="ascendingRating">By rating (ascending)</a>
                            <a id="descendingRating">By rating (descending)</a>
                        </div>
                    </div>
                    <button id="favoriteButton">Favorite</button>
                </div>
            </div>
            <div class="drink-cards">
            <?php
                $result = mysqli_query($conn, "SELECT r.*
                FROM recipe r
                JOIN users u ON r.creator = u.user_id
                WHERE r.category = 3
                AND u.role IN (2, 3) order by name;");
                if (mysqli_num_rows($result) > 0) {
                    while ($resultRow = mysqli_fetch_assoc($result)) {
                        $recipeId = $resultRow['recipe_id'];
                
                        $resultRow['picture'] = "recipes/{$recipeId}.{$resultRow['picture']}";

                        $creator = mysqli_fetch_assoc(mysqli_query($conn, "SELECT username FROM users WHERE user_id = " . (int)$resultRow['creator']))['username'];
                        
                        // Query to find ID of the recipe creator
                        $findCreator = "SELECT creator FROM recipe WHERE recipe_id = '$recipeId'";
                        $resultCreator = $conn->query($findCreator);
                        if ($resultCreator) {
                            $rowCreator = $resultCreator->fetch_assoc();
                            $creatorID = $rowCreator['creator'];
                        } else {
                            $creatorID = null;
                        }
                                
                        // Check if user is logged in
                        if (!empty($_SESSION["id"])) {
                            $userID = $_SESSION["id"];
                
                            // Query to find the current user information
                            $findUser = "SELECT * FROM users WHERE user_id = '$userID'";
                            $resultUser = $conn->query($findUser);
                            if ($resultUser) {
                                $rowUser = $resultUser->fetch_assoc();
                            } else {
                                $rowUser = null;
                            }

                            $isOwner = false;
                            if ($creatorID == $_SESSION["id"]) {
                                $isOwner = true;
                            }
                
                            // Check if user is admin or editor 
                            if (($rowUser['role'] == 2 && $isOwner) || $rowUser['role'] == 3) {
                                echo '<form method="post" action="editRecipe.php">';
                                echo '<input type="hidden" name="recipe_id" value="' . $resultRow['recipe_id'] . '">';
                                echo '<div class="drink-card protein" onclick="openModal(' . $resultRow['recipe_id'] . ', \'' . $resultRow['picture'] . '\', \'' . $resultRow['name'] . '\', \'' . $resultRow['description'] . '\', \'' . $resultRow['total_rating'] . '\', \'' . $sessionID . '\', \'' . $creator . '\')">';
                                echo '<img src="' . $resultRow['picture'] . '" alt="' . $resultRow['name'] . '">';
                                echo '<h1 class="recipe-name" style="text-align: center;">' . $resultRow['name'] . '</h1>';
                                echo '<button class="delete" value="' . $resultRow['recipe_id'] . '" id="confirmButton">&times;</button>';
                                echo '<button type="submit" class="edit" name="edit">E</button>';
                                echo '</div>';
                                echo '</form>';
                            } else {
                                echo '<div class="drink-card protein" onclick="openModal(' . $resultRow['recipe_id'] . ', \'' . $resultRow['picture'] . '\', \'' . $resultRow['name'] . '\', \'' . $resultRow['description'] . '\', \'' . $resultRow['total_rating'] . '\', \'' . $sessionID . '\', \'' . $creator . '\')">';
                                echo '<img src="' . $resultRow['picture'] . '" alt="' . $resultRow['name'] . '">';
                                echo '<h1 class="recipe-name" style="text-align: center;">' . $resultRow['name'] . '</h1></div>';
                            }
                        } else {
                            echo '<div class="drink-card protein" onclick="openModal('. $resultRow['recipe_id'] .', \''. $resultRow['picture'] .'\', \''. $resultRow['name'] .'\', \''. $resultRow['description'] .'\', \''. $resultRow['total_rating'] .'\', \''. $sessionID .'\', \''. $creator .'\')">';
                            echo '<img src="' . $resultRow['picture'] . '" alt="' . $resultRow['name'] . '">';
                            echo '<h1 class="recipe-name" style="text-align: center;">' . $resultRow['name'] . '</h1></div>';
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
            <p class="modalCreator">Made by: </p>
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
                    echo '<input type="text" name="suggestion" placeholder="Enter your suggestion" maxlength="60">';
                    echo '<button type="submit">Send</button>';
                echo '</form>';
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
<script src="common.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('ascendingName').addEventListener('click', function() {
            fetchSortedRecipes(3, 'name', 'ASC');
        });

        document.getElementById('descendingName').addEventListener('click', function() {
            fetchSortedRecipes(3, 'name', 'DESC');
        });

        document.getElementById('ascendingRating').addEventListener('click', function() {
            fetchSortedRecipes(3, 'total_rating', 'ASC');
        });

        document.getElementById('descendingRating').addEventListener('click', function() {
            fetchSortedRecipes(3, 'total_rating', 'DESC');
        });
    });

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

                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'get_username.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        var creator = response.username;

                        recipeCard.addEventListener('click', function() {
                            var formattedRating = parseFloat(recipe.total_rating).toFixed(2);
                            openModal(recipe.recipe_id, imagePath, recipe.name, recipe.description, formattedRating, '<?php echo $sessionID; ?>', creator);
                        });

                        // Check if user is logged in and can edit
                        var loggedStatus = <?php echo isset($_SESSION['id']) ? 'true' : 'false'; ?>;
                        if (loggedStatus) {
                            var userID = <?php echo isset($_SESSION['id']) ? $_SESSION['id'] : 'null'; ?>;
                            var userRole = <?php echo isset($rowUser['role']) ? $rowUser['role'] : 'null'; ?>;
                            var isOwner = (recipe.creator == userID);

                            var canEdit = (userRole == 2 && isOwner) || (userRole == 3);

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
                                deleteButton.addEventListener('click', function(event) {
                                    event.stopPropagation();
                                    var result = confirm("Are you sure you want to delete?");
                                    if (result) {
                                        deleteRecipe(recipe.recipe_id);
                                    }
                                });
                                editForm.appendChild(deleteButton);

                                recipeCard.appendChild(editForm);
                            }
                        }
                    }
                };
                xhr.send('creatorId=' + encodeURIComponent(recipe.creator));

                recipesContainer.appendChild(recipeCard);
            });
        }
    }

    <?php if (!empty($_SESSION["id"])) { ?>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('favoriteButton').addEventListener('click', function() {
            var categoryId = 3;
            var userId = '<?php echo $sessionID; ?>';

            fetchRecipesByCategoryAndUser(categoryId, userId);
        });
    });
    <?php } 

    else if (empty($_SESSION["id"]) || ($_SESSION["id"] == 0)) { ?>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('favoriteButton').addEventListener('click', function() {
            popUpDiv("Guests don't have favorite recipes",'loginSuggestion');
        });
    });
    <?php } ?>

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var searchQuery = document.getElementById('searchInput').value;
            var userId = '<?php echo $sessionID; ?>';

            fetchRecipesBySearchQuery(searchQuery, '', 3, userId, []);
        });
    });
</script>
</html>