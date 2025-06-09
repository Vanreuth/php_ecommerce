<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProducts() {
        $stmt = $this->pdo->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct($name, $description, $category_id, $brand_id, $price, $stock, $image) {
        // Validate Category
        $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        if ($stmt->rowCount() == 0) {
            die("Error: Invalid category ID.");
        }
    
        // Validate Brand
        $stmt = $this->pdo->prepare("SELECT id FROM brands WHERE id = ?");
        $stmt->execute([$brand_id]);
        if ($stmt->rowCount() == 0) {
            die("Error: Invalid brand ID.");
        }
    
        // Insert Product
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, stock, category_id, brand_id, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([$name, $description, $price, $stock, $category_id, $brand_id, $image]);
    }

    public function updateProduct($id, $name, $description, $price, $stock, $category_id, $brand_id, $image = null) {
        // Validate Category
        $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        if ($stmt->rowCount() == 0) {
            die("Error: Invalid category ID.");
        }
    
        // Validate Brand
        $stmt = $this->pdo->prepare("SELECT id FROM brands WHERE id = ?");
        $stmt->execute([$brand_id]);
        if ($stmt->rowCount() == 0) {
            die("Error: Invalid brand ID.");
        }
    
        if ($image) {
            $stmt = $this->pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, brand_id = ?, image = ? WHERE id = ?");
            return $stmt->execute([$name, $description, $price, $stock, $category_id, $brand_id, $image, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, brand_id = ? WHERE id = ?");
            return $stmt->execute([$name, $description, $price, $stock, $category_id, $brand_id, $id]);
        }
    }

    public function deleteProduct($id) {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
