<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<p>Product Catalog</p>

<?php
    if (!has_role("Admin")) {
        $db = getDB();
        $stmt = $db->prepare("SELECT Products.id,name,quantity,price,user_id,visibility, Users.username FROM Products JOIN Users on Products.user_id = Users.id ORDER BY name");
        $r = $stmt->execute([]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } 
    elseif (has_role("Admin")) {
        $db = getDB();
        $stmt = $db->prepare("SELECT Products.id,name,quantity,price,user_id,visibility, Users.username FROM Products JOIN Users on Products.user_id = Users.id ORDER BY name");
        $r = $stmt->execute([]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
?>
<div class="results">
        <div class="list-group">
            <?php foreach ($results as $product): ?>
                <div class="list-group-item">
                    <div>
                        <div>Name: <?php safer_echo($product["name"]); ?></div>
                        <div>Price: $<?php safer_echo($product["price"]); ?></div>
                        <div>Units Available: <?php safer_echo($product["quantity"]); ?></div>
                        <div>Seller: <?php safer_echo($product["username"]); ?></div>
                        <a type="button" href="admin_view_products.php?id=<?php safer_echo($product['id']); ?>">View</a>
                    </div>
                        <br>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
</div>
<nav label="navigation">
  <ul class="pagination">
    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
    <li class="page-item"><a class="page-link" href="#">1</a></li>
    <li class="page-item"><a class="page-link" href="#">2</a></li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item"><a class="page-link" href="#">Next</a>
  </ul>
</nav>