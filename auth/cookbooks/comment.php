<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/db.php';
require_once dirname(__DIR__)    . '/session.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Login required', 'redirect' => SITE_URL . '/auth/login.php']);
    exit;
}

$cookbook_id = (int)($_POST['cookbook_id'] ?? 0);
$comment    = trim($_POST['comment'] ?? '');

if ($cookbook_id < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid cookbook ID']);
    exit;
}

if ($comment === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Comment cannot be empty']);
    exit;
}

$pdo  = get_db();
$user = current_user();

$stmt = $pdo->prepare('INSERT INTO cookbook_comments (cookbook_id, user_id, comment) VALUES (?, ?, ?)');
$stmt->execute([$cookbook_id, $user['id'], $comment]);

echo json_encode([
    'success'    => true,
    'comment'    => htmlspecialchars($comment),
    'author'     => htmlspecialchars($user['name']),
    'initial'    => strtoupper(mb_substr($user['name'], 0, 1)),
    'created_at' => date('M j, Y · g:ia'),
]);
