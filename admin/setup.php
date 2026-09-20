<?php
require __DIR__ . '/../content.php';
if (admin_record()) { header('Location: login.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if (!preg_match('/^[A-Za-z0-9._-]{3,40}$/', $username)) {
        $error = 'Use a username with 3 to 40 letters, numbers, dots, dashes, or underscores.';
    } elseif (strlen($password) < 8) {
        $error = 'Use a password with at least 8 characters.';
    } else {
        file_put_contents(content_path('admin'), json_encode(['username' => $username, 'password_hash' => password_hash($password, PASSWORD_DEFAULT)], JSON_PRETTY_PRINT), LOCK_EX);
        $_SESSION['admin_username'] = $username;
        header('Location: index.php');
        exit;
    }
}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Create admin account | Smart uPVC Bhuna</title><link rel="stylesheet" href="admin.css"></head><body><main class="admin-shell"><section class="admin-card"><p class="eyebrow">First-time setup</p><h1>Create admin account</h1><p class="muted">This account will manage blog posts and uploaded photos or videos.</p><?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Username<input name="username" required autocomplete="username"></label><label>Password<input name="password" type="password" required minlength="8" autocomplete="new-password"></label><button type="submit">Create account</button></form></section></main></body></html>
