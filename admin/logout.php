<?php
require __DIR__ . '/../content.php';
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
