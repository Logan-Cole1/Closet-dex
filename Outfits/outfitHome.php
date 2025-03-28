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
?>

<!DOCTYPE html>
<html>
<head>Welcome to the outfits page! (>^w^<)</head>

<body>

    <form action="addOutfit.php" method="post">
        <p align="left">
            <input type="submit" value="addOutfitPage" name="addOutfitPage">
        </p>
    </form>


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

    }
    ?>
</body>
</html>