<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../config/database.php';

$order = new Order($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == "add") {
        $order->addOrder($_POST['user_id'], $_POST['total_price'], $_POST['status']);
    } elseif ($action == "update") {
        $order->updateOrder($_POST['id'], $_POST['user_id'], $_POST['total_price'], $_POST['status']);
    } elseif ($action == "delete") {
        $order->deleteOrder($_POST['id']);
    }

    header("Location: /ecommerce/admin/index.php?p=orders");
    exit();
}

if ($_GET['action'] == 'getOrders') {
    echo json_encode($order->getAllOrders());
    exit();
}
?>
