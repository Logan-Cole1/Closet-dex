<?php
/* main.php
 * This file is the main page of the application. 
 * It is the page that users see after they have successfully logged in. 
 * It displays a welcome message and provides links to the Closet and Outfits pages. 
 * Users can also log out from this page.
 */
require_once __DIR__ . "/config.php";

if (!isset($_SESSION["username"])) {
	header("LOCATION:index.php");
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="style.css">
	<title>Closet-Dex | Home</title>
</head>

<body>

	<div id="logout">
		<?php echo htmlspecialchars($_SESSION["username"]); ?>
		<a href="logout.php">
			<button class="small-button">Logout</button>
		</a>
	</div>

	
	<div id="center-box">
		<h1>Welcome!</h1>
		<div id="login-box">

			<a href="Closet/closetHome.php"><button class="button">Closet</button></a>
			<a href="Outfits/outfitHome.php"><button class="button">Wardrobe</button></a>
		</div>
	</div>
</body>
</html>

