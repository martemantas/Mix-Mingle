var modal = document.getElementById('newModal');
var modalContent = document.querySelector('.modal-content');
var modalRating = modal.querySelector("#modalRatingStars");
const reviewStars = document.getElementById('reviewStars');
let filledStars = 0;

var recipe_id;
var userId;

function openModal(recipeId, imgSrc, name, description, rating, user) {
    recipe_id = recipeId;
    userId = user;
    var modalImg = modal.querySelector("#modalImg");
    var modalName = modal.querySelector("#modalName");
    var modalDescription = modal.querySelector("#modalDescription");
    var modalRatingNumber = modal.querySelector("#modalRatingText");

    modal.style.display = "block";
    if(imgSrc != 'undefined'){
        modalImg.src = imgSrc;
    }
    modalName.innerText = name;
    modalDescription.innerText = description;
    modalRatingNumber.innerHTML = rating;
    modalRating.innerHTML = '';
    if (rating == 0) {
        modalRatingNumber.innerHTML = null;
        modalRating.innerText = "This recipe has no ratings";
        modalRating.classList.add('noStars');
    } else {
        var fullStars = Math.round(rating);

        for (var i = 1; i <= fullStars; i++) {
            var star = document.createElement('span');
            star.classList.add('star', 'filled');
            star.innerHTML = '&#9733;'; // Unicode for star
            modalRating.appendChild(star);
        }

        var emptyStars = 5 - fullStars;
        for (var j = 0; j < emptyStars; j++) {
            var emptyStar = document.createElement('span');
            emptyStar.classList.add('star');
            emptyStar.innerHTML = '&#9734;'; // Unicode for empty star
            modalRating.appendChild(emptyStar);
        }
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'getUserRating.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);

                if (!isNaN(response.rating)) {
                    fillStars(response.rating - 1);
                } else {
                    console.error('Invalid rating value:', response);
                }
            } else {
                console.error('Failed to fetch user rating:', xhr.status);
            }
        }
    };

    var data = 'recipeId=' + recipe_id + '&userId=' + userId;
    xhr.send(data);

    loadIngredients(recipeId);
    loadRecipeSteps(recipeId);
}

document.getElementById('newModal').addEventListener('click', function(event) {
    var modalContent = document.querySelector('.modal-content');
    if (!modalContent.contains(event.target)) {
        closeModal();
    }
});

function closeModal() {
    modal.style.display = "none";
    modalContent.classList.remove('flipped');
    modalRating.classList.remove('noStars');
}

function flipCard() {
    modalContent.classList.toggle('flipped');
}

for (let i = 0; i < 5; i++) {
    const star = document.createElement('span');
    star.classList.add('reviewStar');
    star.innerHTML = '&#9734;';
    reviewStars.appendChild(star);

    star.addEventListener('mouseenter', function () {
        fillStars(i);
    });

    star.addEventListener('mouseleave', function () {
        fillStars(filledStars - 1);
    });

    star.addEventListener('click', function () {
        filledStars = i + 1;
    
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'calculateRating.php', true);
    
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === "success") {
                        updateStarsDisplay(filledStars);
                        alert(response.message);
                    } else {
                        console.error('Failed to submit rating:', response.message);
                    }
                } else {
                    console.error('Failed to submit rating:', xhr.status);
                }
            }
        };
    
        var data = 'filledStars=' + filledStars + '&recipeId=' + recipe_id + '&userId=' + userId;
        xhr.send(data);
    });
}

function fillStars(index) {
    const stars = reviewStars.querySelectorAll('.reviewStar');
    for (let i = 0; i <= index; i++) {
        stars[i].innerHTML = '&#9733;';
    }
    for (let i = index + 1; i < 5; i++) {
        stars[i].innerHTML = '&#9734;';
    }
}

function updateStarsDisplay(filledStars) {
    const stars = document.querySelectorAll('.reviewStar');
    stars.forEach((star, index) => {
        if (index < filledStars) {
            star.innerHTML = '&#9733;'; // Filled star
        } else {
            star.innerHTML = '&#9734;'; // Empty star
        }
    });
}

function loadIngredients(recipeId) {
    var xhr = new XMLHttpRequest();
    var url = 'getIngredients.php?recipe_id=' + recipeId;
    xhr.open('GET', url, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var ingredients = JSON.parse(xhr.responseText);
                displayIngredients(ingredients);
            } else {
                console.error('Failed to fetch ingredient data: ' + xhr.status);
            }
        }
    };

    xhr.send();
}

function loadRecipeSteps(recipeId) {
    var xhr = new XMLHttpRequest();
    var url = 'getSteps.php?recipe_id=' + recipeId;
    xhr.open('GET', url, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                var recipeSteps = JSON.parse(xhr.responseText);
                displayRecipeSteps(recipeSteps);
            } else {
                console.error('Failed to fetch recipe steps data: ' + xhr.status);
            }
        }
    };
    xhr.send();
}

function displayIngredients(ingredients) {
    var ingredientsDiv = document.getElementById('ingredients');
    var ol = document.createElement('ul');
    
    ingredientsDiv.innerHTML = '';

    ingredients.forEach(function(ingredient) {
        var li = document.createElement('li');

        li.textContent = ingredient.AMOUNT + ' ' + ingredient.unit_name + ' ' + ingredient.product_name;
        ol.appendChild(li);
    });

    ingredientsDiv.appendChild(ol);
}

function displayRecipeSteps(recipeSteps) {
    var recipeStepsDiv = document.getElementById('recipeSteps');
    var ol = document.createElement('ol');

    recipeStepsDiv.innerHTML = '';

    recipeSteps.forEach(function(step) {
        var li = document.createElement('li');
        li.textContent = step.description;
        ol.appendChild(li);
    });

    recipeStepsDiv.appendChild(ol);
}