<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/db.php';
require_once dirname(__DIR__)    . '/session.php';

require_login();

$page_title = 'Cookbooks – ' . SITE_NAME;
$active_nav = 'cookbooks';

$pdo  = get_db();
$user = current_user();

$search = trim($_GET['q'] ?? '');

if (is_admin()) {
    // Admin sees all cookbooks
    if ($search !== '') {
        $stmt = $pdo->prepare(
            'SELECT c.*, u.name AS author
             FROM cookbooks c JOIN users u ON u.id = c.user_id
             WHERE c.name LIKE ? OR c.country LIKE ? OR c.cooking_type LIKE ?
             ORDER BY c.created_at DESC'
        );
        $stmt->execute(["%$search%", "%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->query(
            'SELECT c.*, u.name AS author
             FROM cookbooks c JOIN users u ON u.id = c.user_id
             ORDER BY c.created_at DESC'
        );
    }
} else {
    // Regular users see only their own
    if ($search !== '') {
        $stmt = $pdo->prepare(
            'SELECT c.*, u.name AS author
             FROM cookbooks c JOIN users u ON u.id = c.user_id
             WHERE c.user_id = ? AND (c.name LIKE ? OR c.country LIKE ? OR c.cooking_type LIKE ?)
             ORDER BY c.created_at DESC'
        );
        $stmt->execute([$user['id'], "%$search%", "%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->prepare(
            'SELECT c.*, u.name AS author
             FROM cookbooks c JOIN users u ON u.id = c.user_id
             WHERE c.user_id = ?
             ORDER BY c.created_at DESC'
        );
        $stmt->execute([$user['id']]);
    }
}
$cookbooks = $stmt->fetchAll();

$flash_success = flash_get('success');
$flash_error   = flash_get('error');

ob_start();
?>

<!-- Heading row -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Cookbooks</h1>
    <a href="<?= SITE_URL ?>/auth/cookbooks/create.php"
       class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-5 py-2.5 rounded-full transition">
        + Add Cookbook
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
               placeholder="Search by name, country, or cooking type…"
               class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
        <button type="submit"
                class="bg-gray-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-900 transition">
            Search
        </button>
        <?php if ($search): ?>
        <a href="<?= SITE_URL ?>/auth/cookbooks/index.php"
           class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-500 hover:bg-gray-100 transition">
            Clear
        </a>
        <?php endif; ?>
    </div>
</form>

<!-- Cookbooks grid -->
<?php if (empty($cookbooks)): ?>
<div class="bg-white rounded-2xl shadow-sm p-12 text-center">
    <div class="text-5xl mb-4">📖</div>
    <p class="text-gray-500 text-sm">
        <?= $search ? 'No cookbooks match your search.' : 'No cookbooks yet.' ?>
        <?php if (!$search): ?>
        <a href="<?= SITE_URL ?>/auth/cookbooks/create.php" class="text-orange-500 hover:underline">Add your first cookbook →</a>
        <?php endif; ?>
    </p>
</div>
<?php else: ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach ($cookbooks as $c): ?>
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col hover:shadow-md transition">
        <!-- Photo -->
        <?php if ($c['photo']): ?>
        <img src="<?= SITE_URL ?>/uploads/cookbooks/<?= htmlspecialchars($c['photo']) ?>"
             alt="<?= htmlspecialchars($c['name']) ?>"
             class="w-full h-44 object-cover">
        <?php else: ?>
        <div class="w-full h-44 bg-gradient-to-br from-orange-50 to-amber-100 flex items-center justify-center text-5xl">📖</div>
        <?php endif; ?>

        <!-- Body -->
        <div class="p-5 flex flex-col flex-1">
            <div class="flex items-start justify-between gap-2 mb-2">
                <h2 class="font-bold text-gray-900 text-sm leading-tight"><?= htmlspecialchars($c['name']) ?></h2>
                <?php
                $status_colors = [
                    'pending'  => 'bg-yellow-100 text-yellow-700',
                    'approved' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-600',
                ];
                ?>
                <span class="shrink-0 text-xs font-semibold px-2.5 py-0.5 rounded-full <?= $status_colors[$c['status']] ?>">
                    <?= ucfirst($c['status']) ?>
                </span>
            </div>

            <p class="text-gray-500 text-xs leading-relaxed mb-3 line-clamp-2">
                <?= htmlspecialchars(strtok($c['description'], "\n")) ?>
            </p>

            <!-- Meta chips -->
            <div class="flex flex-wrap gap-2 text-xs text-gray-500 mb-4">
                <span class="bg-gray-100 rounded-full px-2.5 py-1">🌍 <?= htmlspecialchars($c['country']) ?></span>
                <span class="bg-gray-100 rounded-full px-2.5 py-1">🍳 <?= htmlspecialchars($c['cooking_type']) ?></span>
                <span class="bg-gray-100 rounded-full px-2.5 py-1">❤️ <?= (int)$c['total_likes'] ?></span>
            </div>

            <?php if (is_admin()): ?>
            <p class="text-xs text-gray-400 mb-3">By <?= htmlspecialchars($c['author']) ?></p>
            <?php endif; ?>

            <div class="mt-auto flex items-center justify-between">
                <span class="text-xs text-gray-400"><?= date('M j, Y', strtotime($c['created_at'])) ?></span>
                <div class="flex gap-3 items-center">
                    <?php if (is_admin() && $c['status'] !== 'approved'): ?>
                    <a href="<?= SITE_URL ?>/auth/cookbooks/status.php?id=<?= $c['id'] ?>&status=approved"
                       class="text-xs font-semibold text-green-500 hover:text-green-700 transition"
                       onclick="return confirm('Approve this cookbook?')">Approve</a>
                    <?php endif; ?>
                    <?php if (is_admin() && $c['status'] !== 'rejected'): ?>
                    <a href="<?= SITE_URL ?>/auth/cookbooks/status.php?id=<?= $c['id'] ?>&status=rejected"
                       class="text-xs font-semibold text-yellow-500 hover:text-yellow-700 transition"
                       onclick="return confirm('Reject this cookbook?')">Reject</a>
                    <?php endif; ?>
                    <a href="<?= SITE_URL ?>/auth/cookbooks/edit.php?id=<?= $c['id'] ?>"
                       class="text-xs font-semibold text-blue-500 hover:text-blue-700 transition">Edit</a>
                    <a href="<?= SITE_URL ?>/auth/cookbooks/delete.php?id=<?= $c['id'] ?>"
                       class="text-xs font-semibold text-red-400 hover:text-red-600 transition"
                       onclick="return confirm('Delete \'<?= htmlspecialchars(addslashes($c['name'])) ?>\'?')">
                       Delete
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<p class="text-xs text-gray-400 text-center mt-6"><?= count($cookbooks) ?> cookbook<?= count($cookbooks) !== 1 ? 's' : '' ?> found</p>
<?php endif; ?>

<?php
$page_content = ob_get_clean();
include dirname(__DIR__) . '/admin_layout.php';
