<?php
$page_title = 'Contact – Food Fusion';
$meta_description = 'Get in touch or make a reservation at Food Fusion.';
require_once dirname(__DIR__) . '/includes/header.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $guests  = (int)($_POST['guests'] ?? 0);
    $date    = trim($_POST['date'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name))              $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($guests < 1 || $guests > 20) $errors[] = 'Number of guests must be between 1 and 20.';
    if (empty($date))              $errors[] = 'Preferred date is required.';

    if (empty($errors)) {
        // TODO: send email / save to database
        $success = true;
    }
}
?>

<div class="max-w-5xl mx-auto px-4 py-16">
    <div class="text-center mb-14">
        <h1 class="text-4xl font-bold text-gray-900 mb-3">Contact & Reservations</h1>
        <p class="text-gray-500 max-w-xl mx-auto">Have a question or want to book a table? We'd love to hear from you.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Form -->
        <div>
            <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-5 mb-6">
                ✅ Thank you! Your reservation request has been received. We'll confirm shortly.
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
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
                            placeholder="+1 555 000 0000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guests *</label>
                        <input type="number" name="guests" min="1" max="20" value="<?= htmlspecialchars($_POST['guests'] ?? '') ?>"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
                            placeholder="2">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Date *</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($_POST['date'] ?? '') ?>"
                        min="<?= date('Y-m-d') ?>"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="message" rows="4"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition resize-none"
                        placeholder="Any dietary requirements or special requests?"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-orange-500 text-white font-semibold py-3 rounded-xl hover:bg-orange-600 transition text-sm">
                    Send Reservation Request
                </button>
            </form>
        </div>

        <!-- Info -->
        <div class="space-y-8">
            <div class="bg-orange-50 rounded-2xl p-6">
                <h3 class="font-bold text-gray-900 mb-4 text-lg">Visit Us</h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <p>📍 123 Flavor Street, Food City, FC 10001</p>
                    <p>📞 +1 (555) 123-4567</p>
                    <p>✉️ hello@foodfusion.com</p>
                </div>
            </div>
            <div class="bg-orange-50 rounded-2xl p-6">
                <h3 class="font-bold text-gray-900 mb-4 text-lg">Opening Hours</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between"><span>Monday – Friday</span><span class="font-medium">11:00 – 22:00</span></div>
                    <div class="flex justify-between"><span>Saturday</span><span class="font-medium">10:00 – 23:00</span></div>
                    <div class="flex justify-between"><span>Sunday</span><span class="font-medium">10:00 – 21:00</span></div>
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
