<?php
require __DIR__ . '/../content.php';
require_admin();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'upload') {
        $caption = trim((string)($_POST['caption'] ?? ''));
        $file = save_gallery_upload($_FILES['media'] ?? []);
        if ($file === false) {
            $error = 'Upload failed. Use JPG, PNG, WebP, MP4, or WebM under 40 MB.';
        } elseif (!create_media($caption ?: 'Smart uPVC Bhuna project', $file['path'], $file['type'])) {
            @unlink(__DIR__ . '/../' . $file['path']);
            $error = 'The database could not save this gallery item.';
        } else {
            $message = 'Gallery item uploaded successfully.';
        }
    }
    if ($action === 'delete') {
        $file = delete_media((int)($_POST['id'] ?? 0));
        if ($file) {
            if (strpos($file, 'images/') === 0) {
                @unlink(__DIR__ . '/../' . $file);
            }
            $message = 'Gallery item removed.';
        } else {
            $error = 'The gallery item could not be found.';
        }
    }
        if ($action === 'delete_default') {
          if (hide_default_gallery_media((string)($_POST['file'] ?? ''))) {
            $message = 'Default gallery image removed.';
          } else {
            $error = 'The default gallery image could not be found.';
          }
        }
}

function save_gallery_upload(array $file)
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm'];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 40 * 1024 * 1024) {
        return false;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file((string)$file['tmp_name']);
    if (!in_array($mime, $allowedTypes, true)) {
        return false;
    }
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'video/mp4' => 'mp4', 'video/webm' => 'webm'];
    $name = bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    $relativePath = 'images/' . $name;
    if (!move_uploaded_file((string)$file['tmp_name'], __DIR__ . '/../' . $relativePath)) {
        return false;
    }
    return ['path' => $relativePath, 'type' => strpos($mime, 'video/') === 0 ? 'video' : 'image'];
}

$defaultMedia = active_default_gallery_media();
$media = array_reverse(read_media());
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage gallery | Smart uPVC Bhuna</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <main class="admin-shell">
    <div class="admin-header">
      <div><p class="eyebrow">Smart uPVC Bhuna</p><h1>Manage gallery</h1><p class="muted">Upload or remove the photos and videos shown in the gallery.</p></div>
      <div class="admin-actions"><a href="index.php">Dashboard</a><a href="logout.php">Log out</a></div>
    </div>
    <?php if ($message): ?><p class="success"><?= e($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <section class="admin-panel">
      <h2>Upload gallery media</h2>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="upload">
        <label>Photo or video<input name="media" type="file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" required></label>
        <label>Caption<input name="caption" maxlength="160" placeholder="Sliding window installation"></label>
        <button type="submit">Upload to gallery</button>
      </form>
    </section>
    <section class="admin-panel" style="margin-top:24px">
      <h2>Default gallery images</h2>
      <div class="admin-list">
        <?php if (!$defaultMedia): ?><p class="muted">No default gallery images are currently shown.</p><?php else: foreach ($defaultMedia as $item): ?>
          <div class="admin-item"><div><strong><?= e((string)$item['caption']) ?></strong><small>Default image</small></div><div class="admin-actions"><a href="../<?= e((string)$item['file']) ?>" target="_blank">Open</a><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete_default"><input type="hidden" name="file" value="<?= e((string)$item['file']) ?>"><button class="danger" type="submit">Remove</button></form></div></div>
        <?php endforeach; endif; ?>
      </div>
      <h2 style="margin-top:28px">Uploaded gallery media</h2>
      <div class="admin-list">
        <?php if (!$media): ?><p class="muted">No uploaded gallery media yet.</p><?php else: foreach ($media as $item): ?>
          <div class="admin-item"><div><strong><?= e((string)$item['caption']) ?></strong><small><?= e((string)$item['type']) ?> · <?= e(public_date((string)$item['created_at'])) ?></small></div><div class="admin-actions"><a href="../<?= e((string)$item['file']) ?>" target="_blank">Open</a><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e((string)$item['id']) ?>"><button class="danger" type="submit">Delete</button></form></div></div>
        <?php endforeach; endif; ?>
      </div>
    </section>
  </main>
</body>
</html>
