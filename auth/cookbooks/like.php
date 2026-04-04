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
if ($cookbook_id < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid cookbook ID']);
    exit;
}

$pdo     = get_db();
$user_id = (int)current_user()['id'];

// Check if already liked
$stmt = $pdo->prepare('SELECT id FROM cookbook_likes WHERE cookbook_id = ? AND user_id = ?');
$stmt->execute([$cookbook_id, $user_id]);
$existing = $stmt->fetch();

if ($existing) {
    // Unlike
    $pdo->prepare('DELETE FROM cookbook_likes WHERE id = ?')->execute([$existing['id']]);
    $pdo->prepare('UPDATE cookbooks SET total_likes = GREATEST(total_likes - 1, 0) WHERE id = ?')->execute([$cookbook_id]);
    $liked = false;
} else {
    // Like
    $pdo->prepare('INSERT INTO cookbook_likes (cookbook_id, user_id) VALUES (?, ?)')->execute([$cookbook_id, $user_id]);
    $pdo->prepare('UPDATE cookbooks SET total_likes = total_likes + 1 WHERE id = ?')->execute([$cookbook_id]);
    $liked = true;
}

// Get updated count
$count_stmt = $pdo->prepare('SELECT total_likes FROM cookbooks WHERE id = ?');
$count_stmt->execute([$cookbook_id]);
$count = (int)$count_stmt->fetchColumn();

echo json_encode(['liked' => $liked, 'total_likes' => $count]);
