<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? SITE_NAME) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description ?? SITE_DESCRIPTION) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        primary: '#f97316',
                    }
                }
            }
        }
    </script>
    <link rel="icon" href="<?= SITE_URL ?>/assets/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/app.css">
</head>
<body class="bg-white text-gray-800 font-sans antialiased">

<!-- Navbar -->
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">

        <!-- Logo -->
        <a href="<?= SITE_URL ?>" class="text-xl font-bold text-orange-500 tracking-tight shrink-0">
            🍽 <?= SITE_NAME ?>
        </a>

        <!-- Desktop Nav Links -->
        <div class="hidden xl:flex items-center gap-1 text-sm font-medium flex-1 justify-center">
            <a href="<?= SITE_URL ?>/index.php"
               class="<?= is_active('index.php') ?> px-3 py-2 rounded-lg transition whitespace-nowrap">
                Home
            </a>
            <a href="<?= SITE_URL ?>/pages/about.php"
               class="<?= is_active('about.php') ?> px-3 py-2 rounded-lg transition whitespace-nowrap">
                About Us
            </a>
            <a href="<?= SITE_URL ?>/pages/menu.php"
               class="<?= in_array(current_page(), ['menu.php','recipe-detail.php']) ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500' ?> px-3 py-2 rounded-lg transition whitespace-nowrap">
                Recipe Collection
            </a>
            <a href="<?= SITE_URL ?>/pages/contact.php"
               class="<?= is_active('contact.php') ?> px-3 py-2 rounded-lg transition whitespace-nowrap">
                Contact Us
            </a>

            <!-- Resources dropdown -->
            <div class="relative" id="resources-dropdown">
                <button type="button" onclick="document.getElementById('resources-dropdown').classList.toggle('open')"
                        class="<?= in_array(current_page(), ['culinary-resources.php','educational-resources.php']) ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500' ?> px-3 py-2 rounded-lg transition whitespace-nowrap inline-flex items-center gap-1">
                    Resources
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="resources-menu hidden absolute top-full left-0 mt-1 bg-white border border-gray-100 rounded-xl shadow-lg py-1 min-w-[200px] z-50">
                    <a href="<?= SITE_URL ?>/pages/culinary-resources.php"
                       class="block px-4 py-2 text-sm <?= is_active('culinary-resources.php') ?> hover:bg-orange-50 transition">
                        Culinary Resources
                    </a>
                    <a href="<?= SITE_URL ?>/pages/educational-resources.php"
                       class="block px-4 py-2 text-sm <?= is_active('educational-resources.php') ?> hover:bg-orange-50 transition">
                        Educational Resources
                    </a>
                </div>
            </div>

            <a href="<?= SITE_URL ?>/pages/community-cookbook.php"
               class="<?= in_array(current_page(), ['community-cookbook.php','cookbook-detail.php']) ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500' ?> px-3 py-2 rounded-lg transition whitespace-nowrap">
                Community Cookbook
            </a>
        </div>

        <!-- Desktop Auth -->
        <?php if (is_logged_in()): ?>
        <div class="hidden xl:flex items-center gap-3 shrink-0">
            <span class="text-sm text-gray-500">👤 <?= htmlspecialchars(current_user()['name']) ?></span>
            <?php if (is_admin()): ?>
            <a href="<?= SITE_URL ?>/auth/dashboard.php"
               class="text-sm text-orange-500 font-medium hover:underline transition">Dashboard</a>
            <?php endif; ?>
            <a href="<?= SITE_URL ?>/auth/logout.php"
               class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-2 rounded-full hover:bg-red-100 transition font-medium">
                Sign Out
            </a>
        </div>
        <?php else: ?>
        <div class="hidden xl:flex items-center gap-2 shrink-0">
            <a href="<?= SITE_URL ?>/auth/login.php"
               class="<?= is_active('login.php') ?> text-sm font-medium transition px-4 py-2 rounded-lg">
                Login
            </a>
            <a href="<?= SITE_URL ?>/auth/register.php"
               class="bg-orange-500 text-white text-sm px-5 py-2 rounded-full hover:bg-orange-600 transition font-medium">
                Register
            </a>
        </div>
        <?php endif; ?>

        <!-- Mobile menu button -->
        <button id="mobile-menu-btn" class="xl:hidden text-gray-600 hover:text-orange-500 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden xl:hidden border-t border-gray-100 px-4 pb-4 pt-2 space-y-1 text-sm font-medium">
        <a href="<?= SITE_URL ?>/index.php"
           class="block px-3 py-2 rounded-lg <?= is_active('index.php') ?>">Home</a>
        <a href="<?= SITE_URL ?>/pages/about.php"
           class="block px-3 py-2 rounded-lg <?= is_active('about.php') ?>">About Us</a>
        <a href="<?= SITE_URL ?>/pages/menu.php"
           class="block px-3 py-2 rounded-lg <?= in_array(current_page(), ['menu.php','recipe-detail.php']) ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500' ?>">
            Recipe Collection
        </a>
        <a href="<?= SITE_URL ?>/pages/contact.php"
           class="block px-3 py-2 rounded-lg <?= is_active('contact.php') ?>">Contact Us</a>
        <a href="<?= SITE_URL ?>/pages/culinary-resources.php"
           class="block px-3 py-2 rounded-lg <?= is_active('culinary-resources.php') ?>">Culinary Resources</a>
        <a href="<?= SITE_URL ?>/pages/educational-resources.php"
           class="block px-3 py-2 rounded-lg <?= is_active('educational-resources.php') ?>">Educational Resources</a>
        <a href="<?= SITE_URL ?>/pages/community-cookbook.php"
           class="block px-3 py-2 rounded-lg <?= in_array(current_page(), ['community-cookbook.php','cookbook-detail.php']) ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500' ?>">
            Community Cookbook
        </a>
        <div class="pt-2 border-t border-gray-100 space-y-1">
            <?php if (is_logged_in()): ?>
            <?php if (is_admin()): ?>
            <a href="<?= SITE_URL ?>/auth/dashboard.php"
               class="block bg-orange-50 text-orange-600 border border-orange-200 px-4 py-2 rounded-full text-center hover:bg-orange-100 transition">
                Dashboard
            </a>
            <?php endif; ?>
            <a href="<?= SITE_URL ?>/auth/logout.php"
               class="block bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-full text-center hover:bg-red-100 transition">
                Sign Out
            </a>
            <?php else: ?>
            <a href="<?= SITE_URL ?>/auth/login.php"
               class="block border border-gray-200 text-gray-600 px-4 py-2 rounded-full text-center hover:text-orange-500 transition">
                Login
            </a>
            <a href="<?= SITE_URL ?>/auth/register.php"
               class="block bg-orange-500 text-white px-4 py-2 rounded-full text-center hover:bg-orange-600 transition">
                Register
            </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<!-- End Navbar -->

<!-- Resources dropdown: open/close logic -->
<script>
(function() {
    const dropdown = document.getElementById('resources-dropdown');
    if (!dropdown) return;
    const menu = dropdown.querySelector('.resources-menu');

    // Toggle on click
    dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Show/hide based on .open class
    const observer = new MutationObserver(function() {
        menu.classList.toggle('hidden', !dropdown.classList.contains('open'));
    });
    observer.observe(dropdown, { attributes: true, attributeFilter: ['class'] });

    // Close when clicking outside
    document.addEventListener('click', function() {
        dropdown.classList.remove('open');
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') dropdown.classList.remove('open');
    });
})();
</script>
