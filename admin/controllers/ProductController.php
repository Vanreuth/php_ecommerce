<?php
ob_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Brand.php';

class ProductController {
    private $pdo;
    private $productModel;
    private $categoryModel;
    private $brandModel;

    public function __construct() {
        $this->pdo = Database::connect();
        $this->productModel = new Product($this->pdo);
        $this->categoryModel = new Category($this->pdo);
        $this->brandModel = new Brand($this->pdo);
    }

    public function handleRequest() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            try {
                switch ($action) {
                    case 'add':
                        $this->addProduct();
                        break;
                    case 'update':
                        $this->updateProduct();
                        break;
                    case 'delete':
                        $this->deleteProduct();
                        break;
                    case 'search':
                        $this->searchProducts();
                        break;
                    case 'get':
                        $this->getProduct();
                        break;
                    case 'list':
                    default:
                        $this->listProducts();
                        break;
                }
                $_SESSION['success'] = 'Operation completed successfully';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            // Redirect back to the product management page
            header('Location: /eccommerce/admin/?p=product');
            exit;
        }
    }

    private function handleImageUpload() {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Validate file type
        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed');
        }

        // Validate file size
        if ($file['size'] > $max_size) {
            throw new Exception('File size too large. Maximum size is 5MB');
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $upload_path = __DIR__ . '/../uploads/products/';

        // Create directory if it doesn't exist
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $upload_path . $filename)) {
            throw new Exception('Failed to upload file');
        }

        return $filename;
    }

    private function addProduct() {


        $this->validateProductData();

        $result = $this->productModel->addProduct($_POST, $_FILES['image']);

        if ($result) {
            $_SESSION['success'] = "Product added successfully!";
        } else {
            throw new Exception('Failed to add product');
        }

        $this->redirect();
    }

    private function updateProduct() {


        if (!isset($_POST['id'])) { 
            throw new Exception('Product ID is required');
        }

        $this->validateProductData();

        $image = isset($_FILES['image']) && $_FILES['image']['size'] > 0 ? $_FILES['image'] : null;
        $result = $this->productModel->updateProduct($_POST['id'], $_POST, $image);

        if ($result) {
            $_SESSION['success'] = "Product updated successfully!";
        } else {
            throw new Exception('Failed to update product');
        }

        $this->redirect();
    }

    private function deleteProduct() {

        if (!isset($_POST['id'])) {
            throw new Exception('Product ID is required');
        }

        $result = $this->productModel->deleteProduct($_POST['id']);

        if ($result) {
            $_SESSION['success'] = "Product deleted successfully!";
        } else {
            throw new Exception('Failed to delete product');
        }

        $this->redirect();
    }

    private function searchProducts() {
        if (!isset($_GET['q'])) {
            throw new Exception('Search query is required');
        }

        $products = $this->productModel->searchProducts($_GET['q']);
        header('Content-Type: application/json');
        echo json_encode(['products' => $products]);
        exit();
    }

    private function getProduct() {
        if (!isset($_GET['id'])) {
            throw new Exception('Product ID is required');
        }

        $product = $this->productModel->getProductById($_GET['id']);
        
        if ($product) {
            header('Content-Type: application/json');
            echo json_encode(['product' => $product]);
        } else {
            throw new Exception('Product not found');
        }
        exit();
    }

    private function listProducts() {
        $categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : null;
        $brandId = isset($_GET['brand_id']) ? $_GET['brand_id'] : null;

        if ($categoryId) {
            $products = $this->productModel->getProductsByCategory($categoryId);
        } elseif ($brandId) {
            $products = $this->productModel->getProductsByBrand($brandId);
        } else {
            $products = $this->productModel->getAllProducts();
        }

        $categories = $this->categoryModel->getAllCategories();
        $brands = $this->brandModel->getAllBrands();

        // Include the view
        require_once __DIR__ . '/../views/product/list.php';
    }

    private function validateProductData() {
        $requiredFields = ['name', 'description', 'price', 'stock', 'category_id', 'brand_id'];
        $missing = [];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new Exception('Required fields missing: ' . implode(', ', $missing));
        }

        // Validate price and stock are numeric
        if (!is_numeric($_POST['price']) || $_POST['price'] < 0) {
            throw new Exception('Invalid price value');
        }

        if (!is_numeric($_POST['stock']) || $_POST['stock'] < 0) {
            throw new Exception('Invalid stock value');
        }

        // Validate image on new product
        if (!isset($_POST['id']) && (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE)) {
            throw new Exception('Product image is required');
        }
    }

    private function redirect() {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header("Location: /eccommerce/admin/index.php?p=product");
        exit();
    }

    public function getCategories() {
        try {
            $stmt = Database::connect()->query("
                SELECT id, name 
                FROM categories 
                ORDER BY name ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching categories: " . $e->getMessage());
        }
    }

    public function getBrands() {
        try {
            $stmt = Database::connect()->query("
                SELECT id, name 
                FROM brands 
                ORDER BY name ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching brands: " . $e->getMessage());
        }
    }
}

// Handle the request
$controller = new ProductController();
$controller->handleRequest();
ob_end_flush();
?>
