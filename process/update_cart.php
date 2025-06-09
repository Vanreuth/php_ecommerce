<?php
// Start the session
session_start();

// Check if the request is an AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate product ID and action
    if (!isset($_POST['product_id']) || !isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'Missing product_id or action.']);
        exit;
    }

    $productId = $_POST['product_id'];
    $action = $_POST['action'];

    // Validate product ID
    if (!isset($_SESSION['cart'][$productId])) {
        echo json_encode(['success' => false, 'message' => 'Product not found in cart.']);
        exit;
    }

    // Update quantity based on action
    if ($action === 'increase') {
        $_SESSION['cart'][$productId]['quantity'] += 1;
    } elseif ($action === 'decrease' && $_SESSION['cart'][$productId]['quantity'] > 1) {
        $_SESSION['cart'][$productId]['quantity'] -= 1;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action or quantity.']);
        exit;
    }

    // Calculate new total for the product
    $newQuantity = $_SESSION['cart'][$productId]['quantity'];
    $newTotal = $_SESSION['cart'][$productId]['price'] * $newQuantity;

    // Calculate the cart total
    $cartTotal = array_sum(array_map(fn($p) => $p['price'] * $p['quantity'], $_SESSION['cart']));

    // Return JSON response
    echo json_encode([
        'success' => true,
        'newQuantity' => $newQuantity,
        'newTotal' => $newTotal,
        'cartTotal' => $cartTotal
    ]);
    exit;
}

// Invalid request method
echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
exit;
?>
