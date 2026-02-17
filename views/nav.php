<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= url('/dashboard') ?>"><?= APP_NAME ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= url('/dashboard') ?>">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('/products') ?>">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('/stock/in') ?>">Stock In</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('/stock/out') ?>">Stock Out</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('/stock/history') ?>">History</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" data-bs-toggle="dropdown">Reports</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= url('/reports/daily') ?>">Daily Report</a></li>
                        <li><a class="dropdown-item" href="<?= url('/reports/monthly') ?>">Monthly Report</a></li>
                        <li><a class="dropdown-item" href="<?= url('/reports/low-stock') ?>">Low Stock Report</a></li>
                    </ul>
                </li>
            </ul>
            <span class="navbar-text me-3">Welcome, <?= escape(Session::get('user_name')) ?></span>
            <a href="<?= url('/logout') ?>" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>