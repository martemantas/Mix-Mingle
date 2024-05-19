<?php
require 'config.php';

$creatorId = isset($_POST['creatorId']) ? (int)$_POST['creatorId'] : 0;
$query = "SELECT username FROM users WHERE user_id = $creatorId";
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode(['username' => $row['username']]);
} else {
    echo json_encode(['username' => null]);
}

mysqli_close($conn);
?>
