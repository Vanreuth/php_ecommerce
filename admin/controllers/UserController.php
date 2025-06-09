<?php
require_once __DIR__ . '/../models/User.php';

$user = new User($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == "add") {
        $user->addUser($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['role']);
    } elseif ($action == "update") {
        $user->updateUser($_POST['id'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['role']);
    } elseif ($action == "delete") {
        $user->deleteUser($_POST['id']);
    }

    header("Location: /eccommerce/admin/index.php?p=usermagement");
    exit();
}
?>
