<?php
$page_title       = 'Privacy Policy – ' . 'Food Fusion';
$meta_description = 'Read the Food Fusion Privacy Policy, Cookie Policy, and Terms of Service.';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="max-w-3xl mx-auto px-4 py-16">

    <!-- Heading -->
    <div class="mb-10">
        <span class="text-xs font-semibold text-orange-500 uppercase tracking-widest">Legal</span>
        <h1 class="text-4xl font-extrabold text-gray-900 mt-2">Privacy Policy</h1>
        <p class="text-gray-400 text-sm mt-2">Last updated: <?= date('F j, Y') ?></p>
    </div>

    <div class="prose prose-gray max-w-none space-y-10 text-sm text-gray-600 leading-relaxed">

        <!-- Intro -->
        <section>
            <p>
                Welcome to <strong>Food Fusion</strong> ("we", "us", "our"). We are committed to protecting
                your personal information and your right to privacy. This Privacy Policy explains what
                information we collect, how we use it, and what rights you have in relation to it.
            </p>
            <p class="mt-3">
                By using our website at
                <a href="<?= SITE_URL ?>" class="text-orange-500 hover:underline"><?= SITE_URL ?></a>
                you agree to the collection and use of information in accordance with this policy.
            </p>
        </section>

        <!-- 1 Information we collect -->
        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">1. Information We Collect</h2>
            <p>We collect information you provide directly when you:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>Create an account (name, email address, password)</li>
                <li>Contact us through our contact form</li>
                <li>Interact with our content (e.g. saving or sharing recipes)</li>
            </ul>
            <p class="mt-3">We also automatically collect certain technical data when you visit our site, including:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>IP address and browser type</li>
                <li>Pages visited and time spent on the site</li>
                <li>Referring URLs</li>
                <li>Cookie and session identifiers</li>
            </ul>
        </section>

        <!-- 2 How we use your information -->
        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">2. How We Use Your Information</h2>
            <p>We use the information we collect to:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>Provide, operate, and maintain our website</li>
                <li>Manage your account and authenticate your identity</li>
                <li>Send you account-related notifications</li>
                <li>Respond to your enquiries and support requests</li>
                <li>Improve and personalise your experience</li>
                <li>Monitor for security issues (e.g. brute-force login attempts)</li>
                <li>Comply with legal obligations</li>
            </ul>
            <p class="mt-3">
                We do <strong>not</strong> sell, rent, or share your personal data with third parties
                for marketing purposes.
            </p>
        </section>

        <!-- 3 Password & Security -->
        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">3. Account Security</h2>
            <p>
                Your password is hashed using <strong>BCrypt</strong> with a cost factor of 12 before
                storage. We never store plain-text passwords. Sessions use <code>HttpOnly</code> and
                <code>SameSite=Strict</code> cookies and are regenerated on login to prevent session
                fixation attacks.
            </p>
            <p class="mt-2">
                After <strong>5 consecutive failed login attempts</strong>, your account is temporarily
                locked for 15 minutes to protect against brute-force attacks.
            </p>
        </section>

        <!-- 4 Cookies -->
        <section id="cookies">
            <h2 class="text-lg font-bold text-gray-900 mb-3">4. Cookie Policy</h2>
            <p>
                We use cookies — small text files stored on your device — to operate and improve our service.
            </p>
            <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-2 font-semibold text-gray-700">Cookie</th>
                            <th class="text-left px-4 py-2 font-semibold text-gray-700">Purpose</th>
                            <th class="text-left px-4 py-2 font-semibold text-gray-700">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr>
                            <td class="px-4 py-2 font-mono">PHPSESSID</td>
                            <td class="px-4 py-2">Session authentication (essential)</td>
                            <td class="px-4 py-2">Session</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-mono">ff_cookie_consent</td>
                            <td class="px-4 py-2">Stores your cookie preference</td>
                            <td class="px-4 py-2">1 year (localStorage)</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-mono">_ga, _gid</td>
                            <td class="px-4 py-2">Analytics (only if accepted)</td>
                            <td class="px-4 py-2">Up to 2 years</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="mt-3">
                You can withdraw cookie consent at any time by clearing your browser's localStorage and cookies.
                Declining optional cookies will not affect your ability to use the site.
            </p>
        </section>

        <!-- 5 Third-party services -->
        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">5. Third-Party Services</h2>
            <ul class="list-disc pl-5 space-y-1">
                <li>
                    <strong>Google reCAPTCHA</strong> — used on registration and login forms to prevent
                    automated abuse. Subject to Google's
                    <a href="https://policies.google.com/privacy" target="_blank" rel="noopener"
                       class="text-orange-500 hover:underline">Privacy Policy</a>.
                </li>
                <li>
                    <strong>Google Fonts</strong> — fonts are loaded from Google's CDN. Google may collect
                    basic usage data.
                </li>
            </ul>
        </section>

        <!-- 6 Data retention -->
        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">6. Data Retention</h2>
            <p>
                We retain your account data for as long as your account is active. You may request deletion
                of your account and associated data by contacting us at
                <a href="mailto:hello@foodfusion.com" class="text-orange-500 hover:underline">hello@foodfusion.com</a>.
            </p>
        </section>

        <!-- 7 Your rights -->
        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">7. Your Rights</h2>
            <p>Depending on your location, you may have the right to:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>Access the personal data we hold about you</li>
                <li>Request correction of inaccurate data</li>
                <li>Request deletion of your data ("right to be forgotten")</li>
                <li>Withdraw consent at any time (where processing is based on consent)</li>
                <li>Lodge a complaint with your local data protection authority</li>
            </ul>
            <p class="mt-2">To exercise any of these rights, please email us at
               <a href="mailto:hello@foodfusion.com" class="text-orange-500 hover:underline">hello@foodfusion.com</a>.
            </p>
        </section>

        <!-- 8 Terms of service -->
        <section id="terms">
            <h2 class="text-lg font-bold text-gray-900 mb-3">8. Terms of Service</h2>
            <p>
                By accessing Food Fusion you agree to use the service for lawful purposes only. You must not:
            </p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>Attempt to gain unauthorised access to any part of the service</li>
                <li>Upload harmful, offensive, or infringing content</li>
                <li>Use automated tools to scrape, spam, or abuse the platform</li>
                <li>Impersonate another person or entity</li>
            </ul>
            <p class="mt-3">
                We reserve the right to suspend or terminate accounts that violate these terms without notice.
            </p>
        </section>

        <!-- 9 Changes -->
        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">9. Changes to This Policy</h2>
            <p>
                We may update this Privacy Policy from time to time. Changes will be posted on this page
                with an updated "Last updated" date. Continued use of the service after changes constitutes
                acceptance of the updated policy.
            </p>
        </section>

        <!-- 10 Contact -->
        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">10. Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please contact us:</p>
            <div class="mt-3 bg-orange-50 border border-orange-100 rounded-xl px-5 py-4 text-sm">
                <p><strong>Food Fusion</strong></p>
                <p class="mt-1">📍 123 Flavor Street, Food City</p>
                <p>📞 +1 (555) 123-4567</p>
                <p>✉️ <a href="mailto:hello@foodfusion.com" class="text-orange-500 hover:underline">hello@foodfusion.com</a></p>
            </div>
        </section>

    </div>

    <div class="mt-12 text-center">
        <a href="<?= SITE_URL ?>" class="text-sm text-gray-400 hover:text-orange-500 transition">← Back to Home</a>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
