<?php
/* closetHome.php
 * This file is the main page of the Closet section of the application. 
 * It displays the user's closet, which is a collection of clothing items. 
 * Users can view their items by category and add new items to their closet.
 */
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../db.php";

if (!isset($_SESSION["username"])) {
    header("LOCATION:../index.php");
    exit; // Important: Stop further execution after redirect
}

//Handle delete button being pressed
if (isset($_POST["deleteItem"])) {
    $itemName = $_POST["deleteItem"];
    $success = delete_clothing_item($_SESSION["username"], $itemName);
   if ($success) 
   {
        $message = "<p style='color:green;'>Clothing Item Deleted!</p>";
    } else 
	{
        $message = "<p style='color:red;'>Failed to delete clothing!</p>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Closet-Dex | Closet</title>
	<link rel="stylesheet" href="../style.css">
	<link rel="icon" type="image/png" href="../logo-icon.png">
</head>

<body>
	<div id="logout">
		<?php echo htmlspecialchars($_SESSION["username"]); ?>
		<a href="../logout.php">
			<button class="small-button">Logout</button>
		</a>
	</div>

	<div id="exit">
		<a href="../main.php">
			<button class="small-button">Exit Closet</button>
		</a>
	</div>

	<h1 align="center">Your Closet</h1>
	<div style="text-align:center;"><a href="addClothingItem.php">
		<button>Add Items</button>
	</a></div>

	<div id="center-tall">

    <?php
    
    //Category names cannot be changed
    $categoryNames = array("Headwear", "Top", "Outerwear", "Bottom", "Footwear", "Dress", "Accessories");
    
    //For the outer loop, go through each category and display the drop down for it
    foreach ($categoryNames as $catName) {
         echo "<details>";
         echo "<summary>";
         echo $catName;
         echo "</summary>";
         echo "<div class='item-list'>";  
       
        $items = get_items_for_user($catName, $_SESSION["username"]); //Call items function to get the items in the category

        //For each item inside the category, display to screen with its description
        foreach ($items as $item) {
            $fileWithExtension = str_replace(" ", "%20","../ClothingImages/" . findImage($item["username"] . "_" . $item["cName"]));
            echo "<img src=". $fileWithExtension." alt='" . htmlspecialchars($item["cName"]) . "' style='width:200px;height:200px;'>";
            echo "<br>" . htmlspecialchars($item["cName"]);
            echo "<br><br>";
			
			//Delete item functionality
			echo "<form method='post' action='closetHome.php' style='display:inline-block; margin-top:5px;'>";
			echo "<input type='hidden' name='deleteItem' value='" . htmlspecialchars($item["cName"]) . "'>";
			echo "<input type='submit' value='Delete' class='small-button' onclick='return confirm(\"Are you sure you want to delete this item?\");'>";
			echo "</form>";
		}

        //If There are no items in the category, show message
        if ($items == NULL) {
        echo "No items in this category!";
        }
        
	echo "</div>";
        echo "</details>";
    }
    ?>

</div>
<?php 
    if (!empty($message)) {
        echo "<div style='text-align:center; margin-top:20px;'>$message</div>";
    }
?>
</body>
</html>
