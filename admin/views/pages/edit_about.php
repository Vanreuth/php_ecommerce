<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../controllers/PageController.php";

try {
    $pageController = new PageController(Database::connect());
    $page_name = $_GET['page_name'] ?? 'About';
    $page = $pageController->getPage($page_name);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $data = [
            'title' => $_POST['title'],
            'subtitle1' => $_POST['subtitle1'],
            'description1' => $_POST['description1'],
            'subtitle2' => $_POST['subtitle2'],
            'description2' => $_POST['description2'],
            'image1' => $page['image1'],
            'image2' => $page['image2'],
            'banner_image' => $page['banner_image'],
            'page_name' => $page_name
        ];

        // Handle file uploads
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        foreach (['image1', 'image2', 'banner_image'] as $imageField) {
            if (isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$imageField];
                
                // Validate file type
                if (!in_array($file['type'], $allowedTypes)) {
                    throw new Exception("Invalid file type for {$imageField}. Only JPG, PNG and WEBP are allowed.");
                }

                // Validate file size
                if ($file['size'] > $maxFileSize) {
                    throw new Exception("File size too large for {$imageField}. Maximum size is 5MB.");
                }

                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFilename = uniqid($imageField . '_') . '.' . $extension;
                
                // Move file
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFilename)) {
                    // Store only the filename in the database
                    $data[$imageField] = $newFilename;
                    
                    // Delete old file if exists and it's not the default image
                    if (!empty($page[$imageField]) && file_exists($uploadDir . $page[$imageField])) {
                        unlink($uploadDir . $page[$imageField]);
                    }
                } else {
                    throw new Exception("Failed to upload {$imageField}");
                }
            }
        }

        // Update page data
        if ($pageController->updatePage($data)) {
            $success = "Page updated successfully!";
            $page = $pageController->getPage($page_name); // Refresh page data
        } else {
            throw new Exception("Failed to update page");
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?= htmlspecialchars($page_name) ?> Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container mt-5">
    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-lg p-4">
        <h2 class="mb-4">Edit <?= htmlspecialchars($page_name) ?> Page</h2>
        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label">Page Title:</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title']) ?>" required>
                <div class="invalid-feedback">Please provide a title.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Banner Image:</label>
                <div class="input-group">
                    <input type="file" name="banner_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                    <span class="input-group-text"><i class="fas fa-image"></i></span>
                </div>
                <?php if (!empty($page['banner_image'])): ?>
                    <div class="mt-2">
                        <img src="./views/pages/uploads/<?= htmlspecialchars($page['banner_image']) ?>" class="img-thumbnail" width="100%" alt="Banner Image">
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Subtitle 1:</label>
                        <input type="text" name="subtitle1" class="form-control" value="<?= htmlspecialchars($page['subtitle1']) ?>" required>
                        <div class="invalid-feedback">Please provide subtitle 1.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description 1:</label>
                        <textarea name="description1" id="description1" class="form-control" required><?= htmlspecialchars($page['description1']) ?></textarea>
                        <div class="invalid-feedback">Please provide description 1.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image 1:</label>
                        <div class="input-group">
                            <input type="file" name="image1" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                        </div>
                        <?php if (!empty($page['image1'])): ?>
                            <div class="mt-2">
                                <img src="./views/pages/uploads/<?= htmlspecialchars($page['image1']) ?>" class="img-thumbnail" width="150" alt="Image 1">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Subtitle 2:</label>
                        <input type="text" name="subtitle2" class="form-control" value="<?= htmlspecialchars($page['subtitle2']) ?>" required>
                        <div class="invalid-feedback">Please provide subtitle 2.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description 2:</label>
                        <textarea name="description2" id="description2" class="form-control" required><?= htmlspecialchars($page['description2']) ?></textarea>
                        <div class="invalid-feedback">Please provide description 2.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image 2:</label>
                        <div class="input-group">
                            <input type="file" name="image2" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                        </div>
                        <?php if (!empty($page['image2'])): ?>
                            <div class="mt-2">
                                <img src="./views/pages/uploads/<?= htmlspecialchars($page['image2']) ?>" class="img-thumbnail" width="150" alt="Image 2">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <a href="index.php?p=pages" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Pages
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('description1');
    CKEDITOR.replace('description2');

    // Form validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
</body>
</html>
