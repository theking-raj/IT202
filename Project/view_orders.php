<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //Is not logged in
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
      if(isset($_GET["id"])) {
          $userid = $_GET["id"];
          $db = getDB();
         	$stmt = $db->prepare("SELECT * FROM Purchases WHERE user_id=$userid ORDER BY ID DESC LIMIT 1");
         	$r = $stmt->execute();
          $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);     
          $order_id = $orders[0]["id"];
          $total_price = $orders[0]["total_price"];
          $payment = $orders[0]["payment_method"];
          $address = $orders[0]["address"];
         	$stmt = $db->prepare("SELECT username FROM Users WHERE id=$userid");
         	$r = $stmt->execute();
          $usernames = $stmt->fetch(PDO::FETCH_ASSOC);
          $username = $usernames["username"];
         	$stmt = $db->prepare("SELECT product_id,unit_price,quantity FROM OrderItems WHERE order_id=$order_id");
         	$r = $stmt->execute();
          $products = $stmt->fetchAll(PDO::FETCH_ASSOC);     
          $arr = array();
      foreach($products as $product) {
          $product_id = $product["product_id"];
         	$stmt = $db->prepare("SELECT name FROM Products WHERE id=$product_id");
         	$r = $stmt->execute();
          $item = $stmt->fetch(PDO::FETCH_ASSOC);        
          $arr[] = $item["name"];
      }
          $i=0;
  }
?>
<?php
    
    if(isset($_POST["confirm"])) {
        $result = clearCart($userid);
        if($result) {
            flash("Order has been placed.");
              die(header("Location: home.php?id=$userid"));
      }
        else
          flash("There was an error placing your order. Please try again.");
}
  elseif(isset($_POST["cancel"])) {   
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM OrderItems WHERE order_id=$order_id");
    $r = $stmt->execute();   
    $stmt = $db->prepare("DELETE FROM Purchases WHERE id=$order_id");
    $r = $stmt->execute();
    if($r) { 
      flash("Order was cancelled");
      die(header("Location: admin_view_cart.php?id=$userid"));     
    }
    else
      flash("There was an error, please reload the website.");
}
?>
  <h3>Order Details</h3>
  <div class="results">
    <?php if (count($products) > 0):?>
            <?php foreach ($products as $r):?>
                <div class="card">
                <div class="list-group">
                <div>Name: <?php safer_echo($arr[$i++]);?></div>
                <div>Quantity: <?php safer_echo($r["quantity"]);?></div>
                <div>Price: <?php safer_echo($r["unit_price"]);?></div>
                </div>
                </div>
            <?php endforeach; ?>
            <div class="card">
            <div class="list-group">
                <div>Payment: <?php safer_echo($payment);?></div>
                <div>Address: <?php safer_echo($address);?></div>
                <div>Total: <?php safer_echo($total_price);?></div>
            </div>
            </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif;?>
<form method="POST">
    <input type="submit" name="confirm" value="Place Order"/>
    <input type="submit" name="cancel" value="Cancel Order"/>
</form>
<?php require(__DIR__ . "/partials/flash.php");
