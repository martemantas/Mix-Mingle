<?php
require 'config.php';

$sql = "SELECT DISTINCT name FROM product";

$result = mysqli_query($conn, $sql);

if ($result) {
    $ingredients = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $ingredients[] = $row['name'];
    }
    echo json_encode($ingredients);
} else {
    echo json_encode(array());
}
?>
