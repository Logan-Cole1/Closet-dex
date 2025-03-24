<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("LOCATION:../index.php");
    // exit; // Important: Stop further execution after redirect
}
?>


<html>
<head>
    <title>Your Closet</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>


<p>This is your closet</p>


<?php
   
require "../db.php";
    
    //Category names cannot be changed
    $categoryNames = array("Headwear", "Top", "Outerwear", "Bottom", "Footwear", "Dress", "Accessories");
    
    //For the outer loop, go through each category and display the drop down for it
    foreach ($categoryNames as $catName) {
         echo "<details>";
         echo "<summary>";
         echo $catName;
         echo "</summary>";  
       
        $items = get_items_for_user($catName, $_SESSION["username"]); //Call items function to get the items in the category

        //For each item inside the category, display to screen with its description
        foreach ($items as $item) {
            $fileWithExtension = str_replace(" ", "%20","../ClothingImages/" . findImage($item["username"] . "_" . $item["cName"]));
            echo "<img src=". $fileWithExtension." alt='" . htmlspecialchars($item["cName"]) . "' style='width:200px;height:200px;'>";
            echo "<br>";
            echo "<p>" . htmlspecialchars($item["cName"]) . "</p>";
            echo "<br>";
            echo "<p>" . htmlspecialchars($item["category"]) . "</p>";
        }

        //If There are no items in the category, show message
        if ($items == NULL) {
        echo "<p> No items in this category!</p>";
        }
        
        echo "</details>";
    }
   
   

?>


<form action="addClothingItem.php" method="post">
    <p align="left">
        <input type="submit" value="additems" name="additems">
    </p>
</form>


<form action="../index.php" method="post">
    <input type="submit" value="logout" name="logout">
</form>


</body>
</html>

