<?php
require_once 'models/Product.php';

class ProductController {
    public function index($categoryId = null) {
        $productModel = new Product();
        if ($categoryId) {
            $products = $productModel->getProductsByCategory($categoryId);
        } else {
            $products = $productModel->getAllProducts();
        }
        return $products;
    }
    public function show($id) {
        $productModel = new Product();
        $product = $productModel->getProductById($id);
        return $product;
    }
    public function getCategories() {
        $productModel = new Product();
        return $productModel->getAllCategories();
    }

    
}
?>
