<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST requests are accepted.']);
    exit;
}

$name = trim((string)($_POST['name'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$project = trim((string)($_POST['project'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $phone === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please provide your name and phone number.']);
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

$config = file_exists(__DIR__ . '/config.php') ? require __DIR__ . '/config.php' : [];
$host = $config['db_host'] ?? getenv('DB_HOST') ?: '';
$dbName = $config['db_name'] ?? getenv('DB_NAME') ?: '';
$dbUser = $config['db_user'] ?? getenv('DB_USER') ?: '';
$dbPassword = $config['db_password'] ?? getenv('DB_PASSWORD') ?: '';

try {
    if ($host !== '' && $dbName !== '' && $dbUser !== '') {
        $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $statement = $pdo->prepare('INSERT INTO enquiries (name, phone, email, project, message) VALUES (:name, :phone, :email, :project, :message)');
        $statement->execute([':name' => $name, ':phone' => $phone, ':email' => $email, ':project' => $project, ':message' => $message]);
        echo json_encode(['success' => true, 'message' => 'Thank you. Your enquiry has been received.']);
        exit;
    }

    $recipient = $config['enquiry_email'] ?? getenv('ENQUIRY_EMAIL') ?: '';
    if ($recipient !== '') {
        $subject = 'New Smart uPVC Bhuna enquiry';
        $body = "Name: {$name}\nPhone: {$phone}\nEmail: {$email}\nProject: {$project}\nMessage: {$message}";
        $headers = $email !== '' ? "Reply-To: {$email}\r\n" : '';
        if (mail($recipient, $subject, $body, $headers)) {
            echo json_encode(['success' => true, 'message' => 'Thank you. Your enquiry has been received.']);
            exit;
        }
    }

    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'The enquiry service is not configured yet. Please call us directly.']);
} catch (Throwable $error) {
    error_log($error->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'We could not submit the enquiry right now. Please call us directly.']);
}
