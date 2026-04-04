<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/db.php';
require_once dirname(__DIR__)    . '/session.php';

require_login();

$pdo  = get_db();
$user = current_user();
$id   = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT id, name, photo, user_id FROM cookbooks WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$cookbook = $stmt->fetch();

if (!$cookbook) {
    flash_set('error', 'Cookbook not found.');
    header('Location: ' . SITE_URL . '/auth/cookbooks/index.php');
    exit;
}

// Only owner or admin can delete
if (!is_admin() && $cookbook['user_id'] !== (int)$user['id']) {
    flash_set('error', 'You can only delete your own cookbooks.');
    header('Location: ' . SITE_URL . '/auth/cookbooks/index.php');
    exit;
}

// Delete the photo file from disk
if ($cookbook['photo']) {
    $photo_path = dirname(__DIR__, 2) . '/uploads/cookbooks/' . $cookbook['photo'];
    if (file_exists($photo_path)) {
        @unlink($photo_path);
    }
}

// Delete the DB record
$pdo->prepare('DELETE FROM cookbooks WHERE id = ?')->execute([$cookbook['id']]);

flash_set('success', 'Cookbook "' . $cookbook['name'] . '" deleted.');
header('Location: ' . SITE_URL . '/auth/cookbooks/index.php');
exit;
