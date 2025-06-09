<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Product</h5></div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" id="edit-id" name="id">

                    <!-- Product Fields -->
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="updateProductBtn">Update</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>