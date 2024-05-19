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

    var clearSearchBtn = document.querySelector('.clearSearch');
    clearSearchBtn.setAttribute("style", "visibility: visible");
}

function fetchRecipesBySearchQuery(searchQuery, creatorQuery, category, userId, ingredients) {
    var xhr = new XMLHttpRequest();
    var url = 'searchRecipes.php?query=' + encodeURIComponent(searchQuery) + '&creatorQuery=' + creatorQuery  + '&category=' + category + '&ingredients=' + JSON.stringify(ingredients);
    xhr.open('GET', url, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // console.log(xhr.responseText);
                var recipes = JSON.parse(xhr.responseText);
                displayRecipes(recipes, userId);
            } else {
                console.error('Failed to fetch recipes:', xhr.status);
            }
        }
    };

    xhr.send();

    var clearSearchBtn = document.querySelector('.clearSearch');
    clearSearchBtn.setAttribute("style", "visibility: visible");
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

    var clearSearchBtn = document.querySelector('.clearSearch');
    clearSearchBtn.setAttribute("style", "visibility: visible");
}

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('clearSearch').addEventListener('click', function(event) {
        location.reload();
    });
});