<?php
/* registration.php
 * This file is the registration page of the application.
 * It allows users to create a new account by entering a username and password.
 * If the user already exists, an error message is displayed.
 */

 require_once __DIR__ . "/db.php";

if (isset($_POST["register"])) {
	$check = register_user($_POST['username'], $_POST['password']);

	if( $check == false ) {
		echo '<p style="color:red">Username ' . $_POST['username'] . ' already exists. Try again.</p>';
	} else {
		header("LOCATION:index.php");
		exit;
	}
}

if (isset($_POST["cancel"])) {
	header("LOCATION:index.php");
	exit;
}

?>

<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="style.css"></link>
	<title>Closet-Dex | Register Account</title>
</head>

<body>


<div id="center-box">
<img id="login-page-logo" src="logo.png">
<div id="login-box">

<form action="registration.php" method="post">
	<input class="textbox" placeholder="Username" type="text" name="username">
<br>
	<input class="textbox" placeholder="Password" type="password" name="password">
<br>
	<input class="button" type="submit" value="Register" name="register">
	<input class="button" type="submit" value="Cancel" name="cancel">
</form>
</div></div>
</body>
</html>
