<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
if (isset($_GET["id"])) {
    
    $result = deleteRow($_GET["id"]);
    if($result)
      header("Location: admin_view_cart.php");
    else
      flash("error");
}
?>

<?php require(__DIR__ . "/partials/flash.php");
