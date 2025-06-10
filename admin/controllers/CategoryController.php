<?php
ob_start(); // Start output buffering
session_start(); // Start session

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $pdo = Database::connect();
        $this->categoryModel = new Category($pdo);
    }

    public function handleRequest() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            try {
                switch ($action) {
                    case 'add':
                        $this->addCategory();
                        break;
                    case 'update':
                        $this->updateCategory();
                        break;
                    case 'delete':
                        $this->deleteCategory();
                        break;
                    default:
                        throw new Exception('Invalid action');
                }
                $_SESSION['success'] = 'Operation completed successfully';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            // Redirect back to the category management page
            header('Location: /eccommerce/admin/?p=category');
            exit;
        }
    }

    private function addCategory() {

        $data = [
            'name' => $this->validateInput($_POST['name']),
            'description' => $this->validateInput($_POST['description'] ?? '')
        ];

        if (empty($data['name'])) {
            throw new Exception('Category name is required');
        }

        if (!$this->categoryModel->addCategory($data)) {
            throw new Exception('Failed to add category');
        }
    }

    private function updateCategory() {

        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid category ID');
        }

        $data = [
            'name' => $this->validateInput($_POST['name']),
            'description' => $this->validateInput($_POST['description'] ?? '')
        ];

        if (empty($data['name'])) {
            throw new Exception('Category name is required');
        }

        if (!$this->categoryModel->updateCategory($id, $data)) {
            throw new Exception('Failed to update category');
        }
    }

    private function deleteCategory() {

        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid category ID');
        }

        if (!$this->categoryModel->deleteCategory($id)) {
            throw new Exception('Failed to delete category');
        }
    }

    private function validateInput($input) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }
}

// Initialize and handle request
$controller = new CategoryController();
$controller->handleRequest();
?> 