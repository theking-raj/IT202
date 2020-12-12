<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
    $tp = 0;
    if(isset($_GET["id"])) {
        $id = $_GET["id"];
        $db = getDB();
        $stmt = $db->prepare("SELECT id,price from Carts WHERE user_id = :user_id");
        $r = $stmt->execute([":user_id"=>$id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);       
        foreach($products as $prod) {
            $tp+=$prod["price"];
        }
    }
?>
<?php

    if(isset($_POST["save"]) && validateStreetAddress($_POST["address"])) {
        $address = $_POST["address"];
        $payment = $_POST["payment"];
        $total = $_POST["tp"];
      	$db = getDB();
      	$stmt = $db->prepare("INSERT INTO Purchases(user_id, address, payment_method, total_price) VALUES(:user_id, :address, :payment_method, :total_price)");
      	$r = $stmt->execute([
            ":user_id"=>$id,
            ":street_address"=>$street_address,
            ":payment"=>$payment,
            ":total_price"=>$total
        ]);   
      	$stmt = $db->prepare("SELECT * FROM Purchases WHERE user_id=$id ORDER BY ID DESC LIMIT 1");
      	$r = $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);  
        $order_id = $orders[0]["id"];
        $stmt = $db->prepare("SELECT product_id, quantity FROM Carts WHERE user_id=$id");
        $r = $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);      
        foreach($products as $product) {
          $check_pattern = UpdateItem($product["product_id"], $product["quantity"]);
        }    
          $prod_info = 0;
        foreach($products as $product) {
          $prod_info = $product["product_id"];
          $quantity = $product["quantity"];        
          $stmt = $db->prepare("SELECT price FROM Products WHERE id=$prod_info");
          $r = $stmt->execute();
          $items = $stmt->fetch(PDO::FETCH_ASSOC);         
          $item = $items["price"];          
        	$stmt = $db->prepare("INSERT INTO OrderItems(order_id, product_id, quantity, unit_price) VALUES($order_id, $prod_info, $quantity, $item)");
        	$r = $stmt->execute();
        }
        header("Location: view_orders.php?id=$id");
    }
      elseif(isset($_POST["save"]) && !validateStreetAddress($_POST["address"]))
        flash("Invalid street address.");
?>


  <h3>Billing Information</h3>
    <form method="POST">
        <label>Payment Methods</label>
        <select name="payment_method">
            <option value="-1">None</option>
            <option value="American Express">American Express</option>
            <option value="Discover">Discover</option>
            <option value="MasterCard">MasterCard</option>
            <option value="Visa">Visa</option>          
        </select>
        
		<label>Billing Address</label>
        <input type="varchar" name="address"/>
        <label>Total:</label>
        <input type="number" name="tp" value = "<?php safer_echo($tp); ?>"/>
        <input type="submit" name="save" value="Order"/>
    </form>

<?php require(__DIR__ . "/partials/flash.php");