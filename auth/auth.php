<?php
// Number Gear — Auth Helper
// Handles registration, login, sessions, and progress tracking.
// Backed by MySQL (see config/db.php and database/schema.sql).

require_once __DIR__ . '/../config/db.php';

define('NG_ROLES',          ['learner', 'instructor', 'admin']);
define('NG_LEARNING_MODES', ['institution', 'self_paced']);

/* ================================================================
   SESSION
================================================================ */
function ng_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('ng_session');
        session_set_cookie_params([
            'lifetime' => 86400 * 7,   // 7 days
            'path'     => '/',
            'secure'   => false,       // set true on HTTPS
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function ng_current_user(): ?array {
    ng_session_start();
    return $_SESSION['ng_user'] ?? null;
}

function ng_is_logged_in(): bool {
    return ng_current_user() !== null;
}

function ng_logout(): void {
    ng_session_start();
    $_SESSION = [];
    session_destroy();
}

function ng_require_login(string $redirect = '/auth/login.php'): void {
    if (!ng_is_logged_in()) {
        header('Location: ' . $redirect);
        exit;
    }
}

// Restrict a page to one or more roles (e.g. instructor dashboard).
function ng_require_role(array $roles, string $redirect = '../index.php'): array {
    $user = ng_current_user();
    if (!$user || !in_array($user['role'], $roles, true)) {
        header('Location: ' . $redirect);
        exit;
    }
    return $user;
}

/* ================================================================
   USER STORE (MySQL)
================================================================ */
function ng_find_user_by_email(string $email): ?array {
    $stmt = ng_db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([strtolower(trim($email))]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function ng_find_user_by_id($id): ?array {
    $stmt = ng_db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    return $user ?: null;
}

/* ================================================================
   REGISTER
================================================================ */
function ng_register(
    string $name,
    string $email,
    string $password,
    string $role,
    string $learningMode = 'self_paced',
    string $institutionName = '',
    ?int $instructorId = null
): array {
    $name             = trim($name);
    $email            = trim($email);
    $role             = trim($role);
    $learningMode     = trim($learningMode);
    $institutionName  = trim($institutionName);

    // Validate
    if (empty($name))                              return ['ok' => false, 'error' => 'Please enter your name.'];
    if (strlen($name) < 2)                         return ['ok' => false, 'error' => 'Name must be at least 2 characters.'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ['ok' => false, 'error' => 'Please enter a valid email address.'];
    if (strlen($password) < 6)                     return ['ok' => false, 'error' => 'Password must be at least 6 characters.'];
    if (!in_array($role, NG_ROLES, true))          return ['ok' => false, 'error' => 'Please choose a valid role.'];
    if (!in_array($learningMode, NG_LEARNING_MODES, true)) {
        return ['ok' => false, 'error' => 'Please choose how you will be learning.'];
    }
    if ($learningMode === 'institution' && $institutionName === '') {
        return ['ok' => false, 'error' => 'Please enter the name of your institution.'];
    }
    if ($learningMode === 'self_paced') {
        $institutionName = ''; // ignore any stray value
        $instructorId    = null;
    }

    // Only learners get an instructor assignment, and only if that instructor
    // genuinely belongs to the same institution — otherwise silently drop it
    // rather than failing the whole registration (an admin/instructor can
    // assign or fix this later from the dashboard).
    if ($role !== 'learner' || $learningMode !== 'institution' || !$instructorId) {
        $instructorId = null;
    } else {
        $candidate = ng_find_user_by_id($instructorId);
        $matches = $candidate
            && $candidate['role'] === 'instructor'
            && strcasecmp(trim((string)$candidate['institution_name']), $institutionName) === 0;
        if (!$matches) $instructorId = null;
    }

    if (ng_find_user_by_email($email)) {
        return ['ok' => false, 'error' => 'An account with that email already exists.'];
    }

    $stmt = ng_db()->prepare(
        'INSERT INTO users (name, email, password, role, learning_mode, institution_name, instructor_id)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $name,
        strtolower($email),
        password_hash($password, PASSWORD_DEFAULT),
        $role,
        $learningMode,
        $institutionName !== '' ? $institutionName : null,
        $instructorId,
    ]);

    $user = ng_find_user_by_id(ng_db()->lastInsertId());
    return ['ok' => true, 'user' => ng_safe_user($user)];
}

/* ================================================================
   LOGIN
================================================================ */
function ng_login(string $email, string $password): array {
    $user = ng_find_user_by_email($email);
    if (!$user)                                          return ['ok' => false, 'error' => 'No account found with that email.'];
    if (!password_verify($password, $user['password']))  return ['ok' => false, 'error' => 'Incorrect password. Please try again.'];

    ng_session_start();
    $_SESSION['ng_user'] = ng_safe_user($user);

    return ['ok' => true, 'user' => ng_safe_user($user)];
}

/* ================================================================
   PROGRESS TRACKING
================================================================ */

// Save / update a learner's progress for one level (1-7).
function ng_save_progress(int $userId, int $level, int $score, ?array $details = null): bool {
    if ($level < 1 || $level > 7)   return false;
    $score = max(0, min(100, $score));

    $stmt = ng_db()->prepare(
        'INSERT INTO progress (user_id, level, score, details)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            score      = GREATEST(score, VALUES(score)),
            details    = VALUES(details),
            updated_at = CURRENT_TIMESTAMP'
    );
    return $stmt->execute([
        $userId,
        $level,
        $score,
        $details !== null ? json_encode($details) : null,
    ]);
}

// Get one user's progress, keyed by level (1-7).
function ng_get_user_progress(int $userId): array {
    $stmt = ng_db()->prepare('SELECT level, score, details, updated_at FROM progress WHERE user_id = ?');
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll();

    $byLevel = [];
    foreach ($rows as $r) {
        $byLevel[(int)$r['level']] = $r;
    }
    return $byLevel;
}

// Instructor/admin dashboard: every learner with their progress on all 7 levels.
// $institutionFilter: pass an institution name to narrow the list, or null for everyone.
// $instructorFilter:  pass an instructor's user id to see only THEIR assigned learners.
function ng_get_all_learners_with_progress(?string $institutionFilter = null, ?int $instructorFilter = null): array {
    $sql = 'SELECT u.id, u.name, u.email, u.learning_mode, u.institution_name, u.instructor_id, u.created_at,
                   i.name AS instructor_name
            FROM users u
            LEFT JOIN users i ON i.id = u.instructor_id
            WHERE u.role = "learner"';
    $params = [];
    if ($institutionFilter !== null && $institutionFilter !== '') {
        $sql .= ' AND u.institution_name = ?';
        $params[] = $institutionFilter;
    }
    if ($instructorFilter !== null) {
        $sql .= ' AND u.instructor_id = ?';
        $params[] = $instructorFilter;
    }
    $sql .= ' ORDER BY u.name ASC';

    $stmt = ng_db()->prepare($sql);
    $stmt->execute($params);
    $learners = $stmt->fetchAll();

    if (!$learners) return [];

    $ids = array_column($learners, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $pstmt = ng_db()->prepare("SELECT * FROM progress WHERE user_id IN ($placeholders)");
    $pstmt->execute($ids);
    $allProgress = $pstmt->fetchAll();

    $progressByUser = [];
    foreach ($allProgress as $p) {
        $progressByUser[$p['user_id']][(int)$p['level']] = $p;
    }

    foreach ($learners as &$learner) {
        $levels = $progressByUser[$learner['id']] ?? [];
        $learner['levels'] = $levels;

        $scores = [];
        for ($lvl = 1; $lvl <= 7; $lvl++) {
            $scores[] = isset($levels[$lvl]) ? (int)$levels[$lvl]['score'] : 0;
        }
        $learner['overall'] = (int) round(array_sum($scores) / count($scores));

        $lastActive = null;
        foreach ($levels as $lvl) {
            if ($lastActive === null || $lvl['updated_at'] > $lastActive) {
                $lastActive = $lvl['updated_at'];
            }
        }
        $learner['last_active'] = $lastActive;
    }

    return $learners;
}

// Institution-mode learners at a given institution who have no instructor yet
// (e.g. they registered before any instructor for that institution existed).
function ng_get_unassigned_learners(string $institutionName): array {
    $stmt = ng_db()->prepare(
        'SELECT id, name, email, created_at FROM users
         WHERE role = "learner" AND learning_mode = "institution"
           AND institution_name = ? AND instructor_id IS NULL
         ORDER BY name ASC'
    );
    $stmt->execute([$institutionName]);
    return $stmt->fetchAll();
}

// Instructors who belong to a given institution (used to populate the
// "Choose your instructor" dropdown on the registration page).
function ng_list_instructors_for_institution(string $institutionName): array {
    $stmt = ng_db()->prepare(
        'SELECT id, name FROM users
         WHERE role = "instructor" AND institution_name = ?
         ORDER BY name ASC'
    );
    $stmt->execute([$institutionName]);
    return $stmt->fetchAll();
}

// Every instructor (used for the admin dashboard's reassignment dropdown).
function ng_list_all_instructors(): array {
    $stmt = ng_db()->query(
        'SELECT id, name, institution_name FROM users WHERE role = "instructor" ORDER BY name ASC'
    );
    return $stmt->fetchAll();
}

// Assign (or unassign, if $instructorId is null) a learner to an instructor.
// $actingUser is whoever is making the change — enforces who's allowed to do what:
//   - admin:      can assign any learner to any instructor.
//   - instructor: can only claim a learner that is currently unassigned and
//                 shares their own institution (prevents poaching another
//                 instructor's class).
function ng_assign_instructor(int $learnerId, ?int $instructorId, array $actingUser): array {
    $learner = ng_find_user_by_id($learnerId);
    if (!$learner || $learner['role'] !== 'learner') {
        return ['ok' => false, 'error' => 'Learner not found.'];
    }

    if ($instructorId !== null) {
        $instructor = ng_find_user_by_id($instructorId);
        if (!$instructor || $instructor['role'] !== 'instructor') {
            return ['ok' => false, 'error' => 'Instructor not found.'];
        }
    }

    if ($actingUser['role'] === 'admin') {
        // allowed unconditionally
    } elseif ($actingUser['role'] === 'instructor') {
        if ($instructorId !== (int) $actingUser['id']) {
            return ['ok' => false, 'error' => 'Instructors can only assign learners to themselves.'];
        }
        if ($learner['instructor_id'] !== null) {
            return ['ok' => false, 'error' => 'This learner is already assigned to an instructor.'];
        }
        if (strcasecmp(trim((string)$learner['institution_name']), trim((string)$actingUser['institution_name'])) !== 0) {
            return ['ok' => false, 'error' => 'This learner belongs to a different institution.'];
        }
    } else {
        return ['ok' => false, 'error' => 'Not allowed.'];
    }

    $stmt = ng_db()->prepare('UPDATE users SET instructor_id = ? WHERE id = ?');
    $stmt->execute([$instructorId, $learnerId]);
    return ['ok' => true];
}

// Distinct institution names currently in use (for dashboard filter dropdown).
function ng_list_institutions(): array {
    $stmt = ng_db()->query(
        'SELECT DISTINCT institution_name FROM users
         WHERE institution_name IS NOT NULL AND institution_name <> ""
         ORDER BY institution_name ASC'
    );
    return array_column($stmt->fetchAll(), 'institution_name');
}

/* ================================================================
   HELPERS
================================================================ */
// Return user without the password hash
function ng_safe_user(array $user): array {
    $safe = $user;
    unset($safe['password']);
    return $safe;
}

function ng_role_label(string $role): string {
    return match($role) {
        'learner'    => '🎓 Learner',
        'instructor' => '📚 Instructor',
        'admin'      => '⚙️ Admin',
        default      => $role,
    };
}

function ng_role_color(string $role): string {
    return match($role) {
        'learner'    => 'var(--mint)',
        'instructor' => 'var(--sky)',
        'admin'      => 'var(--purple)',
        default      => 'var(--border)',
    };
}

function ng_learning_mode_label(?string $mode): string {
    return match($mode) {
        'institution' => '🏫 Institution',
        'self_paced'  => '🚀 Self-paced',
        default       => '—',
    };
}
