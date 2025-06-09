<?php
require_once __DIR__ . '/../models/Product.php';

$product = new Product($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == "add") {
        $imagePath = ''; 
        if (!empty($_FILES['image']['name'])) {
            $file_name = str_replace(" ", "-", $_FILES['image']['name']);
            $imagePath = 'uploads/' . $file_name;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../' . $imagePath);
        } 
        $product->addProduct($_POST['name'], $_POST['description'], $_POST['category_id'], $_POST['brand_id'], $_POST['price'], $_POST['stock'], $imagePath);
    } elseif ($action == "update") {
        $product->updateProduct($_POST['id'], $_POST['name'], $_POST['description'], $_POST['category_id'], $_POST['brand_id'], $_POST['price'], $_POST['stock']);
    } elseif ($action == "delete") {
        $product->deleteProduct($_POST['id']);
    }

    header("Location: /eccommerce/admin/index.php?p=product");
    exit();
}

?>
