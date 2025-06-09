<?php  
require_once './config/database.php'; 
require_once  './controllers/UserController.php';// Ensure this file correctly sets up $pdo

$sql = "SELECT * FROM users"; 

try {
    $stmt = $pdo->query($sql); // Use $pdo to execute the query
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <h4 class="card-title">User Management</h4>
                        <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fa fa-plus"></i> Add User
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
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Role</th>
                                    <th style="width: 15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= htmlspecialchars($row['address']) ?></td>
                                    <td><?= htmlspecialchars($row['role']) ?></td>
                                    <td>
                                        <div class="form-button-action">
                                            <button class="btn btn-warning btn-sm editBtn" 
                                                    data-id="<?= $row['id'] ?>" 
                                                    data-name="<?= $row['name'] ?>" 
                                                    data-email="<?= $row['email'] ?>" 
                                                    data-phone="<?= $row['phone'] ?>" 
                                                    data-address="<?= $row['address'] ?>" 
                                                    data-role="<?= $row['role'] ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editUserModal">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm deleteBtn" 
                                                    data-id="<?= $row['id'] ?>" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteUserModal">
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal">
        <div class="modal-dialog">
            <form action="controllers/UserController.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Add User</h5></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
                        <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                        <input type="text" name="phone" class="form-control mb-2" placeholder="Phone" required>
                        <input type="text" name="address" class="form-control mb-2" placeholder="Address" required>
                        <input type="text" name="role" class="form-control mb-2" placeholder="Role" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal">
        <div class="modal-dialog">
            <form action="controllers/UserController.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Edit User</h5></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="edit-id" name="id">
                        <input type="text" id="edit-name" name="name" class="form-control mb-2" required>
                        <input type="email" id="edit-email" name="email" class="form-control mb-2" required>
                        <input type="text" id="edit-phone" name="phone" class="form-control mb-2" required>
                        <input type="text" id="edit-address" name="address" class="form-control mb-2" required>
                        <input type="text" id="edit-role" name="role" class="form-control mb-2" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal">
        <div class="modal-dialog">
            <form action="controllers/UserController.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Confirm Delete</h5></div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this user?</p>
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

<script src="../assets/bootstrap.bundle.min.js"></script>
<script>
    // Populate Edit Modal with User Data
    document.querySelectorAll(".editBtn").forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("edit-id").value = this.dataset.id;
            document.getElementById("edit-name").value = this.dataset.name;
            document.getElementById("edit-email").value = this.dataset.email;
            document.getElementById("edit-phone").value = this.dataset.phone;
            document.getElementById("edit-address").value = this.dataset.address;
            document.getElementById("edit-role").value = this.dataset.role;
        });
    });

    // Populate Delete Modal with User ID
    document.querySelectorAll(".deleteBtn").forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("delete-id").value = this.dataset.id;
        });
    });
</script>

</body>
</html>
