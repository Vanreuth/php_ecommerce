<?php
require_once __DIR__ . '/../config/database.php';

class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllOrders() {
        try {
            $stmt = $this->pdo->query("
                SELECT o.*, u.email as user_email,
                       COUNT(oi.id) as total_items,
                       SUM(oi.quantity) as total_quantity
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                GROUP BY o.id
                ORDER BY o.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching orders: " . $e->getMessage());
        }
    }

    public function getOrderById($id) {
        try {
            // Get order details
            $stmt = $this->pdo->prepare("
                SELECT o.*, u.email as user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ");
            $stmt->execute([$id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new Exception("Order not found");
            }

            // Get order items
            $stmt = $this->pdo->prepare("
                SELECT oi.*, p.name as product_name, p.price as product_price
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$id]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $order;
        } catch (Exception $e) {
            throw new Exception("Error fetching order: " . $e->getMessage());
        }
    }

    public function addOrder($data) {
        try {
            $this->pdo->beginTransaction();

            // Validate user
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$data['user_id']]);
            if ($stmt->rowCount() == 0) {
                throw new Exception("Invalid user ID");
            }

            // Insert order
            $stmt = $this->pdo->prepare("
                INSERT INTO orders (
                    user_id, total_price, status, 
                    shipping_address, payment_method, 
                    created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $data['user_id'],
                $data['total_price'],
                $data['status'] ?? 'pending',
                $data['shipping_address'] ?? null,
                $data['payment_method'] ?? null
            ]);

            $order_id = $this->pdo->lastInsertId();

            // Insert order items if provided
            if (!empty($data['items'])) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO order_items (
                        order_id, product_id, quantity, 
                        price, created_at
                    ) VALUES (?, ?, ?, ?, NOW())
                ");

                foreach ($data['items'] as $item) {
                    // Validate product
                    $product_stmt = $this->pdo->prepare("
                        SELECT id, price, stock 
                        FROM products 
                        WHERE id = ?
                    ");
                    $product_stmt->execute([$item['product_id']]);
                    $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$product) {
                        throw new Exception("Product not found: " . $item['product_id']);
                    }

                    if ($product['stock'] < $item['quantity']) {
                        throw new Exception("Insufficient stock for product: " . $item['product_id']);
                    }

                    // Insert order item
                    $stmt->execute([
                        $order_id,
                        $item['product_id'],
                        $item['quantity'],
                        $product['price']
                    ]);

                    // Update product stock
                    $update_stmt = $this->pdo->prepare("
                        UPDATE products 
                        SET stock = stock - ? 
                        WHERE id = ?
                    ");
                    $update_stmt->execute([$item['quantity'], $item['product_id']]);
                }
            }

            $this->pdo->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function updateOrder($id, $data) {
        try {
            $this->pdo->beginTransaction();

            // Check if order exists
            $order = $this->getOrderById($id);
            if (!$order) {
                throw new Exception("Order not found");
            }

            // Validate user if provided
            if (isset($data['user_id'])) {
                $stmt = $this->pdo->prepare("SELECT id FROM users WHERE id = ?");
                $stmt->execute([$data['user_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new Exception("Invalid user ID");
                }
            }

            // Update order
            $stmt = $this->pdo->prepare("
                UPDATE orders 
                SET user_id = ?,
                    total_price = ?,
                    status = ?,
                    shipping_address = ?,
                    payment_method = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $data['user_id'],
                $data['total_price'],
                $data['status'],
                $data['shipping_address'] ?? null,
                $data['payment_method'] ?? null,
                $id
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function deleteOrder($id) {
        try {
            $this->pdo->beginTransaction();

            // Check if order exists
            $order = $this->getOrderById($id);
            if (!$order) {
                throw new Exception("Order not found");
            }

            // Restore product stock
            $stmt = $this->pdo->prepare("
                SELECT product_id, quantity 
                FROM order_items 
                WHERE order_id = ?
            ");
            $stmt->execute([$id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                $update_stmt = $this->pdo->prepare("
                    UPDATE products 
                    SET stock = stock + ? 
                    WHERE id = ?
                ");
                $update_stmt->execute([$item['quantity'], $item['product_id']]);
            }

            // Delete order items
            $stmt = $this->pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$id]);

            // Delete order
            $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function updateOrderStatus($id, $status) {
        try {
            $this->pdo->beginTransaction();

            // Check if order exists
            $order = $this->getOrderById($id);
            if (!$order) {
                throw new Exception("Order not found");
            }

            // Validate status
            $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($status, $valid_statuses)) {
                throw new Exception("Invalid order status");
            }

            // Update status
            $stmt = $this->pdo->prepare("
                UPDATE orders 
                SET status = ? 
                WHERE id = ?
            ");
            $stmt->execute([$status, $id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function searchOrders($query) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT o.*, u.email as user_email,
                       COUNT(oi.id) as total_items,
                       SUM(oi.quantity) as total_quantity
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.id LIKE ? OR 
                      u.email LIKE ? OR 
                      o.status LIKE ?
                GROUP BY o.id
                ORDER BY o.created_at DESC
            ");
            $searchTerm = "%{$query}%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error searching orders: " . $e->getMessage());
        }
    }
}
?>
