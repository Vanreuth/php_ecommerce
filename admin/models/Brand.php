<?php
require_once __DIR__ . '/../config/database.php';

class Brand {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addBrand($name, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO brands (name, description) VALUES (?, ?)");
        return $stmt->execute([$name, $description]);
    }

    public function updateBrand($id, $name, $description) {
        $stmt = $this->pdo->prepare("UPDATE brands SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $id]);
    }

    public function deleteBrand($id) {
        $stmt = $this->pdo->prepare("DELETE FROM brands WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
