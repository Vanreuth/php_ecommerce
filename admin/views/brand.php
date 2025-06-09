<?php  
require_once './config/database.php'; 
require_once  './controllers/BrandController.php';

$sql = "SELECT * FROM brands"; 

try {
    $stmt = $pdo->query($sql);
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Brand Management</h4>
                        <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                            <i class="fa fa-plus"></i> Add Brand
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="add-row" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th style="width: 15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($brands as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                    <td>
                                        <div class="form-button-action">
                                            <button class="btn btn-warning btn-sm editBtn" 
                                                    data-id="<?= $row['id'] ?>" 
                                                    data-name="<?= $row['name'] ?>" 
                                                    data-description="<?= $row['description'] ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editBrandModal">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm deleteBtn" 
                                                    data-id="<?= $row['id'] ?>" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteBrandModal">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Brand Modal -->
    <div class="modal fade" id="addBrandModal">
        <div class="modal-dialog">
            <form action="controllers/BrandController.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Add Brand</h5></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <input type="text" name="name" class="form-control mb-2" placeholder="Brand Name" required>
                        <textarea name="description" class="form-control mb-2" placeholder="Description" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div class="modal fade" id="editBrandModal">
        <div class="modal-dialog">
            <form action="controllers/BrandController.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Edit Brand</h5></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="edit-id" name="id">
                        <input type="text" id="edit-name" name="name" class="form-control mb-2" required>
                        <textarea id="edit-description" name="description" class="form-control mb-2" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Brand Modal -->
    <div class="modal fade" id="deleteBrandModal">
        <div class="modal-dialog">
            <form action="controllers/BrandController.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Confirm Delete</h5></div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this brand?</p>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="delete-id" name="id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
    document.querySelectorAll(".editBtn").forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("edit-id").value = this.dataset.id;
            document.getElementById("edit-name").value = this.dataset.name;
            document.getElementById("edit-description").value = this.dataset.description;
        });
    });

    document.querySelectorAll(".deleteBtn").forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("delete-id").value = this.dataset.id;
        }); 
    });
</script>
