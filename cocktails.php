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
            <h1>Alcoholic cocktails for your taste!</h1>
            <form class="searchBar"> <!-- form should have a name -->
                <input type="text" placeholder="What are you looking for?" required>
                <button type="submit">Search</button>
            </form>
            <div class="drink-cards">
            <?php
                $result = mysqli_query($conn, "SELECT * FROM recipe WHERE category = 1");
                if (mysqli_num_rows($result) > 0) {
                    while ($resultRow = mysqli_fetch_assoc($result)) {
                        $recipeId = $resultRow['recipe_id'];
                        if (empty($resultRow['picture'])) {
                            if(empty($_SESSION["id"]) || $row['role'] > 1){
                                echo '<form method="post" action="deleteRecipe.php">';
                                    echo '<div class="drink-card alcoholic" onclick="openModal(\''. $resultRow['picture'] .'\', \''. $resultRow['name'] .'\', \''. $resultRow['description'] .'\', \''. $resultRow['total_rating'] .'\')">' . $resultRow['name'] . '<button class="delete" value="'. $resultRow['recipe_id'] .'" id="confirmButton">&times;</button></div>';
                                echo '</form>';
                            }
                            else{
                                echo '<div class="drink-card alcoholic" onclick="openModal(\''. $resultRow['picture'] .'\', \''. $resultRow['name'] .'\', \''. $resultRow['description'] .'\', \''. $resultRow['rating'] .'\')">' . $resultRow['name'] . '</div>';
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

    <div id="newModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
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
                <div class="ingredients">
                    <ol>
                        <li>1 tsp granulated sugar</li>
                        <li>100ml lime juice</li>
                        <li>20g mint leaves</li>
                        <li>60ml white rum</li>
                        <li>soda water to taste</li>
                    </ol>
                    <div class="leave-rating">
                        <p>Leave a review</p>
                        <div id="reviewStars" class="stars"></div>
                        <!-- ADD PHP for 'thanks for leaving a review' + only logged users, call leaveReview.php get back to the page -->
                    </div>
                </div> 
                <!-- remove dummy text, inset from database -->
                <div class="recipeSteps">
                    <ol>
                        <li>1 adwaawdawdawadgar</li>
                        <li>100ml AW r3WEFVAEdAWce</li>
                        <li>2d awdacfvavawvawvves</li>
                        <li>6awfawffacaawcawacawcacawwum</li>
                        <li>soda water to taste</li>
                        <li>soda water to taste</li>
                        <li>soda awdawfavaawavaw taste</li>
                        <li>soda water to taste</li>
                    </ol>
                </div>
                <button id="flipButton" class="backBtn" onclick="flipCard()">Back</button>
        </div>
    </div>

    <script>
        var modal = document.getElementById('newModal');
        var modalContent = document.querySelector('.modal-content');
        var modalRating = modal.querySelector("#modalRatingStars");
        const reviewStars = document.getElementById('reviewStars');
        let filledStars = 0;

        function openModal(imgSrc, name, description, rating) {
            var modalImg = modal.querySelector("#modalImg");
            var modalName = modal.querySelector("#modalName");
            var modalDescription = modal.querySelector("#modalDescription");
            var modalRatingNumber = modal.querySelector("#modalRatingText");

            modal.style.display = "block";
            if(imgSrc != 'undefined'){ //not sure if works
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
                console.log('Number of filled stars:', filledStars);
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
    </script>


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