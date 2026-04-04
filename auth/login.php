<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db.php';
require_once __DIR__ . '/session.php';

redirect_if_logged_in();

define('MAX_FAILED_ATTEMPTS',      5);   // lock after 5 wrong passwords
define('LOCKOUT_DURATION_MINUTES', 15);  // locked for 15 minutes

$error   = null;
$old_email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $old_email = $email;

    // reCAPTCHA check
    if (!verify_recaptcha()) {
        $error = 'Please complete the reCAPTCHA check.';
    }

    // Basic input check
    if (empty($error) && (empty($email) || empty($password))) {
        $error = 'Please enter your email and password.';
    } else {
        $pdo  = get_db();

        // Look up the user by email
        $stmt = $pdo->prepare(
            'SELECT id, name, email, password, role, failed_attempts, locked_until
             FROM users
             WHERE email = ?
             LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            /*
             * User not found – return a generic message so we don't reveal
             * which emails are registered (user-enumeration prevention).
             */
            $error = 'Invalid email or password.';

        } else {
            // ── ACCOUNT LOCKOUT CHECK ──────────────────────────────────────
            $now = new DateTimeImmutable();

            if ($user['locked_until'] !== null) {
                $locked_until = new DateTimeImmutable($user['locked_until']);

                if ($now < $locked_until) {
                    // Account is still locked – tell user how long remains
                    $remaining_seconds = $locked_until->getTimestamp() - $now->getTimestamp();
                    $remaining_minutes = ceil($remaining_seconds / 60);

                    $error = sprintf(
                        'Your account is temporarily locked due to too many failed login attempts. '
                        . 'Please try again in %d minute%s.',
                        $remaining_minutes,
                        $remaining_minutes === 1 ? '' : 's'
                    );

                    // Skip password verification entirely while locked
                    $user = null;
                } else {
                    // Lock has expired – automatically reset the counters
                    $pdo->prepare(
                        'UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?'
                    )->execute([$user['id']]);

                    $user['failed_attempts'] = 0;
                    $user['locked_until']    = null;
                }
            }

            if ($user !== null) {
                if (password_verify($password, $user['password'])) {

                    $pdo->prepare(
                        'UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?'
                    )->execute([$user['id']]);

                    session_regenerate_id(true);

                    $_SESSION['user_id']    = $user['id'];
                    $_SESSION['user_name']  = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role']  = $user['role'];

                    flash_set('success', 'Welcome back, ' . $user['name'] . '!');

                    // Admin → dashboard, regular user → home
                    if ($user['role'] === 'admin') {
                        header('Location: ' . SITE_URL . '/auth/dashboard.php');
                    } else {
                        header('Location: ' . SITE_URL . '/index.php');
                    }
                    exit;

                } else {
                    $new_attempts = $user['failed_attempts'] + 1;

                    if ($new_attempts >= MAX_FAILED_ATTEMPTS) {
                        $locked_until = (new DateTimeImmutable())
                            ->modify('+' . LOCKOUT_DURATION_MINUTES . ' minutes')
                            ->format('Y-m-d H:i:s');

                        $pdo->prepare(
                            'UPDATE users
                             SET failed_attempts = ?, locked_until = ?
                             WHERE id = ?'
                        )->execute([$new_attempts, $locked_until, $user['id']]);

                        $error = sprintf(
                            'Too many failed attempts. Your account has been locked for %d minutes.',
                            LOCKOUT_DURATION_MINUTES
                        );

                    } else {
                        $pdo->prepare(
                            'UPDATE users SET failed_attempts = ? WHERE id = ?'
                        )->execute([$new_attempts, $user['id']]);

                        $remaining = MAX_FAILED_ATTEMPTS - $new_attempts;
                        $error = sprintf(
                            'Invalid email or password. %d attempt%s remaining before lockout.',
                            $remaining,
                            $remaining === 1 ? '' : 's'
                        );
                    }
                }
            }
        }
    }
}

$flash_success = flash_get('success');

$page_title = 'Sign In – ' . SITE_NAME;
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
        <p class="text-gray-500 mt-2 text-sm">Sign in to your account</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Login</h1>

        <!-- Flash: registration success -->
        <?php if ($flash_success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm mb-5 flex items-center gap-2">
            <span>✅</span> <?= htmlspecialchars($flash_success) ?>
        </div>
        <?php endif; ?>

        <!-- Error message -->
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm mb-5 flex items-start gap-2">
            <span class="mt-0.5">🔒</span>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate class="space-y-5">

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input id="email" type="email" name="email"
                    value="<?= htmlspecialchars($old_email) ?>"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
                    placeholder="jane@example.com" autocomplete="email" required>
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition pr-10"
                        placeholder="Your password" autocomplete="current-password" required>
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
            </div>

            <!-- reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm mt-2">
                Sign In
            </button>
        </form>

        <!-- Lockout info box – only shown after a failed attempt -->
        <?php if ($error): ?>
        <div class="mt-6 bg-gray-50 border border-gray-100 rounded-xl p-4 text-xs text-gray-500">
            <p class="font-semibold text-gray-700 mb-1">⚠️ Account Security</p>
            <p>After <strong><?= MAX_FAILED_ATTEMPTS ?> failed attempts</strong>, your account will be locked
               for <strong><?= LOCKOUT_DURATION_MINUTES ?> minutes</strong> to protect against unauthorized access.</p>
        </div>
        <?php endif; ?>

        <p class="text-center text-sm text-gray-500 mt-6">
            Don't have an account?
            <a href="<?= SITE_URL ?>/auth/register.php" class="text-orange-500 font-medium hover:underline">Create one</a>
        </p>
    </div>

    <p class="text-center mt-6 text-xs text-gray-400">
        <a href="<?= SITE_URL ?>" class="hover:text-orange-400">← Back to site</a>
    </p>
</div>

<script>
function togglePassword(id, btn) {
    const input    = document.getElementById(id);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.querySelector('.eye-show').classList.toggle('hidden', isHidden);
    btn.querySelector('.eye-hide').classList.toggle('hidden', !isHidden);
}
</script>
</body>
</html>
