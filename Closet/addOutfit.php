'<?php
session_start();
?>

<html>
<p>Create an outfit</p>


<?php
if (!isset($_SESSION["username"])) {
	header("LOACTION:../index.php");
}
?>


<form action="addOutfit.php" method="post">
    <label for="outfitName">Outfit Name:</label>
    <input type="text" id="outfitName" name="outfitName">
    <br>

    <label for="outfitImage">Image:</label>
    <input type="file" id="item" name="item" accept="image/*">
    <br>

    


    <input type="submit" value="Add Outfit" name="addOutfit">
    <br>





</html>
