<?php ob_start(); ?>
<h1>Monthly Report: <?= $year ?>-<?= $month ?></h1>
<form method="get" action="<?= url('/reports/monthly') ?>" class="row g-3 mb-3">
    <div class="col-auto">
        <input type="number" name="year" value="<?= $year ?>" placeholder="Year">
    </div>
    <div class="col-auto">
        <input type="number" name="month" value="<?= $month ?>" placeholder="Month" min="1" max="12">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">View</button>
    </div>
</form>
<table class="table">
    <thead><tr><th>Date</th><th>Stock In</th><th>Stock Out</th></tr></thead>
    <tbody>
        <?php 
        $daily = [];
        foreach ($data as $row) {
            $daily[$row['date']][$row['type']] = $row['total_quantity'];
        }
        ksort($daily);
        foreach ($daily as $d => $types): 
        ?>
        <tr>
            <td><?= $d ?></td>
            <td><?= $types['IN'] ?? 0 ?></td>
            <td><?= $types['OUT'] ?? 0 ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); require 'views/layout.php'; ?>