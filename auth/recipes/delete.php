<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/db.php';
require_once dirname(__DIR__)    . '/session.php';

require_admin();

$pdo = get_db();
$id  = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT id, title, photo FROM recipes WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    flash_set('error', 'Recipe not found.');
    header('Location: ' . SITE_URL . '/auth/recipes/index.php');
    exit;
}

// Delete the photo file from disk
if ($recipe['photo']) {
    $photo_path = dirname(__DIR__, 2) . '/uploads/recipes/' . $recipe['photo'];
    if (file_exists($photo_path)) {
        @unlink($photo_path);
    }
}

// Delete the DB record
$pdo->prepare('DELETE FROM recipes WHERE id = ?')->execute([$recipe['id']]);

flash_set('success', 'Recipe "' . $recipe['title'] . '" deleted.');
header('Location: ' . SITE_URL . '/auth/recipes/index.php');
exit;
