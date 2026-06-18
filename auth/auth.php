<?php
// Number Gear — Auth Helper
// Handles registration, login, sessions using flat JSON file storage
// (No database required — works on any PHP shared hosting)

define('NG_USERS_FILE', __DIR__ . '/../data/users.json');
define('NG_ROLES',      ['learner', 'instructor', 'admin']);

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

/* ================================================================
   USER STORE (JSON file)
================================================================ */
function ng_load_users(): array {
    if (!file_exists(NG_USERS_FILE)) return [];
    $raw = file_get_contents(NG_USERS_FILE);
    return json_decode($raw, true) ?: [];
}

function ng_save_users(array $users): void {
    file_put_contents(NG_USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

function ng_find_user_by_email(string $email): ?array {
    foreach (ng_load_users() as $u) {
        if (strtolower($u['email']) === strtolower(trim($email))) return $u;
    }
    return null;
}

function ng_find_user_by_id(string $id): ?array {
    foreach (ng_load_users() as $u) {
        if ($u['id'] === $id) return $u;
    }
    return null;
}

/* ================================================================
   REGISTER
================================================================ */
function ng_register(string $name, string $email, string $password, string $role): array {
    $name  = trim($name);
    $email = trim($email);
    $role  = trim($role);

    // Validate
    if (empty($name))                           return ['ok' => false, 'error' => 'Please enter your name.'];
    if (strlen($name) < 2)                      return ['ok' => false, 'error' => 'Name must be at least 2 characters.'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ['ok' => false, 'error' => 'Please enter a valid email address.'];
    if (strlen($password) < 6)                  return ['ok' => false, 'error' => 'Password must be at least 6 characters.'];
    if (!in_array($role, NG_ROLES, true))       return ['ok' => false, 'error' => 'Please choose a valid role.'];
    if (ng_find_user_by_email($email))           return ['ok' => false, 'error' => 'An account with that email already exists.'];

    $users = ng_load_users();
    $user  = [
        'id'         => uniqid('u', true),
        'name'       => $name,
        'email'      => strtolower($email),
        'password'   => password_hash($password, PASSWORD_DEFAULT),
        'role'       => $role,
        'created_at' => date('Y-m-d H:i:s'),
        'progress'   => [],
    ];
    $users[] = $user;
    ng_save_users($users);

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
