<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

$id = $_GET['id'] ?? '';

if ($id === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing video id.']);
    exit;
}

$result = openai_request('GET', '/videos/' . urlencode($id));

if (!$result['ok']) {
    http_response_code($result['status'] ?: 500);
    $msg = $result['data']['error']['message'] ?? 'Could not fetch status.';
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

echo json_encode(['ok' => true, 'video' => $result['data']]);
