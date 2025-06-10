<?php
require_once __DIR__ . '/../config/database.php';

class Category {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllCategories() {
        $stmt = $this->pdo->query("
            SELECT id, name, description, created_at 
            FROM categories 
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id) {
        $stmt = $this->pdo->prepare("
            SELECT id, name, description, created_at 
            FROM categories 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getParentCategories() {
        $stmt = $this->pdo->query("
            SELECT id, name 
            FROM categories 
            WHERE parent_id IS NULL 
            ORDER BY name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCategory($data) {
        try {
            $this->pdo->beginTransaction();

            // Check if category with same name exists
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM categories WHERE name = ?
            ");
            $stmt->execute([$data['name']]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Category with this name already exists");
            }

            // Insert category
            $stmt = $this->pdo->prepare("
                INSERT INTO categories (name, description) 
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

    public function updateCategory($id, $data) {
        try {
            $this->pdo->beginTransaction();

            // Check if category exists
            $category = $this->getCategoryById($id);
            if (!$category) {
                throw new Exception("Category not found");
            }

            // Check if category with same name exists (excluding this category)
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM categories 
                WHERE name = ? AND id != ?
            ");
            $stmt->execute([$data['name'], $id]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Category with this name already exists");
            }

            // Update category
            $stmt = $this->pdo->prepare("
                UPDATE categories 
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

    public function deleteCategory($id) {
        try {
            $this->pdo->beginTransaction();

            // Check if category exists
            $category = $this->getCategoryById($id);
            if (!$category) {
                throw new Exception("Category not found");
            }

            // Check if category has products
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM products 
                WHERE category_id = ?
            ");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Cannot delete category with associated products");
            }

            // Delete category
            $stmt = $this->pdo->prepare("
                DELETE FROM categories 
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

    public function toggleStatus($id) {
        try {
            // Get current status
            $stmt = $this->pdo->prepare("SELECT status FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$category) {
                throw new Exception("Category not found");
            }

            $stmt = $this->pdo->prepare("
                UPDATE categories 
                SET status = CASE 
                    WHEN status = 'active' THEN 'inactive' 
                    ELSE 'active' 
                END 
                WHERE id = ?
            ");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function searchCategories($query) {
        $stmt = $this->pdo->prepare("
            SELECT id, name, description, created_at 
            FROM categories 
            WHERE name LIKE ? OR description LIKE ?
            ORDER BY created_at DESC
        ");
        $searchTerm = "%{$query}%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function wouldCreateCycle($categoryId, $newParentId) {
        // Check if new parent is a descendant of the category
        $currentId = $newParentId;
        $visited = [$categoryId];

        while ($currentId) {
            if (in_array($currentId, $visited)) {
                return true;
            }
            $visited[] = $currentId;

            $stmt = $this->pdo->prepare("SELECT parent_id FROM categories WHERE id = ?");
            $stmt->execute([$currentId]);
            $parent = $stmt->fetch(PDO::FETCH_ASSOC);
            $currentId = $parent ? $parent['parent_id'] : null;
        }

        return false;
    }
}
?>
