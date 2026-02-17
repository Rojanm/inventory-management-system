<?php ob_start(); ?>
<h1>Create Product</h1>
<form method="post" action="<?= url('/products/create') ?>">
    <input type="hidden" name="csrf" value="<?= $csrf ?>">
    <div class="mb-3">
        <label for="sku" class="form-label">SKU</label>
        <input type="text" class="form-control" id="sku" name="sku" required>
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="category_id" class="form-label">Category</label>
        <select class="form-control" id="category_id" name="category_id" required>
            <option value="">Select</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= escape($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="supplier_id" class="form-label">Supplier</label>
        <select class="form-control" id="supplier_id" name="supplier_id" required>
            <option value="">Select</option>
            <?php foreach ($suppliers as $sup): ?>
                <option value="<?= $sup['id'] ?>"><?= escape($sup['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
    </div>
    <div class="mb-3">
        <label for="reorder_level" class="form-label">Reorder Level</label>
        <input type="number" class="form-control" id="reorder_level" name="reorder_level" value="10">
    </div>
    <button type="submit" class="btn btn-primary">Create</button>
    <a href="<?= url('/products') ?>" class="btn btn-secondary">Cancel</a>
</form>
<?php $content = ob_get_clean(); require 'views/layout.php'; ?>