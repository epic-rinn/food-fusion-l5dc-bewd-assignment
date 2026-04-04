<?php
require_once 'config.php';
require_once 'db.php';
require_once 'auth/session.php';

$page_title       = 'Food Fusion – Home';
$meta_description = 'Food Fusion brings world-class recipes and culinary experiences to your kitchen.';

$pdo  = get_db();
$stmt = $pdo->query(
    'SELECT id, title, description, category, photo, prep_time, cook_time, servings
     FROM recipes
     ORDER BY created_at DESC
     LIMIT 6'
);
$featured_recipes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'ui-sans-serif'] } } } }</script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        .carousel-track { transition: transform 0.5s cubic-bezier(.4,0,.2,1); }
        .line-clamp-2   { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .cookie-bar     { transition: transform .4s ease, opacity .4s ease; }
        .signup-popup   { transition: transform .4s ease, opacity .4s ease; }
    </style>
</head>
<body class="bg-white text-gray-800 font-sans antialiased">

<?php require_once 'includes/header.php'; ?>

<section class="relative bg-gradient-to-br from-orange-50 via-amber-50 to-white pt-24 pb-20 px-4 overflow-hidden">
    <div class="absolute -top-20 -right-20 w-96 h-96 bg-orange-100 rounded-full opacity-40 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-amber-100 rounded-full opacity-40 blur-3xl pointer-events-none"></div>

    <div class="max-w-6xl mx-auto relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block bg-orange-100 text-orange-600 text-xs font-semibold px-3 py-1 rounded-full mb-5 uppercase tracking-widest">
                    🌍 World-Class Recipes
                </span>
                <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 leading-tight mb-5">
                    Where Every Bite<br>Tells a <span class="text-orange-500">Story</span>
                </h1>
                <p class="text-gray-500 text-lg leading-relaxed mb-4 max-w-lg">
                    Food Fusion is on a mission to celebrate global culinary culture — bringing
                    authentic recipes, expert techniques, and food stories from every corner of the world
                    straight to your kitchen.
                </p>
                <p class="text-gray-400 text-sm mb-8 max-w-lg">
                    Discover hand-curated recipes, learn from master chefs, and join a community
                    of food lovers who believe cooking is the universal language.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="pages/menu.php"
                       class="bg-orange-500 hover:bg-orange-600 text-white px-7 py-3 rounded-full font-semibold transition text-sm shadow-lg shadow-orange-200">
                        Explore Recipes
                    </a>
                    <button id="join-us-btn"
                            class="border-2 border-orange-400 text-orange-500 hover:bg-orange-50 px-7 py-3 rounded-full font-semibold transition text-sm">
                        Join Us — It's Free
                    </button>
                </div>

                <!-- Social proof -->
                <div class="flex items-center gap-6 mt-10 text-sm text-gray-500">
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-gray-900"><?= number_format($pdo->query('SELECT COUNT(*) FROM recipes')->fetchColumn()) ?>+</p>
                        <p class="text-xs">Recipes</p>
                    </div>
                    <div class="w-px h-8 bg-gray-200"></div>
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-gray-900"><?= number_format($pdo->query('SELECT COUNT(*) FROM users')->fetchColumn()) ?>+</p>
                        <p class="text-xs">Members</p>
                    </div>
                    <div class="w-px h-8 bg-gray-200"></div>
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-gray-900">20+</p>
                        <p class="text-xs">Categories</p>
                    </div>
                </div>
            </div>

            <div class="hidden lg:grid grid-cols-2 gap-3">
                <?php
                $hero_imgs = array_slice($featured_recipes, 0, 4);
                $placeholders = ['🥗','🍝','🍜','🥘'];
                foreach ($hero_imgs as $i => $r): ?>
                <div class="rounded-2xl overflow-hidden aspect-square <?= $i === 0 ? 'col-span-2 aspect-video' : '' ?> bg-orange-50">
                    <?php if ($r['photo']): ?>
                    <img src="<?= SITE_URL ?>/uploads/recipes/<?= htmlspecialchars($r['photo']) ?>"
                         alt="<?= htmlspecialchars($r['title']) ?>"
                         class="w-full h-full object-cover">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-5xl bg-gradient-to-br from-orange-100 to-amber-200">
                        <?= $placeholders[$i] ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php if (count($hero_imgs) < 4): ?>
                <?php for ($i = count($hero_imgs); $i < 4; $i++): ?>
                <div class="rounded-2xl bg-gradient-to-br from-orange-100 to-amber-200 aspect-square flex items-center justify-center text-5xl">
                    <?= $placeholders[$i] ?>
                </div>
                <?php endfor; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="py-20 px-4 bg-white">
    <div class="max-w-6xl mx-auto">

        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-xs font-semibold text-orange-500 uppercase tracking-widest">Latest</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-1">Featured Recipes</h2>
            </div>
            <a href="pages/menu.php" class="text-sm text-orange-500 hover:underline font-medium hidden sm:block">
                View all →
            </a>
        </div>

        <?php if (!empty($featured_recipes)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
            <?php foreach ($featured_recipes as $r): ?>
            <a href="<?= SITE_URL ?>/pages/recipe-detail.php?id=<?= $r['id'] ?>"
               class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-md transition group block">
                <?php if ($r['photo']): ?>
                <div class="overflow-hidden h-44">
                    <img src="<?= SITE_URL ?>/uploads/recipes/<?= htmlspecialchars($r['photo']) ?>"
                         alt="<?= htmlspecialchars($r['title']) ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <?php else: ?>
                <div class="w-full h-44 bg-gradient-to-br from-orange-50 to-amber-100 flex items-center justify-center text-5xl">🍽</div>
                <?php endif; ?>

                <div class="p-5">
                    <?php if ($r['category']): ?>
                    <span class="text-xs font-semibold text-orange-500 uppercase tracking-wider"><?= htmlspecialchars($r['category']) ?></span>
                    <?php endif; ?>
                    <h3 class="font-bold text-gray-900 mt-1 mb-2 group-hover:text-orange-500 transition"><?= htmlspecialchars($r['title']) ?></h3>
                    <?php if ($r['description']): ?>
                    <p class="text-gray-500 text-xs leading-relaxed line-clamp-2"><?= htmlspecialchars($r['description']) ?></p>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                        <?php if ($r['prep_time'] || $r['cook_time']): ?>
                        <span>⏱ <?= (int)($r['prep_time'] + $r['cook_time']) ?> min</span>
                        <?php endif; ?>
                        <?php if ($r['servings']): ?>
                        <span>🍽 <?= $r['servings'] ?> servings</span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-xs font-semibold text-orange-500 uppercase tracking-widest">Trending</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-1">Culinary Trends</h2>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            $trends = [
                ['emoji' => '🌱', 'tag' => 'Plant-Based', 'color' => 'bg-green-50 border-green-200',
                 'title' => 'The Rise of Plant-Based Cuisine',
                 'desc'  => 'Chefs worldwide are reimagining vegetables as the centrepiece — not the side dish. Discover bold, meat-free techniques that are taking restaurant menus by storm.'],
                ['emoji' => '🔥', 'tag' => 'Fermentation', 'color' => 'bg-orange-50 border-orange-200',
                 'title' => 'Fermentation is Having a Moment',
                 'desc'  => 'From kimchi to kefir, fermented foods are celebrated for their complex flavours and gut-health benefits. Learn the art of controlled fermentation at home.'],
                ['emoji' => '🌏', 'tag' => 'Fusion', 'color' => 'bg-blue-50 border-blue-200',
                 'title' => 'Korean-Mexican Fusion Explodes',
                 'desc'  => 'Gochujang-spiced carnitas, kimchi quesadillas, bulgogi burritos — East-meets-West mashups are the hottest food trend of the year.'],
            ];
            foreach ($trends as $t): ?>
            <div class="border <?= $t['color'] ?> rounded-2xl p-6 hover:shadow-sm transition">
                <div class="text-3xl mb-3"><?= $t['emoji'] ?></div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider"><?= $t['tag'] ?></span>
                <h3 class="font-bold text-gray-900 mt-1 mb-2 text-sm leading-snug"><?= $t['title'] ?></h3>
                <p class="text-gray-500 text-xs leading-relaxed"><?= $t['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-20 px-4 bg-gray-50 overflow-hidden">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-xs font-semibold text-orange-500 uppercase tracking-widest">Upcoming</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-1">Cooking Events</h2>
            </div>
            <div class="flex gap-2">
                <button id="prev-btn"
                        class="w-10 h-10 rounded-full border border-gray-200 bg-white flex items-center justify-center hover:border-orange-400 hover:text-orange-500 transition text-gray-500">
                    ←
                </button>
                <button id="next-btn"
                        class="w-10 h-10 rounded-full border border-gray-200 bg-white flex items-center justify-center hover:border-orange-400 hover:text-orange-500 transition text-gray-500">
                    →
                </button>
            </div>
        </div>

        <div class="overflow-hidden" id="carousel-viewport">
            <div class="flex gap-5 carousel-track" id="carousel-track">
                <?php
                $events = [
                    ['emoji' => '🍕', 'date' => 'Mar 22, 2026', 'title' => 'Italian Cooking Masterclass',
                     'desc'  => 'Master hand-rolled pasta, authentic Neapolitan pizza dough, and classic sauces with Chef Marco.',
                     'seats' => 12, 'duration' => '3 hours', 'level' => 'Beginner'],
                    ['emoji' => '🍣', 'date' => 'Mar 29, 2026', 'title' => 'Sushi Rolling Workshop',
                     'desc'  => 'Learn the precision of Japanese sushi art — nigiri, maki, and temaki — with Chef Yuki.',
                     'seats' => 8, 'duration' => '2.5 hours', 'level' => 'Intermediate'],
                    ['emoji' => '🥐', 'date' => 'Apr 5, 2026', 'title' => 'French Pastry Intensive',
                     'desc'  => 'Croissants, éclairs, and tarte tatin. A full-day immersion in the art of French pâtisserie.',
                     'seats' => 10, 'duration' => '6 hours', 'level' => 'Advanced'],
                    ['emoji' => '🔥', 'date' => 'Apr 12, 2026', 'title' => 'BBQ & Live Fire Cooking',
                     'desc'  => 'Dry rubs, smoke rings, and fire management — everything you need to master the backyard grill.',
                     'seats' => 15, 'duration' => '4 hours', 'level' => 'All Levels'],
                    ['emoji' => '🌿', 'date' => 'Apr 19, 2026', 'title' => 'Vegan Cuisine Intensive',
                     'desc'  => 'Plant-based proteins, umami-building techniques, and desserts that taste indulgent.',
                     'seats' => 12, 'duration' => '3 hours', 'level' => 'Beginner'],
                    ['emoji' => '🌶', 'date' => 'Apr 26, 2026', 'title' => 'Spice Blending & World Flavours',
                     'desc'  => 'Explore spice markets from India, Mexico, and Morocco — then build your own signature blend.',
                     'seats' => 14, 'duration' => '2 hours', 'level' => 'All Levels'],
                ];
                foreach ($events as $event): ?>
                <div class="carousel-slide flex-shrink-0 w-72 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-br from-orange-400 to-amber-500 h-28 flex items-center justify-center text-5xl">
                        <?= $event['emoji'] ?>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-orange-500"><?= $event['date'] ?></span>
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full"><?= $event['level'] ?></span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-sm mb-2 leading-snug"><?= $event['title'] ?></h3>
                        <p class="text-gray-500 text-xs leading-relaxed mb-4 line-clamp-2"><?= $event['desc'] ?></p>
                        <div class="flex items-center justify-between text-xs text-gray-400 mb-4">
                            <span>⏱ <?= $event['duration'] ?></span>
                            <span>👥 <?= $event['seats'] ?> seats left</span>
                        </div>
                        <button onclick="document.getElementById('join-us-btn').click()"
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold py-2 rounded-xl transition">
                            Reserve a Spot
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex justify-center gap-2 mt-6" id="carousel-dots"></div>
    </div>
</section>

<section class="py-20 px-4 bg-white">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
        <div>
            <span class="text-xs font-semibold text-orange-500 uppercase tracking-widest">Our Mission</span>
            <h2 class="text-3xl font-bold text-gray-900 mt-2 mb-5">Food is More Than<br>Just a Meal</h2>
            <p class="text-gray-500 leading-relaxed mb-4">
                At Food Fusion, we believe every recipe is a story — a piece of culture, history, and heart.
                Our platform connects food lovers with recipes from every corner of the world, making global
                cuisine accessible to home cooks everywhere.
            </p>
            <p class="text-gray-500 leading-relaxed mb-8">
                Whether you're a seasoned chef or a curious beginner, we have the tools, community, and
                inspiration to take your cooking to the next level.
            </p>
            <div class="grid grid-cols-3 gap-6 text-center">
                <?php
                $pillars = [
                    ['icon' => '🌿', 'title' => 'Fresh Ingredients', 'desc' => 'Seasonal, locally sourced produce in every recipe.'],
                    ['icon' => '👨‍🍳', 'title' => 'Expert Chefs',    'desc' => 'Curated by world-class culinary professionals.'],
                    ['icon' => '🌍', 'title' => 'Global Flavours',  'desc' => 'Cuisines from Asia, Europe, and the Americas.'],
                ];
                foreach ($pillars as $p): ?>
                <div>
                    <div class="text-4xl mb-2"><?= $p['icon'] ?></div>
                    <p class="font-semibold text-gray-900 text-sm"><?= $p['title'] ?></p>
                    <p class="text-gray-400 text-xs mt-1"><?= $p['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-amber-500 rounded-3xl p-10 text-white text-center shadow-xl shadow-orange-200">
            <div class="text-6xl mb-5">🍽</div>
            <h3 class="text-2xl font-bold mb-3">Ready to Start Cooking?</h3>
            <p class="opacity-90 text-sm leading-relaxed mb-7">
                Join thousands of food lovers already exploring our recipe library.
                Free forever — no credit card required.
            </p>
            <button id="join-us-btn-2"
                    onclick="document.getElementById('join-modal').classList.remove('hidden')"
                    class="bg-white text-orange-500 font-bold px-8 py-3 rounded-full hover:bg-orange-50 transition text-sm shadow">
                Create Free Account
            </button>
            <!-- Social media links -->
            <div class="flex justify-center gap-5 mt-8 opacity-80">
                <a href="#" aria-label="Facebook" class="hover:opacity-100 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987H7.898v-2.89h2.54V9.845c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                </a>
                <a href="#" aria-label="Instagram" class="hover:opacity-100 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <a href="#" aria-label="Twitter/X" class="hover:opacity-100 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="#" aria-label="YouTube" class="hover:opacity-100 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<div id="join-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
     role="dialog" aria-modal="true" aria-labelledby="modal-title">

    <div id="modal-backdrop"
         class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-amber-500 h-2"></div>

        <div class="p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 id="modal-title" class="text-2xl font-bold text-gray-900">Join Food Fusion 🍽</h2>
                    <p class="text-gray-400 text-sm mt-0.5">Free forever. No credit card required.</p>
                </div>
                <button id="modal-close"
                        class="text-gray-400 hover:text-gray-600 transition text-2xl leading-none"
                        aria-label="Close">✕</button>
            </div>

            <form id="join-form" method="POST" action="<?= SITE_URL ?>/auth/register.php" novalidate class="space-y-4">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="join-first">First Name</label>
                        <input id="join-first" type="text" placeholder="Jane"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="join-last">Last Name</label>
                        <input id="join-last" type="text" placeholder="Smith"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
                               required>
                    </div>
                </div>

                <input type="hidden" name="name" id="join-name">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="join-email">Email Address</label>
                    <input id="join-email" type="email" name="email" placeholder="jane@example.com"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
                           autocomplete="email" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="join-password">Password</label>
                    <div class="relative">
                        <input id="join-password" type="password" name="password"
                               placeholder="Min. 8 chars, letters &amp; numbers"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition pr-10"
                               autocomplete="new-password" required>
                        <button type="button" onclick="toggleJoinPwd()" aria-label="Toggle password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition text-xs font-medium">
                            👁
                        </button>
                    </div>
                    <!-- Strength bar -->
                    <div id="join-strength-bar" class="mt-1.5 h-1 rounded-full bg-gray-100 overflow-hidden hidden">
                        <div id="join-strength-fill" class="h-full rounded-full transition-all duration-300" style="width:0%"></div>
                    </div>
                    <p id="join-strength-label" class="text-xs mt-0.5 text-gray-400"></p>
                </div>

                <!-- Hidden confirm-password (same value) so register.php validation passes -->
                <input type="hidden" name="password_confirm" id="join-password-confirm">

                <!-- reCAPTCHA -->
                <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>

                <p id="join-error" class="text-red-500 text-xs hidden"></p>

                <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl text-sm transition">
                    Create My Account
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-4">
                Already have an account?
                <a href="<?= SITE_URL ?>/auth/login.php" class="text-orange-500 hover:underline font-medium">Sign in</a>
            </p>
            <p class="text-center text-xs text-gray-300 mt-2">
                By joining you agree to our
                <a href="<?= SITE_URL ?>/pages/privacy-policy.php" class="underline hover:text-gray-500">Privacy Policy</a>
            </p>
        </div>
    </div>
</div>

<?php if (!is_logged_in()): ?>
<div id="signup-popup"
     class="signup-popup fixed bottom-20 right-5 z-40 bg-white rounded-2xl shadow-2xl border border-orange-100 p-5 max-w-xs"
     style="display:none;">
    <button id="signup-popup-close"
            class="absolute top-2 right-3 text-gray-400 hover:text-gray-600 transition text-lg leading-none"
            aria-label="Close">&times;</button>
    <div class="flex items-start gap-3">
        <span class="text-3xl shrink-0">🍽</span>
        <div>
            <p class="text-sm font-bold text-gray-900">Join Food Fusion!</p>
            <p class="text-xs text-gray-500 mt-1">
                Sign up for free to save your favourite recipes, get personalised recommendations, and more.
            </p>
            <button id="signup-popup-btn"
                    class="mt-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs px-5 py-2 rounded-full transition">
                Sign Up Now
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<div id="cookie-bar"
     class="cookie-bar fixed bottom-0 left-0 right-0 z-40 bg-gray-900 text-white px-5 py-4 shadow-2xl"
     style="display:none;">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <span class="text-2xl shrink-0">🍪</span>
            <div>
                <p class="text-sm font-semibold">We use cookies</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    We use cookies to enhance your browsing experience, serve personalised content,
                    and analyse traffic. Read our
                    <a href="<?= SITE_URL ?>/pages/privacy-policy.php"
                       class="underline text-orange-400 hover:text-orange-300">Privacy Policy</a>.
                </p>
            </div>
        </div>
        <div class="flex gap-2 shrink-0">
            <button onclick="acceptCookies(false)"
                    class="text-xs border border-gray-600 text-gray-300 hover:bg-gray-800 px-4 py-2 rounded-full transition">
                Decline
            </button>
            <button onclick="acceptCookies(true)"
                    class="text-xs bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2 rounded-full transition">
                Accept All
            </button>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
const modal        = document.getElementById('join-modal');
const backdrop     = document.getElementById('modal-backdrop');
const closeBtn     = document.getElementById('modal-close');
const joinBtn      = document.getElementById('join-us-btn');
const joinBtn2     = document.getElementById('join-us-btn-2');

function openModal()  { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
function closeModal() { modal.classList.add('hidden');    document.body.style.overflow = ''; }

if (joinBtn)  joinBtn.addEventListener('click', openModal);
if (joinBtn2) joinBtn2.addEventListener('click', openModal);
closeBtn.addEventListener('click', closeModal);
backdrop.addEventListener('click', closeModal);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

document.getElementById('join-form').addEventListener('submit', function (e) {
    const first = document.getElementById('join-first').value.trim();
    const last  = document.getElementById('join-last').value.trim();
    const pwd   = document.getElementById('join-password').value;
    const err   = document.getElementById('join-error');

    if (!first || !last) {
        e.preventDefault();
        err.textContent = 'Please enter your first and last name.';
        err.classList.remove('hidden');
        return;
    }
    document.getElementById('join-name').value            = first + ' ' + last;
    document.getElementById('join-password-confirm').value = pwd;
    err.classList.add('hidden');
});

function toggleJoinPwd() {
    const inp = document.getElementById('join-password');
    inp.type  = inp.type === 'password' ? 'text' : 'password';
}

document.getElementById('join-password').addEventListener('input', function () {
    const val   = this.value;
    const bar   = document.getElementById('join-strength-bar');
    const fill  = document.getElementById('join-strength-fill');
    const label = document.getElementById('join-strength-label');

    if (!val) { bar.classList.add('hidden'); label.textContent = ''; return; }
    bar.classList.remove('hidden');

    let score = 0;
    if (val.length >= 8)           score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val))  score++;
    if (val.length >= 12)          score++;

    const levels = [
        { pct: '20%',  color: '#ef4444', text: 'Very Weak' },
        { pct: '40%',  color: '#f97316', text: 'Weak' },
        { pct: '60%',  color: '#eab308', text: 'Fair' },
        { pct: '80%',  color: '#3b82f6', text: 'Strong' },
        { pct: '100%', color: '#22c55e', text: 'Very Strong' },
    ];
    const lvl = levels[Math.max(Math.min(score, levels.length) - 1, 0)];
    fill.style.width            = lvl.pct;
    fill.style.backgroundColor  = lvl.color;
    label.textContent           = lvl.text;
    label.style.color           = lvl.color;
});

(function () {
    const track    = document.getElementById('carousel-track');
    const viewport = document.getElementById('carousel-viewport');
    const dotsEl   = document.getElementById('carousel-dots');
    const slides   = track.querySelectorAll('.carousel-slide');
    const slideW   = 288 + 20;
    let   current  = 0;

    const visible  = () => Math.floor(viewport.offsetWidth / slideW) || 1;
    const maxIdx   = () => Math.max(0, slides.length - visible());

    function buildDots() {
        dotsEl.innerHTML = '';
        const count = maxIdx() + 1;
        for (let i = 0; i < count; i++) {
            const d = document.createElement('button');
            d.className = 'w-2 h-2 rounded-full transition-all ' + (i === current ? 'bg-orange-500 w-4' : 'bg-gray-300');
            d.addEventListener('click', () => goTo(i));
            dotsEl.appendChild(d);
        }
    }

    function goTo(idx) {
        current = Math.max(0, Math.min(idx, maxIdx()));
        track.style.transform = `translateX(-${current * slideW}px)`;
        buildDots();
    }

    document.getElementById('prev-btn').addEventListener('click', () => goTo(current - 1));
    document.getElementById('next-btn').addEventListener('click', () => goTo(current + 1));

    setInterval(() => goTo(current >= maxIdx() ? 0 : current + 1), 4000);

    buildDots();
    window.addEventListener('resize', () => goTo(Math.min(current, maxIdx())));
})();

(function () {
    if (!localStorage.getItem('ff_cookie_consent')) {
        const bar = document.getElementById('cookie-bar');
        bar.style.display = 'block';
    }
})();

function acceptCookies(accepted) {
    localStorage.setItem('ff_cookie_consent', accepted ? 'accepted' : 'declined');
    const bar = document.getElementById('cookie-bar');
    bar.style.transform = 'translateY(100%)';
    bar.style.opacity   = '0';
    setTimeout(() => bar.style.display = 'none', 400);
}

(function () {
    const popup = document.getElementById('signup-popup');
    if (!popup) return;

    if (localStorage.getItem('ff_signup_dismissed')) return;

    popup.style.display = 'block';

    function dismissSignup() {
        localStorage.setItem('ff_signup_dismissed', '1');
        popup.style.transform = 'translateY(20px)';
        popup.style.opacity   = '0';
        setTimeout(() => popup.style.display = 'none', 400);
    }

    document.getElementById('signup-popup-close').addEventListener('click', dismissSignup);

    document.getElementById('signup-popup-btn').addEventListener('click', function () {
        dismissSignup();
        openModal();
    });
})();
</script>

</body>
</html>
