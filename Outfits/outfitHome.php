<html>

<?php
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

</html>