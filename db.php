<?php
function connectDB() {
$config = parse_ini_file(__DIR__ . "/../../db.ini"); //Use __DIR__ for more reliable paths.
$dbh = new PDO($config['dsn'], $config['username'], $config['password']);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$statement = $dbh->prepare("use cfleser");
$statement->execute();
return $dbh;
}


//return number of rows matching the given user and passwd.
function authenticate_customer($user, $passwd) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT count(*) FROM clo_user ". "where username = :username and password = sha2(:passwd,256) ");
        $statement->bindParam(":username", $user);
        $statement->bindParam(":passwd", $passwd);
        $result = $statement->execute();
        $row=$statement->fetch();
        $dbh=null;
        return $row[0];
    }catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function findClothingImage($itemName): ?string {
    // Supported image extensions
    $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'heic', 'tiff', 'svg', 'ico', 
        'jfif', 'pjpeg', 'pjp'
    ];

    // Define the directory to search (relative to current file)

    // Iterate through supported extensions to find the first matching file
    foreach ($allowedExtensions as $extension) {
        $filePath = __DIR__. '/ClothingImages/' . $itemName . '.' . $extension;
        // echo $filePath;

        // Check if the file exists
        if (file_exists($filePath)) {
            return $itemName.'.'.$extension; // Return the full path of the matching file
        }
    }

    return null; // No matching file found
}


function get_items($category) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM clo_clothing_items ". "where category = :category");
        $statement->bindParam(":category", $category);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dbh=null;
        return $results;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


// not used yet
function change_password($user, $password) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("UPDATE customer set password = sha2(:password, 256) where username = :username");
        $statement->bindParam(":password", $password);
        $statement->bindParam(":username", $user);
        $statement->execute();
        $dbh=null;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function register_user($username, $password, $firstname, $lastname, $email, $address) {
    try {
        $dbh = connectDB();
        // check if username already exists in db
        $check = $dbh->prepare("SELECT COUNT(username) FROM clo_user where username = :username");
        $check->bindParam(":username", $username);
        $check->execute();
        $row=$check->fetch();
        if ($row[0] != 0) {
            return false;
        }
        $statement = $dbh->prepare("INSERT INTO clo_user (username, password) values (:username, sha2(:password, 256))");
        $statement->bindParam(":username", $username);
        $statement->bindParam(":password", $password);
        $statement->execute();
        $dbh=null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


//Keeping this for now in case I want to refence transaction syntax later - Logan
function make_order($username) {
    try {
        $dbh = connectDB();
        $dbh->beginTransaction();
        // get id
        $getid = $dbh->prepare("SELECT id from customer where username = :username");
        $getid->bindParam(":username", $username);
        $getid->execute();
        $id=$getid->fetch();
        $items = get_cart_items($username);
        // check to make sure quantities are correct
        foreach($items as $item) {
            $checkqty = $dbh->prepare("SELECT actual_stock_qty from product where id = :id");
            $checkqty->bindParam(":id", $item['product_id']);
            $checkqty->execute();
            $productqty = $checkqty->fetch();
            if ($item['product_qty'] <= 0 || $item['product_qty'] > $productqty['actual_stock_qty'] ) {
                $dbh->rollBack();
                return 1;
            }
        }
        // make an order
        $addorder = $dbh->prepare("SELECT insert_order(:id)");
        $addorder->bindParam(":id", $id['id']);
        $addorder->execute();
        $orderid = $addorder->fetch();
        // add items to the order, also remove them from stocking qty
        foreach($items as $item) {
            // add item to order_id
            $additem = $dbh->prepare("SELECT insert_order_item(:orderid, :productid, :productqty)");
            $additem->bindParam(":orderid", $orderid[0]);
            $additem->bindParam(":productid", $item['product_id']);
            $additem->bindParam(":productqty", $item['product_qty']);
            $additem->execute();
            $checkvalidity = $additem->fetch();
            if ($checkvalidity[0] != 1) {
                $dbh->rollBack();
                return 2;
            }
        }
        //remove the stuff from your shopping cart
        $remove = $dbh->prepare("DELETE from shopping_cart where customer_id = :customerid");
        $remove->bindParam(":customerid", $id['id']);
        $remove->execute();
        // commit to db
        $dbh->commit();
        $dbh=null;
        return 3;
    } catch (PDOException $e) {
        $dbh->rollBack();
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

/**
 * Summary of addOutfit
 * Create an empty outfit
 * @param mixed $username
 * @param mixed $oName
 * @return bool
 */
function createOutfit($username, $oName) {
    try {

        // Move oImage to the correct directory
        $fileExtension = pathinfo($_FILES["outfitImage"]["name"], PATHINFO_EXTENSION);
        $newFilePath = "ClothingImages/" . "OUTFIT" . $_SESSION["username"] . "_" . $oName . "." . $fileExtension;
        if (!move_uploaded_file($_FILES["item"]["tmp_name"], $newFilePath)) { // Use move_uploaded_file()
            return false;  
        }

        $dbh = connectDB();
        $statement = $dbh->prepare("INSERT INTO clo_outfits (username, oName) values (:username, :oName)");
        $statement->bindParam(":username", $username);
        $statement->bindParam(":oName", $oName);
        $statement->execute();
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

/**
 * Summary of addOutfitItem
 * Adds an item to an outfit
 * UNFINISHED
 * @param mixed $username
 * @param mixed $outfitName
 * @param mixed $item
 * @return bool
 */
function addOutfitItem($username, $oName, $cName, $category) {
    try{
        $dbh = connectDB();

        // check the item is of the correct category
        $check = $dbh->prepare("SELECT category FROM clo_clothing_items WHERE username = :username AND cName = :cName");
        $check->bindParam(":username", $username);
        $check->bindParam("cName", $cName);
        $check->execute();
        $row = $check->fetch();
        // if the item is not of the correct category, return false
        if ($row[0] != $category) {
            return false;
        }

        $statement = $dbh->prepare("INSERT INTO clo_outfit_items (username, oName, cName) values (:username, :oName, :cName)");
        $statement->bindParam(":username", $username);
        $statement->bindParam(":oName", $oName);
        $statement->bindParam(":cName", $cName);

        $statement->execute();
        return true;

    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}
?>