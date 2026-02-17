<?php
function redirect($url) {
    header("Location: " . APP_URL . $url);
    exit;
}

function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
    return CsrfMiddleware::generateToken();
}

function old($key, $default = '') {
    return $_POST[$key] ?? $default;
}

function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}