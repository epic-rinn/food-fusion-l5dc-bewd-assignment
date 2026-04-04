<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function is_admin(): bool {
    return is_logged_in() && ($_SESSION['user_role'] ?? '') === 'admin';
}

// Redirect to login if not authenticated
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit;
    }
}

// Redirect to home if not admin
function require_admin(): void {
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit;
    }
    if (!is_admin()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

// Redirect already logged-in users away from guest pages (login/register)
function redirect_if_logged_in(): void {
    if (is_logged_in()) {
        if (is_admin()) {
            header('Location: ' . SITE_URL . '/auth/dashboard.php');
        } else {
            header('Location: ' . SITE_URL . '/index.php');
        }
        exit;
    }
}

function current_user(): array {
    return [
        'id'    => $_SESSION['user_id']    ?? null,
        'name'  => $_SESSION['user_name']  ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['user_role']  ?? 'user',
    ];
}

function flash_set(string $key, string $message): void {
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}
