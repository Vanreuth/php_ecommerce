<?php
require_once 'config/database.php';

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function createOrder($orderData) {
        try {
            $this->db->beginTransaction();

            // Insert into orders table
            $sql = "INSERT INTO orders (customer_name, email, address, city, postal_code, total_amount, payment_status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $orderData['customer_name'],
                $orderData['email'],
                $orderData['address'],
                $orderData['city'],
                $orderData['postal_code'],
                $orderData['total_amount'],
                $orderData['payment_status']
            ]);

            $orderId = $this->db->lastInsertId();

            // Insert order items
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            foreach ($orderData['items'] as $productId => $item) {
                $stmt->execute([
                    $orderId,
                    $productId,
                    $item['quantity'],
                    $item['price']
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getLastOrderId() {
        return $this->db->lastInsertId();
    }

    public function getOrder($orderId) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, p.name, p.image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 