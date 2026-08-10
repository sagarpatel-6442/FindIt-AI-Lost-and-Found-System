<?php
declare(strict_types=1);

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_admin(): bool
{
    return (current_user()['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!current_user()) {
        flash('warning', 'Please log in to continue.');
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        exit('Administrator access is required.');
    }
}

function require_guest(): void
{
    if (current_user()) redirect('dashboard.php');
}

function secure_password_hash(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function secure_password_verify(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

function login_is_rate_limited(): bool
{
    $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'time' => time()];
    if (time() - (int)($attempts['time'] ?? 0) > 900) {
        $_SESSION['login_attempts'] = ['count' => 0, 'time' => time()];
        return false;
    }
    return (int)($attempts['count'] ?? 0) >= 8;
}

function record_login_failure(): void
{
    $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'time' => time()];
    if (time() - (int)($attempts['time'] ?? 0) > 900) $attempts = ['count' => 0, 'time' => time()];
    $attempts['count'] = (int)($attempts['count'] ?? 0) + 1;
    $_SESSION['login_attempts'] = $attempts;
}

function clear_login_failures(): void
{
    unset($_SESSION['login_attempts']);
}
