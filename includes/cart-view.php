<?php 
// Check if the cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart']));
$totalPrice = 0;
foreach ($_SESSION['cart'] as $product) {
    $totalPrice += $product['price'] * $product['quantity'];
}
?>
<div class="header-cart flex-col-l p-l-65 p-r-25">
			<div class="header-cart-title flex-w flex-sb-m p-b-8">
				<span class="mtext-103 cl2">
					Your Cart
				</span>

				<div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
					<i class="zmdi zmdi-close"></i>
				</div>
			</div>
			
			<div class="header-cart-content flex-w js-pscroll">
				<ul class="header-cart-wrapitem w-full">
					<li class="header-cart-item flex-w flex-t m-b-12">
					<?php foreach ($_SESSION['cart'] as $productId => $product): ?>
						<div class="header-cart-item-img">
						<img src="./admin/<?= htmlspecialchars($product['image']); ?>" alt="IMG">
						</div>

						<div class="header-cart-item-txt p-t-8">
							<a href="#" class="header-cart-item-name m-b-18 hov-cl1 trans-04">
							<?= htmlspecialchars($product['name']); ?>
							</a>

							<span class="header-cart-item-info">
							<?= htmlspecialchars($product['quantity']); ?> x $<?= htmlspecialchars($product['price']); ?>
							</span>
						</div>
						<?php endforeach; ?>
					</li>


				</ul>
				
				<div class="w-full">
					<div class="header-cart-total w-full p-tb-40">
						Total:  $<?= number_format($totalPrice, 2); ?>
					</div>

					<div class="header-cart-buttons flex-w w-full">
						<a href="index.php?p=cart" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
							View Cart
						</a>

						<a href="index.php?p=cart" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
							Check Out
						</a>
					</div>
				</div>
			</div>
		</div>