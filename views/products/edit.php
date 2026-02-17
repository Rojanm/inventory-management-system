<?php ob_start(); ?>
<h1>Edit Product</h1>
<form method="post" action="<?= url('/products/edit/' . $product['id']) ?>">
    <input type="hidden" name="csrf" value="<?= $csrf ?>">
    <div class="mb-3">
        <label for="sku" class="form-label">SKU</label>
        <input type="text" class="form-control" id="sku" name="sku" value="<?= escape($product['sku']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?= escape($product['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="category_id" class="form-label">Category</label>
        <select class="form-control" id="category_id" name="category_id" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>><?= escape($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="supplier_id" class="form-label">Supplier</label>
        <select class="form-control" id="supplier_id" name="supplier_id" required>
            <?php foreach ($suppliers as $sup): ?>
                <option value="<?= $sup['id'] ?>" <?= $sup['id'] == $product['supplier_id'] ? 'selected' : '' ?>><?= escape($sup['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $product['price'] ?>" required>
    </div>
    <div class="mb-3">
        <label for="reorder_level" class="form-label">Reorder Level</label>
        <input type="number" class="form-control" id="reorder_level" name="reorder_level" value="<?= $product['reorder_level'] ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="<?= url('/products') ?>" class="btn btn-secondary">Cancel</a>
</form>
<?php $content = ob_get_clean(); require 'views/layout.php'; ?>