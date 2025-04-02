<?php
/* outfitHome.php
 * This file is the main page of the outfits section of the application. 
 * It displays all the outfits that the user has created. 
 * It also provides a link to the addOutfit page.
 */
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../db.php";

if (!isset($_SESSION["username"])) {
	header("LOACTION:../index.php");
    exit;
}

if (isset($_POST["addToCategory"])){
    addOutfitToCategory($_SESSION["username"], $_POST["outfitName"], $_POST["categoryName"]);
    echo "<p style='color:green;'>Outfit added to category successfully!</p>";
}
?>

<!DOCTYPE html>
<html>
<head>Welcome to the outfits page! (>^w^<)</head>
</br>
<button onclick="history.go(-1);">Back</button>

<body>

    <form action="addOutfit.php" method="post">
        <p align="left">
            <input type="submit" value="addOutfitPage" name="addOutfitPage">
        </p>
    </form>

    <?php
    if (isset($_POST["categoryCreate"])) {
    ?>
        <form action="outfitHome.php" method="post">
            <label for="categoryName">Category Name:</label>
            <input type="text" id="categoryName" name="categoryName">
            <br>
            <input type="submit" value="Add Category" name="addCategory">
        </form>
    <?php
    } else {
    ?>
        <form action='outfitHome.php' method='post'>
            <input type='submit' value='Create Category' name='categoryCreate'>
        </form>
    <?php
    }
    if (isset($_POST["addCategory"])) {
        $categoryName = $_POST["categoryName"];
        if (empty($categoryName)) {
            echo "<p style='color:red;'>Please enter a category name.</p>";
        } else {
            $result = create_category($_SESSION["username"], $categoryName);
            if ($result) {
                echo "<p style='color:green;'>Category created successfully!</p>";
            } else {
                echo "<p style='color:red;'>Failed to create category.</p>";
            }
        }
    }
    ?>


    <?php

    $outfits = get_outfits_for_user($_SESSION["username"]); //Call outfits function
    $categoryNames = array("Headwear", "Top", "Outerwear", "Bottom", "Footwear", "Dress", "Accessories");


    foreach($outfits as $outfit) {

        $outfitItems = get_outfit_items($outfit["oName"], $_SESSION["username"]);

        echo "<details>";
        echo "<summary>";
        echo $outfit["oName"] ."<br>";
        $outfitImage = findImage("OUTFIT_".$outfit["username"] . "_" . $outfit["oName"]);
        $fileWithExtension = str_replace(" ", "%20","../ClothingImages/" . $outfitImage);
        echo "<img src=". $fileWithExtension." alt='" . htmlspecialchars($outfit["oName"]) . "' style='width:200px;height:200px;'>";
        
        // Form to add outfit to a category
        ?>
        <form action="outfitHome.php" method="post">
            <input type="hidden" name="outfitName" value="<?php echo htmlspecialchars($outfit["oName"]); ?>">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($_SESSION["username"]); ?>">
            <select name="categoryName">
                <?php
                // Display the categories in the dropdown
                $categoryNames = get_outfit_categories($_SESSION["username"]);
                foreach ($categoryNames as $row) {
                    $categoryName = $row["category"];
                    echo "<option value='" . htmlentities($categoryName) . "'>" . htmlentities($categoryName) . "</option>";
                }
                ?>
            </select>
            <input type="submit" name="addToCategory" value="Add to Category">
        </form>
        <?php
        echo "</summary>";

        foreach ($outfitItems as $outfitItem) {
            $clothingItem = get_clothing_item($outfitItem["cName"], $outfitItem["username"]);
            $fileWithExtension = str_replace(" ", "%20","../ClothingImages/" . findImage($outfitItem["username"] . "_" . $outfitItem["cName"]));
            echo "<img src=". $fileWithExtension." alt='" . htmlspecialchars($clothingItem["cName"]) . "' style='width:200px;height:200px;'>";
            echo "<br>";
            echo "<p>" . htmlspecialchars($clothingItem["cName"]) . "</p>";
            echo "<br>";
            echo "<p>" . htmlspecialchars($clothingItem["category"]) . "</p>";
        }
        echo "</details>";

    }
    ?>
</body>
</html>