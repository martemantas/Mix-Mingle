<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="forgot-pass.css">
</head>
<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <form method="post" action="send-password-reset.php">
            <label for="email">Please insert your email below</label>
            <input type="email" name="email" id="email" required>
            <button>Send Email</button>
        </form>
        <button class="back"><a href="login.php">Go Back</button>
    </div>
</body>
</html>