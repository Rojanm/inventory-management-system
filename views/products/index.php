<?php ob_start(); ?>
<h1>Products</h1>
<?php if (Session::get('user_role') == 'admin'): ?>
    <a href="<?= url('/products/create') ?>" class="btn btn-primary mb-3">Add Product</a>
<?php endif; ?>
<table class="table table-striped" id="productsTable">
    <thead>
        <tr>
            <th>ID</th><th>SKU</th><th>Name</th><th>Category</th><th>Supplier</th><th>Price</th><th>Qty</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= escape($p['sku']) ?></td>
            <td><?= escape($p['name']) ?></td>
            <td><?= escape($p['category_name'] ?? '') ?></td>
            <td><?= escape($p['supplier_name'] ?? '') ?></td>
            <td>$<?= number_format($p['price'], 2) ?></td>
            <td><?= $p['quantity'] ?></td>
            <td>
                <?php if (Session::get('user_role') == 'admin'): ?>
                    <a href="<?= url('/products/edit/' . $p['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form method="post" action="<?= url('/products/delete/' . $p['id']) ?>" style="display:inline">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
$(document).ready(function() {
    $('#productsTable').DataTable();
});
</script>
<?php $content = ob_get_clean(); require 'views/layout.php'; ?>