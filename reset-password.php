<?php

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/config.php";

$sql = "SELECT * FROM users
        WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    header("Location: process-reset-password.php");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Sorry, your token has expired");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="forgot-pass.css">
</head>
<body>
    <div class="container">
        <form id="resetPasswordForm" method="post" action="process-reset-password.php">
            <h1>Reset Password</h1>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        
        <label for="password">Please insert new password</label>
        <input type="password" id="password" name="password">
        
        <label for="password_confirmation">Please repeat your password</label>
        <input type="password" id="password_confirmation" name="password_confirmation">
        
        <button id="resetPasswordBtn">Send</button>
    </form>
</div>
</body>
<script>
        document.getElementById("resetPasswordBtn").addEventListener("click", function(event) {
            event.preventDefault();

            var password = document.getElementById("password").value;
            var passwordConfirmation = document.getElementById("password_confirmation").value;

            if (password.length < 8) {
                alert("Password must be at least 8 characters");
                return;
            }

            if (!/[a-zA-Z]/.test(password)) {
                alert("Password must contain at least one letter");
                return;
            }

            if (!/\d/.test(password)) {
                alert("Password must contain at least one number");
                return;
            }

            if (password !== passwordConfirmation) {
                alert("Passwords must match");
                return;
            }

            document.getElementById("resetPasswordForm").submit();
        });
    </script>
</html>