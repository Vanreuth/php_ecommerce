<?php  
require_once './config/database.php'; 
require_once './controllers/ProductController.php';

// Fetch Categories & Brands
try {
    $categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    $brands = $pdo->query("SELECT * FROM brands")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Fetch Products
$sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id";

try {
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <h4 class="card-title">Product Management</h4>
                        <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="fa fa-plus"></i> Add Product
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="product-table" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td><?= htmlspecialchars($row['brand_name']) ?></td>
                                    <td>$<?= htmlspecialchars($row['price']) ?></td>
                                    <td><?= htmlspecialchars($row['stock']) ?></td>
                                    <td><img src="<?= htmlspecialchars($row['image']) ?>" width="50"></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm editBtn" 
                                                data-id="<?= $row['id'] ?>" 
                                                data-name="<?= $row['name'] ?>" 
                                                data-description="<?= $row['description'] ?>"
                                                data-category="<?= $row['category_id'] ?>"
                                                data-brand="<?= $row['brand_id'] ?>"
                                                data-price="<?= $row['price'] ?>"
                                                data-stock="<?= $row['stock'] ?>"
                                                data-image="<?= $row['image'] ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editProductModal">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm deleteBtn" 
                                                data-id="<?= $row['id'] ?>" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteProductModal">
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal">
        <div class="modal-dialog">
            <form action="controllers/ProductController.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Add Product</h5></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <!-- Product Name -->
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" name="name" id="productName" class="form-control mb-2" placeholder="Product Name" required>

                        <!-- Description -->
                        <label for="productDescription" class="form-label">Description</label>
                        <textarea name="description" id="productDescription" class="form-control mb-2" placeholder="Description"></textarea>

                        <!-- Category -->
                        <label for="categorySelect" class="form-label">Category</label>
                        <select name="category_id" id="categorySelect" class="form-control mb-2" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Brand -->
                        <label for="brandSelect" class="form-label">Brand</label>
                        <select name="brand_id" id="brandSelect" class="form-control mb-2" required>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Price -->
                        <label for="productPrice" class="form-label">Price</label>
                        <input type="number" name="price" id="productPrice" class="form-control mb-2" placeholder="Price" required>

                        <!-- Stock -->
                        <label for="productStock" class="form-label">Stock</label>
                        <input type="number" name="stock" id="productStock" class="form-control mb-2" placeholder="Stock" required>

                        <!-- Image -->
                        <label for="productImage" class="form-label">Image</label>
                        <input type="file" name="image" id="productImage" class="form-control mb-2">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal">
        <div class="modal-dialog">
            <form action="controllers/ProductController.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Product</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="edit-id" name="id">

                        <label for="edit-name" class="form-label">Product Name</label>
                        <input type="text" id="edit-name" name="name" class="form-control mb-2" required>

                        <label for="edit-description" class="form-label">Description</label>
                        <textarea id="edit-description" name="description" class="form-control mb-2"></textarea>

                        <label for="edit-category" class="form-label">Category</label>
                        <select id="edit-category" name="category_id" class="form-control mb-2"></select>

                        <label for="edit-brand" class="form-label">Brand</label>
                        <select id="edit-brand" name="brand_id" class="form-control mb-2"></select>

                        <label for="edit-price" class="form-label">Price</label>
                        <input type="number" id="edit-price" name="price" class="form-control mb-2" required>

                        <label for="edit-stock" class="form-label">Stock</label>
                        <input type="number" id="edit-stock" name="stock" class="form-control mb-2" required>

                        <label for="edit-image-preview" class="form-label">Product Image</label><br>
                        <img id="edit-image-preview" width="50">
                        <input type="file" name="image" id="edit-image" class="form-control mb-2">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Product Modal -->
    <div class="modal fade" id="deleteProductModal">
        <div class="modal-dialog">
            <form action="./controllers/ProductController.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Delete Product</h5></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="delete-id" name="id">
                        <p>Are you sure you want to delete this product?</p>
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
        // Populate product fields
        document.getElementById("edit-id").value = this.dataset.id;
        document.getElementById("edit-name").value = this.dataset.name;
        document.getElementById("edit-description").value = this.dataset.description;
        document.getElementById("edit-price").value = this.dataset.price;
        document.getElementById("edit-stock").value = this.dataset.stock;
        document.getElementById("edit-image-preview").src = this.dataset.image;

        // Fetch and populate category dropdown
        fetch("./controllers/CategoriesController.php?action=getCategories")
            .then(response => response.json())
            .then(categories => {
                let categorySelect = document.getElementById("edit-category");
                categorySelect.innerHTML = ""; // Clear previous options
                categories.forEach(category => {
                    let selected = category.id == this.dataset.category ? "selected" : "";
                    categorySelect.innerHTML += `<option value="${category.id}" ${selected}>${category.name}</option>`;
                });
            });

        // Fetch and populate brand dropdown
        fetch("./controllers/BrandController.php?action=getBrands")
            .then(response => response.json())
            .then(brands => {
                let brandSelect = document.getElementById("edit-brand");
                brandSelect.innerHTML = ""; // Clear previous options
                brands.forEach(brand => {
                    let selected = brand.id == this.dataset.brand ? "selected" : "";
                    brandSelect.innerHTML += `<option value="${brand.id}" ${selected}>${brand.name}</option>`;
                });
            });
    });
});

        // Handle delete button click
        document.querySelectorAll(".deleteBtn").forEach(button => {
            button.addEventListener("click", function() {
                document.getElementById("delete-id").value = this.dataset.id;
            });
        });
    </script>
</div>