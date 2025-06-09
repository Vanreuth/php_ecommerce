<?php
require_once "./config/database.php";
require_once "./controllers/PageController.php";

$pageController = new PageController($pdo);
$page_name = "Contact";
$page = $pageController->getPage($page_name);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = [
        'title' => $_POST['title'],
        'address' => $_POST['address'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email'],
        'image1' => isset($_FILES['image1']['name']) && !empty($_FILES['image1']['name']) ? $_FILES['image1']['name'] : $page['image1'],
        'banner_image' => isset($_FILES['banner_image']['name']) && !empty($_FILES['banner_image']['name']) ? $_FILES['banner_image']['name'] : $page['banner_image'],
        'page_name' => $page_name
    ];

    // Upload Images
    $uploadDir = __DIR__ . '/../pages/uploads/';

    if (!empty($_FILES['image1']['name'])) {
        move_uploaded_file($_FILES['image1']['tmp_name'], $uploadDir . $_FILES['image1']['name']);
    }
    if (!empty($_FILES['banner_image']['name'])) {
        move_uploaded_file($_FILES['banner_image']['tmp_name'], $uploadDir . $_FILES['banner_image']['name']);
    }

    // Update page content
    $pageController->updatePage($data);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h2 class="mb-4">Edit Contact Page</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Title:</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Banner Image:</label>
                <input type="file" name="banner_image" class="form-control">
                <?php if (!empty($page['banner_image'])): ?>
                    <img src="./uploads/<?= htmlspecialchars($page['banner_image']) ?>" class="img-thumbnail mt-2" width="150">
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Contact Image:</label>
                <input type="file" name="image1" class="form-control">
                <?php if (!empty($page['image1'])): ?>
                    <img src="./uploads/<?= htmlspecialchars($page['image1']) ?>" class="img-thumbnail mt-2" width="150">
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Address:</label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($page['address'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone:</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($page['phone'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($page['email'] ?? '') ?>" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
        </form>
    </div>
</div>
</body>
</html>
