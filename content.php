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
    foreach (['posts', 'media', 'gallery-hidden'] as $name) {
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
    $pdo = database_connection();
    if (!$pdo) {
        return null;
    }
    try {
        $statement = $pdo->query('SELECT id, name, email, password FROM users ORDER BY id ASC LIMIT 1');
        $record = $statement->fetch();
        return is_array($record) ? $record : null;
    } catch (Throwable $error) {
        error_log($error->getMessage());
        return null;
    }
}

function database_connection(): ?PDO
{
    static $pdo;
    static $attempted = false;
    static $connectionError = '';
    if ($attempted) {
        return $pdo;
    }
    $attempted = true;
    $config = file_exists(__DIR__ . '/config.php') ? require __DIR__ . '/config.php' : [];
    $host = $config['db_host'] ?? getenv('DB_HOST') ?: '';
    $dbName = $config['db_name'] ?? getenv('DB_NAME') ?: '';
    $dbUser = $config['db_user'] ?? getenv('DB_USER') ?: '';
    $dbPassword = $config['db_password'] ?? getenv('DB_PASSWORD') ?: '';
    if ($host === '' || $dbName === '' || $dbUser === '') {
        $connectionError = 'config.php is missing or one of db_host, db_name, or db_user is empty.';
        return null;
    }
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        $connectionError = 'MySQL connection failed. Check the cPanel database name, username, password, host, and user privileges.';
        $pdo = null;
    }
    return $pdo;
}

function create_admin_user(string $name, string $email, string $password): bool
{
    $pdo = database_connection();
    if (!$pdo) {
        return false;
    }
    $statement = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
    return $statement->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => password_hash($password, PASSWORD_DEFAULT),
    ]);
}

function read_media(): array
{
    $pdo = database_connection();
    if (!$pdo) {
        return read_content('media');
    }
    try {
        $statement = $pdo->query('SELECT id, caption, file, type, created_at FROM gallery_media ORDER BY created_at ASC');
        return $statement->fetchAll();
    } catch (Throwable $error) {
        error_log($error->getMessage());
        return [];
    }
}

function create_media(string $caption, string $file, string $type): bool
{
    $pdo = database_connection();
    if (!$pdo) {
        return false;
    }
    $statement = $pdo->prepare('INSERT INTO gallery_media (caption, file, type) VALUES (:caption, :file, :type)');
    return $statement->execute([':caption' => $caption, ':file' => $file, ':type' => $type]);
}

function default_gallery_media(): array
{
    return [
        ['file' => 'images/WhatsApp Image 2026-09-17 at 17.00.31.jpeg', 'caption' => 'uPVC door profile design', 'type' => 'image'],
        ['file' => 'images/WhatsApp Image 2026-09-17 at 17.09.13 (1).jpeg', 'caption' => 'Modern window frame detail', 'type' => 'image'],
        ['file' => 'images/WhatsApp Image 2026-09-17 at 17.09.13 (2).jpeg', 'caption' => 'Sliding window installation', 'type' => 'image'],
        ['file' => 'images/WhatsApp Image 2026-09-17 at 17.09.13 (3).jpeg', 'caption' => 'White aluminium window detail', 'type' => 'image'],
        ['file' => 'images/WhatsApp Image 2026-09-17 at 17.09.13 (4).jpeg', 'caption' => 'Sliding aluminium frame design', 'type' => 'image'],
        ['file' => 'images/WhatsApp Image 2026-09-17 at 17.09.14 (1).jpeg', 'caption' => 'Window glass panel installation', 'type' => 'image'],
        ['file' => 'images/WhatsApp Image 2026-09-17 at 17.09.15 (1).jpeg', 'caption' => 'Premium sliding door with aluminium profile', 'type' => 'image'],
        ['file' => 'images/WhatsApp Image 2026-09-17 at 17.09.16.jpeg', 'caption' => 'Architectural window frame', 'type' => 'image'],
        ['file' => 'images/IMG-20260919-WA0000.jpg', 'caption' => 'Recent Smart uPVC Bhuna project', 'type' => 'image'],
        ['file' => 'images/IMG-20260919-WA0004.jpg', 'caption' => 'Recent sliding window detail', 'type' => 'image'],
        ['file' => 'images/WhatsApp Image 2026-09-19 at 10.04.20.jpeg', 'caption' => 'Recent glass window installation', 'type' => 'image'],
        ['file' => 'images/WhatsApp Image 2026-09-19 at 10.04.21.jpeg', 'caption' => 'Recent window frame detail', 'type' => 'image'],
        ['file' => 'images/glass gate handle.jpeg', 'caption' => 'Glass gate handle detail', 'type' => 'image'],
        ['file' => 'images/shop glass windows.jpeg', 'caption' => 'Glass shop windows', 'type' => 'image'],
    ];
}

function active_default_gallery_media(): array
{
    $hidden = read_content('gallery-hidden');
    return array_values(array_filter(default_gallery_media(), static function (array $item) use ($hidden): bool {
        return !in_array($item['file'], $hidden, true);
    }));
}

function hide_default_gallery_media(string $file): bool
{
    if (!in_array($file, array_column(default_gallery_media(), 'file'), true)) {
        return false;
    }
    $hidden = read_content('gallery-hidden');
    if (!in_array($file, $hidden, true)) {
        $hidden[] = $file;
        write_content('gallery-hidden', $hidden);
    }
    return true;
}

function delete_media(int $id): ?string
{
    $pdo = database_connection();
    if (!$pdo) {
        return null;
    }
    $statement = $pdo->prepare('SELECT file FROM gallery_media WHERE id = :id LIMIT 1');
    $statement->execute([':id' => $id]);
    $media = $statement->fetch();
    if (!$media) {
        return null;
    }
    $delete = $pdo->prepare('DELETE FROM gallery_media WHERE id = :id');
    $delete->execute([':id' => $id]);
    return (string)$media['file'];
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_user_id']);
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
