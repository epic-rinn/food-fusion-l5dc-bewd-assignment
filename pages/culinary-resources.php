<?php
$page_title = 'Culinary Resources – Food Fusion';
$meta_description = 'Downloadable recipe cards, cooking tutorials, and instructional videos on various cooking techniques and kitchen hacks.';
require_once dirname(__DIR__) . '/includes/header.php';

$pdf_url = 'https://foodhero.org/sites/foodhero-prod/files/health-tools/cookbook.pdf';

$recipe_cards = [
    ['title' => 'Thai Green Curry', 'desc' => 'A fragrant coconut-based curry loaded with vegetables, Thai basil, and aromatic spices. Perfect for weeknight dinners.', 'emoji' => '🍛', 'category' => 'Asian', 'difficulty' => 'Intermediate'],
    ['title' => 'Classic Margherita Pizza', 'desc' => 'Simple yet satisfying — San Marzano tomato sauce, fresh mozzarella, and basil on a crispy homemade dough.', 'emoji' => '🍕', 'category' => 'Italian', 'difficulty' => 'Beginner'],
    ['title' => 'Beef Bourguignon', 'desc' => 'A rich French stew with tender beef chunks braised in red wine alongside mushrooms and pearl onions.', 'emoji' => '🥘', 'category' => 'French', 'difficulty' => 'Advanced'],
    ['title' => 'Sushi Rolls', 'desc' => 'Learn to prepare maki rolls at home with properly seasoned rice, nori, fresh fish, and crisp vegetables.', 'emoji' => '🍣', 'category' => 'Japanese', 'difficulty' => 'Intermediate'],
    ['title' => 'Mexican Street Tacos', 'desc' => 'Warm corn tortillas topped with seasoned carne asada, fresh cilantro, diced onion, and zesty lime salsa.', 'emoji' => '🌮', 'category' => 'Mexican', 'difficulty' => 'Beginner'],
    ['title' => 'Moroccan Lamb Tagine', 'desc' => 'Slow-cooked spiced lamb with dried apricots, toasted almonds, and fluffy couscous on the side.', 'emoji' => '🍲', 'category' => 'Moroccan', 'difficulty' => 'Intermediate'],
];

$tutorials = [
    ['title' => 'Knife Skills for Beginners', 'desc' => 'Master fundamental cuts like julienne, brunoise, and chiffonade. Build speed and confidence with a chef\'s knife.', 'emoji' => '🔪', 'duration' => '15 min', 'level' => 'Beginner'],
    ['title' => 'Fresh Pasta from Scratch', 'desc' => 'Mix, knead, rest, and roll — learn to make silky egg pasta dough and shape it into fettuccine or ravioli.', 'emoji' => '🍝', 'duration' => '22 min', 'level' => 'Intermediate'],
    ['title' => 'The Five Mother Sauces', 'desc' => 'Understand béchamel, velouté, espagnole, hollandaise, and tomato — the building blocks of French cuisine.', 'emoji' => '🥄', 'duration' => '28 min', 'level' => 'Advanced'],
    ['title' => 'Artisan Bread at Home', 'desc' => 'From mixing and proofing to scoring and baking — create bakery-quality crusty loaves in your own oven.', 'emoji' => '🍞', 'duration' => '35 min', 'level' => 'Intermediate'],
];

$videos = [
    ['title' => 'Essential Cooking Techniques', 'desc' => 'A comprehensive overview of core cooking methods including searing, braising, roasting, and blanching.', 'youtube_id' => 'DyKhvsAPO9I'],
    ['title' => 'Wok Hei: The Breath of a Wok', 'desc' => 'Discover how to achieve that signature smoky aroma in stir-fries using high heat and proper wok handling.', 'youtube_id' => 'ZfmklyN_mlw'],
    ['title' => 'Tempering Chocolate', 'desc' => 'Get a glossy finish and satisfying snap on your chocolate work every time with proper tempering technique.', 'youtube_id' => '98r0Z3jeYcE'],
];

$hacks = [
    ['icon' => '🧊', 'title' => 'Revive Wilted Herbs', 'desc' => 'Submerge droopy herbs in ice water for 10 minutes to make them crisp again.'],
    ['icon' => '🧄', 'title' => 'Quick Garlic Peeling', 'desc' => 'Microwave a whole head for 15 seconds — the cloves slide right out of their skins.'],
    ['icon' => '🥑', 'title' => 'Ripen Avocados Overnight', 'desc' => 'Seal an unripe avocado in a paper bag with a banana to speed up ripening.'],
    ['icon' => '🧈', 'title' => 'Room-Temp Butter Fast', 'desc' => 'Dice cold butter into small cubes and it reaches room temperature in under 10 minutes.'],
    ['icon' => '🍋', 'title' => 'Extract More Citrus Juice', 'desc' => 'Firmly roll lemons on the countertop before cutting to break down the membranes inside.'],
    ['icon' => '🫚', 'title' => 'Freeze Your Ginger', 'desc' => 'Frozen ginger root is easier to grate finely and keeps fresh for months in the freezer.'],
    ['icon' => '🥚', 'title' => 'Foolproof Boiled Eggs', 'desc' => 'Place eggs in cold water, bring to a boil, cover, remove from heat, and wait 10 minutes.'],
    ['icon' => '🧅', 'title' => 'Tearless Onion Chopping', 'desc' => 'Pop onions in the freezer for 15 minutes before slicing to reduce the tear-inducing compounds.'],
];
?>

<!-- Hero Section -->
<div class="bg-gradient-to-br from-orange-50 via-amber-50 to-white py-16 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-72 h-72 bg-orange-100 rounded-full -translate-y-1/2 translate-x-1/3 opacity-50"></div>
    <div class="absolute bottom-0 left-0 w-56 h-56 bg-amber-100 rounded-full translate-y-1/3 -translate-x-1/4 opacity-50"></div>
    <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
        <span class="inline-block bg-orange-100 text-orange-600 text-xs font-semibold px-3 py-1 rounded-full mb-4">CULINARY RESOURCES</span>
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">Your Kitchen Companion</h1>
        <p class="text-gray-500 max-w-2xl mx-auto text-lg">
            Browse our collection of downloadable recipe cards, step-by-step cooking tutorials, and hand-picked instructional videos to level up your home cooking skills.
        </p>
    </div>
</div>

<!-- Recipe Cards Section -->
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-3">Downloadable Recipe Cards</h2>
        <p class="text-gray-500 max-w-xl mx-auto">Grab a printable recipe card and take it straight to the kitchen. Each one includes an ingredient list, clear instructions, and helpful chef tips.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($recipe_cards as $card): ?>
        <div class="bg-white border border-gray-100 rounded-2xl p-6 hover:shadow-lg transition group">
            <div class="w-14 h-14 bg-orange-50 rounded-xl flex items-center justify-center text-3xl mb-4 group-hover:scale-110 transition"><?= $card['emoji'] ?></div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-medium bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full"><?= $card['category'] ?></span>
                <span class="text-xs font-medium bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full"><?= $card['difficulty'] ?></span>
            </div>
            <h3 class="font-bold text-gray-900 text-lg mb-1"><?= $card['title'] ?></h3>
            <p class="text-gray-500 text-sm mb-4"><?= $card['desc'] ?></p>
            <a href="<?= $pdf_url ?>" target="_blank"
               class="w-full bg-orange-50 text-orange-600 font-medium text-sm py-2.5 rounded-xl hover:bg-orange-100 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V3"/></svg>
                Download PDF
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Cooking Tutorials Section -->
<div class="bg-gray-50 py-16">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Cooking Tutorials</h2>
            <p class="text-gray-500 max-w-xl mx-auto">Written guides that walk you through essential culinary techniques, from absolute beginner to advanced level.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($tutorials as $tut): ?>
            <div class="bg-white rounded-2xl p-6 flex gap-5 items-start hover:shadow-lg transition">
                <div class="w-16 h-16 bg-amber-50 rounded-xl flex items-center justify-center text-3xl shrink-0"><?= $tut['emoji'] ?></div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-medium bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full"><?= $tut['level'] ?></span>
                        <span class="text-xs text-gray-400"><?= $tut['duration'] ?> read</span>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1"><?= $tut['title'] ?></h3>
                    <p class="text-gray-500 text-sm mb-3"><?= $tut['desc'] ?></p>
                    <a href="<?= $pdf_url ?>" target="_blank"
                       class="text-orange-500 text-sm font-medium hover:text-orange-600 transition inline-flex items-center gap-1">
                        Read Tutorial
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Instructional Videos Section -->
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-3">Instructional Videos</h2>
        <p class="text-gray-500 max-w-xl mx-auto">Watch and learn at your own pace. Each video demonstrates a specific cooking technique with clear, visual guidance.</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <?php foreach ($videos as $vid): ?>
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg transition">
            <div class="aspect-video">
                <iframe
                    src="https://www.youtube.com/embed/<?= $vid['youtube_id'] ?>"
                    title="<?= htmlspecialchars($vid['title']) ?>"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="w-full h-full">
                </iframe>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-gray-900 mb-1"><?= $vid['title'] ?></h3>
                <p class="text-gray-500 text-sm"><?= $vid['desc'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Kitchen Hacks Section -->
<div class="bg-gradient-to-br from-amber-50 to-orange-50 py-16">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Kitchen Hacks & Tips</h2>
            <p class="text-gray-500 max-w-xl mx-auto">Simple tricks that save time, reduce waste, and make everyday cooking a little bit easier.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
            <?php foreach ($hacks as $hack): ?>
            <div class="bg-white rounded-2xl p-5 text-center hover:shadow-md transition">
                <div class="text-3xl mb-3"><?= $hack['icon'] ?></div>
                <h3 class="font-bold text-gray-900 text-sm mb-1"><?= $hack['title'] ?></h3>
                <p class="text-gray-500 text-xs"><?= $hack['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Newsletter CTA -->
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="bg-orange-500 rounded-3xl p-10 md:p-14 text-center text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-orange-400 rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-orange-600 rounded-full translate-y-1/2 -translate-x-1/4"></div>
        <div class="relative z-10">
            <!-- Subscribe Form -->
            <div id="culinary-form">
                <h2 class="text-2xl md:text-3xl font-bold mb-3">Get Fresh Recipes Every Week</h2>
                <p class="text-orange-100 mb-6 max-w-md mx-auto">Join our mailing list and receive a free starter pack of 10 chef-curated recipe cards straight to your inbox.</p>
                <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" id="culinary-email" placeholder="Enter your email" required
                        class="flex-1 px-5 py-3 rounded-xl text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <button onclick="subscribeCulinary()" class="bg-gray-900 text-white font-semibold px-6 py-3 rounded-xl hover:bg-gray-800 transition text-sm whitespace-nowrap">
                        Subscribe
                    </button>
                </div>
                <p id="culinary-error" class="text-orange-200 text-xs mt-3 hidden">Please enter a valid email address.</p>
            </div>
            <!-- Success Message -->
            <div id="culinary-success" class="hidden">
                <div class="text-5xl mb-4">🎉</div>
                <h2 class="text-2xl md:text-3xl font-bold mb-3">You're All Set!</h2>
                <p class="text-orange-100 max-w-md mx-auto">Thanks for subscribing! Check your inbox for your free recipe card starter pack.</p>
            </div>
        </div>
    </div>
</div>

<script>
function subscribeCulinary() {
    const email = document.getElementById('culinary-email').value.trim();
    const error = document.getElementById('culinary-error');
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        error.classList.remove('hidden');
        return;
    }
    error.classList.add('hidden');
    document.getElementById('culinary-form').classList.add('hidden');
    document.getElementById('culinary-success').classList.remove('hidden');
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
