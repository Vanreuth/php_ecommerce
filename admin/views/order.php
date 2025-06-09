<?php
require_once './config/database.php';
require_once './models/Order.php';

$orderModel = new Order($pdo);
$orders = $orderModel->getAllOrders();
?>

<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Orders Management</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" data-bs-target="#addOrderModal">
                        <i class="fa fa-plus"></i> Add Order
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['id']) ?></td>
                                    <td><?= htmlspecialchars($order['user_id']) ?></td>
                                    <td>$<?= htmlspecialchars($order['total_price']) ?></td>
                                    <td><?= htmlspecialchars($order['status']) ?></td>
                                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm editBtn" 
                                                data-id="<?= $order['id'] ?>" 
                                                data-user_id="<?= $order['user_id'] ?>" 
                                                data-total_price="<?= $order['total_price'] ?>" 
                                                data-status="<?= $order['status'] ?>" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editOrderModal">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm deleteBtn" 
                                                data-id="<?= $order['id'] ?>" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteOrderModal">
                                            <i class="fa fa-times"></i>
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
    </div>

    <!-- Add Order Modal -->
    <div class="modal fade" id="addOrderModal">
        <div class="modal-dialog">
            <form action="controllers/OrderController.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Add Order</h5></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">

                        <label>User ID</label>
                        <input type="number" name="user_id" class="form-control mb-2" required>

                        <label>Total Price</label>
                        <input type="number" name="total_price" class="form-control mb-2" required>

                        <label>Status</label>
                        <input type="text" name="status" class="form-control mb-2" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div class="modal fade" id="editOrderModal">
        <div class="modal-dialog">
            <form action="controllers/OrderController.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Edit Order</h5></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="edit-id" name="id">

                        <label>User ID</label>
                        <input type="number" id="edit-user_id" name="user_id" class="form-control mb-2" required>

                        <label>Total Price</label>
                        <input type="number" id="edit-total_price" name="total_price" class="form-control mb-2" required>

                        <label>Status</label>
                        <input type="text" id="edit-status" name="status" class="form-control mb-2" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll(".editBtn").forEach(button => {
    button.addEventListener("click", function() {
        document.getElementById("edit-id").value = this.dataset.id;
        document.getElementById("edit-user_id").value = this.dataset.user_id;
        document.getElementById("edit-total_price").value = this.dataset.total_price;
        document.getElementById("edit-status").value = this.dataset.status;
    });
});

document.querySelectorAll(".deleteBtn").forEach(button => {
    button.addEventListener("click", function() {
        document.getElementById("delete-id").value = this.dataset.id;
    });
});
</script>
