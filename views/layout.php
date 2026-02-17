<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="<?= url('/assets/style.css') ?>">
</head>
<body>
    <?php if (Session::get('user_id')) require 'nav.php'; ?>
    <div class="container mt-4">
        <?php if ($msg = Session::get('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= $msg; Session::delete('success'); ?></div>
        <?php endif; ?>
        <?php if ($err = Session::get('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= $err; Session::delete('error'); ?></div>
        <?php endif; ?>
        <?= $content ?? '' ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= url('/assets/script.js') ?>"></script>
</body>
</html>