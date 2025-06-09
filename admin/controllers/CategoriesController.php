<?php
require_once __DIR__ . '/../models/Categories.php';

$category = new Category($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === "add" && !empty($_POST['name']) && !empty($_POST['description'])) {
        $category->addCategory($_POST['name'], $_POST['description']);
    } elseif ($action === "update" && !empty($_POST['id'])) {
        $category->updateCategory($_POST['id'], $_POST['name'], $_POST['description']);
    } elseif ($action === "delete" && !empty($_POST['id'])) {
        $category->deleteCategory($_POST['id']);
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$action = $_GET['action'] ?? null;
if ($action === 'getCategories') {
    $stmt = $pdo->query("SELECT id, name FROM categories");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit();
}
?>
