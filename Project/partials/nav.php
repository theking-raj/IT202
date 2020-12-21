<link rel="stylesheet" href="static/css/styles.css">
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>

<!-- CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<!-- jQuery and JS bundle w/ Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

<nav>
    <ul class="nav">
        <li><a href="home.php">Home</a></li>
        <?php if (!is_logged_in()): ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
        <?php if (has_role("Administrator")): ?>
            <li><a href="admin_create_products.php">Create Products</a></li>
        <?php endif; ?>
        <?php if (is_logged_in()): ?>
            <li><a href="admin_create_cart.php">Add to Cart</a></li>
            <li><a href="admin_view_cart.php">View Cart</a></li>
            <li><a href="admin_list_products.php">View Products</a></li>
            <li><a href="catalog.php">Catalog</a></li>
            <li><a href="recent_orders.php">Order History</a><li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>           
        <?php endif; ?>
    </ul>
</nav>
