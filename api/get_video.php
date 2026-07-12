<?php
require_once __DIR__ . '/../config/config.php';

$id = $_GET['id'] ?? '';

if ($id === '') {
    http_response_code(400);
    exit('Missing video id.');
}

if (!is_dir(STORAGE_DIR)) {
    mkdir(STORAGE_DIR, 0755, true);
}

$cachePath = STORAGE_DIR . '/' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $id) . '.mp4';

// Serve from local cache if we already downloaded this one
if (file_exists($cachePath)) {
    header('Content-Type: video/mp4');
    header('Content-Length: ' . filesize($cachePath));
    readfile($cachePath);
    exit;
}

$result = openai_request('GET', '/videos/' . urlencode($id) . '/content', null, true);

if (!$result['ok']) {
    http_response_code($result['status'] ?: 500);
    exit('Could not fetch video content.');
}

file_put_contents($cachePath, $result['raw']);

header('Content-Type: video/mp4');
header('Content-Length: ' . strlen($result['raw']));
echo $result['raw'];
