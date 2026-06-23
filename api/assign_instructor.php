<?php
// Number Gear — Assign a learner to an instructor (AJAX)
// Used by the instructor dashboard (instructors claiming unassigned
// learners at their own institution, and admins reassigning anyone).

require_once __DIR__ . '/../auth/auth.php';

header('Content-Type: application/json');
ng_session_start();

$actingUser = ng_current_user();
if (!$actingUser || !in_array($actingUser['role'], ['instructor', 'admin'], true)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Not allowed.']);
    exit;
}

$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!is_array($body) || !isset($body['learner_id'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid request body.']);
    exit;
}

$learnerId    = (int) $body['learner_id'];
$instructorId = isset($body['instructor_id']) && $body['instructor_id'] !== '' && $body['instructor_id'] !== null
    ? (int) $body['instructor_id']
    : null;

$result = ng_assign_instructor($learnerId, $instructorId, $actingUser);

if (!$result['ok']) {
    http_response_code(422);
}
echo json_encode($result);
