<?php
require_once __DIR__ . '/auth.php';
ng_session_start();

// Already logged in — redirect to home
if (ng_is_logged_in()) {
    header('Location: ../index.php');
    exit;
}

$error   = '';
$success = '';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email']    ?? '';
    $password = $_POST['password'] ?? '';
    $result   = ng_login($email, $password);

    if ($result['ok']) {
        header('Location: ../index.php');
        exit;
    } else {
        $error = $result['error'];
    }
}

// Check for success message from register
if (isset($_GET['registered'])) {
    $success = 'Account created! Please log in.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Number Gear</title>
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
            width: 100%; max-width: 420px;
        }
        .auth-logo {
            text-align: center; margin-bottom: 28px;
        }
        .auth-logo .logo-icon { font-size: 48px; line-height: 1; margin-bottom: 8px; }
        .auth-logo h1 { font-size: 24px; font-weight: 900; color: var(--purple); margin-bottom: 2px; }
        .auth-logo p  { font-size: 14px; font-weight: 600; color: var(--text-soft); }

        .auth-title { font-size: 20px; font-weight: 900; color: var(--text); margin-bottom: 20px; }

        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
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
        .auth-success {
            background: var(--success-bg); border: 2px solid var(--success);
            border-radius: 12px; padding: 12px 16px;
            font-size: 14px; font-weight: 700; color: #276749;
            margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
        }

        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 20px 0; color: var(--text-soft);
            font-size: 13px; font-weight: 700;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }

        @media (max-width: 480px) {
            .auth-card { padding: 28px 20px; }
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

        <div class="auth-title">Welcome back! 👋</div>

        <?php if ($error): ?>
        <div class="auth-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="auth-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>

            <div class="form-group">
                <label class="form-label" for="email">📧 Email address</label>
                <input
                    class="form-input"
                    type="email" id="email" name="email"
                    placeholder="you@example.com"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="password">🔒 Password</label>
                <div class="password-wrap">
                    <input
                        class="form-input"
                        type="password" id="password" name="password"
                        placeholder="Enter your password"
                        required autocomplete="current-password"
                    >
                    <button type="button" class="pw-toggle" onclick="togglePw('password', this)" aria-label="Show password">👁️</button>
                </div>
            </div>

            <button type="submit" class="submit-btn">Log In →</button>

        </form>

        <div class="divider">or</div>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create one free →</a>
        </div>

    </div>
</div>

<script>
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
</script>
</body>
</html>
