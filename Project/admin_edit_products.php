<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Administrator")) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
if(isset($_GET["id"])){
	$id = $_GET["id"];
}
?>
<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$quantity = $_POST["quantity"];
	$price = $_POST["price"];
  $category = $_POST["category"];
	$desc = $_POST["description"];
	$user = get_user_id();
	$db = getDB();
	if(isset($id)){
		$stmt = $db->prepare("UPDATE Products set name=:name, quantity=:quantity, category=:category, price=:price, description=:desc where id=:id");
		$r = $stmt->execute([
      ":id"=>$id,
			":name"=>$name,
		  ":quantity"=>$quantity,
      ":category"=>$category,
      ":price"=>$price,
		  ":desc"=>$desc
      
		]);
		if($r){
			flash("Updated successfully with id: " . $id);
		}
		else{
			$e = $stmt->errorInfo();
			flash("Error updating: " . var_export($e, true));
		}
	}
	else{
		flash("ID isn't set, we need an ID in order to update");
	}
}
?>
<?php
//fetching
$result = [];
if(isset($id)){
	$id = $_GET["id"];
	$db = getDB();
	$stmt = $db->prepare("SELECT * FROM Products where id = :id");
	$r = $stmt->execute([":id"=>$id]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<form method="POST">
	<label>Name</label>
	<input name="name" placeholder="Name" value="<?php echo $result["name"];?>"/>
	<label>Quantity</label>
	<input type="number" min="0" name="quantity"/>
	<label>Price</label>
	<input type="number" min="0" name="price"/>
  <label>Category</label>
	<input type="text" name="category"/>
	<label>Description</label>
	<input type="text" name="description"/>
	<input type="submit" name="save" value="Update"/>
</form>


<?php require(__DIR__ . "/partials/flash.php");