<?php
session_start();

// Decode the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Validate the input data
if (isset($data['id'], $data['quantity'], $data['name'], $data['price'], $data['image'])) {
    $productId = $data['id'];
    $quantity = (int)$data['quantity'];
    $productName = $data['name'];
    $productPrice = (float)$data['price'];
    $productImage = $data['image'];

    // Initialize the cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add or update the product in the cart
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'name' => $productName,
            'price' => $productPrice,
            'image' => $productImage,
            'quantity' => $quantity
        ];
    }

    // Return success response
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
    exit();
} else {
    // Return error response if required fields are missing
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}
?>