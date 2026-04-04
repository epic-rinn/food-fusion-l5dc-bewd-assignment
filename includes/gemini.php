<?php

/**
 * Helper: call Gemini generateContent and return the text reply.
 *
 * @return string|null  null on any failure
 */
function gemini_generate(string $prompt, int $max_tokens = 10): ?string
{
    $key = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
    if ($key === '') {
        error_log('[gemini] Skipped — GEMINI_API_KEY is empty');
        return null;
    }

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $key;

    $payload = [
        'contents' => [
            ['parts' => [['text' => $prompt]]],
        ],
        'generationConfig' => [
            'temperature'      => 0,
            'maxOutputTokens'  => $max_tokens,
            'thinkingConfig'   => ['thinkingBudget' => 0],
        ],
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($payload),
    ]);

    $response = curl_exec($ch);
    $err      = curl_error($ch);
    $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        error_log('[gemini] cURL failed: ' . $err);
        return null;
    }

    if ($http !== 200) {
        error_log('[gemini] HTTP ' . $http . ': ' . $response);
        return null;
    }

    $data = json_decode($response, true);
    return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
}

/**
 * Moderate text using Gemini. Returns flagged status or null on failure (fail-open).
 *
 * @return array{flagged: bool}|null
 */
function ai_moderate(string $text): ?array
{
    $prompt = "You are a content moderator. Analyze the following user comment and determine if it contains violence, harassment, hate speech, sexual content, spam, or other inappropriate content.\n\nReply with ONLY the word FLAGGED or OK, nothing else.\n\nComment: " . $text;

    $reply = gemini_generate($prompt, 10);

    if ($reply === null) {
        return null;
    }

    $reply = strtoupper(trim($reply));
    $flagged = str_contains($reply, 'FLAGGED');

    error_log('[ai_moderate] result=' . $reply . ' text=' . substr($text, 0, 80));

    return ['flagged' => $flagged];
}

/**
 * Score a cookbook's legitimacy using Gemini (0-100).
 *
 * @return int|null  Score 0-100, or null on failure (fail-open → stays pending)
 */
function ai_score_cookbook(
    string $name,
    string $description,
    string $country,
    string $cooking_type,
    ?string $tips
): ?int {
    $content = "Name: {$name}\nCountry: {$country}\nCooking Type: {$cooking_type}\nInstructions:\n{$description}";
    if ($tips) {
        $content .= "\nTips: {$tips}";
    }

    $prompt = "You are a cookbook reviewer. Rate the following cookbook submission from 0 to 100 based on whether it is a legitimate cooking recipe with real instructions. Reply with ONLY a single integer, nothing else.\n\n" . $content;

    $reply = gemini_generate($prompt, 10);

    if ($reply === null) {
        return null;
    }

    error_log('[ai_score_cookbook] reply=' . trim($reply) . ' name=' . $name);

    if (preg_match('/(\d+)/', $reply, $m)) {
        return min(100, max(0, (int)$m[1]));
    }

    return null;
}
