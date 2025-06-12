<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Slider.php';

$pdo = Database::connect();
$sliderModel = new Slider($pdo);
$sliders = $sliderModel->getAllSliders();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Slider Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Sliders</li>
    </ol>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div><i class="fas fa-images me-1"></i> Sliders</div>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSliderModal">
                    <i class="fas fa-plus"></i> Add New Slider
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="slidersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sliders as $slider): ?>
                            <tr>
                                <td><?= htmlspecialchars($slider['id']) ?></td>
                                <td>
                                    <img src="/eccommerce/admin/uploads/sliders/<?= htmlspecialchars($slider['image_path']) ?>" 
                                         alt="<?= htmlspecialchars($slider['title']) ?>" 
                                         class="img-thumbnail" 
                                         style="max-width: 100px;">
                                </td>
                                <td><?= htmlspecialchars($slider['title']) ?></td>
                                <td><?= htmlspecialchars($slider['description']) ?></td>
                                <td>
                                    <button class="btn btn-sm <?= $slider['status'] ? 'btn-success' : 'btn-danger' ?> toggle-status"
                                            data-id="<?= $slider['id'] ?>"
                                            data-status="<?= $slider['status'] ?>">
                                        <?= $slider['status'] ? 'Active' : 'Inactive' ?>
                                    </button>
                                </td>
                                <td><?= htmlspecialchars($slider['created_at']) ?></td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-primary btn-sm edit-slider" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editSliderModal"
                                            data-id="<?= $slider['id'] ?>"
                                            data-title="<?= htmlspecialchars($slider['title']) ?>"
                                            data-description="<?= htmlspecialchars($slider['description']) ?>"
                                            data-status="<?= $slider['status'] ?>"
                                            data-image="<?= htmlspecialchars($slider['image_path']) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm delete-slider" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteSliderModal"
                                            data-id="<?= $slider['id'] ?>"
                                            data-title="<?= htmlspecialchars($slider['title']) ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Slider Modal -->
<div class="modal fade" id="addSliderModal" tabindex="-1" aria-labelledby="addSliderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/eccommerce/admin/controllers/SliderController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSliderModalLabel">Add New Slider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <div class="form-text">Recommended size: 1920x600 pixels. Max file size: 2MB</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" checked>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Slider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Slider Modal -->
<div class="modal fade" id="editSliderModal" tabindex="-1" aria-labelledby="editSliderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/eccommerce/admin/controllers/SliderController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_slider_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSliderModalLabel">Edit Slider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Image</label>
                        <div id="current_image" class="mb-2"></div>
                        <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                        <div class="form-text">Leave empty to keep current image. Recommended size: 1920x600 pixels. Max file size: 2MB</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit_status" name="status">
                            <label class="form-check-label" for="edit_status">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Slider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Slider Modal -->
<div class="modal fade" id="deleteSliderModal" tabindex="-1" aria-labelledby="deleteSliderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="/eccommerce/admin/controllers/SliderController.php" method="POST"> 
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_slider_id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSliderModalLabel">Delete Slider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the slider "<span id="delete_slider_title"></span>"?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
        </form>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#slidersTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });

    // Handle Edit Brand Modal
    const editButtons = document.querySelectorAll('.edit-slider');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            const description = this.getAttribute('data-description');
            const status = this.getAttribute('data-status');
            const image = this.getAttribute('data-image');

            document.getElementById('edit_slider_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_status').checked = status === '1';
            document.getElementById('current_image').innerHTML = `<img src="/eccommerce/admin/uploads/sliders/${image}" class="img-thumbnail" style="max-width: 200px;">`;
        });
    });

    // Handle Delete Confirmation
    const deleteModal = document.getElementById('deleteSliderModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');

        document.getElementById('delete_slider_id').value = id;
        document.getElementById('delete_slider_title').textContent = title;
    });
});

</script>