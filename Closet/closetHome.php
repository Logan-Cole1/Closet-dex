<?php
session_start();
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

    $categoryNames = array("Headwear", "Top", "Outerwear", "Bottom", "Footwear", "Dress", "Accessories");
    foreach ($categoryNames as $cName) {
         echo "<details>";
         echo "<summary>";
         echo $cName;
         echo "</summary>";  
       
        $items = get_items($cName);

        foreach ($items as $item) {
            $fileWithExtension = str_replace(" ", "%20","../ClothingImages/" . findClothingImage($item["username"] . "_" . $item["cName"]));
            echo "<img src=". $fileWithExtension." alt='" . htmlspecialchars($item["cName"]) . "' style='width:200px;height:200px;'>";
            echo "<br>";
            echo "<p>" . htmlspecialchars($item["cName"]) . "</p>";
            echo "<br>";
            echo "<p>" . htmlspecialchars($item["category"]) . "</p>";
        }

        if ($items == NULL) {
        echo "<p> No items in this category!</p>";
        }
        
        echo "</details>";
    }
   
   
if (!isset($_SESSION["username"])) {
    header("LOCATION:../index.php");
    exit; // Important: Stop further execution after redirect
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

