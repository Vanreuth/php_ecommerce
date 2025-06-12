<?php
require_once './admin/config/database.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAllProducts() {
        $sql = "SELECT * FROM products";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsByCategory($categoryId) {
        $sql = "SELECT * FROM products WHERE category_id = ?";
        $query = $this->db->prepare($sql);
        $query->execute([$categoryId]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    public function getAllCategories() {
        $stmt = $this->db->prepare("SELECT id, name FROM categories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
