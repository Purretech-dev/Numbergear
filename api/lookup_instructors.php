<?php
// Number Gear — Lookup instructors for a given institution (AJAX)
// Called from auth/register.php while a learner is typing their
// institution name, to populate the "Choose your instructor" dropdown.
// Public endpoint — only returns instructor names, nothing sensitive.

require_once __DIR__ . '/../auth/auth.php';

header('Content-Type: application/json');

$institution = trim($_GET['institution'] ?? '');

if ($institution === '') {
    echo json_encode(['instructors' => []]);
    exit;
}

$instructors = ng_list_instructors_for_institution($institution);

echo json_encode([
    'instructors' => array_map(fn($i) => ['id' => (int) $i['id'], 'name' => $i['name']], $instructors),
]);
