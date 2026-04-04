<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/db.php';
require_once dirname(__DIR__)    . '/session.php';

require_admin();

$page_title = 'Add Recipe – ' . SITE_NAME;
$active_nav = 'recipes';

$errors = [];
$old    = ['title' => '', 'description' => '', 'category' => '',
           'prep_time' => '', 'cook_time' => '', 'servings' => '', 'cooking_method' => ''];

$categories      = ['Breakfast', 'Lunch', 'Dinner', 'Appetizer', 'Dessert', 'Snack', 'Beverage', 'Soup', 'Salad', 'Other'];
$cooking_methods = ['Bake', 'Pan-fry', 'Pan-sear', 'Simmer', 'Grill', 'Deep-fry', 'Boil', 'Roast', 'Steam', 'Blend', 'Toast', 'No-Cook', 'Slow Cook', 'Other'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old['title']          = trim($_POST['title']          ?? '');
    $old['description']    = trim($_POST['description']    ?? '');
    $old['category']       = trim($_POST['category']       ?? '');
    $old['prep_time']      = trim($_POST['prep_time']      ?? '');
    $old['cook_time']      = trim($_POST['cook_time']      ?? '');
    $old['servings']       = trim($_POST['servings']       ?? '');
    $old['cooking_method'] = trim($_POST['cooking_method'] ?? '');

    // Validate
    if (empty($old['title'])) {
        $errors['title'] = 'Recipe title is required.';
    } elseif (strlen($old['title']) > 255) {
        $errors['title'] = 'Title must be 255 characters or less.';
    }

    foreach (['prep_time', 'cook_time', 'servings'] as $field) {
        if ($old[$field] !== '' && (!ctype_digit($old[$field]) || (int)$old[$field] < 0)) {
            $errors[$field] = 'Must be a positive whole number.';
        }
    }

    // Photo upload
    $photo_filename = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['photo'];
        $max_size = 2 * 1024 * 1024; // 2 MB
        $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if ($file['size'] > $max_size) {
            $errors['photo'] = 'Image must be 2 MB or smaller.';
        } elseif (!in_array($file['type'], $allowed, true)) {
            $errors['photo'] = 'Only JPG, PNG, WebP, or GIF images are allowed.';
        } else {
            $ext             = pathinfo($file['name'], PATHINFO_EXTENSION);
            $photo_filename  = uniqid('recipe_', true) . '.' . strtolower($ext);
            $upload_path     = dirname(__DIR__, 2) . '/uploads/recipes/' . $photo_filename;

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
            'INSERT INTO recipes (title, description, category, prep_time, cook_time, servings, cooking_method, photo, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $old['title'],
            $old['description']    ?: null,
            $old['category']       ?: null,
            $old['prep_time']      !== '' ? (int)$old['prep_time'] : null,
            $old['cook_time']      !== '' ? (int)$old['cook_time'] : null,
            $old['servings']       !== '' ? (int)$old['servings']  : null,
            $old['cooking_method'] ?: null,
            $photo_filename,
            current_user()['id'],
        ]);

        flash_set('success', 'Recipe "' . $old['title'] . '" added successfully!');
        header('Location: ' . SITE_URL . '/auth/recipes/index.php');
        exit;
    }
}

ob_start();
?>

<!-- Back link + heading -->
<div class="mb-6">
    <a href="<?= SITE_URL ?>/auth/recipes/index.php"
       class="text-sm text-gray-400 hover:text-orange-500 transition inline-flex items-center gap-1 mb-3">
        ← Back to Recipes
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Add New Recipe</h1>
</div>

<div class="bg-white rounded-2xl shadow-sm p-6 md:p-8 max-w-2xl">
    <form method="POST" enctype="multipart/form-data" novalidate class="space-y-6">

        <!-- Title -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="title">Recipe Title <span class="text-red-500">*</span></label>
            <input id="title" type="text" name="title"
                   value="<?= htmlspecialchars($old['title']) ?>"
                   placeholder="e.g. Spaghetti Carbonara"
                   class="w-full border <?= isset($errors['title']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
            <?php if (isset($errors['title'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['title']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Category -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="category">Category</label>
            <select id="category" name="category"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition bg-white">
                <option value="">— Select a category —</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat ?>" <?= $old['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="description">Description</label>
            <textarea id="description" name="description" rows="4"
                      placeholder="Short description of the recipe…"
                      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition resize-none"><?= htmlspecialchars($old['description']) ?></textarea>
        </div>

        <!-- Cooking Method -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="cooking_method">Cooking Method</label>
            <select id="cooking_method" name="cooking_method"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition bg-white">
                <option value="">— Select a method —</option>
                <?php foreach ($cooking_methods as $m): ?>
                <option value="<?= $m ?>" <?= $old['cooking_method'] === $m ? 'selected' : '' ?>><?= $m ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Time + Servings -->
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="prep_time">Prep Time (min)</label>
                <input id="prep_time" type="number" name="prep_time" min="0"
                       value="<?= htmlspecialchars($old['prep_time']) ?>"
                       placeholder="e.g. 15"
                       class="w-full border <?= isset($errors['prep_time']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                <?php if (isset($errors['prep_time'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['prep_time']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="cook_time">Cook Time (min)</label>
                <input id="cook_time" type="number" name="cook_time" min="0"
                       value="<?= htmlspecialchars($old['cook_time']) ?>"
                       placeholder="e.g. 30"
                       class="w-full border <?= isset($errors['cook_time']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                <?php if (isset($errors['cook_time'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['cook_time']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="servings">Servings</label>
                <input id="servings" type="number" name="servings" min="1"
                       value="<?= htmlspecialchars($old['servings']) ?>"
                       placeholder="e.g. 4"
                       class="w-full border <?= isset($errors['servings']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                <?php if (isset($errors['servings'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['servings']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Photo upload -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Recipe Photo</label>
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
                Save Recipe
            </button>
            <a href="<?= SITE_URL ?>/auth/recipes/index.php"
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
    // sync to real input
    const dt = new DataTransfer();
    dt.items.add(file);
    fileInput.files = dt.files;
}
</script>

<?php
$page_content = ob_get_clean();
include dirname(__DIR__) . '/admin_layout.php';
