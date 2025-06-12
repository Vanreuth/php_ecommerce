<?php

// Start session at the top of the file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
}
// Handle clearing the cart
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header("Location: index.php?p=cart");
    exit;
}

// Initialize total price
$totalPrice = 0;

// Check if the cart is empty
$cartIsEmpty = !isset($_SESSION['cart']) || empty($_SESSION['cart']);

if (!$cartIsEmpty) {
    // Calculate total price
    foreach ($_SESSION['cart'] as $product) {
        $totalPrice += $product['price'] * $product['quantity'];
    }
}
?>
    <!-- Breadcrumb -->
    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-90 p-lr-0-lg">
            <a href="index.php" class="stext-109 cl8 hov-cl1 trans-04">
                Home
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>
            <span class="stext-109 cl4">
                Shopping Cart
            </span>
        </div>
    </div>

    <!-- Shopping Cart -->
    <form class="bg0 p-t-75 p-b-85" method="POST" action="index.php?p=cart">
        <div class="container">
            <?php if ($cartIsEmpty): ?>
            <div class="alert alert-info text-center">
                Your cart is empty. <a href="index.php?p=product" class="text-primary">Continue shopping</a>
            </div>
            <?php else: ?>
            <div class="row">
                <div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
                    <div class="m-l-25 m-r--38 m-lr-0-xl">
                        <div class="wrap-table-shopping-cart">
                            <table class="table-shopping-cart">
                                <tr class="table_head">
                                    <th class="column-1">Product</th>
                                    <th class="column-2">Name</th>
                                    <th class="column-3">Price</th>
                                    <th class="column-4">Quantity</th>
                                    <th class="column-5">Total</th>
                                    <th class="column-6">Action</th>
                                </tr>

                                <?php foreach ($_SESSION['cart'] as $productId => $product): ?>
                                    <tr class="table_row">
                                        <td class="column-1">
                                            <div class="how-itemcart1">
                                                <img src="./admin/<?= htmlspecialchars($product['image']); ?>" alt="IMG">
                                            </div>
                                        </td>
                                        <td class="column-2"><?= htmlspecialchars($product['name']); ?></td>
                                        <td class="column-3">$<?= number_format($product['price'], 2); ?></td>
                                        <td class="column-4">
                                            <div class="wrap-num-product flex-w m-l-auto m-r-0">
                                                <!-- Decrease Quantity Button -->
                                                <button class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m" data-product-id="<?= $productId; ?>" data-action="decrease">
                                                    <i class="fs-16 zmdi zmdi-minus"></i>
                                                </button>

                                                <!-- Display Quantity -->
                                                <span class="mtext-104 cl3 txt-center num-product" id="quantity-<?= $productId; ?>">
                                                    <?= $product['quantity']; ?>
                                                </span>

                                                <!-- Increase Quantity Button -->
                                                <button class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m" data-product-id="<?= $productId; ?>" data-action="increase">
                                                    <i class="fs-16 zmdi zmdi-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="column-5" id="total-<?= $productId; ?>">
                                            $<?= number_format($product['price'] * $product['quantity'], 2); ?>
                                        </td>
                                        <td class="column-6">
                                            <a href="index.php?p=cart&action=remove&product_id=<?= $productId; ?>" class="flex-c-m stext-101 cl2 size-100 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-10" onclick="return confirm('Are you sure you want to remove this item from the cart?');">Remove</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>

                        <!-- Clear Cart Button -->
                        <div class="flex-w flex-sb-m bor15 p-t-18 p-b-15 p-lr-40 p-lr-15-sm">
                            <form method="post" action="index.php?p=cart&action=remove&product_id=<?= $productId; ?>" style="display: inline;">
                                <input type="hidden" name="clear_cart" value="1">
                                <button type="submit" class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-10">
                                    Delete Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Cart Totals -->
                <div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-b-50">
                    <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
                        <h4 class="mtext-109 cl2 p-b-30">
                            Cart Totals
                        </h4>

                        <div class="flex-w flex-t bor12 p-b-13">
                            <div class="size-208">
                                <span class="stext-110 cl2">
                                    Subtotal:
                                </span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl2" id="cart-total">
                                    $<?= number_format($totalPrice, 2); ?>
                                </span>
                            </div>
                        </div>

                        <div class="flex-w flex-t bor12 p-t-15 p-b-30">
                            <div class="size-208 w-full-ssm">
                                <span class="stext-110 cl2">
                                    Shipping:
                                </span>
                            </div>
                            <div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
                                <p class="stext-111 cl6 p-t-2">
                                    There are no shipping methods available. Please double check your address, or contact us if you need any help.
                                </p>
                            </div>
                        </div>

                        <div class="flex-w flex-t p-t-27 p-b-33">
                            <div class="size-208">
                                <span class="mtext-101 cl2">
                                    Total:
                                </span>
                            </div>
                            <div class="size-209 p-t-1">
                                <span class="mtext-110 cl2" id="cart-total">
                                    $<?= number_format($totalPrice, 2); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <div id="payment-form" class="p-t-20">
                            <h4 class="mtext-109 cl2 p-b-20">Payment Information</h4>
                            
                            <div class="bor8 m-b-20 how-pos4-parent">
                                <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="full_name" placeholder="Full Name" required>
                            </div>

                            <div class="bor8 m-b-20 how-pos4-parent">
                                <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="email" name="email" placeholder="Email Address" required>
                            </div>

                            <div class="bor8 m-b-20 how-pos4-parent">
                                <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="address" placeholder="Address" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="bor8 m-b-20 how-pos4-parent">
                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="city" placeholder="City" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bor8 m-b-20 how-pos4-parent">
                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="postal_code" placeholder="Postal Code" required>
                                    </div>
                                </div>
                            </div>

                            <div class="bor8 m-b-20 how-pos4-parent">
                                <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="card_number" placeholder="Card Number" maxlength="16" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="bor8 m-b-20 how-pos4-parent">
                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="expiry_date" placeholder="MM/YY" maxlength="5" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bor8 m-b-20 how-pos4-parent">
                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="cvv" placeholder="CVV" maxlength="4" required>
                                    </div>
                                </div>
                            </div>

                            <div id="payment-errors" class="text-danger m-b-20" style="display: none;"></div>

                            <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer" id="process-payment">
                                Process Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </form>

    <!-- JavaScript for AJAX -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add event listeners to all quantity buttons
        document.querySelectorAll('.btn-num-product-up, .btn-num-product-down').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-product-id');
                const action = this.getAttribute('data-action');

                // Send AJAX request to update quantity
                updateCartQuantity(productId, action);
            });
        });

        // Function to update cart quantity via AJAX
        function updateCartQuantity(productId, action) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', './process/update_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        // Update the quantity displayed on the page
                        document.getElementById(`quantity-${productId}`).textContent = response.newQuantity;

                        // Update the total price for the product
                        document.getElementById(`total-${productId}`).textContent = `$${response.newTotal.toFixed(2)}`;

                        // Update the cart total
                        document.getElementById('cart-total').textContent = `$${response.cartTotal.toFixed(2)}`;
                    } else {
                        alert('Failed to update cart.');
                    }
                } else {
                    alert('An error occurred. Please try again.');
                }
            };

            xhr.send(`product_id=${productId}&action=${action}`);
        }

        // Payment form submission
        const paymentForm = document.getElementById('payment-form');
        const processPaymentBtn = document.getElementById('process-payment');
        const paymentErrors = document.getElementById('payment-errors');

        processPaymentBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Collect form data
            const formData = new FormData(paymentForm);
            
            // Send payment request
            fetch('./process/process_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message and redirect
                    alert('Payment successful! Order ID: ' + data.order_id);
                    window.location.href = 'index.php?p=home';
                } else {
                    // Show error messages
                    paymentErrors.style.display = 'block';
                    paymentErrors.innerHTML = data.errors ? data.errors.join('<br>') : data.message;
                }
            })
            .catch(error => {
                paymentErrors.style.display = 'block';
                paymentErrors.innerHTML = 'An error occurred. Please try again.';
            });
        });

        // Format card number input
        const cardNumberInput = document.querySelector('input[name="card_number"]');
        cardNumberInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });

        // Format expiry date input
        const expiryDateInput = document.querySelector('input[name="expiry_date"]');
        expiryDateInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0,2) + '/' + value.slice(2);
            }
            this.value = value;
        });

        // Format CVV input
        const cvvInput = document.querySelector('input[name="cvv"]');
        cvvInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
    });
    </script>