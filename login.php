<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    header("Location: home.php");
}
if(isset($_POST["submitlogin"])){
    $loginname = $_POST["email"];
    $password = $_POST["password"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$loginname'");
    $row = mysqli_fetch_assoc($result);
    if(mysqli_num_rows($result) > 0){
        if(password_verify($password, $row['password'])){
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["user_id"];
            header("Location: home.php");
        }
        else{
            echo "<script>alert('Wrong password.')</script>";
        }
    }
    else{
        echo "<script>alert('E-mail does not exist.')</script>";
    }
}
if(isset($_POST["submitsignup"])){
   $name = $_POST["username"];
   $email = $_POST["email"];
   $password = $_POST["password"];
   $confirm_password = $_POST["confirm_password"];
   
   $dublicate = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

   if(mysqli_num_rows($dublicate) > 0){
       echo "<script>alert('This e-mail is already in use.')</script>";
   }
   else{
       if($password == $confirm_password){
           $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
           $query = "INSERT INTO users VALUES('', '$name', '$password', '$email', '1', NULL, NULL)";
           mysqli_query($conn,$query);
           echo "<script>alert('Registered successfully.')</script>";
           //header("Location: login.php");
       }
       else{
           echo "<script>alert('Passwords do not match.')</script>";
       }
   }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Login</title>
      <link rel="stylesheet" href="style.css">
      <link rel="stylesheet" href="login.css">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
         echo '</ul>';
         echo '<li class="login"><a href="login.php">Login</a></li>';
      echo '</nav>';
      ?>
      <div class="wrapper">
         <div class="title-text">
            <div class="title login">
               Login Form
            </div>
            <div class="title signup">
               Signup Form
            </div>
         </div>
         <div class="form-container">
            <div class="slide-controls">
               <input type="radio" name="slide" id="login" checked>
               <input type="radio" name="slide" id="signup">
               <label for="login" class="slide login">Login</label>
               <label for="signup" class="slide signup">Signup</label>
               <div class="slider-tab"></div>
            </div>
         
            <div class="form-inner">
               <form method="POST" action="" class="login">
                  <div class="field">
                     <input type="text" name="email" placeholder="Email Address" required>
                  </div>
                  <div class="field">
                     <input type="password" name="password" placeholder="Password" required>
                  </div>
                  <div class="pass-link">
                     <a href="forgot-password.php">Forgot password?</a>
                  </div>
                  <div class="field btn">
                     <div class="btn-layer"></div>
                     <button type="submit" name="submitlogin">Login</button>
                  </div>
                  <div class="signup-link">
                     Not a member? <a href="">Signup now</a>
                  </div>
               </form>
               <form action="" method="POST" class="signup">
                  <div class="field">
                     <input type="text" name="username" placeholder="Username" required>
                  </div>
                  <div class="field">
                     <input type="text" name="email" placeholder="Email Address" required>
                  </div>
                  <div class="field">
                     <input type="password" name="password" placeholder="Password" required>
                  </div>
                  <div class="field">
                     <input type="password"  name="confirm_password" placeholder="Confirm password" required>
                  </div>
                  <div class="field btn">
                     <div class="btn-layer"></div>
                     <button type="submit" name="submitsignup">Sign-up</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </body>
   <script src="login.js"></script>
</html>