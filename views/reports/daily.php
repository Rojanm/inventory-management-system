<?php ob_start(); ?>
<h1>Daily Report: <?= $date ?></h1>
<form method="get" action="<?= url('/reports/daily') ?>" class="mb-3">
    <label for="date">Select Date:</label>
    <input type="date" name="date" id="date" value="<?= $date ?>">
    <button type="submit" class="btn btn-sm btn-primary">View</button>
</form>
<table class="table">
    <thead><tr><th>Type</th><th>Total Quantity</th><th>Transactions</th></tr></thead>
    <tbody>
        <?php foreach ($summary as $row): ?>
        <tr>
            <td><?= $row['type'] ?></td>
            <td><?= $row['total_quantity'] ?></td>
            <td><?= $row['transaction_count'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); require 'views/layout.php'; ?>