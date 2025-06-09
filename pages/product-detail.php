<?php
require_once 'controllers/ProductController.php';

$productController = new ProductController();
$productId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$productId) {
    die("Product not found");
}

$product = $productController->show($productId);

if (!$product) {
    die("Product not found");
}
?>

<section class="sec-product-detail bg0 p-t-65 p-b-60">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-lg-7 p-b-30">
                <div class="p-l-25 p-r-30 p-lr-0-lg">
                    <div class="wrap-slick3 flex-sb flex-w">
                        <div class="wrap-slick3-dots"></div>
                        <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                        <div class="slick3 gallery-lb">
                            <div class="item-slick3" data-thumb="<?= './admin/' . htmlspecialchars($product['image']); ?>">
                                <div class="wrap-pic-w pos-relative">
                                    <img src="<?= './admin/' . htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                                    <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="<?= './admin/' . htmlspecialchars($product['image']); ?>">
                                        <i class="fa fa-expand"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-5 p-b-30">
                <div class="p-r-50 p-t-5 p-lr-0-lg">
                    <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                        <?= htmlspecialchars($product['name']); ?>
                    </h4>

                    <span class="mtext-106 cl2">
                        $<?= htmlspecialchars($product['price']); ?>
                    </span>

                    <p class="stext-102 cl3 p-t-23">
                        <?= htmlspecialchars($product['description']); ?>
                    </p>

                    <div class="p-t-33">
                        <input type="number" id="quantity" value="1" min="1" class="size-113 bor8 stext-102 cl2 p-lr-20" style="width: 70px;">
                        <button class="add-to-cart"
                            data-id="<?= $product['id']; ?>"
                            data-name="<?= $product['name']; ?>"
                            data-price="<?= $product['price']; ?>"
                            data-image="<?= $product['image']; ?>">
                            Add to Cart
                        </button>
                        <p id="cart-message" class="stext-102 cl3 p-t-10"></p>
                    </div>


                    <p id="cart-message" class="stext-102 cl3 p-t-10"></p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.querySelector(".add-to-cart").addEventListener("click", function() {
        let productId = this.dataset.id;
        let productName = this.dataset.name;
        let productPrice = this.dataset.price;
        let productImage = this.dataset.image;
        let quantity = document.getElementById("quantity").value;

        fetch("./process/add_to_cart.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    image: productImage,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("cart-message").textContent = data.message;
                } else {
                    alert(data.message); // Show error message
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>