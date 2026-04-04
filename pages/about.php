<?php
$page_title = 'About Us – Food Fusion';
$meta_description = 'Learn about the story behind Food Fusion.';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="max-w-5xl mx-auto px-4 py-16">
    <!-- Header -->
    <div class="text-center mb-16">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Our Story</h1>
        <p class="text-gray-500 max-w-2xl mx-auto text-lg">
            Born from a love of travel and food, Food Fusion is where the world's best flavors meet on one plate.
        </p>
    </div>

    <!-- Story -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
        <div class="bg-gradient-to-br from-orange-100 to-amber-200 rounded-3xl h-72 flex items-center justify-center text-8xl">
            🍜
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">How We Started</h2>
            <p class="text-gray-600 leading-relaxed mb-4">
                Founded in 2018, Food Fusion started as a small pop-up kitchen in the heart of the city. Our founder, Chef Maya, spent years traveling across Asia, Europe, and Latin America collecting recipes, techniques, and inspiration.
            </p>
            <p class="text-gray-600 leading-relaxed">
                Today, we serve hundreds of guests daily, all united by the joy of discovery through food. Every dish on our menu tells a story of a culture, a place, and a moment in time.
            </p>
        </div>
    </div>

    <!-- Values -->
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-3">Our Values</h2>
        <p class="text-gray-500">The principles that guide every dish we make.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-20">
        <?php
        $values = [
            ['icon' => '❤️', 'title' => 'Passion', 'desc' => 'We cook with heart in every dish.'],
            ['icon' => '🌿', 'title' => 'Freshness', 'desc' => 'Only the finest seasonal ingredients.'],
            ['icon' => '🤝', 'title' => 'Community', 'desc' => 'Supporting local farmers and producers.'],
            ['icon' => '🌎', 'title' => 'Diversity', 'desc' => 'Celebrating global culinary traditions.'],
        ];
        foreach ($values as $v): ?>
        <div class="bg-orange-50 rounded-2xl p-6 text-center">
            <div class="text-4xl mb-3"><?= $v['icon'] ?></div>
            <h3 class="font-bold text-gray-900 mb-1"><?= $v['title'] ?></h3>
            <p class="text-gray-500 text-sm"><?= $v['desc'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Team -->
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-3">Meet the Team</h2>
        <p class="text-gray-500">The talented people behind your favorite dishes.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
        <?php
        $team = [
            ['name' => 'Chef Maya', 'role' => 'Founder & Head Chef', 'emoji' => '👩‍🍳'],
            ['name' => 'Carlos Reyes', 'role' => 'Sous Chef', 'emoji' => '👨‍🍳'],
            ['name' => 'Rin Tanaka', 'role' => 'Pastry Chef', 'emoji' => '🧑‍🍳'],
        ];
        foreach ($team as $member): ?>
        <div class="text-center">
            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-orange-100 to-amber-200 flex items-center justify-center text-5xl mx-auto mb-4">
                <?= $member['emoji'] ?>
            </div>
            <h3 class="font-bold text-gray-900"><?= $member['name'] ?></h3>
            <p class="text-orange-500 text-sm"><?= $member['role'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
