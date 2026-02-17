<?php ob_start(); ?>
<h1>Dashboard</h1>
<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Products</h5>
                <p class="card-text display-6"><?= $totalProducts ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Stock Value</h5>
                <p class="card-text display-6">₱<?= number_format($totalValue, 2) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Low Stock Items</h5>
                <p class="card-text display-6"><?= count($lowStock) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Low Stock Alerts</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead><tr><th>Product</th><th>Quantity</th><th>Reorder Level</th></tr></thead>
                    <tbody>
                        <?php foreach ($lowStock as $p): ?>
                        <tr><td><?= escape($p['name']) ?></td><td><?= $p['quantity'] ?></td><td><?= $p['reorder_level'] ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Recent Stock Movements</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead><tr><th>Product</th><th>Type</th><th>Qty</th><th>User</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentMovements as $m): ?>
                        <tr>
                            <td><?= escape($m['product_name']) ?></td>
                            <td><span class="badge bg-<?= $m['type']=='IN'?'success':'danger' ?>"><?= $m['type'] ?></span></td>
                            <td><?= $m['quantity'] ?></td>
                            <td><?= escape($m['user_name']) ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($m['movement_date'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <canvas id="categoryChart"></canvas>
    </div>
</div>

<script>
const ctx = document.getElementById('categoryChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($categoryValues, 'name')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($categoryValues, 'value')) ?>,
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
        }]
    }
});
</script>
<?php $content = ob_get_clean(); require 'layout.php'; ?>