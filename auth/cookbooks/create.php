<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/db.php';
require_once dirname(__DIR__)    . '/session.php';
require_once dirname(__DIR__, 2) . '/includes/gemini.php';

require_login();

$page_title = 'Add Cookbook – ' . SITE_NAME;
$active_nav = 'cookbooks';

$errors = [];
$old    = ['name' => '', 'description' => '', 'country' => '', 'cooking_type' => '', 'tips' => ''];

require_once dirname(__DIR__, 2) . '/includes/cookbook_options.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old['name']         = trim($_POST['name']         ?? '');
    $old['description']  = trim($_POST['description']  ?? '');
    $old['country']      = trim($_POST['country']      ?? '');
    $old['cooking_type'] = trim($_POST['cooking_type'] ?? '');
    $old['tips']         = trim($_POST['tips']         ?? '');

    if (empty($old['name'])) {
        $errors['name'] = 'Cookbook name is required.';
    } elseif (strlen($old['name']) > 255) {
        $errors['name'] = 'Name must be 255 characters or less.';
    }

    if (empty($old['description'])) {
        $errors['description'] = 'Step-by-step instructions are required.';
    }

    if (empty($old['country'])) {
        $errors['country'] = 'Please select a country.';
    }

    if (empty($old['cooking_type'])) {
        $errors['cooking_type'] = 'Please select a cooking type.';
    }

    // Photo upload
    $photo_filename = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['photo'];
        $max_size = 2 * 1024 * 1024;
        $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if ($file['size'] > $max_size) {
            $errors['photo'] = 'Image must be 2 MB or smaller.';
        } elseif (!in_array($file['type'], $allowed, true)) {
            $errors['photo'] = 'Only JPG, PNG, WebP, or GIF images are allowed.';
        } else {
            $ext             = pathinfo($file['name'], PATHINFO_EXTENSION);
            $photo_filename  = 'cookbook_' . uniqid('', true) . '.' . strtolower($ext);
            $upload_path     = dirname(__DIR__, 2) . '/uploads/cookbooks/' . $photo_filename;

            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                $errors['photo'] = 'Failed to save the image. Please try again.';
                $photo_filename  = null;
            }
        }
    } elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors['photo'] = 'Upload error. Please try again.';
    }

    if (empty($errors)) {
        $pdo  = get_db();
        $stmt = $pdo->prepare(
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

        $cookbook_id = $pdo->lastInsertId();
        $score = ai_score_cookbook(
            $old['name'],
            $old['description'],
            $old['country'],
            $old['cooking_type'],
            $old['tips'] ?: null
        );

        if ($score !== null && $score >= 70) {
            $pdo->prepare('UPDATE cookbooks SET status = ? WHERE id = ?')
                ->execute(['approved', $cookbook_id]);
            flash_set('success', 'Cookbook "' . $old['name'] . '" has been approved!');
        } else {
            flash_set('success', 'Cookbook "' . $old['name'] . '" submitted for review!');
        }

        header('Location: ' . SITE_URL . '/auth/cookbooks/index.php');
        exit;
    }
}

ob_start();
?>

<!-- Back link + heading -->
<div class="mb-6">
    <a href="<?= SITE_URL ?>/auth/cookbooks/index.php"
       class="text-sm text-gray-400 hover:text-orange-500 transition inline-flex items-center gap-1 mb-3">
        ← Back to Cookbooks
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Add New Cookbook</h1>
    <p class="text-sm text-gray-500 mt-1">Your submission will be reviewed by an admin before it appears publicly.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm p-6 md:p-8 max-w-2xl">
    <form method="POST" enctype="multipart/form-data" novalidate class="space-y-6">

        <!-- Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="name">Cookbook Name <span class="text-red-500">*</span></label>
            <input id="name" type="text" name="name"
                   value="<?= htmlspecialchars($old['name']) ?>"
                   placeholder="e.g. Filipino Chicken Adobo"
                   class="w-full border <?= isset($errors['name']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
            <?php if (isset($errors['name'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['name']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Country & Cooking Type -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="country">Country <span class="text-red-500">*</span></label>
                <select id="country" name="country"
                        class="w-full border <?= isset($errors['country']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition bg-white">
                    <option value="">— Select country —</option>
                    <?php foreach ($countries as $c): ?>
                    <option value="<?= $c ?>" <?= $old['country'] === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['country'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['country']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="cooking_type">Cooking Type <span class="text-red-500">*</span></label>
                <select id="cooking_type" name="cooking_type"
                        class="w-full border <?= isset($errors['cooking_type']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition bg-white">
                    <option value="">— Select type —</option>
                    <?php foreach ($cooking_types as $ct): ?>
                    <option value="<?= $ct ?>" <?= $old['cooking_type'] === $ct ? 'selected' : '' ?>><?= $ct ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['cooking_type'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['cooking_type']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Description (step-by-step) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="description">Step-by-Step Instructions <span class="text-red-500">*</span></label>
            <p class="text-xs text-gray-400 mb-2">Write one step per line. Each line will be displayed as a numbered step.</p>
            <textarea id="description" name="description" rows="8"
                      placeholder="Marinate chicken in soy sauce, vinegar, and garlic for 30 minutes.&#10;Heat oil in a large pot over medium-high heat.&#10;Sear chicken pieces until golden brown on all sides.&#10;Add marinade and bay leaves, bring to a boil.&#10;Reduce heat and simmer for 30-35 minutes until tender."
                      class="w-full border <?= isset($errors['description']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition resize-none"><?= htmlspecialchars($old['description']) ?></textarea>
            <?php if (isset($errors['description'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['description']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Tips -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="tips">Tips & Notes</label>
            <textarea id="tips" name="tips" rows="3"
                      placeholder="Any helpful tips, substitutions, or serving suggestions…"
                      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition resize-none"><?= htmlspecialchars($old['tips']) ?></textarea>
        </div>

        <!-- Photo upload -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cookbook Photo</label>
            <div id="drop-zone"
                 class="border-2 border-dashed <?= isset($errors['photo']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl p-6 text-center cursor-pointer hover:border-orange-400 transition group">
                <input id="photo" type="file" name="photo" accept="image/*" class="hidden">
                <div id="drop-placeholder">
                    <div class="text-4xl mb-2 group-hover:scale-110 transition">📷</div>
                    <p class="text-sm text-gray-500">Click to upload or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP, GIF · max 2 MB</p>
                </div>
                <img id="preview-img" src="" alt="" class="hidden mx-auto max-h-48 rounded-xl object-cover">
            </div>
            <?php if (isset($errors['photo'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['photo']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-8 py-3 rounded-xl text-sm transition">
                Submit Cookbook
            </button>
            <a href="<?= SITE_URL ?>/auth/cookbooks/index.php"
               class="px-8 py-3 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition font-medium">
                Cancel
            </a>
        </div>

    </form>
</div>

<script>
const dropZone  = document.getElementById('drop-zone');
const fileInput = document.getElementById('photo');
const placeholder = document.getElementById('drop-placeholder');
const previewImg  = document.getElementById('preview-img');

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
</script>

<?php
$page_content = ob_get_clean();
include dirname(__DIR__) . '/admin_layout.php';
