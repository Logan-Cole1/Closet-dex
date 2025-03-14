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
        echo "</details>";
    }
   
   
if (!isset($_SESSION["username"])) {
    header("LOCATION:../login.php");
    exit; // Important: Stop further execution after redirect
}
?>


<form action="addClothingItem.php" method="post">
    <p align="left">
        <input type="submit" value="additems" name="additems">
    </p>
</form>


<?php
$dbh = connectDB();


$stmt = $dbh->prepare("SELECT * FROM clo_clothing_items WHERE username = :username");
$stmt->bindParam(':username', $_SESSION["username"]);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>


</body>
</html>

