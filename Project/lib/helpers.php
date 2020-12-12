<?php
session_start();//we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");
//this file will contain any helpful functions we create
//I have provided two for you
function is_logged_in() {
    return isset($_SESSION["user"]);
}

function has_role($role) {
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}

function get_username() {
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}

function get_email() {
    if (is_logged_in() && isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
    return "";
}

function get_user_id() {
    if (is_logged_in() && isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
    return -1;
}

function safer_echo($var) {
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}

function flash($msg) {
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $msg);
    }
    else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $msg);
    }

}

function getMessages() {
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

function getQuantityPrice($quantity,$id) {

    $db = getDB();
    $stmt = $db->prepare("SELECT price FROM Products WHERE id=:id");
    $r = $stmt->execute([
    ":id" => $id,
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
    
    $pr = $result["price"];
    
    $total = $pr*$quantity;
    return $total;
}

function deleteRow($id) {	
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM Carts WHERE product_id=$id");
    $r = $stmt->execute();
    if($r)
      return true;
    else
      return false;
}

function clearCart($id) {	
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM Carts WHERE user_id=$id");
    $r = $stmt->execute();
    if($r)
      return true;
    else
      return false;
}

function CheckItem($id, $ShopBag) {
     $db = getDB();
     $stmt = $db->prepare("SELECT quantity FROM Products WHERE id=$id");
     $r = $stmt->execute();
     $result = $stmt->fetch(PDO::FETCH_ASSOC);    
     $quantity = $result["quantity"];    
         if($ShopBag<=$quantity)
             return true; 
         else
             return false;
}

function UpdateItem($id, $ShopBag) {
     $db = getDB();
     $stmt = $db->prepare("SELECT quantity FROM Products WHERE id=$id");
     $r = $stmt->execute();
     $result = $stmt->fetch(PDO::FETCH_ASSOC);     
     $quantity = $result["quantity"];    
     $quantity2 = $quantity-$ShopBag;
         $stmt = $db->prepare("UPDATE Products set quantity=:quantity WHERE id=:id");
         $r = $stmt->execute([
           ":id"=>$id,
	         ":quantity"=>$quantity2 ]);
     return true;
}

function PullItem($arr) {
     $db = getDB();
     $stmt = $db->prepare("SELECT name, quantity FROM Products WHERE id=$arr[0]");
     $r = $stmt->execute();
     $result = $stmt->fetch(PDO::FETCH_ASSOC);   
         if($result["quantity"]>0){
             $LimitedStock = $result["quantity"]. "stock left of" .$result["name"];
         }
         else{
             $LimitedStock = $result["name"]. "is out of stock";
         }     
         if(count($arr)>1){
             for($i=1; $i<count($arr); $i++){
                 $stmt = $db->prepare("SELECT name, quantity FROM Products WHERE id=$arr[$i]");
                 $r = $stmt->execute();
                 $result = $stmt->fetch(PDO::FETCH_ASSOC);          
                     if($result["quantity"]>0){
                       $LimitedStock = $LimitedStock. "&" .$result["name"]. "limited stock of" .$result["quantity"];
                     }
                     else
                     {
                       $LimitedStock = $LimitedStock. "&" .$result["name"]. "is out of stock";
                 }
              }
          } 
         return $LimitedStock;
}

function validateStreetAddress($string) {
    $check_pattern = '/\d+ [0-9a-zA-Z ]+/';
    return preg_match($check_pattern, $string);
}

?>
