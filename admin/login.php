<?php
require __DIR__ . '/../content.php';
if (is_admin()) { header('Location: index.php'); exit; }
$record = admin_record();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if (!$record || !hash_equals((string)$record['username'], $username) || !password_verify($password, (string)$record['password_hash'])) {
        $error = 'The username or password is incorrect.';
    } else {
        session_regenerate_id(true);
        $_SESSION['admin_username'] = $username;
        header('Location: index.php');
        exit;
    }
}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin login | Smart uPVC Bhuna</title><link rel="stylesheet" href="admin.css"></head><body><main class="admin-shell"><section class="admin-card"><p class="eyebrow">Smart uPVC Bhuna</p><h1>Admin login</h1><?php if (!$record): ?><p class="notice">No admin account exists yet. <a href="setup.php">Create the first account</a>.</p><?php endif; ?><?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Username<input name="username" required autocomplete="username"></label><label>Password<input name="password" type="password" required autocomplete="current-password"></label><button type="submit">Log in</button></form><a class="back-link" href="../index.html">Back to website</a></section></main></body></html>
