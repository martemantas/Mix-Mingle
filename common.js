document.addEventListener("click", function(event) {
    if (event.target.classList.contains("delete")) {
        var result = confirm("Are you sure you want to delete?");
        if (!result) {
            event.preventDefault();
        } else {
            var recipeId = event.target.value;

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

function fetchRecipesBySearchQuery(searchQuery, category) {
    var xhr = new XMLHttpRequest();
    var url = 'searchRecipes.php?query=' + encodeURIComponent(searchQuery) + '&category=' + category;
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

            var deleteBtn = document.createElement('button');
            deleteBtn.classList.add('delete');
            deleteBtn.setAttribute("id", "confirmButton");
            deleteBtn.value = recipe.recipe_id;
            deleteBtn.textContent = 'x';
            recipeCard.appendChild(deleteBtn);

            recipesContainer.appendChild(recipeCard);
            recipeCard.addEventListener('click', function() {
                var formattedRating = parseFloat(recipe.total_rating).toFixed(2);
                openModal(recipe.recipe_id, imagePath, recipe.name, recipe.description, formattedRating, $_SESSION["id"]);
            });
            closeModal(false);
        });
    }
}

// not used in search.php
function fetchSortedRecipes(categoryId, orderBy, orderDirection) {
    var xhr = new XMLHttpRequest();
    var url = 'sortRecipes.php?category=' + categoryId + '&orderBy=' + orderBy + '&orderDirection=' + orderDirection;
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