<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/db.php';
require_once dirname(__DIR__)    . '/session.php';

require_admin();

$pdo    = get_db();
$id     = (int)($_GET['id'] ?? 0);
$status = trim($_GET['status'] ?? '');

if (!in_array($status, ['approved', 'rejected'], true)) {
    flash_set('error', 'Invalid status.');
    header('Location: ' . SITE_URL . '/auth/cookbooks/index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, name FROM cookbooks WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$cookbook = $stmt->fetch();

if (!$cookbook) {
    flash_set('error', 'Cookbook not found.');
    header('Location: ' . SITE_URL . '/auth/cookbooks/index.php');
    exit;
}

$pdo->prepare('UPDATE cookbooks SET status = ? WHERE id = ?')->execute([$status, $id]);

flash_set('success', 'Cookbook "' . $cookbook['name'] . '" ' . $status . '.');
header('Location: ' . SITE_URL . '/auth/cookbooks/index.php');
exit;
