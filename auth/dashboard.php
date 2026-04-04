<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db.php';
require_once __DIR__ . '/session.php';

require_admin();

$page_title  = 'Dashboard – ' . SITE_NAME;
$active_nav  = 'dashboard';
$flash_success = flash_get('success');

$pdo = get_db();

// Stats
$total_recipes   = (int) $pdo->query('SELECT COUNT(*) FROM recipes')->fetchColumn();
$total_users     = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$total_cookbooks = (int) $pdo->query('SELECT COUNT(*) FROM cookbooks')->fetchColumn();
$pending_cookbooks = (int) $pdo->query("SELECT COUNT(*) FROM cookbooks WHERE status = 'pending'")->fetchColumn();

// Latest 5 recipes
$stmt = $pdo->query(
    'SELECT r.id, r.title, r.category, r.photo, r.created_at, u.name AS author
     FROM recipes r
     JOIN users u ON u.id = r.created_by
     ORDER BY r.created_at DESC
     LIMIT 5'
);
$latest_recipes = $stmt->fetchAll();

ob_start();
?>

<?php if ($flash_success): ?>
<div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-4 text-sm mb-8 flex items-center gap-2">
    ✅ <?= htmlspecialchars($flash_success) ?>
</div>
<?php endif; ?>

<!-- Page heading -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-500 text-sm mt-1">Welcome back, <strong><?= htmlspecialchars(current_user()['name']) ?></strong> 👋</p>
</div>

<!-- Stats row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

    <!-- Total Recipes -->
    <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-5 border-l-4 border-orange-400">
        <div class="w-14 h-14 rounded-full bg-orange-50 flex items-center justify-center text-3xl shrink-0">🍴</div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-0.5">Total Recipes</p>
            <p class="text-3xl font-bold text-gray-900"><?= $total_recipes ?></p>
        </div>
    </div>

    <!-- Total Users -->
    <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-5 border-l-4 border-blue-400">
        <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-3xl shrink-0">👥</div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-0.5">Registered Users</p>
            <p class="text-3xl font-bold text-gray-900"><?= $total_users ?></p>
        </div>
    </div>

    <!-- Total Cookbooks -->
    <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-5 border-l-4 border-green-400">
        <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-3xl shrink-0">📖</div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-0.5">Cookbooks</p>
            <p class="text-3xl font-bold text-gray-900"><?= $total_cookbooks ?></p>
        </div>
    </div>

    <!-- Pending Cookbooks -->
    <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-5 border-l-4 border-yellow-400">
        <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center text-3xl shrink-0">⏳</div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-0.5">Pending Review</p>
            <p class="text-3xl font-bold text-gray-900"><?= $pending_cookbooks ?></p>
        </div>
    </div>

</div>

<!-- Latest recipes table -->
<div class="bg-white rounded-2xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-base font-bold text-gray-900">Latest Recipes</h2>
        <a href="<?= SITE_URL ?>/auth/recipes/create.php"
           class="text-xs bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-full transition">
            + Add Recipe
        </a>
    </div>

    <?php if (empty($latest_recipes)): ?>
    <p class="text-sm text-gray-400 text-center py-8">No recipes yet. <a href="<?= SITE_URL ?>/auth/recipes/create.php" class="text-orange-500 hover:underline">Add your first one →</a></p>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-100">
                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Recipe</th>
                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Category</th>
                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Added</th>
                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
            <?php foreach ($latest_recipes as $recipe): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3 pr-4">
                        <div class="flex items-center gap-3">
                            <?php if ($recipe['photo']): ?>
                            <img src="<?= SITE_URL ?>/uploads/recipes/<?= htmlspecialchars($recipe['photo']) ?>"
                                 alt="" class="w-10 h-10 rounded-lg object-cover shrink-0">
                            <?php else: ?>
                            <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center text-xl shrink-0">🍽</div>
                            <?php endif; ?>
                            <span class="font-medium text-gray-800"><?= htmlspecialchars($recipe['title']) ?></span>
                        </div>
                    </td>
                    <td class="py-3 pr-4 hidden sm:table-cell">
                        <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-1 rounded-full">
                            <?= htmlspecialchars($recipe['category'] ?: '—') ?>
                        </span>
                    </td>
                    <td class="py-3 pr-4 text-gray-400 text-xs hidden md:table-cell">
                        <?= date('M j, Y', strtotime($recipe['created_at'])) ?>
                    </td>
                    <td class="py-3 text-right">
                        <a href="<?= SITE_URL ?>/auth/recipes/edit.php?id=<?= (int)$recipe['id'] ?>"
                           class="text-blue-500 hover:text-blue-700 font-medium mr-3 text-xs">Edit</a>
                        <a href="<?= SITE_URL ?>/auth/recipes/delete.php?id=<?= (int)$recipe['id'] ?>"
                           class="text-red-400 hover:text-red-600 font-medium text-xs"
                           onclick="return confirm('Delete this recipe?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_recipes > 5): ?>
    <div class="text-center mt-5">
        <a href="<?= SITE_URL ?>/auth/recipes/index.php" class="text-sm text-orange-500 hover:underline">View all <?= $total_recipes ?> recipes →</a>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/admin_layout.php';
