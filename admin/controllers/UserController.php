<?php
ob_start(); // Start output buffering
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        $pdo = Database::connect();
        $this->userModel = new User($pdo);
    }

    public function handleRequest() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            try {
                switch ($action) {
                    case 'add':
                        $this->addUser();
                        break;
                    case 'update':
                        $this->updateUser();
                        break;
                    case 'delete':
                        $this->deleteUser();
                        break;
                    case 'toggle':
                        $this->toggleUserStatus();
                        break;
                    default:
                        throw new Exception('Invalid action');
                }
                $_SESSION['success'] = 'Operation completed successfully';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            // Redirect back to the user management page
            header('Location: /admin/?p=usermagement');
            exit;
        }
    }

    private function addUser() {
        $data = [
            'name' => $this->validateInput($_POST['name']),
            'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
            'phone' => $this->validateInput($_POST['phone'] ?? ''),
            'address' => $this->validateInput($_POST['address'] ?? ''),
            'password' => $_POST['password'] ?: $this->generateRandomPassword(),
            'role' => in_array($_POST['role'], ['admin', 'user']) ? $_POST['role'] : 'user',
            'status' => in_array($_POST['status'], ['active', 'inactive']) ? $_POST['status'] : 'active'
        ];

        if (!$data['email']) {
            throw new Exception('Invalid email address');
        }

        if (!$this->userModel->addUser($data)) {
            throw new Exception('Failed to add user');
        }
    }

    private function updateUser() {


        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid user ID');
        }

        $data = [
            'name' => $this->validateInput($_POST['name']),
            'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
            'phone' => $this->validateInput($_POST['phone'] ?? ''),
            'address' => $this->validateInput($_POST['address'] ?? ''),
            'role' => in_array($_POST['role'], ['admin', 'user']) ? $_POST['role'] : 'user',
            'status' => in_array($_POST['status'], ['active', 'inactive']) ? $_POST['status'] : 'active'
        ];

        if (!$data['email']) {
            throw new Exception('Invalid email address');
        }

        if (!$this->userModel->updateUser($id, $data)) {
            throw new Exception('Failed to update user');
        }
    }

    private function deleteUser() {


        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid user ID');
        }

        // Prevent self-deletion
        if ($id == $_SESSION['user_id']) {
            throw new Exception('You cannot delete your own account');
        }

        if (!$this->userModel->deleteUser($id)) {
            throw new Exception('Failed to delete user');
        }
    }

    private function toggleUserStatus() {


        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid user ID');
        }

        // Prevent self-deactivation
        if ($id == $_SESSION['user_id']) {
            throw new Exception('You cannot change your own status');
        }

        if (!$this->userModel->toggleStatus($id)) {
            throw new Exception('Failed to change user status');
        }
    }

    private function validateInput($input) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }

    private function generateRandomPassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
}

// Initialize and handle request
$controller = new UserController();
$controller->handleRequest();
ob_end_flush(); // End output buffering
?>
