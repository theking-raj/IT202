<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php 
    if (!is_logged_in()) {
        flash("You don't have permission to access this page");
        die(header("Location: login.php"));
    }
?>
<?php
    if (isset($_GET["id"])) {
      $userID = $_GET["id"];
      $db = getDB();
      $stmt = $db->prepare("SELECT product_id, quantity FROM Carts WHERE user_id=$userID");
      $r = $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);       
      $verify = true;
      $products = array();
      foreach($results as $result) {
        $verify2 = CheckItem($result["product_id"], $result["quantity"]);
      if(!$verify2) {
        $products[] = $result["product_id"]; 
      }
    }        
      if(count($products)==0) {
        header("Location: orders.php?id=$userID");
      }
      else {
        $LimitedStock = PullItem($products)."There was an error, please refresh the page.";
        flash($LimitedStock);
        die(header("Location: admin_view_cart.php"));
        }
    }
?>