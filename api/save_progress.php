<?php
// Number Gear — Save Progress (AJAX endpoint)
// Called from assets/js/storage.js whenever a learner's score changes.
// Always trusts the SESSION for the user id — never a client-supplied id.

require_once __DIR__ . '/../auth/auth.php';

header('Content-Type: application/json');
ng_session_start();

$user = ng_current_user();
if (!$user) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Not logged in.']);
    exit;
}

$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid request body.']);
    exit;
}

$level   = isset($body['level']) ? (int) $body['level'] : 0;
$score   = isset($body['score']) ? (int) $body['score'] : 0;
$details = isset($body['details']) && is_array($body['details']) ? $body['details'] : null;

if ($level < 1 || $level > 7) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Level must be between 1 and 7.']);
    exit;
}

$saved = ng_save_progress((int) $user['id'], $level, $score, $details);

echo json_encode(['ok' => (bool) $saved]);
