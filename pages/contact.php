<?php
$page_title = 'Contact Us – Food Fusion';
$meta_description = 'Get in touch with the Food Fusion team — we\'d love to hear your feedback, questions, or ideas.';
require_once dirname(__DIR__) . '/includes/header.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name))    $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (empty($subject)) $errors[] = 'Please select a subject.';
    if (empty($message)) $errors[] = 'Message is required.';

    if (empty($errors)) {
        // TODO: send email / save to database
        $success = true;
    }
}
?>

<div class="max-w-5xl mx-auto px-4 py-16">
    <div class="text-center mb-14">
        <h1 class="text-4xl font-bold text-gray-900 mb-3">Contact Us</h1>
        <p class="text-gray-500 max-w-xl mx-auto">Have a question, suggestion, or just want to say hello? We'd love to hear from you.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Form -->
        <div>
            <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-5 mb-6">
                Thank you for reaching out! We've received your message and will get back to you soon.
            </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-5 mb-6">
                <ul class="list-disc list-inside space-y-1 text-sm">
                    <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
                        placeholder="Jane Smith">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
                        placeholder="jane@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                    <select name="subject"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition bg-white">
                        <option value="">-- Select a subject --</option>
                        <option value="General Inquiry" <?= ($_POST['subject'] ?? '') === 'General Inquiry' ? 'selected' : '' ?>>General Inquiry</option>
                        <option value="Feedback" <?= ($_POST['subject'] ?? '') === 'Feedback' ? 'selected' : '' ?>>Feedback & Suggestions</option>
                        <option value="Recipe Submission" <?= ($_POST['subject'] ?? '') === 'Recipe Submission' ? 'selected' : '' ?>>Recipe / Cookbook Help</option>
                        <option value="Bug Report" <?= ($_POST['subject'] ?? '') === 'Bug Report' ? 'selected' : '' ?>>Report a Problem</option>
                        <option value="Other" <?= ($_POST['subject'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                    <textarea name="message" rows="5"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition resize-none"
                        placeholder="Tell us what's on your mind…"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-orange-500 text-white font-semibold py-3 rounded-xl hover:bg-orange-600 transition text-sm">
                    Send Message
                </button>
            </form>
        </div>

        <!-- Info -->
        <div class="space-y-8">
            <div class="bg-orange-50 rounded-2xl p-6">
                <h3 class="font-bold text-gray-900 mb-4 text-lg">Get In Touch</h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <p>✉️ support@foodfusion.com</p>
                    <p>💬 We typically respond within 24 hours</p>
                </div>
            </div>
            <div class="bg-orange-50 rounded-2xl p-6">
                <h3 class="font-bold text-gray-900 mb-4 text-lg">Quick Links</h3>
                <div class="space-y-2 text-sm">
                    <a href="<?= SITE_URL ?>/pages/menu.php" class="block text-gray-600 hover:text-orange-500 transition">📖 Browse Recipe Collection</a>
                    <a href="<?= SITE_URL ?>/pages/community-cookbook.php" class="block text-gray-600 hover:text-orange-500 transition">👨‍🍳 Community Cookbook</a>
                    <a href="<?= SITE_URL ?>/pages/culinary-resources.php" class="block text-gray-600 hover:text-orange-500 transition">🍳 Culinary Resources</a>
                    <a href="<?= SITE_URL ?>/pages/educational-resources.php" class="block text-gray-600 hover:text-orange-500 transition">🎓 Educational Resources</a>
                </div>
            </div>
            <div class="bg-orange-50 rounded-2xl p-6">
                <h3 class="font-bold text-gray-900 mb-4 text-lg">Follow Us</h3>
                <div class="flex gap-4">
                    <a href="#" class="text-orange-500 hover:text-orange-600 transition text-sm font-medium">Instagram</a>
                    <a href="#" class="text-orange-500 hover:text-orange-600 transition text-sm font-medium">Facebook</a>
                    <a href="#" class="text-orange-500 hover:text-orange-600 transition text-sm font-medium">TikTok</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
