<?php
require_once dirname(__DIR__) . '/db.php';

$pdo = new PDO(
    "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET,
    DB_USER,
    DB_PASS
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
$pdo->exec("USE " . DB_NAME);

$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

$results = [];

foreach ($files as $file) {
    $name = basename($file);
    $sql  = file_get_contents($file);

    try {
        $pdo->exec($sql);
        $results[] = ['name' => $name, 'ok' => true];
    } catch (PDOException $e) {
        $results[] = ['name' => $name, 'ok' => false, 'error' => $e->getMessage()];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Migrate – Food Fusion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'ui-sans-serif'] } } } }</script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6 font-sans antialiased">
    <div class="bg-white rounded-2xl shadow p-8 w-full max-w-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">🛠 Database Migration</h1>
        <p class="text-sm text-gray-400 mb-6">Database: <strong><?= DB_NAME ?></strong></p>

        <ul class="space-y-2 mb-6">
            <?php foreach ($results as $r): ?>
            <li class="flex items-center gap-3 text-sm p-3 rounded-xl <?= $r['ok'] ? 'bg-green-50' : 'bg-red-50' ?>">
                <span><?= $r['ok'] ? '✅' : '❌' ?></span>
                <span class="font-mono <?= $r['ok'] ? 'text-green-700' : 'text-red-700' ?>">
                    <?= htmlspecialchars($r['name']) ?>
                </span>
                <?php if (!$r['ok']): ?>
                <span class="text-red-500 text-xs"><?= htmlspecialchars($r['error']) ?></span>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>

        <div class="flex gap-3">
            <a href="migrate.php" class="bg-orange-500 text-white px-5 py-2 rounded-full text-sm hover:bg-orange-600">
                Run Again
            </a>
            <a href="../index.php" class="text-gray-400 px-5 py-2 rounded-full text-sm hover:text-orange-500">
                ← Back to site
            </a>
        </div>
    </div>
</body>
</html>
