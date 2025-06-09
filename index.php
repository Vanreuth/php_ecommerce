<?php

$page = "./pages/home.php";
$p="home";
if(isset($_GET['p']))
{
    $p = $_GET['p'];
    switch($p){
        case "product" :
           $page ="./pages/product.php" ;
           break;
        case "cart" :
            $page ="./pages/shop-cart.php";
            break;
        case "about" :
                $page ="./pages/about.php";
                break;
           case "contact" :
            $page ="./pages/contact.php" ;
            break;
            case "product-detail" :
                $page ="./pages/product-detail.php" ;
                break;
            default:
            $page ="./pages/home.php" ;
            break;

    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    print_r($_POST); // Debugging: Check if data is received

    if (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']); // Clear cart
        header("Location: index.php?p=cart"); // Redirect to refresh the page
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include "./includes/head.php" ?>
<body class="animsition">
<?php include "./includes/header.php" ?>
<div class="wrap-header-cart js-panel-cart">
		<div class="s-full js-hide-cart"></div>
  <?php include "./includes/cart-view.php" ?>
		
	</div>
<?php include $page ?>
<?php include "./includes/footer.php"?>
</body>
</html>
