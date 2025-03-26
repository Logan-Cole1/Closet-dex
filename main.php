<?php
session_start();
?>

<html>

	<head>
		<link rel="stylesheet" href="style.css">
		<title>Main Page</title>
	</head>

	<body>

<?php
require "db.php";

if (!isset($_SESSION["username"])) {
	header("LOCATION:index.php");
} else { 
	echo '<div id="logout">'. $_SESSION["username"].' <form action="index.php" method="post"><input type="submit", value="Logout" name="logout"></div></form>';
?>
	<div id="center-box">
		<h1>Welcome!</h1>
		<div id="login-box">

<a href="Closet/closetHome.php"><button class="button">Closet</button></a>
<a href="Outfits/outfitHome.php"><button class="button">Wardrobe</button></a>
</div></div>
</body>
</html>
<?php
}
?>
