<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

$input = json_decode(file_get_contents('php://input'), true);

$prompt  = trim($input['prompt'] ?? '');
$seconds = $input['seconds'] ?? '8';
$size    = $input['size'] ?? '1280x720';
$model   = $input['model'] ?? DEFAULT_MODEL;

if ($prompt === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Prompt is empty. Write the scene first.']);
    exit;
}

if (strpos(OPENAI_API_KEY, 'PASTE_YOUR') !== false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Server has no API key configured yet. Add it to config/config.php.']);
    exit;
}

$result = openai_request('POST', '/videos', [
    'model'   => $model,
    'prompt'  => $prompt,
    'seconds' => (string) $seconds,
    'size'    => $size,
]);

if (!$result['ok']) {
    http_response_code($result['status'] ?: 500);
    $msg = $result['data']['error']['message'] ?? ($result['error'] ?? 'Could not start the generation.');
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

echo json_encode(['ok' => true, 'video' => $result['data']]);
