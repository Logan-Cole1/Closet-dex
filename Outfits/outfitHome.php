<?php
/* outfitHome.php
 * This file is the main page of the outfits section of the application. 
 * It displays all the outfits that the user has created. 
 * It also provides a link to the addOutfit page.
 */
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../db.php";

if (!isset($_SESSION["username"])) {
	header("LOACTION:../index.php");
    exit;
}

if (isset($_POST["addToCategory"])){
    addOutfitToCategory($_SESSION["username"], $_POST["outfitName"], $_POST["categoryName"]);
    echo "<p style='color:green;'>Outfit added to category successfully!</p>";

}
if (isset($_POST["randOutfit"])){
    $check = createRandOutfit($_SESSION["username"], $_POST["oName"]);
    if ($check == true) {   
        echo "<p style='color:green;'>Outfit created successfully!</p>";
    } else {
        echo "<p style='color:red;'>Failed to create outfit.</p>";
    }
}
?>

<!DOCTYPE html> 
<html>
<head>
	<title>Closet-Dex | Wardrobe</title>
	<link rel="stylesheet" href="../style.css">
</head>

<body>

	<div id="logout">
		<?php echo htmlspecialchars($_SESSION["username"]); ?>
		<a href="../logout.php">
			<button class="small-button">Logout</button>
		</a>
	</div>

	<div id="exit">
	<a href="../main.php" class="small-button">Exit Wardrobe</a>
	</div>

	<h1 align="center">Your Wardrobe</h1>
	<div style="text-align:center;"><a href="addOutfit.php">
		<button>Add Outfit</button>
	</a></div>

	<div id="center-tall">

        <form action='outfitHome.php' method='post'>
            <input type="text" name="oName" placeholder="Outfit Name" required>
            <br>
            <input type='submit' value='Generate Random Outfit' name='randOutfit'>
        </form>

    <?php
    if (isset($_POST["categoryCreate"])) {
    ?>
        <form action="outfitHome.php" method="post">
            <label for="categoryName">Category Name:</label>
            <input type="text" id="categoryName" name="categoryName">
            <br>
            <input type="submit" value="Add Category" name="addCategory">
        </form>
    <?php
    } else {
    ?>
        <form action='outfitHome.php' method='post'>
            <input type='submit' value='Create Category' name='categoryCreate'>
        </form>
    <?php
    }
    if (isset($_POST["addCategory"])) {
        $categoryName = $_POST["categoryName"];
        if (empty($categoryName)) {
            echo "<p style='color:red;'>Please enter a category name.</p>";
        } else {
            $result = create_category($_SESSION["username"], $categoryName);
            if ($result) {
                echo "<p style='color:green;'>Category created successfully!</p>";
            } else {
                echo "<p style='color:red;'>Failed to create category.</p>";
            }
        }
    }
    ?>


    <?php
    $oCategories = get_outfit_categories($_SESSION["username"]);

    foreach ($oCategories as $row) {

        echo "<details>";
        echo "<summary class='outfit-summary'>";
        echo $row["category"];
        echo "</summary>";
	echo "<div class='outfit-list'>";

        $outfits = get_outfits_for_user($_SESSION["username"], $row["category"]); // Call outfits function
        $categoryNames = ["Headwear", "Top", "Outerwear", "Bottom", "Footwear", "Dress", "Accessories"];

        // Reorder outfits based on $categoryNames
        usort($outfits, function($a, $b) use ($categoryNames) {
            $posA = array_search($a['category'], $categoryNames);
            $posB = array_search($b['category'], $categoryNames);
            return $posA - $posB;
        });

        foreach($outfits as $outfit) {
            $outfitItems = get_outfit_items($outfit["oName"], $_SESSION["username"]);

            $outfitImage = findImage("OUTFIT_".$outfit["username"] . "_" . $outfit["oName"]);
            $fileWithExtension = str_replace(" ", "%20","../ClothingImages/" . $outfitImage);
            echo "<img src=". $fileWithExtension." alt='" . htmlspecialchars($outfit["oName"]) . "' style='width:200px;height:200px;'>";

            // Form to add outfit to a category
            ?>
            <form action="outfitHome.php" method="post">
                <input type="hidden" name="outfitName" value="<?php echo htmlspecialchars($outfit["oName"]); ?>">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($_SESSION["username"]); ?>">
                <select name="categoryName">
                    <?php
                    // Display the categories in the dropdown
                    $categoryNames = get_outfit_categories($_SESSION["username"]);
                    foreach ($categoryNames as $row) {
                        $categoryName = $row["category"];
                        echo "<option value='" . htmlentities($categoryName) . "'>" . htmlentities($categoryName) . "</option>";
                    }
                    ?>
                </select>
                <input type="submit" name="addToCategory" value="Add to Category">
            </form>
            <?php
            echo "<details>";
            echo "<summary>";
            echo $outfit["oName"];
            echo "</summary>";
        echo "<div class='item-list'>";

            foreach ($outfitItems as $outfitItem) {
                $clothingItem = get_clothing_item($outfitItem["cName"], $outfitItem["username"]);
                $fileWithExtension = str_replace(" ", "%20","../ClothingImages/" . findImage($outfitItem["username"] . "_" . $outfitItem["cName"]));
                echo "<img src=". $fileWithExtension." alt='" . htmlspecialchars($clothingItem["cName"]) . "' style='width:200px;height:200px;'>";
                echo "<br>";
                echo htmlspecialchars($clothingItem["cName"]) . " (" . htmlspecialchars($clothingItem["category"]) . ")<br><br>";
            }
        echo "</div>";
            echo "</details><br><br><br>";

        }
	echo "</div>";
        echo "</details>";
    }

    //Display outfits that are not in any category
    $outfits = get_uncategorized_outfits($_SESSION["username"]);
    if (count($outfits) > 0) {
        echo "<details>";
        echo "<summary class='outfit-summary'>Uncategorized Outfits</summary>";
        echo "<div class='outfit-list'>";

        foreach ($outfits as $outfit) {
            $outfitItems = get_outfit_items($outfit["oName"], $_SESSION["username"]);
            $outfitImage = findImage("OUTFIT_".$outfit["username"] . "_" . $outfit["oName"]);
            $fileWithExtension = str_replace(" ", "%20","../ClothingImages/" . $outfitImage);
            echo "<img src=". $fileWithExtension." alt='" . htmlspecialchars($outfit["oName"]) . "' style='width:200px;height:200px;'>";
            // Form to add outfit to a category
            ?>
            <form action="outfitHome.php" method="post">
                <input type="hidden" name="outfitName" value="<?php echo htmlspecialchars($outfit["oName"]); ?>">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($_SESSION["username"]); ?>">
                <select name="categoryName">
                    <?php
                    // Display the categories in the dropdown
                    $categoryNames = get_outfit_categories($_SESSION["username"]);
                    foreach ($categoryNames as $row) {
                        $categoryName = $row["category"];
                        echo "<option value='" . htmlentities($categoryName) . "'>" . htmlentities($categoryName) . "</option>";
                    }
                    ?>
                </select>
                <input type="submit" name="addToCategory" value="Add to Category">
            </form>
            <?php
            echo "<details>";
            echo "<summary>";
            echo $outfit["oName"];
            echo "</summary>";
            echo "<div class='item-list'>";

            foreach ($outfitItems as $outfitItem) {
                $clothingItem = get_clothing_item($outfitItem["cName"], $outfitItem["username"]);
                $fileWithExtension = str_replace(" ", "%20","../ClothingImages/" . findImage($outfitItem["username"] . "_" . $outfitItem["cName"]));
                echo "<img src=". $fileWithExtension." alt='" . htmlspecialchars($clothingItem["cName"]) . "' style='width:200px;height:200px;'>";
                echo "<br>";
                echo htmlspecialchars($clothingItem["cName"]) . " (" . htmlspecialchars($clothingItem["category"]) . ")<br><br>";
            }
            echo "</div>";
            echo "</details><br><br><br>";
        }
        echo "</div>";
        echo "</details>";
    }
    ?>
</div>
</body>
</html>
