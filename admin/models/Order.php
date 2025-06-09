<?php
class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllOrders() {
        $stmt = $this->pdo->query("SELECT * FROM orders");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addOrder($user_id, $total_price, $status) {
        $stmt = $this->pdo->prepare("INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$user_id, $total_price, $status]);
    }

    public function updateOrder($id, $user_id, $total_price, $status) {
        $stmt = $this->pdo->prepare("UPDATE orders SET user_id = ?, total_price = ?, status = ? WHERE id = ?");
        return $stmt->execute([$user_id, $total_price, $status, $id]);
    }

    public function deleteOrder($id) {
        $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
