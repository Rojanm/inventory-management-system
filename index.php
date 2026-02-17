<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes.php';
require_once __DIR__ . '/functions.php';

Session::start();

// Get the full requested path
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Get the base directory of this script (e.g., /group2)
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

// Remove the base from the request
if ($base && strpos($request, $base) === 0) {
    $route = substr($request, strlen($base));
} else {
    $route = $request;
}

// Clean up slashes
$route = trim($route, '/');

$method = $_SERVER['REQUEST_METHOD'];

// Routing table
if ($route == '' || $route == 'login') {
    if ($method == 'POST') {
        (new AuthController())->login();
    } else {
        (new AuthController())->showLogin();
    }
} elseif ($route == 'logout') {
    (new AuthController())->logout();
} elseif ($route == 'dashboard') {
    (new DashboardController())->index();
} elseif ($route == 'products') {
    (new ProductController())->index();
} elseif ($route == 'products/create') {
    (new ProductController())->create();
} elseif (preg_match('#^products/edit/(\d+)$#', $route, $matches)) {
    (new ProductController())->edit($matches[1]);
} elseif (preg_match('#^products/delete/(\d+)$#', $route, $matches) && $method == 'POST') {
    (new ProductController())->delete($matches[1]);
} elseif ($route == 'stock/in') {
    (new StockController())->stockIn();
} elseif ($route == 'stock/out') {
    (new StockController())->stockOut();
} elseif ($route == 'stock/history') {
    (new StockController())->history();
} elseif ($route == 'reports') {
    (new ReportController())->index();
} elseif ($route == 'reports/daily') {
    (new ReportController())->daily();
} elseif ($route == 'reports/monthly') {
    (new ReportController())->monthly();
} elseif ($route == 'reports/low-stock') {
    (new ReportController())->lowStock();
} else {
    http_response_code(404);
    echo "404 Not Found";
}