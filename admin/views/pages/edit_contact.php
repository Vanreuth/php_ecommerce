<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../controllers/PageController.php";

try {
    $pageController = new PageController(Database::connect());
    $page_name = "Contact";
    $page = $pageController->getPage($page_name);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $data = [
            'title' => $_POST['title'],
            'address' => $_POST['address'],
            'phone' => $_POST['phone'],
            'email' => $_POST['email'],
            'image1' => $page['image1'],
            'banner_image' => $page['banner_image'],
            'page_name' => $page_name
        ];

        // Handle file uploads
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        foreach (['image1', 'banner_image'] as $imageField) {
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
            $success = "Contact page updated successfully!";
            $page = $pageController->getPage($page_name); // Refresh page data
        } else {
            throw new Exception("Failed to update contact page");
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
    <title>Edit Contact Page</title>
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
        <h2 class="mb-4">Edit Contact Page</h2>
        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Page Title:</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title'] ?? '') ?>" required>
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
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Address:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($page['address'] ?? '') ?>" required>
                        </div>
                        <div class="invalid-feedback">Please provide an address.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($page['phone'] ?? '') ?>" required>
                        </div>
                        <div class="invalid-feedback">Please provide a phone number.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($page['email'] ?? '') ?>" required>
                        </div>
                        <div class="invalid-feedback">Please provide a valid email address.</div>
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

<!-- Form validation -->
<script>
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
