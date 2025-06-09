<?php
session_start();
require_once '../models/Order.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required_fields = ['full_name', 'email', 'address', 'city', 'postal_code', 'card_number', 'expiry_date', 'cvv'];
    $errors = [];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }

    // Validate card number (simple validation)
    if (!preg_match('/^\d{16}$/', $_POST['card_number'])) {
        $errors[] = 'Invalid card number';
    }

    // Validate expiry date (MM/YY format)
    if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $_POST['expiry_date'])) {
        $errors[] = 'Invalid expiry date';
    }

    // Validate CVV
    if (!preg_match('/^\d{3,4}$/', $_POST['cvv'])) {
        $errors[] = 'Invalid CVV';
    }

    if (empty($errors)) {
        // Calculate total amount
        $totalAmount = 0;
        foreach ($_SESSION['cart'] as $product) {
            $totalAmount += $product['price'] * $product['quantity'];
        }

        // Create order
        $order = new Order();
        $orderData = [
            'customer_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'address' => $_POST['address'],
            'city' => $_POST['city'],
            'postal_code' => $_POST['postal_code'],
            'total_amount' => $totalAmount,
            'payment_status' => 'completed',
            'items' => $_SESSION['cart']
        ];

        if ($order->createOrder($orderData)) {
            // Clear the cart after successful order
            unset($_SESSION['cart']);
            
            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Payment processed successfully',
                'order_id' => $order->getLastOrderId()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create order'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
} 