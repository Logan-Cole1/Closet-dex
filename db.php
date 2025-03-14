<?php
function connectDB()
{
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


function authenticate_employee($user, $passwd) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT count(*) FROM employee ". "where username = :username and password = sha2(:passwd,256) ");
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


function get_categories() {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT name FROM category");
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dbh=null;
        return $results;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
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


function change_password_employee($user, $password) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("UPDATE employee set password = sha2(:password, 256), changed_pass = true where username = :username");
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
        $check = $dbh->prepare("SELECT COUNT(username) FROM customer where username = :username");
        $check->bindParam(":username", $username);
        $check->execute();
        $row=$check->fetch();
        if ($row[0] != 0) {
            return false;
        }
        $statement = $dbh->prepare("INSERT INTO customer (username, password, first_name, last_name, email, shipping_address) values (:username, sha2(:password, 256), :firstname, :lastname, :email, :address)");
        $statement->bindParam(":username", $username);
        $statement->bindParam(":password", $password);
        $statement->bindParam(":firstname", $firstname);
        $statement->bindParam(":lastname", $lastname);
        $statement->bindParam(":email", $email);
        $statement->bindParam(":address", $address);
        $statement->execute();
        $dbh=null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function get_cart_items($username) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT p.price, p.image, p.name, s.product_qty, s.customer_id, s.product_id FROM customer c join shopping_cart s on c.id = s.customer_id join product p on s.product_id = p.id ". "where username = :username");
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


function change_shopping_cart_qty($productid, $customerid, $qty) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("UPDATE shopping_cart set product_qty = :qty where product_id = :productid and customer_id = :customerid");
        $statement->bindParam(":qty", $qty);
        $statement->bindParam(":productid", $productid);
        $statement->bindParam(":customerid", $customerid);
        $statement->execute();
        $dbh=null;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function remove_shopping_cart_item($productid, $customerid) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("DELETE from shopping_cart where product_id = :productid and customer_id = :customerid");
        $statement->bindParam(":productid", $productid);
        $statement->bindParam(":customerid", $customerid);
        $statement->execute();
        $dbh=null;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function add_shopping_cart_item($productid, $username) {
    try {
        $dbh = connectDB();
        $getid = $dbh->prepare("SELECT id from customer where username = :username");
        $getid->bindParam(":username", $username);
        $getid->execute();
        $id=$getid->fetch();
        $check = $dbh->prepare("SELECT COUNT(product_id) from shopping_cart where customer_id = :customerid and product_id = :productid");
        $check->bindParam(":customerid", $id['id']);
        $check->bindParam(":productid", $productid);
        $check->execute();
        $row=$check->fetch();
        if($row[0] != 0) {
            return false;
        }
        $statement = $dbh->prepare("INSERT INTO shopping_cart values (:customerid, :productid, 1)");
        $statement->bindParam(":productid", $productid);
        $statement->bindParam(":customerid", $id['id']);
        $statement->execute();
        $dbh=null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function get_total_amount($username) {
    try {
        $dbh = connectDB();
        $getid = $dbh->prepare("SELECT id from customer where username = :username");
        $getid->bindParam(":username", $username);
        $getid->execute();
        $id=$getid->fetch();
        $statement = $dbh->prepare("SELECT SUM(p.price*s.product_qty) from shopping_cart s join product p on s.product_id = p.id where s.customer_id = :customerid");
        $statement->bindParam(":customerid", $id['id']);
        $statement->execute();
        $results=$statement->fetch();
        $dbh=null;
        return $results[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function get_orders($username) {
    try {
        $dbh = connectDB();
        $getid = $dbh->prepare("SELECT id from customer where username = :username");
        $getid->bindParam(":username", $username);
        $getid->execute();
        $id=$getid->fetch();
        $statement = $dbh->prepare("SELECT * FROM order_info where customer_id = :customerid");
        $statement->bindParam(":customerid", $id['id']);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dbh=null;
        return $results;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function get_order_items($orderid) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM order_items o join product p on o.product_id = p.id ". "where order_id = :orderid");
        $statement->bindParam(":orderid", $orderid);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dbh=null;
        return $results;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


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


function restock_item($item, $qty) {
    try {
        $dbh = connectDB();
        // restock the item
        if ($qty < 0) {
            return false;
        }
        $addorder = $dbh->prepare("CALL restock_product(:productid, :productqty)");
        $addorder->bindParam(":productid", $item);
        $addorder->bindParam(":productqty", $qty);
        $addorder->execute();
        $dbh=null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function change_item_price($item, $amount, $username) {
    try {
        $dbh = connectDB();
        // get id
        $getid = $dbh->prepare("SELECT id from employee where username = :username");
        $getid->bindParam(":username", $username);
        $getid->execute();
        $id=$getid->fetch();
        // restock the item
        if ($amount < 0) {
            return false;
        }
        $addorder = $dbh->prepare("CALL update_product_price(:productid, :amount, :id)");
        $addorder->bindParam(":productid", $item);
        $addorder->bindParam(":amount", $amount);
        $addorder->bindParam(":id", $id['id']);
        $addorder->execute();
        $dbh=null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function get_price_history($productid) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT timestamp, old_price, new_price from product_history where product_id = :productid and old_price != new_price");
        $statement->bindParam(":productid", $productid);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dbh=null;
        return $results;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function get_qty_history($productid) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT timestamp, old_actual_qty, new_actual_qty from product_history where product_id = :productid and old_actual_qty != new_actual_qty");
        $statement->bindParam(":productid", $productid);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dbh=null;
        return $results;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function get_changed_password($username) {
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT changed_pass from employee where username = :username");
        $statement->bindParam(":username", $username);
        $statement->execute();
        $result = $statement->fetch();
        $dbh=null;
        return $result['changed_pass'];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}
?>





