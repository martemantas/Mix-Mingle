<?php

$token = $_POST["token"] ?? '';

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
    $message = "Sorry, your token was not found";
}
else if (strtotime($user["reset_token_expires_at"]) <= time()) {
    $message = "Sorry, your token has expired";
}
else{
    $password_hash = password_hash($_POST["password"], PASSWORD_BCRYPT);

    $sql = "UPDATE users
            SET password = ?,
                reset_token_hash = NULL,
                reset_token_expires_at = NULL
            WHERE user_id = ?";

    $stmt = $mysqli->prepare($sql);

    $stmt->bind_param("ss", $password_hash, $user["user_id"]);

    $stmt->execute();

    $message = "Password updated. You can now login.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Sent</title>
    <link rel="stylesheet" href="forgot-pass.css">
</head>
<body>
    <div class="container">
        <?php echo '<p>' . $message . '</p>'; ?>
        <button><a href="login.php">Back to login</a></button>
    </div>
</body>
</html>