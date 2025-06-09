<?php
// Assume $pageController is an instance of the PageController
$pages = $pageController->getAllPages(); // Get all pages from the database
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Pages</title>
</head>
<body>
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Category Management</h4>
                        <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fa fa-plus"></i> Add Category
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="add-row" class="display table table-striped table-hover">
        </thead>
        <tbody>
            <?php if ($pages): ?>
                <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($page['title']); ?></td>
                        <td><?php echo htmlspecialchars($page['page_name']); ?></td>
                        <td>
                            <a href="index.php?p=pages&action=edit&title=<?php echo urlencode($page['page_name']); ?>">Edit</a>
                            <!-- Add more actions as needed -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No pages found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
