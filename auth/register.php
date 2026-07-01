<?php
require_once __DIR__ . '/auth.php';
ng_session_start();

// Already logged in — redirect to home
if (ng_is_logged_in()) {
    header('Location: ../index.php');
    exit;
}

$error  = '';
$fields = [
    'name'             => '',
    'email'            => '',
    'role'             => 'learner',
    'learning_mode'    => 'self_paced',
    'institution_name' => '',
    'instructor_id'    => '',
];

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = $_POST['name']             ?? '';
    $email            = $_POST['email']            ?? '';
    $password         = $_POST['password']         ?? '';
    $confirm          = $_POST['confirm']          ?? '';
    $role             = $_POST['role']             ?? '';
    $learningMode     = $_POST['learning_mode']    ?? 'self_paced';
    $institutionName  = $_POST['institution_name'] ?? '';
    $instructorId     = $_POST['instructor_id']    ?? '';

    // Keep fields sticky on error
    $fields = [
        'name'             => $name,
        'email'            => $email,
        'role'             => $role,
        'learning_mode'    => $learningMode,
        'institution_name' => $institutionName,
        'instructor_id'    => $instructorId,
    ];

    if ($password !== $confirm) {
        $error = 'Passwords do not match. Please try again.';
    } else {
        $result = ng_register(
            $name, $email, $password, $role, $learningMode, $institutionName,
            $instructorId !== '' ? (int) $instructorId : null
        );
        if ($result['ok']) {
            header('Location: login.php?registered=1');
            exit;
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — Number Gear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { min-height: 100vh; display: flex; flex-direction: column; background: var(--bg); }
        .auth-page {
            flex: 1; display: flex; align-items: center;
            justify-content: center; padding: 24px 20px;
        }
        .auth-card {
            background: var(--surface);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            padding: 36px 32px;
            width: 100%; max-width: 440px;
        }
        .auth-logo {
            text-align: center; margin-bottom: 24px;
        }
        .auth-logo .logo-icon { font-size: 44px; line-height: 1; margin-bottom: 6px; }
        .auth-logo h1 { font-size: 22px; font-weight: 900; color: var(--purple); margin-bottom: 2px; }
        .auth-logo p  { font-size: 13px; font-weight: 600; color: var(--text-soft); }

        .auth-title { font-size: 20px; font-weight: 900; color: var(--text); margin-bottom: 20px; }

        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 15px; }
        .form-label {
            font-size: 13px; font-weight: 800; color: var(--text);
            display: flex; align-items: center; gap: 6px;
        }
        .form-input {
            padding: 13px 16px;
            border: 2px solid var(--border);
            border-radius: 14px;
            font-size: 15px; font-weight: 600;
            font-family: inherit; color: var(--text);
            background: var(--bg);
            transition: border-color 0.18s ease;
            outline: none;
        }
        .form-input:focus { border-color: var(--purple); background: var(--surface); }
        .form-input::placeholder { color: var(--text-soft); font-weight: 600; }

        .password-wrap { position: relative; }
        .password-wrap .form-input { padding-right: 48px; width: 100%; }
        .pw-toggle {
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            font-size: 18px; cursor: pointer; color: var(--text-soft);
            padding: 0; line-height: 1;
        }

        /* Role selector */
        .role-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;
        }
        .role-option { display: none; }
        .role-label {
            display: flex; flex-direction: column; align-items: center;
            gap: 6px; padding: 14px 8px;
            border: 2.5px solid var(--border);
            border-radius: 16px; cursor: pointer;
            transition: 0.18s ease; text-align: center;
            background: var(--bg);
        }
        .role-icon  { font-size: 28px; line-height: 1; }
        .role-name  { font-size: 13px; font-weight: 900; color: var(--text-soft); }
        .role-desc  { font-size: 10px; font-weight: 700; color: var(--text-soft); line-height: 1.3; }

        .role-option:checked + .role-label {
            border-color: var(--purple);
            background: var(--purple-light);
        }
        .role-option:checked + .role-label .role-name { color: var(--purple-dark); }
        .role-option:checked + .role-label .role-desc  { color: var(--purple); }

        /* Learner — mint */
        #role-learner:checked + .role-label    { border-color: var(--mint); background: var(--mint-light); }
        #role-learner:checked + .role-label .role-name { color: var(--mint-dark); }
        #role-learner:checked + .role-label .role-desc  { color: var(--mint-dark); }

        /* Instructor — sky */
        #role-instructor:checked + .role-label  { border-color: var(--sky); background: var(--sky-light); }
        #role-instructor:checked + .role-label .role-name { color: var(--sky-dark); }
        #role-instructor:checked + .role-label .role-desc  { color: var(--sky-dark); }

        /* Admin — purple */
        #role-admin:checked + .role-label       { border-color: var(--purple); background: var(--purple-light); }
        #role-admin:checked + .role-label .role-name { color: var(--purple-dark); }
        #role-admin:checked + .role-label .role-desc  { color: var(--purple-dark); }

        /* Learning-mode selector (institution vs self-paced) */
        .mode-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;
        }
        .mode-option { display: none; }
        .mode-label {
            display: flex; flex-direction: column; align-items: center;
            gap: 6px; padding: 14px 8px;
            border: 2.5px solid var(--border);
            border-radius: 16px; cursor: pointer;
            transition: 0.18s ease; text-align: center;
            background: var(--bg);
        }
        .mode-icon { font-size: 26px; line-height: 1; }
        .mode-name { font-size: 13px; font-weight: 900; color: var(--text-soft); }
        .mode-desc { font-size: 10px; font-weight: 700; color: var(--text-soft); line-height: 1.3; }

        .mode-option:checked + .mode-label {
            border-color: var(--sky); background: var(--sky-light);
        }
        .mode-option:checked + .mode-label .mode-name { color: var(--sky-dark); }
        .mode-option:checked + .mode-label .mode-desc  { color: var(--sky-dark); }

        #mode-self_paced:checked + .mode-label { border-color: var(--mint); background: var(--mint-light); }
        #mode-self_paced:checked + .mode-label .mode-name { color: var(--mint-dark); }
        #mode-self_paced:checked + .mode-label .mode-desc  { color: var(--mint-dark); }

        .institution-field { margin-top: 12px; }
        .institution-field.hidden { display: none; }

        .instructor-field-wrap { margin-top: 12px; }
        .instructor-field-wrap.hidden { display: none; }
        .instructor-note {
            font-size: 11px; font-weight: 700; color: var(--text-soft);
            margin-top: 6px; line-height: 1.4;
        }
        select.form-input { cursor: pointer; }

        .submit-btn {
            width: 100%; padding: 16px;
            background: var(--purple); color: white;
            border: none; border-radius: 16px;
            font-size: 16px; font-weight: 900;
            cursor: pointer; font-family: inherit;
            transition: 0.2s ease; margin-top: 4px;
        }
        .submit-btn:hover { background: var(--purple-dark); transform: translateY(-1px); }
        .submit-btn:active { transform: translateY(0); }

        .auth-footer {
            text-align: center; margin-top: 20px;
            font-size: 14px; font-weight: 700; color: var(--text-soft);
        }
        .auth-footer a { color: var(--purple); text-decoration: none; font-weight: 800; }
        .auth-footer a:hover { text-decoration: underline; }

        .auth-error {
            background: var(--error-bg); border: 2px solid var(--error);
            border-radius: 12px; padding: 12px 16px;
            font-size: 14px; font-weight: 700; color: #9b2335;
            margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
        }

        .pw-strength { margin-top: 5px; height: 5px; border-radius: 4px; background: var(--border); overflow: hidden; }
        .pw-strength-fill { height: 100%; border-radius: 4px; transition: width 0.3s, background 0.3s; width: 0%; }
        .pw-strength-label { font-size: 11px; font-weight: 700; color: var(--text-soft); margin-top: 3px; }

        @media (max-width: 480px) {
            .auth-card { padding: 24px 16px; }
            .role-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
        }
    </style>
</head>
<body>
<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo">
            <div class="logo-icon">⚙️</div>
            <h1>Number Gear</h1>
            <p>Pre-Primary Mathematics</p>
        </div>

        <div class="auth-title">Create your account 🎉</div>

        <?php if ($error): ?>
        <div class="auth-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>

            <!-- Name -->
            <div class="form-group">
                <label class="form-label" for="name">👤 Full name</label>
                <input
                    class="form-input"
                    type="text" id="name" name="name"
                    placeholder="Your full name"
                    value="<?= htmlspecialchars($fields['name']) ?>"
                    required autocomplete="name"
                >
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">📧 Email address</label>
                <input
                    class="form-input"
                    type="email" id="email" name="email"
                    placeholder="you@example.com"
                    value="<?= htmlspecialchars($fields['email']) ?>"
                    required autocomplete="email"
                >
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">🔒 Password</label>
                <div class="password-wrap">
                    <input
                        class="form-input"
                        type="password" id="password" name="password"
                        placeholder="At least 6 characters"
                        required autocomplete="new-password"
                        oninput="checkStrength(this.value)"
                    >
                    <button type="button" class="pw-toggle" onclick="togglePw('password', this)" aria-label="Show password">👁️</button>
                </div>
                <div class="pw-strength"><div class="pw-strength-fill" id="strengthFill"></div></div>
                <div class="pw-strength-label" id="strengthLabel"></div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label class="form-label" for="confirm">🔒 Confirm password</label>
                <div class="password-wrap">
                    <input
                        class="form-input"
                        type="password" id="confirm" name="confirm"
                        placeholder="Repeat your password"
                        required autocomplete="new-password"
                    >
                    <button type="button" class="pw-toggle" onclick="togglePw('confirm', this)" aria-label="Show password">👁️</button>
                </div>
            </div>

            <!-- Role selector -->
            <div class="form-group">
                <div class="form-label">🎭 I am joining as a...</div>
                <div class="role-grid">

                    <div>
                        <input type="radio" class="role-option" id="role-learner" name="role" value="learner"
                            onclick="toggleInstructorField()"
                            <?= $fields['role'] === 'learner' ? 'checked' : '' ?>>
                        <label class="role-label" for="role-learner">
                            <span class="role-icon">🎓</span>
                            <span class="role-name">Learner</span>
                            <span class="role-desc">I am here to learn</span>
                        </label>
                    </div>

                    <div>
                        <input type="radio" class="role-option" id="role-instructor" name="role" value="instructor"
                            onclick="toggleInstructorField()"
                            <?= $fields['role'] === 'instructor' ? 'checked' : '' ?>>
                        <label class="role-label" for="role-instructor">
                            <span class="role-icon">📚</span>
                            <span class="role-name">Instructor</span>
                            <span class="role-desc">I teach learners</span>
                        </label>
                    </div>

                    <div>
                        <input type="radio" class="role-option" id="role-admin" name="role" value="admin"
                            onclick="toggleInstructorField()"
                            <?= $fields['role'] === 'admin' ? 'checked' : '' ?>>
                        <label class="role-label" for="role-admin">
                            <span class="role-icon">⚙️</span>
                            <span class="role-name">Admin</span>
                            <span class="role-desc">I manage the platform</span>
                        </label>
                    </div>

                </div>
            </div>

            <!-- Learning mode selector -->
            <div class="form-group">
                <div class="form-label">🧭 I will be learning via...</div>
                <div class="mode-grid">

                    <div>
                        <input type="radio" class="mode-option" id="mode-institution" name="learning_mode" value="institution"
                            onclick="toggleInstitutionField()"
                            <?= $fields['learning_mode'] === 'institution' ? 'checked' : '' ?>>
                        <label class="mode-label" for="mode-institution">
                            <span class="mode-icon">🏫</span>
                            <span class="mode-name">Institution</span>
                            <span class="mode-desc">I'm with a school/centre</span>
                        </label>
                    </div>

                    <div>
                        <input type="radio" class="mode-option" id="mode-self_paced" name="learning_mode" value="self_paced"
                            onclick="toggleInstitutionField()"
                            <?= $fields['learning_mode'] !== 'institution' ? 'checked' : '' ?>>
                        <label class="mode-label" for="mode-self_paced">
                            <span class="mode-icon">🚀</span>
                            <span class="mode-name">Self-paced</span>
                            <span class="mode-desc">I'm learning on my own</span>
                        </label>
                    </div>

                </div>

                <div class="institution-field <?= $fields['learning_mode'] === 'institution' ? '' : 'hidden' ?>" id="institutionField">
                    <label class="form-label" for="institution_name">🏫 Name of institution</label>
                    <input
                        class="form-input"
                        type="text" id="institution_name" name="institution_name"
                        placeholder="e.g. Sunrise Primary School"
                        value="<?= htmlspecialchars($fields['institution_name']) ?>"
                        autocomplete="organization"
                        oninput="onInstitutionInput()"
                    >

                    <div class="instructor-field-wrap hidden" id="instructorFieldWrap">
                        <label class="form-label" for="instructor_id">👩‍🏫 Choose your instructor</label>
                        <select class="form-input" id="instructor_id" name="instructor_id" disabled>
                            <option value="">— type your institution name first —</option>
                        </select>
                        <div class="instructor-note" id="instructorNote"></div>
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn">Create Account →</button>

        </form>


        <div class="auth-footer">
            Already have an account? <a href="login.php">Log in →</a>
        </div>

    </div>
</div>

<script>
window.NG_STICKY_INSTRUCTOR_ID = <?= json_encode($fields['instructor_id'] !== '' ? (int) $fields['instructor_id'] : null) ?>;

function togglePw(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁️';
    }
}

function getSelectedRole() {
    const checked = document.querySelector('input[name="role"]:checked');
    return checked ? checked.value : 'learner';
}

function toggleInstitutionField() {
    const isInstitution = document.getElementById('mode-institution').checked;
    const field = document.getElementById('institutionField');
    const input = document.getElementById('institution_name');
    field.classList.toggle('hidden', !isInstitution);
    input.required = isInstitution;
    if (!isInstitution) input.value = '';
    toggleInstructorField();
}
document.addEventListener('DOMContentLoaded', toggleInstitutionField);

function toggleInstructorField() {
    const isInstitution = document.getElementById('mode-institution').checked;
    const isLearner     = getSelectedRole() === 'learner';
    const wrap = document.getElementById('instructorFieldWrap');
    const show = isInstitution && isLearner;
    wrap.classList.toggle('hidden', !show);
    if (show) {
        fetchInstructors();
    } else {
        document.getElementById('instructor_id').value = '';
    }
}

let _instructorFetchTimer = null;
function onInstitutionInput() {
    clearTimeout(_instructorFetchTimer);
    _instructorFetchTimer = setTimeout(fetchInstructors, 450);
}

function fetchInstructors() {
    const isInstitution = document.getElementById('mode-institution').checked;
    const isLearner     = getSelectedRole() === 'learner';
    if (!isInstitution || !isLearner) return;

    const name   = document.getElementById('institution_name').value.trim();
    const select = document.getElementById('instructor_id');
    const note   = document.getElementById('instructorNote');

    if (!name) {
        select.innerHTML = '<option value="">— type your institution name first —</option>';
        select.disabled = true;
        note.textContent = '';
        return;
    }

    select.disabled = true;
    select.innerHTML = '<option value="">Loading…</option>';
    note.textContent = '';

    fetch('../api/lookup_instructors.php?institution=' + encodeURIComponent(name))
        .then(r => r.json())
        .then(data => {
            const list = data.instructors || [];
            if (list.length === 0) {
                select.innerHTML = '<option value="">No instructor found yet</option>';
                select.disabled = true;
                note.textContent = "We couldn't find an instructor registered for that institution yet — "
                    + "you can still create your account, and an instructor or admin can assign you once one joins.";
            } else {
                select.innerHTML = '<option value="">— select your instructor —</option>'
                    + list.map(i => `<option value="${i.id}">${i.name.replace(/</g, '&lt;')}</option>`).join('');
                select.disabled = false;
                if (window.NG_STICKY_INSTRUCTOR_ID) select.value = window.NG_STICKY_INSTRUCTOR_ID;
            }
        })
        .catch(() => {
            select.innerHTML = '<option value="">— select your instructor —</option>';
            select.disabled = false;
            note.textContent = '';
        });
}

function checkStrength(val) {
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    if (!val) { fill.style.width = '0%'; label.textContent = ''; return; }

    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { w: '20%', c: '#fc8181', t: 'Too short' },
        { w: '40%', c: '#f4a571', t: 'Weak' },
        { w: '60%', c: '#fdc536', t: 'Fair' },
        { w: '80%', c: '#68d391', t: 'Good' },
        { w: '100%', c: '#48bb78', t: 'Strong' },
    ];
    const lvl = levels[Math.min(score, 4)];
    fill.style.width      = lvl.w;
    fill.style.background = lvl.c;
    label.textContent     = lvl.t;
}
</script>
<footer class="ng-footer">
    <span>&copy; 2026 Number Gear. Developed by <strong>Purretech Solutions</strong>.</span>
</footer>
</body>
</html>
