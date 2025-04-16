<?php
/* addOutfit.php
 * This file allows users to create an outfit. 
 * Users can input an outfit name, upload an image, and add items to the outfit. 
 * The outfit is then added to the database.
 */

require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../db.php";

if (!isset($_SESSION["username"])) {
    header("LOCATION:../index.php");
    exit;
}

// Define categories globally
$categories = array("Headwear", "Top", "Outerwear", "Bottom", "Footwear", "Dress", "Accessories");

// Image previews handling logic
$imagePreviews = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($categories as $category) {
        if (!empty($_POST[$category])) {
            $itemName = $_POST[$category];
            $itemImage = findImage($_SESSION["username"] . "_" . $itemName);
            $imagePreviews[$category] = $itemImage ? "../ClothingImages/$itemImage" : "../ClothingImages/default.jpg";
        } else {
            $imagePreviews[$category] = "../ClothingImages/default.jpg";
        }
    }
}

// Handle final submission
$returnMsg = "";
if (isset($_POST["addOutfit"])) {
    $returnMsg = processOutfitCreation($_SESSION["username"], $_POST, $_FILES);
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <p>Create an outfit:</p>

    <form action="addOutfit.php" method="post" enctype="multipart/form-data">
        <label for="outfitName">Outfit Name:</label>
        <input type="text" id="outfitName" name="outfitName" required>
        <br>

        <label for="outfitImage">Image:</label>
        <input type="file" id="outfitImage" name="outfitImage" accept="image/*" required>
        <br>

        <?php
        // Dynamically render dropdown menus for clothing items
        foreach ($categories as $category) {
            echo "<label for='$category'>$category:</label><br>";
            echo "<select id='$category' name='$category' onchange='this.form.submit()'>";
            echo "<option value=''>None</option>"; // Default option

            $items = get_items_for_user($category, $_SESSION["username"]);
            foreach ($items as $item) {
                $itemName = htmlentities($item["cName"]);
                $selected = (!empty($_POST[$category]) && $_POST[$category] === $itemName) ? "selected" : "";
                echo "<option value='$itemName' $selected>$itemName</option>";
            }
            echo "</select>";

            // Image preview for the dropdown selection
            $imagePath = $imagePreviews[$category] ?? "../ClothingImages/default.jpg";
            echo "<img src='$imagePath' alt='$category Preview' style='width:100px;height:100px;'><br><br>";
        }
        ?>

        <input type="submit" value="Add Outfit" name="addOutfit">
        <br>
    </form>
    <button onclick="history.go(-1);">Cancel</button>

    <?php
    // Display success or error message after submission
    if (!empty($returnMsg)) {
        echo "<p>$returnMsg</p>";
    }
    ?>
</body>
</html>