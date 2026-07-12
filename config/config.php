<?php
/**
 * Sora Studio — server-side config
 * -----------------------------------------------------------
 * IMPORTANT: This file must NEVER be served directly or committed
 * to a public repo. Keep it outside your web root if possible,
 * or add a rule in .htaccess (see storage/.htaccess) to block it.
 */

// Paste your OpenAI API key below (starts with "sk-...")
define('OPENAI_API_KEY', 'PASTE_YOUR_OPENAI_API_KEY_HERE');

// Base URL for OpenAI's REST API
define('OPENAI_API_BASE', 'https://api.openai.com/v1');

// Default model — "sora-2" (faster/cheaper) or "sora-2-pro" (higher quality)
define('DEFAULT_MODEL', 'sora-2');

// Where completed videos get cached locally after download (optional)
define('STORAGE_DIR', __DIR__ . '/../storage');

/**
 * Shared cURL helper for talking to the OpenAI API.
 * $method: GET | POST
 * $path:   e.g. "/videos" or "/videos/{id}"
 * $body:   assoc array (will be JSON-encoded) or null
 * $raw:    if true, returns raw binary body instead of decoded JSON
 */
function openai_request($method, $path, $body = null, $raw = false) {
    $ch = curl_init(OPENAI_API_BASE . $path);

    $headers = [
        'Authorization: Bearer ' . OPENAI_API_KEY,
    ];

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['ok' => false, 'status' => 0, 'error' => $error];
    }

    if ($raw) {
        return ['ok' => $httpCode >= 200 && $httpCode < 300, 'status' => $httpCode, 'raw' => $response];
    }

    $decoded = json_decode($response, true);
    return [
        'ok' => $httpCode >= 200 && $httpCode < 300,
        'status' => $httpCode,
        'data' => $decoded,
    ];
}
