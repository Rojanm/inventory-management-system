<?php ob_start(); ?>
<h1>Low Stock Report</h1>
<table class="table">
    <thead><tr><th>SKU</th><th>Product</th><th>Current Quantity</th><th>Reorder Level</th></tr></thead>
    <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?= escape($p['sku']) ?></td>
            <td><?= escape($p['name']) ?></td>
            <td><?= $p['quantity'] ?></td>
            <td><?= $p['reorder_level'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); require 'views/layout.php'; ?>