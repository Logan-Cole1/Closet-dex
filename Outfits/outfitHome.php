<html>

<?php
session_start();
if (!isset($_SESSION["username"])) {
	header("LOACTION:../index.php");
}
?>

<head>Welcome to the outfits page! (>^w^<)</head>




<form action="addOutfit.php" method="post">
    <p align="left">
        <input type="submit" value="addOutfitPage" name="addOutfitPage">
    </p>
</form>


<?php
require "../db.php";


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


</html>