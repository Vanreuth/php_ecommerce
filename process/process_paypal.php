<?php
session_start();
require_once '../config/database.php';

// Get the JSON POST data
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Initialize response array
$response = ['success' => false, 'message' => ''];

try {
    // Validate required data
    if (!isset($data['orderID']) || !isset($data['payerID']) || !isset($data['paymentID']) || !isset($data['paymentStatus'])) {
        throw new Exception('Missing required payment information');
    }

    // Verify payment status
    if ($data['paymentStatus'] !== 'COMPLETED') {
        throw new Exception('Payment not completed');
    }

    // Get cart data
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        throw new Exception('Cart is empty');
    }

    // Calculate total amount
    $totalAmount = 0;
    foreach ($_SESSION['cart'] as $product) {
        $totalAmount += $product['price'] * $product['quantity'];
    }

    // Start transaction
    $pdo->beginTransaction();

    // Create order record
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_method, payment_id, status, created_at) VALUES (?, ?, 'paypal', ?, 'completed', NOW())");
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $stmt->execute([$userId, $totalAmount, $data['paymentID']]);
    $orderId = $pdo->lastInsertId();

    // Insert order items
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $productId => $product) {
        $stmt->execute([$orderId, $productId, $product['quantity'], $product['price']]);
    }

    // Commit transaction
    $pdo->commit();

    // Clear the cart
    unset($_SESSION['cart']);

    // Send success response
    $response = [
        'success' => true,
        'order_id' => $orderId,
        'message' => 'Payment processed successfully'
    ];

} catch (Exception $e) {
    // Rollback transaction if started
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response); 