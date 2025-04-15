<?php
function connectDB() {
    $config = parse_ini_file(__DIR__ . "/../../db.ini"); //Use __DIR__ for more reliable paths.
    $dbh = new PDO($config['dsn'], $config['username'], $config['password']);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $statement = $dbh->prepare("use cfleser");
    $statement->execute();
    return $dbh;
}



// Process outfit creation upon final submission
function processOutfitCreation($username, $postData, $fileData) {
    try {
        $dbh = connectDB();
        $outfitName = $postData["outfitName"];

        $dbh->beginTransaction();

        // Create outfit
        if (!createOutfit($username, $outfitName)) {
            throw new Exception("Error creating outfit.");
        }

        // Upload outfit image
        $fileExtension = pathinfo($fileData["outfitImage"]["name"], PATHINFO_EXTENSION);
        $newFilePath = __DIR__ . "/../ClothingImages/" . "OUTFIT_" . $username . "_" . $outfitName . "." . $fileExtension;
        if (!move_uploaded_file($fileData["outfitImage"]["tmp_name"], $newFilePath)) {
            throw new Exception("Failed to upload outfit image.");
        }

        // Add selected items to the outfit
        global $categories; // Ensure $categories is accessible
        foreach ($categories as $category) {
            $itemName = $postData[$category];
            if (!empty($itemName)) {
                if (!addOutfitItem($username, $outfitName, $itemName, $category)) {
                    throw new Exception("Error adding item $itemName to outfit.");
                }
            }
        }

        $dbh->commit();
        return "Outfit added successfully!";
    } catch (Exception $e) {
        $dbh->rollBack();
        return $e->getMessage();
    } finally {
        $dbh = null;
    }
}


//return number of rows matching the given user and passwd.
function authenticate_customer($user, $passwd) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT count(*) FROM clo_user where username = :username and password = sha2(:passwd,256) ");
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

function get_clothing_item($cName, $username) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM clo_clothing_items where cName = :cName and username = :username");
        $statement->bindParam(":cName", $cName);
        $statement->bindParam(":username", $username);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $dbh=null;
        return $results;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function findImage($itemName): ?string {
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


function get_items_for_user($category, $username) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM clo_clothing_items where category = :category and username = :username");
        $statement->bindParam(":category", $category);
        $statement->bindParam(":username", $username);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dbh=null;
        return $results;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function get_outfits_for_user($username, $category) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM clo_outfits where username = :username and category = :category");
        $statement->bindParam(":username", $username);
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

function get_uncategorized_outfits($username) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM clo_outfits where username = :username and category is null");
        $statement->bindParam(":username", $username);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dbh=null;
        return $results;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function get_outfit_items($outfitName, $username) {
    try {
        $dbh = connectDB();
        
        $statement = $dbh->prepare("SELECT clo_outfit_items.* 
            FROM clo_outfit_items
            JOIN clo_clothing_items ON clo_outfit_items.cName = clo_clothing_items.cName
            WHERE clo_outfit_items.oName = :outfitName 
            AND clo_outfit_items.username = :username
            ORDER BY 
                CASE clo_clothing_items.category
                    WHEN 'Headwear' THEN 1
                    WHEN 'Top' THEN 2
                    WHEN 'Outerwear' THEN 3
                    WHEN 'Bottom' THEN 4
                    WHEN 'Footwear' THEN 5
                    WHEN 'Dress' THEN 6
                    WHEN 'Accessories' THEN 7
                    ELSE 8 -- Categories not listed above will appear last
                END");
        $statement->bindParam(":outfitName", $outfitName);
        $statement->bindParam(":username", $username);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dbh = null;
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

function user_exists($username) {
    try {
        $dbh = connectDB();
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM clo_user WHERE username = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        $dbh = null;
        return $count > 0;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function create_user($username, $password) {
    try {
        $dbh = connectDB();
        $stmt = $dbh->prepare("INSERT INTO clo_user (username, password) VALUES (:username, sha2(:password, 256))");
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->execute();
        $dbh = null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function create_category($username, $category){
    try {
        $dbh = connectDB();
        $check = $dbh->prepare("SELECT COUNT(category) FROM clo_outfit_categories where username = :username and category = :category");
        $check->bindParam(":username", $username);
        $check->bindParam(":category", $category);
        $check->execute();
        $row = $check->fetch();
        if ($row[0] != 0) {
            $dbh = null;
            return false;
        }

        $statement = $dbh->prepare("INSERT INTO clo_outfit_categories(username, category) values (:username, :category)");
        $statement->bindParam(":username", $username);
        $statement->bindParam(":category", $category);
        $statement->execute();
        $dbh = null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function get_outfit_categories($username) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT category FROM clo_outfit_categories where username = :username");
        $statement->bindParam(":username", $username);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dbh=null;
        return $results;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function addOutfitToCategory($username, $outfitName, $categoryName) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("UPDATE clo_outfits SET category = :category WHERE username = :username AND oName = :oName");
        $statement->bindParam(":category", $categoryName);
        $statement->bindParam(":username", $username);
        $statement->bindParam(":oName", $outfitName);
        $statement->execute();
        $dbh=null;
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
        $dbh = connectDB();
        $statement = $dbh->prepare("INSERT INTO clo_outfits (username, oName) values (:username, :oName)");
        $statement->bindParam(":username", $username);
        $statement->bindParam(":oName", $oName);
        $statement->execute();
        $dbh = null;
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
            $dbh = null;
            return false;
        }

        $statement = $dbh->prepare("INSERT INTO clo_outfit_items (username, oName, cName) values (:username, :oName, :cName)");
        $statement->bindParam(":username", $username);
        $statement->bindParam(":oName", $oName);
        $statement->bindParam(":cName", $cName);

        $statement->execute();
        $dbh = null;
        return true;

    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function createRandOutfit($username, $oName){
    try {
        $dbh = connectDB();
        $dbh->beginTransaction();

        createOutfit($username, $oName);

        // Add one random item from each category to the outfit
        $categoryNames = ["Headwear", "Top", "Outerwear", "Bottom", "Footwear", "Dress", "Accessories"];
        $itemAdded = false;
        foreach ($categoryNames as $categoryName) {
            $temp = get_items_for_user($categoryName, $username);

            if (sizeof($temp) == 0) {
                continue; // Skip if no items in this category
            }
            
            $item = $temp[array_rand($temp)];
            // Add the item to the outfit
            addOutfitItem($username, $oName, $item["cName"], $categoryName);
            $itemAdded = true;
        }

        if (!$itemAdded) {
            $dbh->rollBack(); // Rollback if no items were added
            $dbh = null;
            return false;
        }

        $dbh->commit();
        $dbh = null;
        return true;

    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

//Function to validate password
//pregmatch: check if a string matches a regular expression 
function validate_password($password) {
    $errors = [];

    // Check length
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    // Check for uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }

    // Check for lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }

    // Check for number
    if (!preg_match('/\d/', $password)) {
        $errors[] = "Password must contain at least one number.";
    }

    // Check for special character
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = "Password must contain at least one special character.";
    }

    // Return the array of errors (empty if no issues)
    return $errors;
}

?>