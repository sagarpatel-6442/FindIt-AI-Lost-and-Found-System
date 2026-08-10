<?php
declare(strict_types=1);

function h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = BASE_URL;
    if ($path === '') return $base !== '' ? $base . '/' : '/';
    return ($base !== '' ? $base : '') . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function pull_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function old(string $key, string $default = ''): string
{
    return h((string)($_POST[$key] ?? $default));
}

function item_image_url(?string $path): string
{
    return $path ? url($path) : url('assets/images/placeholder.svg');
}

function status_badge(string $status): string
{
    return '<span class="badge badge-' . h($status) . '">' . h(ucfirst($status)) . '</span>';
}

function valid_date(string $date): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function image_to_data_url(?string $relativePath): ?string
{
    if (!$relativePath) return null;
    $full = ROOT_PATH . '/' . ltrim($relativePath, '/');
    if (!is_file($full) || filesize($full) > 6 * 1024 * 1024) return null;
    $mime = mime_content_type($full) ?: 'application/octet-stream';
    return 'data:' . $mime . ';base64,' . base64_encode((string)file_get_contents($full));
}
