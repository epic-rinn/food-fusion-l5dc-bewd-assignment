<?php
$page_title       = 'Community Cookbook – Food Fusion';
$meta_description = 'Browse community-submitted cookbooks from around the world.';
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/auth/session.php';

// --- Lists for the submit form ---
$form_countries = [
    'Philippines', 'United States', 'Japan', 'Korea', 'China', 'India', 'Thailand',
    'Vietnam', 'Mexico', 'Italy', 'France', 'Spain', 'Greece', 'Turkey', 'Morocco',
    'Brazil', 'United Kingdom', 'Australia', 'Germany', 'Canada',
];

$form_cooking_types = [
    'Baking', 'Grilling', 'Frying', 'Steaming', 'Boiling', 'Roasting', 'Stir-fry',
    'Slow Cooking', 'Smoking', 'Raw/No-Cook', 'Braising', 'Sauteing', 'Fermenting',
    'Soup/Stew', 'Other',
];

// --- Handle POST: create cookbook (logged-in users only) ---
$form_errors = [];
$old = ['name' => '', 'description' => '', 'country' => '', 'cooking_type' => '', 'tips' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {

    $old['name']         = trim($_POST['name']         ?? '');
    $old['description']  = trim($_POST['description']  ?? '');
    $old['country']      = trim($_POST['country']      ?? '');
    $old['cooking_type'] = trim($_POST['cooking_type'] ?? '');
    $old['tips']         = trim($_POST['tips']         ?? '');

    if (empty($old['name'])) {
        $form_errors['name'] = 'Cookbook name is required.';
    } elseif (strlen($old['name']) > 255) {
        $form_errors['name'] = 'Name must be 255 characters or less.';
    }

    if (empty($old['description'])) {
        $form_errors['description'] = 'Step-by-step instructions are required.';
    }

    if (empty($old['country'])) {
        $form_errors['country'] = 'Please select a country.';
    }

    if (empty($old['cooking_type'])) {
        $form_errors['cooking_type'] = 'Please select a cooking type.';
    }

    // Photo upload
    $photo_filename = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['photo'];
        $max_size = 2 * 1024 * 1024;
        $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if ($file['size'] > $max_size) {
            $form_errors['photo'] = 'Image must be 2 MB or smaller.';
        } elseif (!in_array($file['type'], $allowed, true)) {
            $form_errors['photo'] = 'Only JPG, PNG, WebP, or GIF images are allowed.';
        } else {
            $ext             = pathinfo($file['name'], PATHINFO_EXTENSION);
            $photo_filename  = 'cookbook_' . uniqid('', true) . '.' . strtolower($ext);
            $upload_path     = dirname(__DIR__) . '/uploads/cookbooks/' . $photo_filename;

            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                $form_errors['photo'] = 'Failed to save the image. Please try again.';
                $photo_filename  = null;
            }
        }
    } elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $form_errors['photo'] = 'Upload error. Please try again.';
    }

    if (empty($form_errors)) {
        $pdo_w = get_db();
        $stmt  = $pdo_w->prepare(
            'INSERT INTO cookbooks (name, description, photo, country, cooking_type, tips, user_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $old['name'],
            $old['description'],
            $photo_filename,
            $old['country'],
            $old['cooking_type'],
            $old['tips'] ?: null,
            current_user()['id'],
        ]);

        flash_set('success', 'Cookbook "' . $old['name'] . '" submitted for review!');
        header('Location: ' . SITE_URL . '/pages/community-cookbook.php');
        exit;
    }
}

// If there were form errors, we'll reopen the modal via JS
$show_modal = !empty($form_errors);

require_once dirname(__DIR__) . '/includes/header.php';

$pdo = get_db();

// Search & filters
$search         = trim($_GET['q'] ?? '');
$country_filter = trim($_GET['country'] ?? '');
$type_filter    = trim($_GET['type'] ?? '');

// Distinct countries & types for filters
$all_countries = $pdo->query(
    "SELECT DISTINCT country FROM cookbooks WHERE status = 'approved' ORDER BY country"
)->fetchAll(PDO::FETCH_COLUMN);

$all_types = $pdo->query(
    "SELECT DISTINCT cooking_type FROM cookbooks WHERE status = 'approved' ORDER BY cooking_type"
)->fetchAll(PDO::FETCH_COLUMN);

// Build dynamic query — only approved
$where  = ["c.status = 'approved'"];
$params = [];

if ($search !== '') {
    $where[]  = '(c.name LIKE ? OR c.description LIKE ? OR c.country LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($country_filter !== '') {
    $where[]  = 'c.country = ?';
    $params[] = $country_filter;
}
if ($type_filter !== '') {
    $where[]  = 'c.cooking_type = ?';
    $params[] = $type_filter;
}

$where_sql = 'WHERE ' . implode(' AND ', $where);

$stmt = $pdo->prepare(
    "SELECT c.*, u.name AS author_name
     FROM   cookbooks c
     JOIN   users u ON u.id = c.user_id
     {$where_sql}
     ORDER  BY c.total_likes DESC, c.created_at DESC"
);
$stmt->execute($params);
$cookbooks = $stmt->fetchAll();
?>

<!-- Page wrapper -->
<div class="max-w-6xl mx-auto px-4 py-12">

    <!-- Flash messages -->
    <?php if ($msg = flash_get('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl text-sm">
        <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>
    <?php if ($msg = flash_get('error')): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm">
        <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>

    <!-- Page header -->
    <div class="text-center mb-10">
        <span class="text-xs font-semibold text-orange-500 uppercase tracking-widest">Community</span>
        <h1 class="text-4xl font-extrabold text-gray-900 mt-2 mb-3">Community Cookbook</h1>
        <p class="text-gray-500 max-w-xl mx-auto text-sm">
            Discover step-by-step cooking instructions shared by our community from around the world.
        </p>
        <div class="mt-5">
            <?php if (is_logged_in()): ?>
            <button onclick="document.getElementById('cookbook-modal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 bg-orange-500 text-white px-6 py-2.5 rounded-full text-sm font-semibold hover:bg-orange-600 transition">
                + Submit Your Cookbook
            </button>
            <?php else: ?>
            <a href="<?= SITE_URL ?>/auth/login.php"
               class="inline-flex items-center gap-2 bg-orange-500 text-white px-6 py-2.5 rounded-full text-sm font-semibold hover:bg-orange-600 transition">
                Login to Submit
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search / filter bar -->
    <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-10 max-w-3xl mx-auto">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
               placeholder="Search cookbooks…"
               class="flex-1 border border-gray-200 rounded-full px-5 py-2.5 text-sm
                      focus:outline-none focus:ring-2 focus:ring-orange-300">
        <select name="country"
                class="border border-gray-200 rounded-full px-5 py-2.5 text-sm bg-white
                       focus:outline-none focus:ring-2 focus:ring-orange-300">
            <option value="">All Countries</option>
            <?php foreach ($all_countries as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>" <?= $country_filter === $c ? 'selected' : '' ?>>
                <?= htmlspecialchars($c) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <select name="type"
                class="border border-gray-200 rounded-full px-5 py-2.5 text-sm bg-white
                       focus:outline-none focus:ring-2 focus:ring-orange-300">
            <option value="">All Cooking Types</option>
            <?php foreach ($all_types as $t): ?>
            <option value="<?= htmlspecialchars($t) ?>" <?= $type_filter === $t ? 'selected' : '' ?>>
                <?= htmlspecialchars($t) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <button type="submit"
                class="bg-orange-500 text-white px-6 py-2.5 rounded-full text-sm font-semibold
                       hover:bg-orange-600 transition shrink-0">
            Search
        </button>
        <?php if ($search || $country_filter || $type_filter): ?>
        <a href="<?= SITE_URL ?>/pages/community-cookbook.php"
           class="border border-gray-200 text-gray-500 px-5 py-2.5 rounded-full text-sm
                  hover:text-orange-500 transition text-center shrink-0">
            Clear
        </a>
        <?php endif; ?>
    </form>

    <?php if (empty($cookbooks)): ?>
    <!-- Empty state -->
    <div class="text-center py-24 text-gray-400">
        <p class="text-6xl mb-5">📖</p>
        <p class="text-xl font-semibold text-gray-600">No cookbooks found</p>
        <p class="text-sm mt-2">Try adjusting your search or clearing filters.</p>
        <a href="<?= SITE_URL ?>/pages/community-cookbook.php"
           class="inline-block mt-6 border border-gray-200 px-6 py-2.5 rounded-full text-sm
                  hover:text-orange-500 transition">
            View all cookbooks
        </a>
    </div>
    <?php else: ?>

    <?php if ($search || $country_filter || $type_filter): ?>
    <p class="text-sm text-gray-500 mb-5">
        Found <strong><?= count($cookbooks) ?></strong>
        cookbook<?= count($cookbooks) !== 1 ? 's' : '' ?>
        <?= $search ? 'for "<strong>' . htmlspecialchars($search) . '</strong>"' : '' ?>
        <?= $country_filter ? 'in <strong>' . htmlspecialchars($country_filter) . '</strong>' : '' ?>
        <?= $type_filter ? '· <strong>' . htmlspecialchars($type_filter) . '</strong>' : '' ?>
    </p>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($cookbooks as $cb): ?>
        <a href="<?= SITE_URL ?>/pages/cookbook-detail.php?id=<?= (int)$cb['id'] ?>"
           class="group bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-200 flex flex-col">
            <!-- Photo -->
            <div class="aspect-video overflow-hidden bg-gray-100">
                <?php if ($cb['photo']): ?>
                <img src="<?= SITE_URL ?>/uploads/cookbooks/<?= htmlspecialchars($cb['photo']) ?>"
                     alt="<?= htmlspecialchars($cb['name']) ?>"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-gray-300 text-5xl">📖</div>
                <?php endif; ?>
            </div>
            <!-- Body -->
            <div class="p-5 flex flex-col flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-semibold text-orange-500 uppercase tracking-wide">
                        <?= htmlspecialchars($cb['country']) ?>
                    </span>
                    <span class="text-gray-300">·</span>
                    <span class="text-xs text-gray-400"><?= htmlspecialchars($cb['cooking_type']) ?></span>
                </div>
                <h3 class="font-bold text-gray-900 group-hover:text-orange-500 transition leading-snug">
                    <?= htmlspecialchars($cb['name']) ?>
                </h3>
                <p class="text-gray-500 text-sm mt-1.5 line-clamp-2 flex-1">
                    <?= htmlspecialchars(strtok($cb['description'], "\n")) ?>
                </p>
                <!-- Meta -->
                <div class="flex items-center justify-between mt-3 text-xs text-gray-400">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1">❤️ <?= (int)$cb['total_likes'] ?></span>
                        <span>By <?= htmlspecialchars($cb['author_name']) ?></span>
                    </div>
                    <span><?= date('M j, Y', strtotime($cb['created_at'])) ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<?php if (is_logged_in()): ?>
<!-- Submit Cookbook Modal -->
<div id="cookbook-modal" class="<?= $show_modal ? '' : 'hidden' ?> fixed inset-0 z-[60] flex items-center justify-center">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('cookbook-modal').classList.add('hidden')"></div>
    <!-- Modal card -->
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4 p-6 md:p-8">
        <!-- Close button -->
        <button onclick="document.getElementById('cookbook-modal').classList.add('hidden')"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition text-2xl leading-none">&times;</button>

        <h2 class="text-2xl font-bold text-gray-900 mb-1">Submit Your Cookbook</h2>
        <p class="text-sm text-gray-500 mb-6">Your submission will be reviewed by an admin before it appears publicly.</p>

        <?php if (!empty($form_errors)): ?>
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            Please fix the errors below and try again.
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate class="space-y-5">

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="modal-name">Cookbook Name <span class="text-red-500">*</span></label>
                <input id="modal-name" type="text" name="name"
                       value="<?= htmlspecialchars($old['name']) ?>"
                       placeholder="e.g. Filipino Chicken Adobo"
                       class="w-full border <?= isset($form_errors['name']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                <?php if (isset($form_errors['name'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($form_errors['name']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Country & Cooking Type -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="modal-country">Country <span class="text-red-500">*</span></label>
                    <select id="modal-country" name="country"
                            class="w-full border <?= isset($form_errors['country']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition bg-white">
                        <option value="">-- Select country --</option>
                        <?php foreach ($form_countries as $fc): ?>
                        <option value="<?= $fc ?>" <?= $old['country'] === $fc ? 'selected' : '' ?>><?= $fc ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($form_errors['country'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($form_errors['country']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="modal-cooking_type">Cooking Type <span class="text-red-500">*</span></label>
                    <select id="modal-cooking_type" name="cooking_type"
                            class="w-full border <?= isset($form_errors['cooking_type']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition bg-white">
                        <option value="">-- Select type --</option>
                        <?php foreach ($form_cooking_types as $fct): ?>
                        <option value="<?= $fct ?>" <?= $old['cooking_type'] === $fct ? 'selected' : '' ?>><?= $fct ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($form_errors['cooking_type'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($form_errors['cooking_type']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="modal-description">Step-by-Step Instructions <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-400 mb-2">Write one step per line. Each line will be displayed as a numbered step.</p>
                <textarea id="modal-description" name="description" rows="6"
                          placeholder="Marinate chicken in soy sauce, vinegar, and garlic for 30 minutes.&#10;Heat oil in a large pot over medium-high heat.&#10;Sear chicken pieces until golden brown on all sides."
                          class="w-full border <?= isset($form_errors['description']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition resize-none"><?= htmlspecialchars($old['description']) ?></textarea>
                <?php if (isset($form_errors['description'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($form_errors['description']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Tips -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="modal-tips">Tips & Notes</label>
                <textarea id="modal-tips" name="tips" rows="3"
                          placeholder="Any helpful tips, substitutions, or serving suggestions…"
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition resize-none"><?= htmlspecialchars($old['tips']) ?></textarea>
            </div>

            <!-- Photo upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cookbook Photo</label>
                <div id="modal-drop-zone"
                     class="border-2 border-dashed <?= isset($form_errors['photo']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl p-6 text-center cursor-pointer hover:border-orange-400 transition group">
                    <input id="modal-photo" type="file" name="photo" accept="image/*" class="hidden">
                    <div id="modal-drop-placeholder">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition">📷</div>
                        <p class="text-sm text-gray-500">Click to upload or drag & drop</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP, GIF · max 2 MB</p>
                    </div>
                    <img id="modal-preview-img" src="" alt="" class="hidden mx-auto max-h-48 rounded-xl object-cover">
                </div>
                <?php if (isset($form_errors['photo'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($form_errors['photo']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-8 py-3 rounded-xl text-sm transition">
                    Submit Cookbook
                </button>
                <button type="button"
                        onclick="document.getElementById('cookbook-modal').classList.add('hidden')"
                        class="px-8 py-3 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Modal JS: drag-and-drop photo preview -->
<script>
(function() {
    const dropZone     = document.getElementById('modal-drop-zone');
    const fileInput    = document.getElementById('modal-photo');
    const placeholder  = document.getElementById('modal-drop-placeholder');
    const previewImg   = document.getElementById('modal-preview-img');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-orange-400', 'bg-orange-50'); });
    dropZone.addEventListener('dragleave', ()  => dropZone.classList.remove('border-orange-400', 'bg-orange-50'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('border-orange-400', 'bg-orange-50');
        if (e.dataTransfer.files.length) showPreview(e.dataTransfer.files[0]);
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) showPreview(fileInput.files[0]);
    });

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = e => {
            previewImg.src = e.target.result;
            previewImg.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
    }

    // Close modal on Escape key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.getElementById('cookbook-modal').classList.add('hidden');
    });
})();
</script>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
