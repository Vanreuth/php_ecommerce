<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add Product</h5></div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveProductBtn">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>