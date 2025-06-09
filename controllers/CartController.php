<?php
require_once './models/Cart.php';

class CartController {
    private $cartModel;

    public function __construct() {
        $this->cartModel = new Cart();
    }

    public function addToCart($productId, $productName, $productPrice, $productImage, $quantity) {
        $this->cartModel->addToCart($productId, $productName, $productPrice, $productImage, $quantity);
    }

    public function getCartItems() {
        return $this->cartModel->getCartItems();
    }

    public function clearCart() {
        $this->cartModel->clearCart();
    }
}
?>
