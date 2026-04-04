<?php
$page_title = 'About Us – Food Fusion';
$meta_description = 'Learn about Food Fusion — a culinary platform dedicated to promoting home cooking and culinary creativity.';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="max-w-5xl mx-auto px-4 py-16">
    <!-- Header -->
    <div class="text-center mb-16">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">About Food Fusion</h1>
        <p class="text-gray-500 max-w-2xl mx-auto text-lg">
            A culinary platform dedicated to promoting home cooking and culinary creativity among food enthusiasts worldwide.
        </p>
    </div>

    <!-- Story -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
        <div class="bg-gradient-to-br from-orange-100 to-amber-200 rounded-3xl h-72 flex items-center justify-center text-8xl">
            🍜
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h2>
            <p class="text-gray-600 leading-relaxed mb-4">
                Food Fusion was created to be a central hub for sharing recipes, culinary tips, and fostering a vibrant food community. We believe that great cooking starts at home, and everyone deserves access to inspiration from kitchens around the world.
            </p>
            <p class="text-gray-600 leading-relaxed">
                Whether you're a beginner learning the basics or an experienced home cook exploring new cuisines, Food Fusion brings together a growing library of recipes, community-submitted cookbooks, and educational resources to help you on your culinary journey.
            </p>
        </div>
    </div>

    <!-- What We Offer -->
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-3">What We Offer</h2>
        <p class="text-gray-500">Everything you need to cook, learn, and share.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-20">
        <?php
        $features = [
            ['icon' => '📖', 'title' => 'Recipe Collection', 'desc' => 'Browse a curated collection of recipes across categories and cooking methods.'],
            ['icon' => '👨‍🍳', 'title' => 'Community Cookbook', 'desc' => 'Submit your own step-by-step cookbooks and discover creations from fellow cooks.'],
            ['icon' => '🎓', 'title' => 'Educational Resources', 'desc' => 'Learn cooking techniques, kitchen safety, and nutrition fundamentals.'],
            ['icon' => '🍳', 'title' => 'Culinary Resources', 'desc' => 'Explore tips, tools, and guides to elevate your home cooking skills.'],
        ];
        foreach ($features as $f): ?>
        <div class="bg-orange-50 rounded-2xl p-6 text-center">
            <div class="text-4xl mb-3"><?= $f['icon'] ?></div>
            <h3 class="font-bold text-gray-900 mb-1"><?= $f['title'] ?></h3>
            <p class="text-gray-500 text-sm"><?= $f['desc'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Values -->
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-3">Our Values</h2>
        <p class="text-gray-500">The principles that drive our platform.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-20">
        <?php
        $values = [
            ['icon' => '❤️', 'title' => 'Passion', 'desc' => 'We celebrate the love of home cooking in every recipe shared.'],
            ['icon' => '🌎', 'title' => 'Diversity', 'desc' => 'Embracing culinary traditions from every corner of the world.'],
            ['icon' => '🤝', 'title' => 'Community', 'desc' => 'Connecting home cooks to share, learn, and inspire each other.'],
            ['icon' => '📚', 'title' => 'Education', 'desc' => 'Making culinary knowledge accessible to cooks of all skill levels.'],
        ];
        foreach ($values as $v): ?>
        <div class="bg-orange-50 rounded-2xl p-6 text-center">
            <div class="text-4xl mb-3"><?= $v['icon'] ?></div>
            <h3 class="font-bold text-gray-900 mb-1"><?= $v['title'] ?></h3>
            <p class="text-gray-500 text-sm"><?= $v['desc'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- CTA -->
    <div class="text-center bg-gradient-to-br from-orange-50 to-amber-50 rounded-3xl p-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Join Our Community</h2>
        <p class="text-gray-500 max-w-lg mx-auto mb-6 text-sm">
            Create an account to submit your own cookbooks, like your favorite recipes, and become part of a growing community of food enthusiasts.
        </p>
        <div class="flex items-center justify-center gap-3">
            <a href="<?= SITE_URL ?>/pages/community-cookbook.php"
               class="bg-orange-500 text-white px-6 py-2.5 rounded-full text-sm font-semibold hover:bg-orange-600 transition">
                Browse Cookbooks
            </a>
            <a href="<?= SITE_URL ?>/auth/register.php"
               class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-full text-sm font-medium hover:text-orange-500 transition">
                Create Account
            </a>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
