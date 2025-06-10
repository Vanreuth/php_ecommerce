<?php
session_start();
require_once '../models/User.php';
require_once '../config/database.php';

$pdo = Database::connect();

class AuthController {
    private $userModel;

    public function __construct() {
        global $pdo;
        $this->userModel = new User($pdo);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $this->userModel->login($email, $password);

            if ($user) {
                $_SESSION['user'] = $user;
                $_SESSION['success'] = "Login successful!";
                header('Location: ../index.php');
                exit();
            } else {
                $_SESSION['error'] = "Invalid email or password.";
                header('Location: ../views/login.php');
                exit();
            }
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validate password match
            if ($password !== $confirm_password) {
                $_SESSION['error'] = "Passwords do not match.";
                header('Location: ../views/register.php');
                exit();
            }

            // Try to register
            $result = $this->userModel->register($name, $email, $phone, $address, $password);

            if ($result) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header('Location: ../views/login.php');
                exit();
            } else {
                $_SESSION['error'] = "Email already exists or registration failed.";
                header('Location: ../views/register.php');
                exit();
            }
        }
    }

    public function logout() {
        session_destroy();
        header('Location: ../views/login.php');
        exit();
    }
}

// Handle the incoming request
$action = isset($_GET['action']) ? $_GET['action'] : 'login';
$authController = new AuthController();

if ($action == 'login') {
    $authController->login();
} elseif ($action == 'register') {
    $authController->register();
} elseif ($action == 'logout') {
    $authController->logout();
}
?>
