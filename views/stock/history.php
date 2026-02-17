<?php ob_start(); ?>
<h1>Transaction History</h1>
<table class="table table-striped" id="historyTable">
    <thead>
        <tr>
            <th>Date</th><th>Product</th><th>Type</th><th>Quantity</th><th>Previous</th><th>New</th><th>User</th><th>Note</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($movements as $m): ?>
        <tr>
            <td><?= date('Y-m-d H:i', strtotime($m['movement_date'])) ?></td>
            <td><?= escape($m['product_name']) ?></td>
            <td><span class="badge bg-<?= $m['type']=='IN'?'success':'danger' ?>"><?= $m['type'] ?></span></td>
            <td><?= $m['quantity'] ?></td>
            <td><?= $m['previous_quantity'] ?></td>
            <td><?= $m['new_quantity'] ?></td>
            <td><?= escape($m['user_name']) ?></td>
            <td><?= escape($m['reference_note']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
$(document).ready(function() {
    $('#historyTable').DataTable({
        order: [[0, 'desc']]
    });
});
</script>
<?php $content = ob_get_clean(); require 'views/layout.php'; ?>