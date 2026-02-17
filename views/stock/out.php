<?php ob_start(); ?>
<h1>Stock Out</h1>
<form method="post" action="<?= url('/stock/out') ?>">
    <input type="hidden" name="csrf" value="<?= $csrf ?>">
    <div class="mb-3">
        <label for="product_id" class="form-label">Product</label>
        <select class="form-control" id="product_id" name="product_id" required>
            <option value="">Select Product</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>"><?= escape($p['name']) ?> (<?= $p['quantity'] ?> in stock)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
    </div>
    <div class="mb-3">
        <label for="reference_note" class="form-label">Reference Note</label>
        <input type="text" class="form-control" id="reference_note" name="reference_note">
    </div>
    <button type="submit" class="btn btn-danger">Process Stock Out</button>
    <a href="<?= url('/dashboard') ?>" class="btn btn-secondary">Cancel</a>
</form>
<?php $content = ob_get_clean(); require 'views/layout.php'; ?>