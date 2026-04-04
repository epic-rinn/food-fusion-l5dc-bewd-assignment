<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db.php';
require_once __DIR__ . '/session.php';

redirect_if_logged_in();

$errors  = [];
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name']  = trim($_POST['name']  ?? '');
    $old['email'] = trim($_POST['email'] ?? '');

    $name     = $old['name'];
    $email    = $old['email'];
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    if (empty($name) || strlen($name) < 2) {
        $errors['name'] = 'Name must be at least 2 characters.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Password must contain at least one letter and one number.';
    }

    if ($password !== $confirm) {
        $errors['password_confirm'] = 'Passwords do not match.';
    }

    if (!verify_recaptcha()) {
        $errors['recaptcha'] = 'Please complete the reCAPTCHA check.';
    }

    if (empty($errors)) {
        $pdo  = get_db();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors['email'] = 'That email address is already registered.';
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // First registered user becomes admin, everyone else is a regular user
        $count = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $role  = ($count == 0) ? 'admin' : 'user';

        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $email, $hashed_password, $role]);

        flash_set('success', 'Account created! You can now log in.');
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit;
    }
}

// ── View ───────────────────────────────────────────────────────────────────
$page_title = 'Create Account – ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'ui-sans-serif'] } } } }</script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-gradient-to-br from-orange-50 to-amber-100 min-h-screen flex items-center justify-center px-4 py-12 font-sans antialiased">

<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <a href="<?= SITE_URL ?>" class="text-3xl font-extrabold text-orange-500">🍽 <?= SITE_NAME ?></a>
        <p class="text-gray-500 mt-2 text-sm">Create your account</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Register</h1>

        <form method="POST" action="" novalidate class="space-y-5">

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input id="name" type="text" name="name"
                    value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                    class="w-full border <?= isset($errors['name']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
                    placeholder="Jane Smith" autocomplete="name">
                <?php if (isset($errors['name'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['name']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input id="email" type="email" name="email"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    class="w-full border <?= isset($errors['email']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
                    placeholder="jane@example.com" autocomplete="email">
                <?php if (isset($errors['email'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password"
                        class="w-full border <?= isset($errors['password']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition pr-10"
                        placeholder="Min. 8 chars, letters & numbers" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password', this)"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" aria-label="Toggle password">
                        <!-- Eye icon (show) -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 eye-show" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <!-- Eye-off icon (hide) -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 eye-hide hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.563-4.568M6.228 6.228A9.97 9.97 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.51 5.22M6.228 6.228L3 3m3.228 3.228l3.65 3.65M17.772 17.772L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88"/>
                        </svg>
                    </button>
                </div>
                <?php if (isset($errors['password'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
                <!-- Strength indicator -->
                <div id="strength-bar" class="mt-2 h-1 rounded-full bg-gray-200 overflow-hidden hidden">
                    <div id="strength-fill" class="h-full transition-all duration-300 rounded-full" style="width:0%"></div>
                </div>
                <p id="strength-label" class="text-xs mt-1 text-gray-400"></p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <div class="relative">
                    <input id="password_confirm" type="password" name="password_confirm"
                        class="w-full border <?= isset($errors['password_confirm']) ? 'border-red-400' : 'border-gray-200' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition pr-10"
                        placeholder="Repeat your password" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password_confirm', this)"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" aria-label="Toggle password">
                        <!-- Eye icon (show) -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 eye-show" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <!-- Eye-off icon (hide) -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 eye-hide hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.563-4.568M6.228 6.228A9.97 9.97 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.51 5.22M6.228 6.228L3 3m3.228 3.228l3.65 3.65M17.772 17.772L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88"/>
                        </svg>
                    </button>
                </div>
                <?php if (isset($errors['password_confirm'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['password_confirm']) ?></p>
                <?php endif; ?>
            </div>

            <!-- reCAPTCHA -->
            <div>
                <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
                <?php if (isset($errors['recaptcha'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['recaptcha']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm mt-2">
                Create Account
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Already have an account?
            <a href="<?= SITE_URL ?>/auth/login.php" class="text-orange-500 font-medium hover:underline">Sign in</a>
        </p>
    </div>

    <p class="text-center mt-6 text-xs text-gray-400">
        <a href="<?= SITE_URL ?>" class="hover:text-orange-400">← Back to site</a>
    </p>
</div>

<script>
function togglePassword(id, btn) {
    const input   = document.getElementById(id);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.querySelector('.eye-show').classList.toggle('hidden', isHidden);
    btn.querySelector('.eye-hide').classList.toggle('hidden', !isHidden);
}

// Live password strength meter
document.getElementById('password').addEventListener('input', function () {
    const val = this.value;
    const bar  = document.getElementById('strength-bar');
    const fill = document.getElementById('strength-fill');
    const label = document.getElementById('strength-label');

    if (!val) { bar.classList.add('hidden'); label.textContent = ''; return; }
    bar.classList.remove('hidden');

    let score = 0;
    if (val.length >= 8)                          score++;
    if (/[A-Z]/.test(val))                        score++;
    if (/[0-9]/.test(val))                        score++;
    if (/[^A-Za-z0-9]/.test(val))                score++;
    if (val.length >= 12)                         score++;

    const levels = [
        { pct: '20%',  color: 'bg-red-500',    text: 'Very Weak' },
        { pct: '40%',  color: 'bg-orange-400', text: 'Weak' },
        { pct: '60%',  color: 'bg-yellow-400', text: 'Fair' },
        { pct: '80%',  color: 'bg-blue-400',   text: 'Strong' },
        { pct: '100%', color: 'bg-green-500',  text: 'Very Strong' },
    ];

    const idx = Math.min(score, levels.length) - 1;
    const lvl = levels[Math.max(idx, 0)];
    fill.style.width = lvl.pct;
    fill.className = 'h-full transition-all duration-300 rounded-full ' + lvl.color;
    label.textContent = lvl.text;
    label.className = 'text-xs mt-1 ' + lvl.color.replace('bg-', 'text-');
});
</script>
</body>
</html>
