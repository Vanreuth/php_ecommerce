<?php
require_once __DIR__ . '/../models/Brand.php';

$brand = new Brand($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === "add" && !empty($_POST['name']) && !empty($_POST['description'])) {
        $brand->addBrand($_POST['name'], $_POST['description']);
    } elseif ($action === "update" && !empty($_POST['id'])) {
        $brand->updateBrand($_POST['id'], $_POST['name'], $_POST['description']);
    } elseif ($action === "delete" && !empty($_POST['id'])) {
        $brand->deleteBrand($_POST['id']);
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$action = $_GET['action'] ?? null;
if ($action === 'getBrands') {
    $stmt = $pdo->query("SELECT id, name FROM brands");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit();
}
?>
