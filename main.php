<?php
session_start();
?>

<html>

<head>
	<link rel="stylesheet" href="styles/style.css">
</head>

<p>Welcome to our the main page.</p>

<?php
require "db.php";

if (!isset($_SESSION["username"])) {
	header("LOCATION:index.php");
} else {
	echo '<p align="right"> Welcome '. $_SESSION["username"].'</p>';
?>

<form action="index.php" method="post">
	<p align="right">
		<input type="submit" value="logout" name="logout">
	</p>
</form>

<form action="Closet/closetHome.php" method = "post">
	<p align="center">
		<input type="submit" value="View Closet" name="closet">
	</p>
</form>

<form action="Outfits/outfitHome.php" method = "post">
	<p align="center">
		<input type="submit" value="View Outfits" name="outfits">
	</p>

<?php
}
?>
