<?php
/* registration.php
 * This file is the registration page of the application.
 * It allows users to create a new account by entering a username and password.
 * If the user already exists, an error message is displayed.
 */

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

<form action="registration.php" method="post">
	<label for="username">Username:</label>
	<input type="text" name="username">
<br>
	<label for="password" >Password:</label>
	<input type="password" name="password">
<br>
	<input type="submit" value="Register Account" name="register">
	<input type="submit" value="Cancel" name="cancel">
</form>
</html>