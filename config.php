<?php
define('SITE_NAME',        'Food Fusion');
define('SITE_URL',         'http://localhost/food-fusion');
define('SITE_DESCRIPTION', 'A delicious experience awaits you.');

define('RECAPTCHA_SITE_KEY',   '6LeYOoIsAAAAAEN5kGed_Gmc-As-bSVxqF9XjWwZ');
define('RECAPTCHA_SECRET_KEY', '6LeYOoIsAAAAAEuRXbj0J6we6ShH6w2-23KIjcAm');

function verify_recaptcha(): bool {
    $token = $_POST['g-recaptcha-response'] ?? '';

    if (empty($token)) {
        return false;
    }

    $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,       // fail after 5 seconds, not infinity
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => [
            'secret'   => RECAPTCHA_SECRET_KEY,
            'response' => $token,
        ],
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return false; // cURL failed or timed out — don't block the user
    }

    $data = json_decode($response, true);

    return $data['success'] ?? false;
}

// Current page helper
function current_page(): string {
    return basename($_SERVER['PHP_SELF']);
}

function is_active(string $page): string {
    return current_page() === $page
        ? 'text-orange-500 font-semibold'
        : 'text-gray-600 hover:text-orange-500';
}
