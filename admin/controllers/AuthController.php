<?php
require_once '../models/User.php';
require_once '../config/database.php';

class AuthController {
    private $userModel;

    public function __construct() {
        global $pdo;
        $this->userModel = new User($pdo);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validate input
            $errors = [];

            if (empty($name)) {
                $errors[] = "Name is required";
            }

            if (empty($email)) {
                $errors[] = "Email is required";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }

            if (empty($phone)) {
                $errors[] = "Phone number is required";
            }

            if (empty($address)) {
                $errors[] = "Address is required";
            }

            if (empty($password)) {
                $errors[] = "Password is required";
            } elseif (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters long";
            }

            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }

            if (empty($errors)) {
                if ($this->userModel->register($name, $email, $phone, $address, $password)) {
                    // Registration successful
                    $_SESSION['success'] = "Registration successful! Please login.";
                    header('Location: ../views/login.php');
                    exit();
                } else {
                    $errors[] = "Email already exists";
                }
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ../views/register.php');
                exit();
            }
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $this->userModel->login($email, $password);

            if ($user) {
                session_start();
                $_SESSION['user'] = $user;
                header('Location: ../index.php');
                exit();
            } else {
                $_SESSION['error'] = "Invalid email or password.";
                header('Location: ../views/login.php');
                exit();
            }
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ../views/login.php');
        exit();
    }
}

// Handle actions
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
