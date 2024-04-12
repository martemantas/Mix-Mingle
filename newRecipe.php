<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);
    $user = $row["user_id"];
}
if(isset($_POST["add"])){
    $name = $_POST["name"];
    $description = $_POST["description"];
    $category = $_POST["category_id"];

    if (empty($name) || empty($description)) {
        echo "<script>alert('Please fill in all the fields.')</script>";
    }

    $filename = basename($_FILES["file"]["name"]);
    $target_file = $filename;
    $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Insert new recipe into the database
    $insertRecipe = "INSERT INTO `recipe` (`name`, `description`, `picture`, `creator`, `category`) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertRecipe);
    $stmt->bind_param("ssssi", $name, $description, $fileType, $user, $category);

    if ($stmt->execute()) {
        $recipeID = $stmt->insert_id;

        // Save uploaded picture in recipes folder
        $targetFolder = "recipes/";
        $targetFile = $targetFolder . $recipeID . "." . $fileType;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            echo "<script>alert('Recipe added.')</script>";
        } else {
           echo "<script>alert('Failed to save the picture.')</script>";
           $queryDelete = "DELETE FROM `recipe` WHERE `recipe`.`recipe_id` ='$recipeID'";
           $result = $conn->query($queryDelete);
        }
   } else {
        echo "<script>alert('Recipe already exists.')</script>";
   }

    /*$query = "INSERT INTO recipe VALUES('', '$name', '$description', '', '$user', '', '$category')";
           mysqli_query($conn,$query);
           echo "<script>alert('Recipe has been saved')</script>";*/

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
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
            echo '</ul>';
        echo '</nav>';
    ?>
    <div class="container">
        <h1 class="title">New recipe</h1>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="field">
                <input type="text" name="name" placeholder="Recipe name" required>
            </div>
            <div class="field">
                <input type="text" name="description" placeholder="Recipe description" required>
            </div>

            <div class="gap">
                <label style = "margin-left:10px" for="file">Upload recipe photo:</label>
                <input type="file" name="file" id="file" accept="image/png, image/jpeg, image/jpg" required>
            </div>

            <div class="field">
                <select name="category_id">
                    <?php
                        require 'config.php';
                        $categories = mysqli_query($conn, "SELECT * FROM categories");
                        while($c = mysqli_fetch_array($categories)){
                    ?>
                    <option value="<?php echo $c['category_id']?>"><?php echo $c['name']?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="field btn">
                <div class="btn-layer"></div>
                <button type="submit" name="add" >Add</button>
            </div>
        </form>
    </div>
    
    <footer style="position:fixed; bottom:0;">
        <p style="border-top: none;">Tai nera komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>
    </footer>
</body>
<script>
    function goBack() {
        window.history.back();
    }

    function validateForm() {
            var fileInput = document.getElementById('file');
            var filePath = fileInput.value;
            var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

            if (!allowedExtensions.exec(filePath)) {
                alert('Incorrect file type. Please upload only PNG, JPEG or JPG type files.');
                fileInput.value = '';
                return false;
            }

            return true;
        }
</script>
</html>