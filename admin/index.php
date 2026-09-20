<?php
require __DIR__ . '/../content.php';
require_admin();
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'post') {
        $title = trim((string)($_POST['title'] ?? ''));
        $body = trim((string)($_POST['body'] ?? ''));
        $category = trim((string)($_POST['category'] ?? 'Journal'));
        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $image = save_upload($_FILES['image'], ['image/jpeg', 'image/png', 'image/webp']);
        }
        if ($title === '' || $body === '') {
            $error = 'A title and article body are required.';
        } elseif ($image === false) {
            $error = 'The article image could not be uploaded. Use JPG, PNG, or WebP under 8 MB.';
        } else {
            $posts = read_content('posts');
            $posts[] = ['id' => bin2hex(random_bytes(8)), 'title' => $title, 'body' => $body, 'category' => $category ?: 'Journal', 'image' => $image, 'published_at' => date('c')];
            write_content('posts', $posts);
            $message = 'Blog post published.';
        }
    }
    if ($action === 'media') {
        $caption = trim((string)($_POST['caption'] ?? ''));
        $file = save_upload($_FILES['media'] ?? [], ['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm']);
        if ($file === false) {
            $error = 'The media could not be uploaded. Use JPG, PNG, WebP, MP4, or WebM under 40 MB.';
        } else {
            $media = read_content('media');
            $media[] = ['id' => bin2hex(random_bytes(8)), 'caption' => $caption ?: 'Smart uPVC Bhuna project', 'file' => $file, 'type' => strpos((string)($_FILES['media']['type'] ?? ''), 'video/') === 0 ? 'video' : 'image', 'created_at' => date('c')];
            write_content('media', $media);
            $message = 'Media published to the website gallery feed.';
        }
    }
    if ($action === 'delete_post') {
        $posts = array_values(array_filter(read_content('posts'), static fn (array $post): bool => (string)($post['id'] ?? '') !== (string)($_POST['id'] ?? '')));
        write_content('posts', $posts);
        $message = 'Blog post removed.';
    }
}
function save_upload(array $file, array $allowedTypes)
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 40 * 1024 * 1024) return false;
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file((string)$file['tmp_name']);
    if (!in_array($mime, $allowedTypes, true)) return false;
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'video/mp4' => 'mp4', 'video/webm' => 'webm'];
    $name = bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    $destination = __DIR__ . '/../images/uploads/' . $name;
    if (!move_uploaded_file((string)$file['tmp_name'], $destination)) return false;
    return 'images/uploads/' . $name;
}
$posts = read_content('posts');
$media = read_content('media');
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Website admin | Smart uPVC Bhuna</title><link rel="stylesheet" href="admin.css"></head><body><main class="admin-shell"><div class="admin-header"><div><p class="eyebrow">Smart uPVC Bhuna</p><h1>Website admin</h1><p class="muted">Publish articles and fresh project media.</p></div><a href="logout.php">Log out</a></div><nav class="admin-nav"><a href="../blog.php" target="_blank">View blog</a><a href="../gallery.html" target="_blank">View gallery</a></nav><?php if ($message): ?><p class="success"><?= e($message) ?></p><?php endif; ?><?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><div class="admin-grid"><section class="admin-panel"><h2>Publish a blog post</h2><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="post"><label>Title<input name="title" required maxlength="160"></label><label>Category<input name="category" value="Journal" maxlength="60"></label><label>Article image <span class="muted">(optional, 8 MB max)</span><input name="image" type="file" accept="image/jpeg,image/png,image/webp"></label><label>Article text<textarea name="body" rows="9" required></textarea></label><button type="submit">Publish post</button></form></section><section class="admin-panel"><h2>Publish photo or video</h2><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="media"><label>Photo or video<input name="media" type="file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" required></label><label>Caption<input name="caption" maxlength="160" placeholder="Sliding window installation"></label><button type="submit">Publish media</button></form></section></div><section class="admin-panel" style="margin-top:24px"><h2>Published blog posts</h2><div class="admin-list"><?php if (!$posts): ?><p class="muted">No posts published yet.</p><?php else: foreach (array_reverse($posts) as $post): ?><div class="admin-item"><div><strong><?= e((string)$post['title']) ?></strong><small><?= e(public_date((string)$post['published_at'])) ?></small></div><form method="post" class="admin-actions"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete_post"><input type="hidden" name="id" value="<?= e((string)$post['id']) ?>"><button class="danger" type="submit">Delete</button></form></div><?php endforeach; endif; ?></div><h2 style="margin-top:28px">Published media</h2><div class="admin-list"><?php if (!$media): ?><p class="muted">No uploaded media yet.</p><?php else: foreach (array_reverse($media) as $item): ?><div class="admin-item"><div><strong><?= e((string)$item['caption']) ?></strong><small><?= e((string)$item['type']) ?> · <?= e(public_date((string)$item['created_at'])) ?></small></div><a href="../<?= e((string)$item['file']) ?>" target="_blank">Open</a></div><?php endforeach; endif; ?></div></section></main></body></html>
