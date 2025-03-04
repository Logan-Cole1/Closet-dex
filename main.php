<?php
session_start();
?>

<html>

<p>Welcome to our the main page.</p>

<?php
require "db.php";
?>

<?php
if (!isset($_SESSION["username"])) {
	header("LOCATION:login.php");
} else {
	echo '<p align="right"> Welcome '. $_SESSION["username"].'</p>';
?>

<form action="login.php" method="post">
<p align="right">
<input type="submit" value="logout" name="logout">
</p>
</form>

<?php
}
?>