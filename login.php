<?php
session_start();
?>

<html>

<form action="login.php" method="post">
	<label for="username">Username: </label>
	<input type="text" name="username" placeholder="Your Name">
<br>
	<label for="password" >Password: </label>
	<input type="password" name="password" placeholder="Your Password">
<br>
	<input type="submit" value="Login" name="login">
	<input type="submit" value="Register Account" name="register">
</form>
</html>

<?php
require "db.php";

if (isset($_POST["login"])) {
	if (authenticate_customer($_POST["username"], $_POST["password"]) == 1) {
		$_SESSION["username"]=$_POST["username"];
		header("LOCATION:main.php");
		return;
	} else {
		echo '<p style="color:red">incorrect username and password</p>';
	}
}


if (isset($_POST["register"])) {
	header("LOCATION:registration.php");
}

if (isset($_POST["logout"])) {
	session_destroy();
}

?>