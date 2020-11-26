<link rel="stylesheet" href="static/css/styles.css">
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
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
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>           
        <?php endif; ?>
    </ul>
</nav>
