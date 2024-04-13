var modal = document.getElementById('newModal');
var modalContent = document.querySelector('.modal-content');
var modalRating = modal.querySelector("#modalRatingStars");
var modalRatingText = modal.querySelector("#modalRatingText");
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

    modal.style.display = "block";
    if(imgSrc != 'undefined'){
        modalImg.src = imgSrc;
    }
    modalName.innerText = name;
    modalDescription.innerText = description;
    modalRatingText.innerHTML = rating;
    modalRating.innerHTML = '';
    if (rating == 0) {
        modalRatingText.innerHTML = null;
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

    fetchRating(recipe_id, modalRatingText);
    location.reload();
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

    // GLITCHES WHEN HOVER FAST, BUG
    star.addEventListener('mouseleave', function () {
        setTimeout(function() {
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
        }, 1000);
    });

    star.addEventListener('click', function () {
        filledStars = i + 1;
    
        if(userId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'calculateRating.php', true);
        
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === "success") {
                            updateStarsDisplay(filledStars);
                            popUpDiv(response.message, 'confirmationMessage', 5000);
            
                            if ('updatedTotalRating' in response) {
                                modalRatingText.textContent = parseFloat(response.updatedTotalRating).toFixed(2);
                                modalRating.innerHTML = '';
                                var fullStars = Math.round(response.updatedTotalRating);
                                if (fullStars == 0) {
                                    modalRatingText.innerHTML = null;
                                    modalRating.innerText = "This recipe has no ratings";
                                    modalRating.classList.add('noStars');
                                } else {
                                    for (var i = 1; i <= fullStars; i++) {
                                        var star = document.createElement('span');
                                        star.classList.add('star', 'filled');
                                        star.innerHTML = '&#9733;';
                                        modalRating.appendChild(star);
                                    }
                                    var emptyStars = 5 - fullStars;
                                    for (var j = 0; j < emptyStars; j++) {
                                        var emptyStar = document.createElement('span');
                                        emptyStar.classList.add('star');
                                        emptyStar.innerHTML = '&#9734;';
                                        modalRating.appendChild(emptyStar);
                                    }
                                }
                            }
                            modalRating.classList.remove('noStars');
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
        } else {
            popUpDiv("User not found Please login",'loginSuggestion');
        }
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
            star.innerHTML = '&#9733;';
        } else {
            star.innerHTML = '&#9734;';
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

function fetchRating(recipeId, modalRatingText) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'getUserRating.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (!isNaN(response.rating)) {
                    modalRatingText.innerHTML = parseFloat(response.rating);
                    console.log(modalRatingText.innerHTML);
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
}

function popUpDiv(text, style, time){
    var messageDiv = document.createElement('div');
    messageDiv.textContent = text;
    messageDiv.classList.add(style);
    document.body.appendChild(messageDiv);

    if(time){
        setTimeout(function () {
            document.body.removeChild(messageDiv);
        }, time);
    }
    else{
        var closeButton = document.createElement('span');
        closeButton.innerHTML = '&times;';
        closeButton.classList.add('closeButton');
        messageDiv.appendChild(closeButton);

        var loginLink = document.createElement('a');
        loginLink.textContent = 'Here';
        loginLink.setAttribute('href', 'login.php');
        messageDiv.appendChild(loginLink);

        closeButton.addEventListener('click', function () {
            document.body.removeChild(messageDiv);
        });
    }
}