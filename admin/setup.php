<?php
require __DIR__ . '/../content.php';
$configuredAdmin = admin_record();
if (!$configuredAdmin) {
    $config = file_exists(__DIR__ . '/../config.php') ? require __DIR__ . '/../config.php' : [];
    $configuredName = trim((string)($config['admin_name'] ?? ''));
    $configuredEmail = trim((string)($config['admin_email'] ?? ''));
    $configuredPassword = (string)($config['admin_password'] ?? '');
    if ($configuredName !== '' && filter_var($configuredEmail, FILTER_VALIDATE_EMAIL) && strlen($configuredPassword) >= 8 && database_connection()) {
        try {
            create_admin_user($configuredName, $configuredEmail, $configuredPassword);
            $configuredAdmin = admin_record();
        } catch (Throwable $exception) {
            $configuredAdmin = null;
        }
    }
}
if ($configuredAdmin) { header('Location: login.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if (mb_strlen($name) < 2 || mb_strlen($name) > 120) {
        $error = 'Enter a name between 2 and 120 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Use a password with at least 8 characters.';
    } elseif (!database_connection()) {
        $error = file_exists(__DIR__ . '/../config.php')
            ? 'MySQL connection failed. Check the cPanel database name, username, password, host, and user privileges.'
            : 'config.php was not found beside enquiry.php.';
    } else {
        try {
            create_admin_user($name, $email, $password);
            $record = admin_record();
            $_SESSION['admin_user_id'] = $record['id'];
            $_SESSION['admin_name'] = $record['name'];
            header('Location: index.php');
            exit;
        } catch (Throwable $exception) {
            $error = 'This email may already be registered, or the users table is missing.';
        }
    }
}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Create admin account | Smart uPVC Bhuna</title><link rel="stylesheet" href="admin.css"></head><body><main class="admin-shell"><section class="admin-card"><p class="eyebrow">First-time setup</p><h1>Create admin account</h1><p class="muted">This account will manage blog posts and uploaded photos or videos.</p><?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Full name<input name="name" required maxlength="120" autocomplete="name"></label><label>Email<input name="email" type="email" required maxlength="190" autocomplete="email"></label><label>Password<input name="password" type="password" required minlength="8" autocomplete="new-password"></label><button type="submit">Create account</button></form></section></main></body></html>
