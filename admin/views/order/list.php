<?php
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/OrderController.php';

// Initialize Order model and controller
$pdo = Database::connect(); 
$orderModel = new Order($pdo);
$controller = new OrderController();

// Get all orders and users
try {
    $orders = $orderModel->getAllOrders();
    $users = $controller->getUsers();
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading data: " . $e->getMessage();
    $orders = [];
    $users = [];
}

// Define order status colors
$status_colors = [
    'pending' => 'warning',
    'processing' => 'info',
    'shipped' => 'primary',
    'delivered' => 'success',
    'cancelled' => 'danger'
];
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Order Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
        <li class="breadcrumb-item active">Orders</li>
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
                <i class="fas fa-shopping-cart me-1"></i>
                Orders List
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
                <i class="fas fa-plus"></i> Add Order
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="ordersTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Total Items</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                                <td>
                                    <?php echo $order['total_items']; ?> 
                                    (<?php echo $order['total_quantity']; ?> units)
                                </td>
                                <td><?php echo number_format($order['total_price'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $status_colors[$order['status']]; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm view-order" 
                                            data-id="<?php echo $order['id']; ?>"
                                            data-bs-toggle="modal" data-bs-target="#viewOrderModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm edit-order" 
                                            data-id="<?php echo $order['id']; ?>"
                                            data-user="<?php echo $order['user_id']; ?>"
                                            data-price="<?php echo $order['total_price']; ?>"
                                            data-status="<?php echo $order['status']; ?>"
                                            data-address="<?php echo htmlspecialchars($order['shipping_address'] ?? ''); ?>"
                                            data-payment="<?php echo htmlspecialchars($order['payment_method'] ?? ''); ?>"
                                            data-bs-toggle="modal" data-bs-target="#editOrderModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="/admin/controllers/OrderController.php" method="POST" class="d-inline delete-form">
                                        <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Order Modal -->
<div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/admin/controllers/OrderController.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOrderModalLabel">Add New Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Customer</label>
                        <select class="form-control" id="user_id" name="user_id" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                    (<?php echo htmlspecialchars($user['name']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="total_price" class="form-label">Total Price</label>
                        <input type="number" class="form-control" id="total_price" name="total_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Shipping Address</label>
                        <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method">
                            <option value="">Select Payment Method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash_on_delivery">Cash on Delivery</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Order Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/admin/controllers/OrderController.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOrderModalLabel">Edit Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_user_id" class="form-label">Customer</label>
                        <select class="form-control" id="edit_user_id" name="user_id" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                    (<?php echo htmlspecialchars($user['name']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_total_price" class="form-label">Total Price</label>
                        <input type="number" class="form-control" id="edit_total_price" name="total_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_shipping_address" class="form-label">Shipping Address</label>
                        <textarea class="form-control" id="edit_shipping_address" name="shipping_address" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_payment_method" class="form-label">Payment Method</label>
                        <select class="form-control" id="edit_payment_method" name="payment_method">
                            <option value="">Select Payment Method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash_on_delivery">Cash on Delivery</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewOrderModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="orderDetails">
                    Loading...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#ordersTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });

    // Handle Edit Order Modal
    const editButtons = document.querySelectorAll('.edit-order');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const user = this.getAttribute('data-user');
            const price = this.getAttribute('data-price');
            const status = this.getAttribute('data-status');
            const address = this.getAttribute('data-address');
            const payment = this.getAttribute('data-payment');

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_user_id').value = user;
            document.getElementById('edit_total_price').value = price;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_shipping_address').value = address;
            document.getElementById('edit_payment_method').value = payment;
        });
    });

    // Handle View Order Modal
    const viewButtons = document.querySelectorAll('.view-order');
    viewButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const id = this.getAttribute('data-id');
            const detailsContainer = document.getElementById('orderDetails');
            
            try {
                const response = await fetch(`/admin/controllers/OrderController.php?action=view&id=${id}`);
                const data = await response.json();
                
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p><strong>Order ID:</strong> ${data.id}</p>
                            <p><strong>Customer:</strong> ${data.user_email}</p>
                            <p><strong>Status:</strong> <span class="badge bg-${status_colors[data.status]}">${data.status}</span></p>
                            <p><strong>Total Price:</strong> $${parseFloat(data.total_price).toFixed(2)}</p>
                            <p><strong>Created At:</strong> ${new Date(data.created_at).toLocaleString()}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Shipping Information</h6>
                            <p><strong>Address:</strong> ${data.shipping_address || 'N/A'}</p>
                            <p><strong>Payment Method:</strong> ${data.payment_method || 'N/A'}</p>
                        </div>
                    </div>
                    <hr>
                    <h6>Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                data.items.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>$${parseFloat(item.price).toFixed(2)}</td>
                            <td>${item.quantity}</td>
                            <td>$${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}</td>
                        </tr>
                    `;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                `;

                detailsContainer.innerHTML = html;
            } catch (error) {
                detailsContainer.innerHTML = `<div class="alert alert-danger">Error loading order details: ${error.message}</div>`;
            }
        });
    });

    // Handle Delete Confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
                this.submit();
            }
        });
    });
});
</script> 