
<?php
require "db.php";

session_start();

if (isset($_POST["login"])) {
	if (authenticate_customer($_POST["username"], $_POST["password"]) == 1) {
		$_SESSION["username"]=$_POST["username"];
		header("LOCATION:main.php");
		return;
	} else {
		echo '<p style="color:red">Incorrect username or password</p>';
	}
}


if (isset($_POST["register"])) {
	header("LOCATION:registration.php");
}
?>


<html>
<head>
    <link rel="stylesheet" href="style.css">
</head>




<body>
    <div id="center-box">
        <img id="login-page-logo" src="logo.png">
        <div id="login-box">
            <form action="index.php" method="post">
                <input class="textbox" type="text" name="username" placeholder="Your Name"><br>
                <input class="textbox" type="password" name="password" placeholder="Your Password"><br>
                <input class="button" type="submit" value="Login" name="login">
                <input class="button" type="submit" value="Register" name="register">
            </form>
        </div>
    </div>

</body>
</html>

