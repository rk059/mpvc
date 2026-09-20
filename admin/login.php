<?php
require __DIR__ . '/../content.php';
if (is_admin()) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $record = null;
    $pdo = database_connection();
    if ($pdo) {
        $statement = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1');
        $statement->execute([':email' => $email]);
        $record = $statement->fetch();
    }
    if (!$record || !password_verify($password, (string)$record['password'])) {
        $error = 'The username or password is incorrect.';
    } else {
        session_regenerate_id(true);
        $_SESSION['admin_user_id'] = $record['id'];
        $_SESSION['admin_name'] = $record['name'];
        header('Location: index.php');
        exit;
    }
}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin login | Smart uPVC Bhuna</title><link rel="stylesheet" href="admin.css"></head><body><main class="admin-shell"><section class="admin-card"><p class="eyebrow">Smart uPVC Bhuna</p><h1>Admin login</h1><?php if (!database_connection()): ?><p class="notice">Database connection is not configured. Add config.php on the server.</p><?php elseif (!admin_record()): ?><p class="notice">No admin account exists yet. <a href="setup.php">Create the first account</a>.</p><?php endif; ?><?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Email<input name="email" type="email" required autocomplete="email"></label><label>Password<input name="password" type="password" required autocomplete="current-password"></label><button type="submit">Log in</button></form><a class="back-link" href="../index.html">Back to website</a></section></main></body></html>
