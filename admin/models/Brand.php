<?php
require_once __DIR__ . '/../config/database.php';

class Brand {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllBrands() {
        $stmt = $this->pdo->query("
            SELECT id, name, description, created_at 
            FROM brands 
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBrandById($id) {
        $stmt = $this->pdo->prepare("
            SELECT id, name, description, created_at 
            FROM brands 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addBrand($data) {
        try {
            $this->pdo->beginTransaction();

            // Check if brand with same name exists
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM brands WHERE name = ?
            ");
            $stmt->execute([$data['name']]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Brand with this name already exists");
            }

            // Insert brand
            $stmt = $this->pdo->prepare("
                INSERT INTO brands (name, description) 
                VALUES (?, ?)
            ");

            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? ''
            ]);

            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function updateBrand($id, $data) {
        try {
            $this->pdo->beginTransaction();

            // Check if brand exists
            $brand = $this->getBrandById($id);
            if (!$brand) {
                throw new Exception("Brand not found");
            }

            // Check if brand with same name exists (excluding this brand)
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM brands 
                WHERE name = ? AND id != ?
            ");
            $stmt->execute([$data['name'], $id]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Brand with this name already exists");
            }

            // Update brand
            $stmt = $this->pdo->prepare("
                UPDATE brands 
                SET name = ?,
                    description = ?
                WHERE id = ?
            ");

            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $id
            ]);

            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function deleteBrand($id) {
        try {
            $this->pdo->beginTransaction();

            // Check if brand exists
            $brand = $this->getBrandById($id);
            if (!$brand) {
                throw new Exception("Brand not found");
            }

            // Check if brand has products
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM products 
                WHERE brand_id = ?
            ");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Cannot delete brand with associated products");
            }

            // Delete brand
            $stmt = $this->pdo->prepare("
                DELETE FROM brands 
                WHERE id = ?
            ");
            $result = $stmt->execute([$id]);

            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function searchBrands($query) {
        $stmt = $this->pdo->prepare("
            SELECT id, name, description, created_at 
            FROM brands 
            WHERE name LIKE ? OR description LIKE ?
            ORDER BY created_at DESC
        ");
        $searchTerm = "%{$query}%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
