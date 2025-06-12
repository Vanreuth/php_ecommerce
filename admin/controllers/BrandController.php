<?php
ob_start(); // Start output buffering
session_start(); // Start session

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Brand.php';

class BrandController {
    private $brandModel;

    public function __construct() {
        $pdo = Database::connect();
        $this->brandModel = new Brand($pdo);
    }

    public function handleRequest() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            try {
                switch ($action) {
                    case 'add':
                        $this->addBrand();
                        break;
                    case 'update':
                        $this->updateBrand();
                        break;
                    case 'delete':
                        $this->deleteBrand();
                        break;
                    default:
                        throw new Exception('Invalid action');
                }
                $_SESSION['success'] = 'Operation completed successfully';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            // Redirect back to the brand management page
            header('Location: /admin/?p=brand');
            exit;
        }

        $action = $_GET['action'] ?? null;
        if ($action === 'getBrands') {
            $stmt = $pdo->query("SELECT id, name FROM brands");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            exit();
        }
    }

    private function addBrand() {


        $data = [
            'name' => $this->validateInput($_POST['name']),
            'description' => $this->validateInput($_POST['description'] ?? '')
        ];

        if (empty($data['name'])) {
            throw new Exception('Brand name is required');
        }

        if (!$this->brandModel->addBrand($data)) {
            throw new Exception('Failed to add brand');
        }
    }

    private function updateBrand() {

        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid brand ID');
        }

        $data = [
            'name' => $this->validateInput($_POST['name']),
            'description' => $this->validateInput($_POST['description'] ?? '')
        ];

        if (empty($data['name'])) {
            throw new Exception('Brand name is required');
        }

        if (!$this->brandModel->updateBrand($id, $data)) {
            throw new Exception('Failed to update brand');
        }
    }

    private function deleteBrand() {


        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid brand ID');
        }

        if (!$this->brandModel->deleteBrand($id)) {
            throw new Exception('Failed to delete brand');
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
$controller = new BrandController();
$controller->handleRequest();
