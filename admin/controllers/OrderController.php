<?php
ob_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController {
    private $orderModel;

    public function __construct() {
        $pdo = Database::connect();
        $this->orderModel = new Order($pdo);
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            try {
                switch ($action) {
                    case 'add':
                        $this->addOrder();
                        break;
                    case 'update':
                        $this->updateOrder();
                        break;
                    case 'delete':
                        $this->deleteOrder();
                        break;
                    case 'update_status':
                        $this->updateOrderStatus();
                        break;
                    default:
                        throw new Exception('Invalid action');
                }
                $_SESSION['success'] = 'Operation completed successfully';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            // Redirect back to the order management page
            header('Location: /eccommerce/admin/?p=order');
            exit;
        }
    }

    private function validateOrderData() {
        $required_fields = ['user_id', 'total_price'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        if (!is_numeric($_POST['total_price']) || $_POST['total_price'] < 0) {
            throw new Exception("Invalid total price value");
        }

        if (!is_numeric($_POST['user_id'])) {
            throw new Exception("Invalid user ID");
        }
    }

    private function addOrder() {
        $this->validateOrderData();

        $data = [
            'user_id' => filter_var($_POST['user_id'], FILTER_VALIDATE_INT),
            'total_price' => filter_var($_POST['total_price'], FILTER_VALIDATE_FLOAT),
            'status' => $this->validateInput($_POST['status'] ?? 'pending'),
            'shipping_address' => $this->validateInput($_POST['shipping_address'] ?? ''),
            'payment_method' => $this->validateInput($_POST['payment_method'] ?? '')
        ];

        // Handle order items
        if (!empty($_POST['items'])) {
            $data['items'] = [];
            foreach ($_POST['items'] as $item) {
                $data['items'][] = [
                    'product_id' => filter_var($item['product_id'], FILTER_VALIDATE_INT),
                    'quantity' => filter_var($item['quantity'], FILTER_VALIDATE_INT)
                ];
            }
        }

        if (!$this->orderModel->addOrder($data)) {
            throw new Exception('Failed to add order');
        }
    }

    private function updateOrder() {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid order ID');
        }

        $this->validateOrderData();

        $data = [
            'user_id' => filter_var($_POST['user_id'], FILTER_VALIDATE_INT),
            'total_price' => filter_var($_POST['total_price'], FILTER_VALIDATE_FLOAT),
            'status' => $this->validateInput($_POST['status']),
            'shipping_address' => $this->validateInput($_POST['shipping_address'] ?? ''),
            'payment_method' => $this->validateInput($_POST['payment_method'] ?? '')
        ];

        if (!$this->orderModel->updateOrder($id, $data)) {
            throw new Exception('Failed to update order');
        }
    }

    private function deleteOrder() {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid order ID');
        }

        if (!$this->orderModel->deleteOrder($id)) {
            throw new Exception('Failed to delete order');
        }
    }

    private function updateOrderStatus() {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid order ID');
        }

        $status = $this->validateInput($_POST['status']);
        if (!$this->orderModel->updateOrderStatus($id, $status)) {
            throw new Exception('Failed to update order status');
        }
    }

    private function validateInput($input) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }

    public function getUsers() {
        try {
            $stmt = Database::connect()->query("
                SELECT id, email, name 
                FROM users 
                ORDER BY name ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching users: " . $e->getMessage());
        }
    }
}

// Initialize and handle request
$controller = new OrderController();
$controller->handleRequest();
