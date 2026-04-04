<?php
$page_title       = 'Recipe Collection – ' . 'Food Fusion';
$meta_description = 'Browse our full collection of fusion recipes by category.';
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/includes/header.php';

$pdo = get_db();

// Search & filter
$search          = trim($_GET['q'] ?? '');
$category_filter = trim($_GET['category'] ?? '');

// All distinct categories for the filter dropdown
$all_categories = $pdo->query(
    'SELECT DISTINCT category FROM recipes WHERE category IS NOT NULL ORDER BY category'
)->fetchAll(PDO::FETCH_COLUMN);

// Build dynamic query
$where  = [];
$params = [];
if ($search !== '') {
    $where[]  = '(r.title LIKE ? OR r.description LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($category_filter !== '') {
    $where[]  = 'r.category = ?';
    $params[] = $category_filter;
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare(
    "SELECT r.id, r.title, r.description, r.category, r.photo,
            r.prep_time, r.cook_time, r.servings
     FROM   recipes r
     {$where_sql}
     ORDER  BY r.category ASC, r.created_at DESC"
);
$stmt->execute($params);
$recipes = $stmt->fetchAll();

// Group by category (only used when no filter active)
$grouped = [];
foreach ($recipes as $r) {
    $grouped[$r['category'] ?? 'Other'][] = $r;
}

function fmt_time_m(int $mins): string {
    if ($mins <= 0) return '';
    if ($mins < 60) return $mins . ' min';
    $h = intdiv($mins, 60);
    $m = $mins % 60;
    return $m > 0 ? "{$h}h {$m}m" : "{$h}h";
}

function recipe_card(array $r): void {
    $total = ((int)($r['prep_time'] ?? 0)) + ((int)($r['cook_time'] ?? 0));
    $photo = $r['photo']
        ? SITE_URL . '/uploads/recipes/' . htmlspecialchars($r['photo'])
        : null;
    ?>
    <a href="<?= SITE_URL ?>/pages/recipe-detail.php?id=<?= (int)$r['id'] ?>"
       class="group bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-200 flex flex-col">
        <!-- Photo -->
        <div class="aspect-video overflow-hidden bg-gray-100">
            <?php if ($photo): ?>
            <img src="<?= $photo ?>"
                 alt="<?= htmlspecialchars($r['title']) ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                 onerror="this.src='https://placehold.co/400x250/f3f4f6/9ca3af?text=No+Image'">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-gray-300 text-5xl">🍽</div>
            <?php endif; ?>
        </div>
        <!-- Body -->
        <div class="p-5 flex flex-col flex-1">
            <?php if ($r['category']): ?>
            <span class="text-xs font-semibold text-orange-500 uppercase tracking-wide mb-1">
                <?= htmlspecialchars($r['category']) ?>
            </span>
            <?php endif; ?>
            <h3 class="font-bold text-gray-900 group-hover:text-orange-500 transition leading-snug">
                <?= htmlspecialchars($r['title']) ?>
            </h3>
            <?php if ($r['description']): ?>
            <p class="text-gray-500 text-sm mt-1.5 line-clamp-2 flex-1">
                <?= htmlspecialchars($r['description']) ?>
            </p>
            <?php endif; ?>
            <!-- Meta chips -->
            <div class="flex flex-wrap items-center gap-3 mt-3 text-xs text-gray-400">
                <?php if ($r['prep_time']): ?>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Prep: <?= fmt_time_m((int)$r['prep_time']) ?>
                </span>
                <?php endif; ?>
                <?php if ($r['cook_time']): ?>
                <span class="flex items-center gap-1">
                    🔥 Cook: <?= fmt_time_m((int)$r['cook_time']) ?>
                </span>
                <?php endif; ?>
                <?php if ($r['servings']): ?>
                <span>👥 <?= (int)$r['servings'] ?></span>
                <?php endif; ?>
                <?php if ($total > 0): ?>
                <span class="ml-auto bg-orange-50 text-orange-500 font-semibold px-2 py-0.5 rounded-full">
                    <?= fmt_time_m($total) ?> total
                </span>
                <?php endif; ?>
            </div>
        </div>
    </a>
    <?php
}
?>

<!-- Page wrapper -->
<div class="max-w-6xl mx-auto px-4 py-12">

    <!-- Page header -->
    <div class="text-center mb-10">
        <span class="text-xs font-semibold text-orange-500 uppercase tracking-widest">Browse</span>
        <h1 class="text-4xl font-extrabold text-gray-900 mt-2 mb-3">Recipe Collection</h1>
        <p class="text-gray-500 max-w-xl mx-auto text-sm">
            Explore our globally inspired recipes, crafted fresh with passion.
        </p>
    </div>

    <!-- Search / filter bar -->
    <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-10 max-w-2xl mx-auto">
        <input type="text"
               name="q"
               value="<?= htmlspecialchars($search) ?>"
               placeholder="Search recipes…"
               class="flex-1 border border-gray-200 rounded-full px-5 py-2.5 text-sm
                      focus:outline-none focus:ring-2 focus:ring-orange-300">
        <select name="category"
                class="border border-gray-200 rounded-full px-5 py-2.5 text-sm bg-white
                       focus:outline-none focus:ring-2 focus:ring-orange-300">
            <option value="">All Categories</option>
            <?php foreach ($all_categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>"
                    <?= $category_filter === $cat ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <button type="submit"
                class="bg-orange-500 text-white px-6 py-2.5 rounded-full text-sm font-semibold
                       hover:bg-orange-600 transition shrink-0">
            Search
        </button>
        <?php if ($search || $category_filter): ?>
        <a href="<?= SITE_URL ?>/pages/menu.php"
           class="border border-gray-200 text-gray-500 px-5 py-2.5 rounded-full text-sm
                  hover:text-orange-500 transition text-center shrink-0">
            Clear
        </a>
        <?php endif; ?>
    </form>

    <?php if (empty($recipes)): ?>
    <!-- Empty state -->
    <div class="text-center py-24 text-gray-400">
        <p class="text-6xl mb-5">🍽</p>
        <p class="text-xl font-semibold text-gray-600">No recipes found</p>
        <p class="text-sm mt-2">Try adjusting your search or clearing filters.</p>
        <a href="<?= SITE_URL ?>/pages/menu.php"
           class="inline-block mt-6 border border-gray-200 px-6 py-2.5 rounded-full text-sm
                  hover:text-orange-500 transition">
            View all recipes
        </a>
    </div>

    <?php elseif ($search || $category_filter): ?>
    <!-- Flat grid for search/filter results -->
    <p class="text-sm text-gray-500 mb-5">
        Found <strong><?= count($recipes) ?></strong>
        recipe<?= count($recipes) !== 1 ? 's' : '' ?>
        <?= $search ? 'for "<strong>' . htmlspecialchars($search) . '</strong>"' : '' ?>
        <?= $category_filter ? 'in <strong>' . htmlspecialchars($category_filter) . '</strong>' : '' ?>
    </p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($recipes as $r): recipe_card($r); endforeach; ?>
    </div>

    <?php else: ?>
    <!-- Grouped by category -->
    <?php foreach ($grouped as $cat => $items): ?>
    <section class="mb-14">
        <div class="flex items-center gap-3 mb-6">
            <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($cat) ?></h2>
            <span class="bg-orange-50 text-orange-500 text-xs font-semibold px-2.5 py-1 rounded-full">
                <?= count($items) ?>
            </span>
            <div class="flex-1 h-px bg-gray-100"></div>
            <a href="?category=<?= urlencode($cat) ?>"
               class="text-xs text-gray-400 hover:text-orange-500 transition">
                View all →
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($items as $r): recipe_card($r); endforeach; ?>
        </div>
    </section>
    <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
