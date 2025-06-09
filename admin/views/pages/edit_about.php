<?php
require_once "./config/database.php";
require_once "./controllers/PageController.php";

$pageController = new PageController($pdo);
$page_name = $_GET['page_name'] ?? 'About';  // Default to 'About'
$page = $pageController->getPage($page_name);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = [
        'title' => $_POST['title'],
        'subtitle1' => $_POST['subtitle1'],
        'description1' => $_POST['description1'],
        'subtitle2' => $_POST['subtitle2'],
        'description2' => $_POST['description2'],
        'image1' => isset($_FILES['image1']['name']) && !empty($_FILES['image1']['name']) ? $_FILES['image1']['name'] : $page['image1'],
        'image2' => isset($_FILES['image2']['name']) && !empty($_FILES['image2']['name']) ? $_FILES['image2']['name'] : $page['image2'],
        'banner_image' => isset($_FILES['banner_image']['name']) && !empty($_FILES['banner_image']['name']) ? $_FILES['banner_image']['name'] : $page['banner_image'],
        'page_name' => $page_name
    ];

    
    $uploadDir = __DIR__ . '/../pages/uploads/';

    if (!empty($_FILES['image1']['name'])) {
        move_uploaded_file($_FILES['image1']['tmp_name'], $uploadDir . $_FILES['image1']['name']);
    }
    if (!empty($_FILES['image2']['name'])) {
        move_uploaded_file($_FILES['image2']['tmp_name'], $uploadDir . $_FILES['image2']['name']);
    }
    if (!empty($_FILES['banner_image']['name'])) {
        move_uploaded_file($_FILES['banner_image']['tmp_name'], $uploadDir . $_FILES['banner_image']['name']);
    }

    // Update page data
    $pageController->updatePage($data);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h2 class="mb-4"><?= htmlspecialchars($page_name) ?> Page</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Title:</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title']) ?>" required>
            </div>
            <div class="mb-3">
    <label class="form-label">Banner Image:</label>
    <input type="file" name="banner_image" class="form-control">
    <?php if (!empty($page['banner_image'])): ?>
        <img src="./uploads/<?= htmlspecialchars($page['banner_image']) ?>" class="img-thumbnail mt-2" width="150">
    <?php endif; ?>
</div>

            <div class="mb-3">
                <label class="form-label">Subtitle 1:</label>
                <input type="text" name="subtitle1" class="form-control" value="<?= htmlspecialchars($page['subtitle1']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description for Subtitle 1:</label>
                <textarea name="description1" id="description1" class="form-control"><?= htmlspecialchars_decode($page['description1']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Subtitle 2:</label>
                <input type="text" name="subtitle2" class="form-control" value="<?= htmlspecialchars($page['subtitle2']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description for Subtitle 2:</label>
                <textarea name="description2" id="description2" class="form-control"><?= htmlspecialchars_decode($page['description2']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Image 1:</label>
                <input type="file" name="image1" class="form-control">
                <?php if (!empty($page['image1'])): ?>
                    <img src="./uploads/<?= htmlspecialchars($page['image1']) ?>" class="img-thumbnail mt-2" width="150">
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Image 2:</label>
                <input type="file" name="image2" class="form-control">
                <?php if (!empty($page['image2'])): ?>
                    <img src="./uploads/<?= htmlspecialchars($page['image2']) ?>" class="img-thumbnail mt-2" width="150">
                <?php endif; ?>
            </div>
           

            <button type="submit" class="btn btn-primary w-100">Save</button>
        </form>
    </div>
</div>

<!-- CKEditor Integration -->
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('description1');
    CKEDITOR.replace('description2');
</script>
</body>
</html>
