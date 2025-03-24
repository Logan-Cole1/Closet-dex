<?php
session_start();
?>

<html>
<p>Create an outfit</p>


<?php
if (!isset($_SESSION["username"])) {
	header("LOACTION:../index.php");
}
?>


<form action="addOutfit.php" method="post" enctype="multipart/form-data">
    <label for="outfitName">Outfit Name:</label>
    <input type="text" id="outfitName" name="outfitName">
    <br>

    <label for="outfitImage">Image:</label>
    <input type="file" id="outfitImage" name="outfitImage" accept="image/*">
    <br>

    <label for="Headwear">Headwear:</label>
    <input type="text" id="Headwear" name="Headwear">
    <br>

    <label for="Top">Top:</label>
    <input type="text" id="Top" name="Top">
    <br>

    <label for="Outerwear">Outerwear:</label>
    <input type="text" id="Outerwear" name="Outerwear">
    <br>

    <label for="Bottom">Bottom:</label>
    <input type="text" id="Bottom" name="Bottom">
    <br>

    <label for="Footwear">Footwear:</label>
    <input type="text" id="Footwear" name="Footwear">
    <br>

    <label for="Dress">Dress:</label>
    <input type="text" id="Dress" name="Dress">
    <br>

    <label for="Accessory">Accessory:</label>
    <input type="text" id="Accessory" name="Accessory">
    <br>


    <input type="submit" value="Add Outfit" name="addOutfit">
    <br>
</form>

<?php
require "../db.php";

if (isset($_POST["addOutfit"])) {
    $outfitName = $_POST["outfitName"];

    if(!createOutfit($_SESSION["username"], $outfitName)) {
        echo "Error creating outfit";
    } else{
        // Move oImage to the correct directory
        $fileExtension = pathinfo($_FILES["outfitImage"]["name"], PATHINFO_EXTENSION);
        $newFilePath = "../ClothingImages/" . "OUTFIT" ."_". $_SESSION["username"] . "_" . $_POST["outfitName"] . "." . $fileExtension;
        if (!move_uploaded_file($_FILES["outfitImage"]["tmp_name"], $newFilePath)) { // Use move_uploaded_file()
            return false;  
        }
        $categorys = array("Headwear", 
                            "Top", 
                            "Outerwear",
                            "Bottom",
                            "Footwear", 
                            "Dress",
                            "Accessories");
        foreach ($categorys as $category) {
            $cName = $_POST[$category];
            if ($cName != "") {
                if(!addOutfitItem($_SESSION["username"], $outfitName, $cName, $category)){
                    echo "Error adding item " . $cName . " to outfit: Item of worng category.";
                }
            }
        }
        echo "Outfit added successfully";
    }
    
}

?>


</html>
