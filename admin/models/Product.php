<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProducts() {
        try {
            $stmt = $this->pdo->query("
                SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                ORDER BY p.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching products: " . $e->getMessage());
        }
    }

    public function getProductById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching product: " . $e->getMessage());
        }
    }

    public function getProductsByCategory($category_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.category_id = ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$category_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching products by category: " . $e->getMessage());
        }
    }

    public function addProduct($data) {
        try {
            $this->pdo->beginTransaction();

            // Validate required fields
            $required_fields = ['name', 'price', 'stock', 'category_id', 'brand_id'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("$field is required");
                }
            }

            // Validate Category
            $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE id = ?");
            $stmt->execute([$data['category_id']]);
            if ($stmt->rowCount() == 0) {
                throw new Exception("Invalid category ID");
            }

            // Validate Brand
            $stmt = $this->pdo->prepare("SELECT id FROM brands WHERE id = ?");
            $stmt->execute([$data['brand_id']]);
            if ($stmt->rowCount() == 0) {
                throw new Exception("Invalid brand ID");
            }

            // Validate numeric fields
            if (!is_numeric($data['price']) || $data['price'] < 0) {
                throw new Exception("Invalid price value");
            }
            if (!is_numeric($data['stock']) || $data['stock'] < 0) {
                throw new Exception("Invalid stock value");
            }

            // Insert Product
            $stmt = $this->pdo->prepare("
                INSERT INTO products (
                    name, description, price, stock, 
                    category_id, brand_id, image, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $data['price'],
                $data['stock'],
                $data['category_id'],
                $data['brand_id'],
                $data['image'] ?? null
            ]);

            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function updateProduct($id, $data) {
        try {
            $this->pdo->beginTransaction();

            // Check if product exists
            $product = $this->getProductById($id);
            if (!$product) {
                throw new Exception("Product not found");
            }

            // Validate required fields
            $required_fields = ['name', 'price', 'stock', 'category_id', 'brand_id'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("$field is required");
                }
            }

            // Validate Category
            $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE id = ?");
            $stmt->execute([$data['category_id']]);
            if ($stmt->rowCount() == 0) {
                throw new Exception("Invalid category ID");
            }

            // Validate Brand
            $stmt = $this->pdo->prepare("SELECT id FROM brands WHERE id = ?");
            $stmt->execute([$data['brand_id']]);
            if ($stmt->rowCount() == 0) {
                throw new Exception("Invalid brand ID");
            }

            // Validate numeric fields
            if (!is_numeric($data['price']) || $data['price'] < 0) {
                throw new Exception("Invalid price value");
            }
            if (!is_numeric($data['stock']) || $data['stock'] < 0) {
                throw new Exception("Invalid stock value");
            }

            // Update product
            if (isset($data['image']) && !empty($data['image'])) {
                $stmt = $this->pdo->prepare("
                    UPDATE products 
                    SET name = ?, description = ?, price = ?, 
                        stock = ?, category_id = ?, brand_id = ?, 
                        image = ? 
                    WHERE id = ?
                ");
                $result = $stmt->execute([
                    $data['name'],
                    $data['description'] ?? '',
                    $data['price'],
                    $data['stock'],
                    $data['category_id'],
                    $data['brand_id'],
                    $data['image'],
                    $id
                ]);
            } else {
                $stmt = $this->pdo->prepare("
                    UPDATE products 
                    SET name = ?, description = ?, price = ?, 
                        stock = ?, category_id = ?, brand_id = ?
                    WHERE id = ?
                ");
                $result = $stmt->execute([
                    $data['name'],
                    $data['description'] ?? '',
                    $data['price'],
                    $data['stock'],
                    $data['category_id'],
                    $data['brand_id'],
                    $id
                ]);
            }

            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function deleteProduct($id) {
        try {
            $this->pdo->beginTransaction();

            // Check if product exists
            $product = $this->getProductById($id);
            if (!$product) {
                throw new Exception("Product not found");
            }

            // Check if product has any associated orders
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM order_items 
                WHERE product_id = ?
            ");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Cannot delete product with associated orders");
            }

            // Delete product image if exists
            if (!empty($product['image'])) {
                $image_path = __DIR__ . '/../uploads/products/' . $product['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            // Delete product
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
            $result = $stmt->execute([$id]);

            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function searchProducts($query) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.name LIKE ? OR p.description LIKE ?
                ORDER BY p.created_at DESC
            ");
            $searchTerm = "%{$query}%";
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error searching products: " . $e->getMessage());
        }
    }
}
?>
