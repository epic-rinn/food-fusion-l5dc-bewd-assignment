<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/db.php';
require_once dirname(__DIR__)    . '/session.php';

require_admin();

$page_title = 'Recipes – ' . SITE_NAME;
$active_nav = 'recipes';

$pdo = get_db();

// Simple search
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare(
        'SELECT r.*, u.name AS author
         FROM recipes r JOIN users u ON u.id = r.created_by
         WHERE r.title LIKE ? OR r.category LIKE ?
         ORDER BY r.created_at DESC'
    );
    $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
} else {
    $stmt = $pdo->query(
        'SELECT r.*, u.name AS author
         FROM recipes r JOIN users u ON u.id = r.created_by
         ORDER BY r.created_at DESC'
    );
}
$recipes = $stmt->fetchAll();

$flash_success = flash_get('success');
$flash_error   = flash_get('error');

ob_start();
?>

<!-- Heading row -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Recipes</h1>
    <a href="<?= SITE_URL ?>/auth/recipes/create.php"
       class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-5 py-2.5 rounded-full transition">
        + Add Recipe
    </a>
</div>

<!-- Flash messages -->
<?php if ($flash_success): ?>
<div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 text-sm mb-6 flex items-center gap-2">
    ✅ <?= htmlspecialchars($flash_success) ?>
</div>
<?php endif; ?>
<?php if ($flash_error): ?>
<div class="bg-red-50 border border-red-200 text-red-600 rounded-xl px-5 py-3 text-sm mb-6 flex items-center gap-2">
    ⚠️ <?= htmlspecialchars($flash_error) ?>
</div>
<?php endif; ?>

<!-- Search bar -->
<form method="GET" class="mb-6">
    <div class="flex gap-2">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
               placeholder="Search by title or category…"
               class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
        <button type="submit"
                class="bg-gray-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-900 transition">
            Search
        </button>
        <?php if ($search): ?>
        <a href="<?= SITE_URL ?>/auth/recipes/index.php"
           class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-500 hover:bg-gray-100 transition">
            Clear
        </a>
        <?php endif; ?>
    </div>
</form>

<!-- Recipes grid -->
<?php if (empty($recipes)): ?>
<div class="bg-white rounded-2xl shadow-sm p-12 text-center">
    <div class="text-5xl mb-4">🍽</div>
    <p class="text-gray-500 text-sm">
        <?= $search ? 'No recipes match your search.' : 'No recipes yet.' ?>
        <?php if (!$search): ?>
        <a href="<?= SITE_URL ?>/auth/recipes/create.php" class="text-orange-500 hover:underline">Add your first recipe →</a>
        <?php endif; ?>
    </p>
</div>
<?php else: ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach ($recipes as $r): ?>
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col hover:shadow-md transition">
        <!-- Photo -->
        <?php if ($r['photo']): ?>
        <img src="<?= SITE_URL ?>/uploads/recipes/<?= htmlspecialchars($r['photo']) ?>"
             alt="<?= htmlspecialchars($r['title']) ?>"
             class="w-full h-44 object-cover">
        <?php else: ?>
        <div class="w-full h-44 bg-gradient-to-br from-orange-50 to-amber-100 flex items-center justify-center text-5xl">🍽</div>
        <?php endif; ?>

        <!-- Body -->
        <div class="p-5 flex flex-col flex-1">
            <div class="flex items-start justify-between gap-2 mb-2">
                <h2 class="font-bold text-gray-900 text-sm leading-tight"><?= htmlspecialchars($r['title']) ?></h2>
                <?php if ($r['category']): ?>
                <span class="shrink-0 bg-orange-100 text-orange-600 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                    <?= htmlspecialchars($r['category']) ?>
                </span>
                <?php endif; ?>
            </div>

            <?php if ($r['description']): ?>
            <p class="text-gray-500 text-xs leading-relaxed mb-3 line-clamp-2">
                <?= htmlspecialchars($r['description']) ?>
            </p>
            <?php endif; ?>

            <!-- Meta chips -->
            <div class="flex flex-wrap gap-2 text-xs text-gray-500 mb-4">
                <?php if ($r['prep_time']): ?>
                <span class="bg-gray-100 rounded-full px-2.5 py-1">⏱ Prep: <?= $r['prep_time'] ?>m</span>
                <?php endif; ?>
                <?php if ($r['cook_time']): ?>
                <span class="bg-gray-100 rounded-full px-2.5 py-1">🔥 Cook: <?= $r['cook_time'] ?>m</span>
                <?php endif; ?>
                <?php if ($r['servings']): ?>
                <span class="bg-gray-100 rounded-full px-2.5 py-1">🍽 <?= $r['servings'] ?> servings</span>
                <?php endif; ?>
            </div>

            <div class="mt-auto flex items-center justify-between">
                <span class="text-xs text-gray-400"><?= date('M j, Y', strtotime($r['created_at'])) ?></span>
                <div class="flex gap-3">
                    <a href="<?= SITE_URL ?>/auth/recipes/edit.php?id=<?= $r['id'] ?>"
                       class="text-xs font-semibold text-blue-500 hover:text-blue-700 transition">Edit</a>
                    <a href="<?= SITE_URL ?>/auth/recipes/delete.php?id=<?= $r['id'] ?>"
                       class="text-xs font-semibold text-red-400 hover:text-red-600 transition"
                       onclick="return confirm('Delete \'<?= htmlspecialchars(addslashes($r['title'])) ?>\'?')">
                       Delete
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<p class="text-xs text-gray-400 text-center mt-6"><?= count($recipes) ?> recipe<?= count($recipes) !== 1 ? 's' : '' ?> found</p>
<?php endif; ?>

<?php
$page_content = ob_get_clean();
include dirname(__DIR__) . '/admin_layout.php';
