<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/auth/session.php';

// Validate ID
$id = (int)($_GET['id'] ?? 0);
if ($id < 1) {
    header('Location: ' . SITE_URL . '/pages/menu.php');
    exit;
}

$pdo = get_db();

// Fetch recipe + author name (including cooking_method)
$stmt = $pdo->prepare(
    'SELECT r.*, u.name AS author_name
     FROM   recipes r
     LEFT   JOIN users u ON u.id = r.created_by
     WHERE  r.id = ?
     LIMIT  1'
);
$stmt->execute([$id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header('Location: ' . SITE_URL . '/pages/menu.php');
    exit;
}

// Related recipes — same category, exclude current, max 3
$related = [];
if ($recipe['category']) {
    $stmt2 = $pdo->prepare(
        'SELECT id, title, photo, prep_time, cook_time, category
         FROM   recipes
         WHERE  category = ? AND id != ?
         ORDER  BY created_at DESC
         LIMIT  3'
    );
    $stmt2->execute([$recipe['category'], $id]);
    $related = $stmt2->fetchAll();
}

// Meta
$page_title       = htmlspecialchars($recipe['title']) . ' – Food Fusion';
$meta_description = $recipe['description']
    ? substr(strip_tags($recipe['description']), 0, 160)
    : 'Explore this recipe on Food Fusion.';

require_once dirname(__DIR__) . '/includes/header.php';

// ── helpers ────────────────────────────────────────────────────────────────
function fmt_mins(int $mins): string {
    if ($mins <= 0) return '—';
    if ($mins < 60)  return $mins . ' min';
    $h = intdiv($mins, 60);
    $m = $mins % 60;
    return $m > 0 ? "{$h}h {$m}m" : "{$h}h";
}

$prep_time  = (int)($recipe['prep_time']  ?? 0);
$cook_time  = (int)($recipe['cook_time']  ?? 0);
$total_time = $prep_time + $cook_time;
$servings   = (int)($recipe['servings']   ?? 0);

$photo_src = $recipe['photo']
    ? SITE_URL . '/uploads/recipes/' . htmlspecialchars($recipe['photo'])
    : null;
?>

<!-- ── Breadcrumb ─────────────────────────────────────────────────────────── -->
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-5xl mx-auto px-4 py-3 text-sm text-gray-400 flex items-center gap-2 flex-wrap">
        <a href="<?= SITE_URL ?>" class="hover:text-orange-500 transition">Home</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="<?= SITE_URL ?>/pages/menu.php" class="hover:text-orange-500 transition">Recipe Collection</a>
        <?php if ($recipe['category']): ?>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="<?= SITE_URL ?>/pages/menu.php?category=<?= urlencode($recipe['category']) ?>"
           class="hover:text-orange-500 transition">
            <?= htmlspecialchars($recipe['category']) ?>
        </a>
        <?php endif; ?>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 font-medium truncate max-w-[200px]">
            <?= htmlspecialchars($recipe['title']) ?>
        </span>
    </div>
</div>

<!-- ── Main content ───────────────────────────────────────────────────────── -->
<div class="max-w-5xl mx-auto px-4 py-12">

    <!-- Hero grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">

        <!-- Photo -->
        <div class="rounded-3xl overflow-hidden shadow-lg bg-gray-100 aspect-[4/3]">
            <?php if ($photo_src): ?>
            <img src="<?= $photo_src ?>"
                 alt="<?= htmlspecialchars($recipe['title']) ?>"
                 class="w-full h-full object-cover"
                 onerror="this.onerror=null;this.src='https://placehold.co/800x600/f3f4f6/9ca3af?text=No+Image'">
            <?php else: ?>
            <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 gap-3">
                <span class="text-7xl">🍽</span>
                <span class="text-sm">No photo available</span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Info panel -->
        <div class="flex flex-col gap-5">

            <!-- Category badge -->
            <?php if ($recipe['category']): ?>
            <a href="<?= SITE_URL ?>/pages/menu.php?category=<?= urlencode($recipe['category']) ?>"
               class="inline-flex self-start items-center gap-1.5 bg-orange-100 text-orange-600 text-xs font-bold
                      px-3 py-1 rounded-full uppercase tracking-wide hover:bg-orange-200 transition">
                <?= htmlspecialchars($recipe['category']) ?>
            </a>
            <?php endif; ?>

            <!-- Title -->
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">
                <?= htmlspecialchars($recipe['title']) ?>
            </h1>

            <!-- Description -->
            <?php if ($recipe['description']): ?>
            <p class="text-gray-500 leading-relaxed text-[15px]">
                <?= nl2br(htmlspecialchars($recipe['description'])) ?>
            </p>
            <?php endif; ?>

            <!-- Stats grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 text-center">
                    <p class="text-[10px] font-semibold text-orange-400 uppercase tracking-widest mb-1">Prep</p>
                    <p class="font-bold text-gray-800 text-sm"><?= fmt_mins($prep_time) ?></p>
                </div>
                <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 text-center">
                    <p class="text-[10px] font-semibold text-orange-400 uppercase tracking-widest mb-1">Cook</p>
                    <p class="font-bold text-gray-800 text-sm"><?= fmt_mins($cook_time) ?></p>
                </div>
                <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 text-center">
                    <p class="text-[10px] font-semibold text-orange-400 uppercase tracking-widest mb-1">Total</p>
                    <p class="font-bold text-gray-800 text-sm"><?= fmt_mins($total_time) ?></p>
                </div>
                <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 text-center">
                    <p class="text-[10px] font-semibold text-orange-400 uppercase tracking-widest mb-1">Serves</p>
                    <p class="font-bold text-gray-800 text-sm"><?= $servings > 0 ? $servings : '—' ?></p>
                </div>
            </div>

            <!-- Cooking method badge -->
            <?php if (!empty($recipe['cooking_method'])): ?>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 font-medium uppercase tracking-widest">Cooking Method</span>
                <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                    <?php
                    $method_icons = [
                        'Bake'      => '🔥', 'Pan-fry'   => '🍳', 'Simmer'    => '🫕',
                        'Grill'     => '🥩', 'Deep-fry'  => '🛢',  'Boil'      => '💧',
                        'Blend'     => '🥤', 'No-Cook'   => '🥗',  'Toast'     => '🍞',
                        'Pan-sear'  => '🍳', 'Roast'     => '🔥',  'Steam'     => '♨️',
                    ];
                    $m = htmlspecialchars($recipe['cooking_method']);
                    echo ($method_icons[$recipe['cooking_method']] ?? '👨‍🍳') . ' ' . $m;
                    ?>
                </span>
            </div>
            <?php endif; ?>

            <!-- Divider -->
            <div class="border-t border-gray-100"></div>

            <!-- Author & date row -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center
                            text-white font-bold text-sm shrink-0 select-none">
                    <?= strtoupper(mb_substr($recipe['author_name'] ?? 'U', 0, 1)) ?>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">
                        <?= htmlspecialchars($recipe['author_name'] ?? 'Unknown') ?>
                    </p>
                    <p class="text-xs text-gray-400">
                        Added <?= date('F j, Y', strtotime($recipe['created_at'])) ?>
                    </p>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-wrap gap-3 pt-1">
                <a href="<?= SITE_URL ?>/pages/menu.php"
                   class="inline-flex items-center gap-2 border border-gray-200 text-gray-600
                          hover:border-orange-400 hover:text-orange-500 text-sm font-medium
                          px-5 py-2.5 rounded-full transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Recipe Collection
                </a>
                <?php if (!is_logged_in()): ?>
                <a href="<?= SITE_URL ?>/auth/register.php"
                   class="inline-flex items-center gap-2 bg-orange-500 text-white text-sm font-medium
                          px-5 py-2.5 rounded-full hover:bg-orange-600 transition">
                    ❤ Save Recipe
                </a>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- ── Related Recipes ──────────────────────────────────────────────── -->
    <?php if (!empty($related)): ?>
    <div class="mt-16 border-t border-gray-100 pt-12">
        <div class="flex items-center gap-3 mb-7">
            <h2 class="text-xl font-bold text-gray-900">
                More <?= htmlspecialchars($recipe['category']) ?> Recipes
            </h2>
            <div class="flex-1 h-px bg-gray-100"></div>
            <a href="<?= SITE_URL ?>/pages/menu.php?category=<?= urlencode($recipe['category']) ?>"
               class="text-sm text-orange-500 hover:underline font-medium transition">
                View all →
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <?php foreach ($related as $rel):
                $rel_photo = $rel['photo']
                    ? SITE_URL . '/uploads/recipes/' . htmlspecialchars($rel['photo'])
                    : null;
                $rel_total = ((int)($rel['prep_time'] ?? 0)) + ((int)($rel['cook_time'] ?? 0));
            ?>
            <a href="<?= SITE_URL ?>/pages/recipe-detail.php?id=<?= $rel['id'] ?>"
               class="group bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-200">
                <div class="aspect-video overflow-hidden bg-gray-100">
                    <?php if ($rel_photo): ?>
                    <img src="<?= $rel_photo ?>"
                         alt="<?= htmlspecialchars($rel['title']) ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                         onerror="this.onerror=null;this.src='https://placehold.co/400x300/f3f4f6/9ca3af?text=No+Image'">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-4xl">🍽</div>
                    <?php endif; ?>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 group-hover:text-orange-500 transition leading-snug">
                        <?= htmlspecialchars($rel['title']) ?>
                    </h3>
                    <?php if ($rel_total > 0): ?>
                    <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <?= fmt_mins($rel_total) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
