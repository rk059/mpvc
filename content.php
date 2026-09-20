<?php
declare(strict_types=1);

function content_path(string $name): string
{
    return __DIR__ . '/data/' . $name . '.json';
}

function ensure_content_storage(): void
{
    $dataDirectory = __DIR__ . '/data';
    $uploadDirectory = __DIR__ . '/images/uploads';
    if (!is_dir($dataDirectory)) {
        mkdir($dataDirectory, 0750, true);
    }
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }
    foreach (['posts', 'media'] as $name) {
        if (!file_exists(content_path($name))) {
            file_put_contents(content_path($name), "[]", LOCK_EX);
        }
    }
}

function read_content(string $name): array
{
    ensure_content_storage();
    $contents = file_get_contents(content_path($name));
    $items = json_decode($contents ?: '[]', true);
    return is_array($items) ? $items : [];
}

function write_content(string $name, array $items): void
{
    ensure_content_storage();
    file_put_contents(content_path($name), json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
}

function admin_record(): ?array
{
    ensure_content_storage();
    $path = content_path('admin');
    if (!file_exists($path)) {
        return null;
    }
    $record = json_decode(file_get_contents($path) ?: '', true);
    return is_array($record) && isset($record['username'], $record['password_hash']) ? $record : null;
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_username']);
}

function require_admin(): void
{
    if (!is_admin()) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = (string)($_POST['csrf_token'] ?? '');
    if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $token)) {
        http_response_code(400);
        exit('Invalid form token. Please go back and try again.');
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function public_date(string $date): string
{
    $timestamp = strtotime($date);
    return $timestamp ? date('F j, Y', $timestamp) : $date;
}

session_start();
ensure_content_storage();
