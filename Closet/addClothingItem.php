<?php
session_start();
?>

<html>

<head>
	<link rel="stylesheet" href="../style.css">
</head>

<p>Input a item to add to your closet</p>

<?php
require "../db.php";

$returnMsg = "";

if (!isset($_SESSION["username"])) {
    header("LOCATION:../index.php");
}

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

    <?php

    if ($returnMsg != "") {
        echo "<p>$returnMsg</p>";
    }
    ?>
</form>
</html>
