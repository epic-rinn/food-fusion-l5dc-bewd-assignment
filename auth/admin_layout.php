<?php

$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Admin – ' . SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'ui-sans-serif'] } } } }</script>
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased">

<!-- ── Top Navbar ─────────────────────────────────────────────────────────── -->
<nav class="bg-white shadow-sm sticky top-0 z-50 h-16 flex items-center">
    <div class="w-full px-4 flex items-center justify-between">
        <!-- Brand -->
        <a href="<?= SITE_URL ?>" class="text-xl font-bold text-orange-500 flex items-center gap-2">
            🍽 <?= SITE_NAME ?>
            <span class="text-xs font-semibold bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full">Admin</span>
        </a>

        <!-- Right side -->
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500 hidden sm:block">
                👋 <strong><?= htmlspecialchars($user['name']) ?></strong>
            </span>
            <a href="<?= SITE_URL ?>/auth/logout.php"
               class="text-sm bg-red-50 text-red-600 border border-red-200 px-4 py-1.5 rounded-full hover:bg-red-100 transition font-medium">
                Sign Out
            </a>
        </div>
    </div>
</nav>

<!-- ── Body: sidebar + content ───────────────────────────────────────────── -->
<div class="flex min-h-[calc(100vh-4rem)]">

    <!-- Sidebar -->
    <aside class="w-56 bg-white shadow-sm flex-shrink-0 hidden md:flex flex-col py-6 px-3 gap-1">
        <?php
        $nav_items = [
            'dashboard' => ['href' => SITE_URL . '/auth/dashboard.php',         'icon' => '📊', 'label' => 'Dashboard'],
            'recipes'   => ['href' => SITE_URL . '/auth/recipes/index.php',     'icon' => '🍴', 'label' => 'Recipes'],
            'cookbooks' => ['href' => SITE_URL . '/auth/cookbooks/index.php',   'icon' => '📖', 'label' => 'Cookbooks'],
        ];
        foreach ($nav_items as $key => $item):
            $is_active = ($active_nav ?? '') === $key;
        ?>
        <a href="<?= $item['href'] ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                  <?= $is_active
                        ? 'bg-orange-50 text-orange-600 font-semibold'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
            <span class="text-lg leading-none"><?= $item['icon'] ?></span>
            <?= $item['label'] ?>
        </a>
        <?php endforeach; ?>

        <!-- Divider -->
        <div class="border-t border-gray-100 mt-4 pt-4">
            <a href="<?= SITE_URL ?>/index.php"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition">
                <span class="text-lg leading-none">🏠</span>
                View Site
            </a>
        </div>
    </aside>

    <!-- Mobile sidebar strip -->
    <div class="md:hidden w-full fixed bottom-0 left-0 z-40 bg-white border-t border-gray-200 flex justify-around py-2">
        <?php foreach ($nav_items as $key => $item):
            $is_active = ($active_nav ?? '') === $key;
        ?>
        <a href="<?= $item['href'] ?>"
           class="flex flex-col items-center gap-0.5 text-xs px-4 py-1 rounded-lg transition
                  <?= $is_active ? 'text-orange-500 font-semibold' : 'text-gray-500' ?>">
            <span class="text-xl"><?= $item['icon'] ?></span>
            <?= $item['label'] ?>
        </a>
        <?php endforeach; ?>
        <a href="<?= SITE_URL ?>/index.php"
           class="flex flex-col items-center gap-0.5 text-xs px-4 py-1 rounded-lg text-gray-500 transition">
            <span class="text-xl">🏠</span>
            Site
        </a>
    </div>

    <!-- Main content -->
    <main class="flex-1 p-6 md:p-8 pb-24 md:pb-8 overflow-auto">
        <?= $page_content ?? '' ?>
    </main>
</div>

</body>
</html>
