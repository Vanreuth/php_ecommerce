<?php
require_once __DIR__ . '/../../models/User.php';

// Initialize User model
$pdo = Database::connect();
$userModel = new User($pdo);

// Get users based on role filter
$roleFilter = isset($_GET['role']) ? $_GET['role'] : null;
if ($roleFilter) {
    $users = $userModel->getUsersByRole($roleFilter);
} else {
    $users = $userModel->getAllUsers();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">User Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/eccommerce/admin">Dashboard</a></li>
        <li class="breadcrumb-item active">Users</li>
    </ol>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Users List
            </div>
            <div>
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Filter by Role
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?php echo !isset($_GET['role']) ? 'active' : ''; ?>" href="?p=usermagement">All Users</a></li>
                        <li><a class="dropdown-item <?php echo isset($_GET['role']) && $_GET['role'] === 'admin' ? 'active' : ''; ?>" href="?p=usermagement&role=admin">Admins</a></li>
                        <li><a class="dropdown-item <?php echo isset($_GET['role']) && $_GET['role'] === 'user' ? 'active' : ''; ?>" href="?p=usermagement&role=user">Users</a></li>
                    </ul>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-user-plus"></i> Add User
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="usersTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo htmlspecialchars($user['address']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'primary' : 'secondary'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm <?php echo $user['status'] === 'active' ? 'btn-success' : 'btn-secondary'; ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#statusModal"
                                            data-id="<?php echo $user['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                            data-status="<?php echo $user['status']; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </button>
                                </td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary btn-sm edit-user" 
                                                data-id="<?php echo $user['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                data-phone="<?php echo htmlspecialchars($user['phone']); ?>"
                                                data-address="<?php echo htmlspecialchars($user['address']); ?>"
                                                data-role="<?php echo $user['role']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editUserModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal"
                                                data-id="<?php echo $user['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($user['name']); ?>">
                                            <i class="fas fa-trash"></i>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="8">
                        <div class="form-text">Leave empty to generate a random password</div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user">Regular User</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/eccommerce/admin/controllers/UserController.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Address</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role</label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="user">Regular User</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/eccommerce/admin/controllers/UserController.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" id="status_user_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Change User Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to change the status of user <strong id="status_user_name"></strong>?</p>
                    <p>Current status: <span id="current_status" class="badge"></span></p>
                    <p>New status: <span id="new_status" class="badge"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/eccommerce/admin/controllers/UserController.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_user_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This action cannot be undone!
                    </div>
                    <p>Are you sure you want to delete user <strong id="delete_user_name"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#usersTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });

    // Handle Edit User Modal
    const editButtons = document.querySelectorAll('.edit-user');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');
            const phone = this.getAttribute('data-phone');
            const address = this.getAttribute('data-address');
            const role = this.getAttribute('data-role');

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_address').value = address;
            document.getElementById('edit_role').value = role;
        });
    });

    // Handle Status Modal
    const statusModal = document.getElementById('statusModal');
    statusModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const currentStatus = button.getAttribute('data-status');
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

        document.getElementById('status_user_id').value = id;
        document.getElementById('status_user_name').textContent = name;
        
        const currentStatusBadge = document.getElementById('current_status');
        currentStatusBadge.textContent = currentStatus.toUpperCase();
        currentStatusBadge.className = `badge bg-${currentStatus === 'active' ? 'success' : 'secondary'}`;
        
        const newStatusBadge = document.getElementById('new_status');
        newStatusBadge.textContent = newStatus.toUpperCase();
        newStatusBadge.className = `badge bg-${newStatus === 'active' ? 'success' : 'secondary'}`;
    });

    // Handle Delete Modal
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');

        document.getElementById('delete_user_id').value = id;
        document.getElementById('delete_user_name').textContent = name;
    });
});
</script>
