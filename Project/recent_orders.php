<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //Is not logged in
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<?php
   $username="";
   $calculate = 0;
   $items = 0;
   $page = 1;
   $per_page = 10;
   $total_pages = 0;
   $val=false;
   $order_id = array();
   $usernamearr = array();
   $time = array();
   $total_price = array();
   $unit_price = array();
   $productids = array();
   $quantity = array();
   $payment = array();
   $address = array();
   $names = array();
   $prds = array();

    if(isset($_GET["page"])){
        try {
            $page = (int)$_GET["page"];
        }
        catch(Exception $e){
    
        }
    }
    if (!has_role("Admin")) {
        $user = get_user_id();
        
        $db = getDB();
          	$stmt = $db->prepare("SELECT username FROM Users WHERE id=$user");
          	$r = $stmt->execute();
            $usernames = $stmt->fetch(PDO::FETCH_ASSOC);
            $username = $usernames["username"];
            
        $db = getDB();
          	$stmt = $db->prepare("SELECT * FROM Purchases WHERE user_id=$user ORDER BY ID DESC");
          	$r = $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
           $total = 0;
           $offset = 0;
           $calculate = 0;
           $in = 0;
           $orderid="";
           foreach($orders as $order){
                 if($in==0){
                  $orderid ="".$order["id"];
                  $in++;
                  }
                  else
                    $orderid .=" OR order_id=".$order["id"];
                 
                
                $db = getDB();
              	$stmt = $db->prepare("SELECT count(*) as total from OrderItems WHERE order_id=$orderid");
              	$r = $stmt->execute();
                $products = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($products){
                    $total += (int)$products["total"];
                }
                
                $total_pages = ceil($total / $per_page);
                $offset = ($page-1) * $per_page;
           }
    
                $stmt = $db->prepare("SELECT product_id,unit_price,quantity,created FROM OrderItems WHERE order_id=$orderid LIMIT :offset, :count");
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
                
                $stmt->execute();
                $e = $stmt->errorInfo();
                if($e[0] != "00000"){
                    flash(var_export($e, true), "alert");
                }
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                foreach($products as $product)
                {

                $calculate += $product["quantity"]*$product["unit_price"];
                $time[] = $product["created"];
                $product_id = $product["product_id"];
                $unit_price[] = $product["unit_price"];
                $quantity[] = $product["quantity"];
                $db = getDB();
              	$stmt = $db->prepare("SELECT name FROM Products WHERE id=$product_id");
              	$r = $stmt->execute();
                $productName = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $names[] = $productName["name"];
                $productids[] = $product_id;
                $items++;
                
            }
}
        elseif(has_role("Admin")){
            $db = getDB();
            $q1 = "SELECT count(*) as total from OrderItems";
            $q2 = "SELECT product_id,unit_price,quantity,created FROM OrderItems";
            $stmt = $db->prepare("SELECT * FROM Orders ORDER BY ID DESC");
            $r = $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($_POST["start"]) && !empty($_POST["end"]) && !empty($_POST["category"]) && $_POST["category"]!="None" && isset($_POST["Filter"])){
            $start = $_POST["start"];
            $end = $_POST["end"];
            $category = $_POST["category"];
            $cts = "";
            
            $stmt = $db->prepare("SELECT id FROM Products WHERE category=:category");
       	    $r = $stmt->execute([":category"=>$category]);
            $ca = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $in = 0;
            foreach($ca as $cas)
            {
                if($in==0)
                {
                    $cts = "".$cas["id"]." ";
                }
                else
                {
                    $cts .= "OR product_id = ".$cas["id"]." ";
                }
                $in++;
            }
            $q1 = "SELECT count(*) as total from `OrderItems` WHERE product_id=".$cts."AND DATE(created) between '$start' and '$end'";
            $q2="SELECT * FROM `OrderItems` WHERE product_id=".$cts."AND DATE(created) between '$start' and '$end' ORDER BY `created` DESC";
        }
        elseif(!empty($_POST["category"]) && $_POST["category"]!="None" &&  isset($_POST["Filter"])){
            $category = $_POST["category"];
            $cts = "";
            
            $stmt = $db->prepare("SELECT id FROM Products WHERE category=:category");
       	    $r = $stmt->execute([":category"=>$category]);
            $ca = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $in = 0;
            foreach($ca as $cas)
            {
                if($in==0)
                {
                    $cts = "".$cas["id"]." ";
                }
                else
                {
                    $cts .= "OR product_id = ".$cas["id"]." ";
                }
                $in++;
            }
            $q1= "SELECT count(*) as total from OrderItems WHERE product_id=".$cts;
            $q2="SELECT * FROM OrderItems WHERE product_id=".$cts;
    
        }
        elseif(!empty($_POST["start"]) && !empty($_POST["end"]) && isset($_POST["Filter"])){
            $start = $_POST["start"];
            $end = $_POST["end"];
            
            $q1= "SELECT count(*) as total from OrderItems WHERE DATE(created) between '$start' and '$end'";
            $q2="SELECT * FROM OrderItems WHERE DATE(created) between '$start' and '$end' ORDER BY created DESC";
    
        }
        elseif(isset($_GET["page"])){
            $q1 = "SELECT count(*) as total from OrderItems";
            $q2 = "SELECT product_id,unit_price,quantity,created FROM OrderItems";
        }
            
            $order_id = array();
            $time = array();
            $calculate = 0;
            
            $unit_price = array();
            $quantity = array();
            $items = 0;
            $val=false;
            $names = array();
            $usernamearr = array();
    
            $total = 0;
            $offset = 0;
            
                $db = getDB();
              	$stmt = $db->prepare($q1);
              	$r = $stmt->execute();
                $prds = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($prds){
                    $total += (int)$prds["total"];
                }
                
                $total_pages = ceil($total / $per_page);
                $offset = ($page-1) * $per_page;
            
            $q2.=" LIMIT :offset, :count";
            $stmt = $db->prepare($q2);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
                
                $stmt->execute();
                $e = $stmt->errorInfo();
                if($e[0] != "00000"){
                    flash(var_export($e, true), "alert");
                } 
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
          foreach($products as $product)
                {
                    $calculate += $product["quantity"]*$product["unit_price"];
                }
    
           foreach($orders as $order){
                $order_id[] = $order["id"];
                $orderid = $order["id"];
                $payment[] = $order["payment_method"];
                $address[] = $order["address"];
                $user = $order["user_id"];
                $db = getDB();
              	$stmt = $db->prepare("SELECT username FROM Users WHERE id=$user");
              	$r = $stmt->execute();
                $usernames = $stmt->fetch(PDO::FETCH_ASSOC);
                $usernamearr[] = $usernames["username"];
                
                foreach($products as $product)
                {
                    $time[] = $product["created"];
                    $product_id = $product["product_id"];
                    $unit_price[] = $product["unit_price"];
                    $quantity[] = $product["quantity"];
                    $db = getDB();
                  	$stmt = $db->prepare("SELECT name FROM Products WHERE id=$product_id");
                  	$r = $stmt->execute();
                    $productName = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $names[] = $productName["name"];
                    $items++;
                }
            }
      }
      $User_Order = 0;
      $Name_Order = 0;
      $Quatity_Order = 0;
      $Price_Order = 0;
      $Rate_Order = 0;

?>
<div class="results">
    <br>
    <div class="card-subtitle">Total Price: $<?php safer_echo($calculate); ?></div>
    </br>
    <?php if ($items>0 && count($usernamearr)>0): ?>
        <div class="list-group">
            <?php foreach ($products as $product): ?>
                <div>
                    <div>Username: <?php safer_echo($usernamearr[$User_Order++]); ?></div>
                </div>
                <div class="list-group-item">
                    <div>
                        <div>Name: <?php safer_echo($names[$Name_Order++]); ?></div>
                    </div>
                    <div>
                        <div>Quantity: <?php safer_echo($quantity[$Quatity_Order++]); ?></div>
                    </div>
                    <div>
                        <div>Price: <?php safer_echo($unit_price[$Price_Order++]); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($items > 0 && count($usernamearr)==0): ?>
        <div class="list-group">
            <div>
                <div>Username: <?php safer_echo($username); ?></div>
            </div>
            <?php foreach ($products as $product): ?>
                <div class="list-group-item">
                    <div>
                        <div>Name: <?php safer_echo($names[$Name_Order++]); ?></div>
                    </div>
                    <div>
                        <div>Quantity: <?php safer_echo($quantity[$Quatity_Order++]); ?></div>
                    </div>
                    <div>
                        <div>Price: <?php safer_echo($unit_price[$Price_Order++]); ?></div>
                    </div>
                    <div>
                        <a type="button" href="admin_view_products.php?id=<?php safer_echo($productids[$Rate_Order++]); ?>">Rate</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div>
        <div>Username: <?php safer_echo($username); ?></div>
        </div>
        <p>No Purchases</p>
    <?php endif; ?>
</div>
</div>
<nav aria-label="Recent Orders">
  <ul class="pagination">
    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
    <li class="page-item"><a class="page-link" href="#">1</a></li>
    <li class="page-item"><a class="page-link" href="#">2</a></li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item"><a class="page-link" href="#">Next</a>
  </ul>
</nav>
    </div>

<?php require(__DIR__ . "/partials/flash.php");