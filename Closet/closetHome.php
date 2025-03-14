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

<p>Closet items</p>

<?php
$dbh = connectDB();

$stmt = $dbh->prepare("SELECT * FROM clo_clothing_items WHERE username = :username");
$stmt->bindParam(':username', $_SESSION["username"]);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
    echo "<img src='../ClothingImages/" .$item["id"] . ".jpg' alt='" . htmlspecialchars($item["cName"]) . "' style='width:100px;height:100px;'>";
    echo "<br>";
    echo "<p>" . htmlspecialchars($item["cName"]) . "</p>";
    echo "<br>";
    echo "<p>" . htmlspecialchars($item["category"]) . "</p>";
}
?>

</body>
</html>
