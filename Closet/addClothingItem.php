<?php
/* addClothingItem.php
 * This file allows users to add a clothing item to their closet. 
 * Users can input the name of the item, select the category of the item, and upload an image of the item. 
 * The item is then added to the database and the image is stored in the ClothingImages folder.
 */
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../db.php";

if (!isset($_SESSION["username"])) {
    header("LOCATION:../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="../style.css">
	<title>Closet-Dex | Add Item</title>
	<link rel="icon" type="image/png" href="../logo-icon.png">
</head>

<body>

    <p>Input a item to add to your closet</p>

    <?php

    $returnMsg = "";

    if (isset($_POST["addItem"])) {
        $dbh = connectDB();
        $itemName = $_POST["itemName"];
        $category = $_POST["category"];

        $dbh->beginTransaction();

        $statement = $dbh->prepare("call clo_insert_clothing(:username, :itemName, :category)");
        $statement->bindParam(':username', $_SESSION["username"]);
        $statement->bindParam(':itemName', $itemName);
        $statement->bindParam(':category', $category);
        $result = $statement->execute();

        if ($result) {
            $fileExtension = pathinfo($_FILES["item"]["name"], PATHINFO_EXTENSION);
            $newFilePath = "../ClothingImages/".$_SESSION["username"] . "_" . $itemName . "." . $fileExtension;

            if (move_uploaded_file($_FILES["item"]["tmp_name"], $newFilePath)) { // Use move_uploaded_file()
                $dbhresult = $dbh->commit();
                if ($dbhresult) {
                    $dbh=null;
                    $returnMsg = "Item added successfully";
                    header("LOCATION:closetHome.php");
                } else {
                    $dbh->rollBack();
                    $returnMsg = "Error moving item"; //more accurate error message.
                }
            } else {
                $dbh->rollBack();
                $returnMsg = "Error moving item"; //more accurate error message.
            }
        } else {
            $dbh->rollBack();
            $returnMsg = "Error adding item to database";
        }

    }
    ?>
    <form action="addClothingItem.php" method="post" enctype="multipart/form-data">
        <label for="itemName">Item Name:</label>
        <input type="text" id="itemName" name="itemName">
        <br>
        <label for="item">Item Image:</label>
        <input type="file" id="item" name="item" accept="image/*">
        <br>
        <label for="itemType">Item Type:</label>
        <select id="itemType" name="category">
            <option value="headwear">Headwear</option>
            <option value="top">Top</option>
            <option value="outerwear">Outerwear</option>
            <option value="dress">Dress</option>
            <option value="bottom">Bottom</option>
            <option value="footwear">Footwear</option>
            <option value="accessories">Accessory</option>
        </select>
        <input type="submit" value="Add Item" name="addItem">
        <br>
    </form>
		<a href="closetHome.php" class="small-button">Cancel</a>
    <?php

    if ($returnMsg != "") {
        echo "<p>$returnMsg</p>";
    }
    ?>
    
</body>
</html>
