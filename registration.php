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

<?php
require "db.php";

if (isset($_POST["register"])) {
	$check = register_user($_POST['username'], $_POST['password'], $_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['address']);

	if( $check == false ) {
		echo '<p style="color:red">Username ' . $_POST['username'] . ' already exists. Try again.</p>';
	} else {
		header("LOCATION:index.php");
	}
}

if (isset($_POST["cancel"])) {
	header("LOCATION:index.php");
}

?>