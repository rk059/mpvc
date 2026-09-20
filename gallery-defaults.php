<?php
require __DIR__ . '/content.php';
header('Content-Type: application/json; charset=utf-8');
echo json_encode(active_default_gallery_media(), JSON_UNESCAPED_SLASHES);