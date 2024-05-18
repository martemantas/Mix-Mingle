<?php
require 'config.php';

// Check if session id is set
if (!empty($_SESSION["id"])) {
    $sessionID = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$sessionID'");
    $row = mysqli_fetch_assoc($result);
    $user = $row["user_id"];  
}

// Get products from the database
$queryProducts = "SELECT * FROM product ORDER BY name";

// Get units from the database
$queryUnits = "SELECT * FROM units";

//
$userID = $_SESSION['id'];
$queryItems = "SELECT user_items.list_id as list_id, user_items.type as type, user_items.fk_product_id as product_id, user_items.fk_user_id as user_id, product.name as name
                FROM user_items
                INNER JOIN product ON product.product_id = user_items.fk_product_id
                WHERE `fk_user_id` = $userID
                ORDER BY name";

$resultProducts = $conn->query($queryProducts);
$resultUnits = $conn->query($queryUnits);
$resultItems = $conn->query($queryItems);

// Check if both queries were successful
if ($resultProducts && $resultUnits && $resultItems) {
    $productArray = $resultProducts->fetch_all(MYSQLI_ASSOC);

    $unitsArray = $resultUnits->fetch_all(MYSQLI_ASSOC);

    $itemArray = $resultItems->fetch_all(MYSQLI_ASSOC);

    foreach($itemArray as $item) {
        $productID[] = $item['product_id'];
    }
    
} else {
    // Handle the error
    echo "Error: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item-data'])) {
    $items = json_decode($_POST['item-data'], true);

    if ($items) {
        foreach ($items as $item) {
            $productIDS = intval($item['id']);
            $type = intval($item['type']);
            //echo $productIDS;
            // Perform your insert or update operations here
            // Example: Update the type of the item in the database
            if(count($itemArray) > 0)
            {
                if(in_array($productIDS, $productID))
                {
                    $stmt = $conn->prepare("UPDATE user_items SET type = ? WHERE fk_user_id = ? AND fk_product_id = ?");
                    $stmt->bind_param('iii', $type, $userID, $productIDS);
                    $stmt->execute();
                }
                else
                {
                    $stmt = $conn->prepare("INSERT INTO user_items (type, fk_product_id, fk_user_id ) VALUES (?, ?, ?)");
                    $stmt->bind_param('iii', $type, $productIDS, $userID);
                    $stmt->execute();
                }
            }
            else {
                $stmt = $conn->prepare("INSERT INTO user_items (type, fk_product_id, fk_user_id ) VALUES (?, ?, ?)");
                $stmt->bind_param('iii', $type, $productIDS, $userID);
                $stmt->execute();
            }
        }

        foreach($itemArray as $item)
        {
            $itemID = intval($item['product_id']);  

            foreach($items as $itm) {
                $productID1[] = $itm['id'];
            }

            if(!in_array($itemID, $productID1))
            {
                $stmt = $conn->prepare("DELETE FROM user_items WHERE fk_user_id = ? AND fk_product_id = ?");
                $stmt->bind_param('ii', $userID, $itemID);
                $stmt->execute();
            }
        }

        header("Location: ownedList.php");
        echo "<script>alert('Updated.')</script>";
    } else {
        if(count($itemArray) > 0){
            foreach($itemArray as $item)
            {
                $itemID = intval($item['product_id']);  

                $stmt = $conn->prepare("DELETE FROM user_items WHERE fk_user_id = ? AND fk_product_id = ?");
                $stmt->bind_param('ii', $userID, $itemID);
                $stmt->execute();
            }
        }

        header("Location: ownedList.php");
        echo "<script>alert('Updated.')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="style.css">
    <title>Owned Items</title>
    <style>
        button {
            border-radius: 10px;
            background-color: var(--fourth-bg-color);
            color: white;
            padding: 14px 20px;
            margin-left: 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .draggable-container {
            min-height: 200px;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .draggable {
            padding: 5px;
            margin: 5px;
            background-color: #f0f0f0;
            cursor: move;
        }
        .container {
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <nav>
        <div class="hamburger">
            <span class="line"></span>
            <span class="line"></span>
            <span class="line"></span>
        </div>
        <ul>
            <li><a href="home.php">Home</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1 class="title">Ingredients</h1>
        <!-- Container for draggable items -->
        <div class="draggable-container" id="draggable-container-grey" style="background-color: grey;">
            <h4 class="title" style="font-size: 20px; text-align: left; padding-left: 10px">All items:</h4>
            <?php
            // Loop through productArray to create draggable items
            foreach ($productArray as $product) {
                if(count($itemArray) > 0)
                {
                    if (!in_array($product['product_id'], $productID)) {
                        echo '<div class="draggable" id="grey-' . $product['product_id'] . '">' . $product['name'] . '</div>';
                    }
                }
                else {
                    echo '<div class="draggable" id="grey-' . $product['product_id'] . '">' . $product['name'] . '</div>';
                }
            }
            ?>
        </div>

        <div class="draggable-container" id="draggable-container-shop" style="background-color: yellow;">
            <h4 class="title" style="font-size: 20px; text-align: left; padding-left: 10px">Shopping list:</h4>
            <?php
                foreach ($itemArray as $item) {
                    if ($item['type'] == 1) {
                        echo '<div class="draggable" id="shop-' . $item['product_id'] . '">' . $item['name'] . '</div>';
                    }
                }
            ?>
        </div>

        <div class="draggable-container" id="draggable-container-ownd" style="background-color: green;">
            <h4 class="title" style="font-size: 20px; text-align: left; padding-left: 10px">Owned items:</h4>
            <?php
                foreach ($itemArray as $item) {
                    if ($item['type'] == 2) {
                        echo '<div class="draggable" id="ownd-' . $item['product_id'] . '">' . $item['name'] . '</div>';
                    }
                }
            ?>
        </div>

        <!-- Form and Save Button -->
        <form id="saveForm" action="ownedList.php" method="post">
            <input type="hidden" id="item-data" name="item-data">
            <button type="button" onclick="saveAllItems()">Save</button>
        </form>
    </div>
    
    <footer style="position:fixed; bottom:0;">
        <p style="border-top: none;">Tai nėra komercinis projektas, darbas atliktas mokymosi tikslais Manto ir Mariaus @KTU</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

    <script>
        $(document).ready(function() {
            // Make draggable items draggable
            $(".draggable").draggable({
                revert: "invalid", // Snap back if not dropped on droppable
                cursor: "move"
            });

            // Make containers droppable
            $(".draggable-container").droppable({
                accept: ".draggable",
                drop: function(event, ui) {
                    $(this).append(ui.draggable.css({top: "", left: ""}));
                }
            });
        });

        // Function to collect draggable item IDs and post them in form
        function saveAllItems() {
            var items = [];

            // Collect item IDs and types for each container
            $("#draggable-container-shop .draggable").each(function() {
                items.push({ id: $(this).attr("id").substring(5), type: 1 }); // Remove prefix "shop-"
            });
            $("#draggable-container-ownd .draggable").each(function() {
                items.push({ id: $(this).attr("id").substring(5), type: 2 }); // Remove prefix "owned-"
            });

            // Debugging: log the collected items to the console
            console.log(items);

            // Add the array as a hidden input field in the form
            $("#item-data").val(JSON.stringify(items));

            // Submit the form
            $("#saveForm").submit();
        }
    </script>
</body>
</html>
