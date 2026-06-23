<?php
// Number Gear — Instructor Dashboard
// Instructors see progress for THEIR assigned learners (plus a list of
// unassigned learners at their institution they can claim).
// Admins see every learner, with filters and the ability to (re)assign
// any learner to any instructor.

require_once __DIR__ . '/../auth/auth.php';
ng_session_start();
$user = ng_require_role(['instructor', 'admin'], '../index.php');

$isAdmin = $user['role'] === 'admin';

$institutionFilter   = trim($_GET['institution'] ?? '');
$instructorFilterRaw = $_GET['instructor'] ?? '';

if ($isAdmin) {
    $instructorFilter = $instructorFilterRaw !== '' ? (int) $instructorFilterRaw : null;
} else {
    // Instructors only ever see their own learners.
    $instructorFilter = (int) $user['id'];
}

$institutions    = ng_list_institutions();
$allInstructors  = $isAdmin ? ng_list_all_instructors() : [];
$learners        = ng_get_all_learners_with_progress(
    $institutionFilter !== '' ? $institutionFilter : null,
    $instructorFilter
);

$unassigned = (!$isAdmin && $user['institution_name'])
    ? ng_get_unassigned_learners($user['institution_name'])
    : [];

$totalLearners  = count($learners);
$avgOverall     = $totalLearners
    ? (int) round(array_sum(array_column($learners, 'overall')) / $totalLearners)
    : 0;
$activeLearners = count(array_filter($learners, fn($l) => $l['last_active'] !== null));

$levelNames = [
    1 => 'Number Recognition', 2 => 'Counting Objects', 3 => 'Number Gear',
    4 => 'Multiply & Divide',  5 => 'Prime Numbers',    6 => 'Ordinal Numbers',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard — Number Gear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: var(--bg); }

        .dash-header {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
            padding: 16px 24px; background: var(--surface);
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .dash-brand { display: flex; align-items: center; gap: 10px; }
        .dash-brand .icon { font-size: 30px; }
        .dash-brand h1 { font-size: 19px; font-weight: 900; color: var(--purple); }
        .dash-brand p  { font-size: 12px; font-weight: 700; color: var(--text-soft); }
        .dash-actions a {
            font-size: 13px; font-weight: 800; color: var(--text-soft);
            text-decoration: none; border: 1.5px solid var(--border);
            border-radius: 10px; padding: 7px 14px; margin-left: 8px;
            transition: 0.15s ease;
        }
        .dash-actions a:hover { border-color: var(--purple); color: var(--purple); }

        .dash-body { max-width: 1180px; margin: 0 auto; padding: 24px 20px 60px; }

        .stat-row {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 16px; margin-bottom: 24px;
        }
        .stat-card {
            background: var(--surface); border-radius: 18px;
            padding: 18px 20px; box-shadow: var(--shadow);
            border-top: 4px solid var(--purple);
        }
        .stat-card .stat-num { font-size: 28px; font-weight: 900; color: var(--text); }
        .stat-card .stat-lbl { font-size: 12px; font-weight: 700; color: var(--text-soft); }
        .stat-card.mint  { border-top-color: var(--mint); }
        .stat-card.sky   { border-top-color: var(--sky); }

        .section-title {
            font-size: 15px; font-weight: 900; color: var(--text);
            margin: 28px 0 12px; display: flex; align-items: center; gap: 8px;
        }

        .filter-row {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 18px; flex-wrap: wrap;
        }
        .filter-row label { font-size: 13px; font-weight: 800; color: var(--text); }
        .filter-row select {
            padding: 9px 14px; border: 2px solid var(--border); border-radius: 12px;
            font-family: inherit; font-size: 13px; font-weight: 700; color: var(--text);
            background: var(--surface);
        }
        .filter-row .clear-link { font-size: 12px; font-weight: 700; color: var(--purple); text-decoration: none; }

        .table-wrap {
            background: var(--surface); border-radius: 18px;
            box-shadow: var(--shadow); overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; min-width: 960px; }
        thead th {
            text-align: left; font-size: 11px; font-weight: 900; letter-spacing: 0.4px;
            text-transform: uppercase; color: var(--text-soft);
            padding: 14px 14px; border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }
        tbody td {
            padding: 12px 14px; border-bottom: 1px solid var(--border);
            font-size: 13px; vertical-align: middle;
        }
        tbody tr:hover { background: var(--bg); }

        .learner-name { font-weight: 800; color: var(--text); }
        .learner-email { font-size: 11px; color: var(--text-soft); }

        .badge {
            display: inline-block; font-size: 11px; font-weight: 800;
            padding: 3px 10px; border-radius: 20px; color: white; white-space: nowrap;
        }
        .badge.institution { background: var(--sky); }
        .badge.self_paced  { background: var(--mint); }

        .mini-bars { display: flex; gap: 3px; align-items: flex-end; height: 28px; }
        .mini-bar {
            width: 10px; background: var(--border); border-radius: 3px;
            position: relative; height: 100%;
            display: flex; align-items: flex-end;
        }
        .mini-bar-fill { width: 100%; border-radius: 3px; background: var(--purple); }
        .overall-pill {
            display: inline-block; min-width: 46px; text-align: center;
            font-weight: 900; font-size: 13px; padding: 4px 8px;
            border-radius: 10px; color: white;
        }
        .last-active { font-size: 11px; color: var(--text-soft); white-space: nowrap; }

        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-soft); }
        .empty-state .big { font-size: 40px; margin-bottom: 10px; }

        .assign-select {
            padding: 6px 10px; border: 1.5px solid var(--border); border-radius: 8px;
            font-family: inherit; font-size: 12px; font-weight: 700; color: var(--text);
            background: var(--surface);
        }

        .claim-btn {
            font-size: 12px; font-weight: 800; color: white; background: var(--sky);
            border: none; border-radius: 10px; padding: 6px 14px; cursor: pointer;
            font-family: inherit; transition: 0.15s ease;
        }
        .claim-btn:hover { background: var(--sky-dark); }
        .claim-btn:disabled { opacity: 0.6; cursor: default; }

        .toast {
            position: fixed; bottom: 24px; right: 24px;
            background: var(--text); color: white; font-size: 13px; font-weight: 700;
            padding: 12px 18px; border-radius: 12px; box-shadow: var(--shadow-lg);
            opacity: 0; transform: translateY(8px); transition: 0.25s ease;
            pointer-events: none; z-index: 999;
        }
        .toast.show { opacity: 1; transform: translateY(0); }

        @media (max-width: 700px) { .stat-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="dash-header">
    <div class="dash-brand">
        <div class="icon">📊</div>
        <div>
            <h1>Instructor Dashboard</h1>
            <p><?= $isAdmin ? 'Number Gear — All Learners' : 'Number Gear — My Learners' ?></p>
        </div>
    </div>
    <div class="dash-actions">
        <a href="../index.php">← Home</a>
        <a href="../auth/logout.php">Log out</a>
    </div>
</div>

<div class="dash-body">

    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-num"><?= $totalLearners ?></div>
            <div class="stat-lbl"><?= $isAdmin ? 'Total learners' . (($institutionFilter !== '' || $instructorFilterRaw !== '') ? ' (filtered)' : '') : 'My learners' ?></div>
        </div>
        <div class="stat-card mint">
            <div class="stat-num"><?= $activeLearners ?></div>
            <div class="stat-lbl">Have started at least one level</div>
        </div>
        <div class="stat-card sky">
            <div class="stat-num"><?= $avgOverall ?>%</div>
            <div class="stat-lbl">Average overall completion</div>
        </div>
    </div>

    <form class="filter-row" method="get">
        <label for="institution">🏫 Institution:</label>
        <select name="institution" id="institution" onchange="this.form.submit()">
            <option value="">All institutions</option>
            <?php foreach ($institutions as $inst): ?>
                <option value="<?= htmlspecialchars($inst) ?>" <?= $institutionFilter === $inst ? 'selected' : '' ?>>
                    <?= htmlspecialchars($inst) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php if ($isAdmin): ?>
            <label for="instructor">👩‍🏫 Instructor:</label>
            <select name="instructor" id="instructor" onchange="this.form.submit()">
                <option value="">All instructors (incl. unassigned)</option>
                <?php foreach ($allInstructors as $inst): ?>
                    <option value="<?= (int) $inst['id'] ?>" <?= (string) $instructorFilterRaw === (string) $inst['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($inst['name']) ?><?= $inst['institution_name'] ? ' — ' . htmlspecialchars($inst['institution_name']) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <?php if ($institutionFilter !== '' || ($isAdmin && $instructorFilterRaw !== '')): ?>
            <a class="clear-link" href="dashboard.php">Clear filters</a>
        <?php endif; ?>
    </form>

    <?php if (empty($learners)): ?>
        <div class="table-wrap">
            <div class="empty-state">
                <div class="big">🎓</div>
                <p>
                    <?php if ($isAdmin): ?>
                        No learners found<?= ($institutionFilter !== '' || $instructorFilterRaw !== '') ? ' for this filter' : ' yet' ?>.
                    <?php else: ?>
                        No learners are assigned to you yet.
                        <?php if (!empty($unassigned)): ?>
                            Check the list below — you can claim learners from your institution.
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Learner</th>
                        <th>Mode</th>
                        <th>Institution</th>
                        <?php if ($isAdmin): ?><th>Instructor</th><?php endif; ?>
                        <th>Levels 1–7 progress</th>
                        <th>Overall</th>
                        <th>Last active</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($learners as $l): ?>
                    <?php
                        $overall = (int) $l['overall'];
                        $overallColor = $overall >= 80 ? 'var(--mint)' : ($overall >= 40 ? 'var(--sky)' : 'var(--peach)');
                    ?>
                    <tr>
                        <td>
                            <div class="learner-name"><?= htmlspecialchars($l['name']) ?></div>
                            <div class="learner-email"><?= htmlspecialchars($l['email']) ?></div>
                        </td>
                        <td>
                            <span class="badge <?= $l['learning_mode'] ?>">
                                <?= ng_learning_mode_label($l['learning_mode']) ?>
                            </span>
                        </td>
                        <td><?= $l['institution_name'] ? htmlspecialchars($l['institution_name']) : '—' ?></td>
                        <?php if ($isAdmin): ?>
                        <td>
                            <select class="assign-select" data-learner-id="<?= (int) $l['id'] ?>" onchange="ngAssignInstructor(this)">
                                <option value="">— Unassigned —</option>
                                <?php foreach ($allInstructors as $inst): ?>
                                    <option value="<?= (int) $inst['id'] ?>" <?= (int) $l['instructor_id'] === (int) $inst['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($inst['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <?php endif; ?>
                        <td>
                            <div class="mini-bars" title="Level 1 → 6">
                                <?php for ($lvl = 1; $lvl <= 7; $lvl++): ?>
                                    <?php $score = isset($l['levels'][$lvl]) ? (int) $l['levels'][$lvl]['score'] : 0; ?>
                                    <div class="mini-bar" title="<?= $levelNames[$lvl] ?>: <?= $score ?>%">
                                        <div class="mini-bar-fill" style="height: <?= max(4, $score) ?>%;"></div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td><span class="overall-pill" style="background:<?= $overallColor ?>;"><?= $overall ?>%</span></td>
                        <td class="last-active">
                            <?= $l['last_active'] ? date('M j, Y g:ia', strtotime($l['last_active'])) : 'Not started' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (!$isAdmin && !empty($unassigned)): ?>
        <div class="section-title">🙋 Unassigned learners at <?= htmlspecialchars($user['institution_name']) ?></div>
        <p style="font-size:12px;color:var(--text-soft);margin-bottom:12px;">
            These learners picked your institution when registering but don't have an instructor yet. Claim the ones that are yours.
        </p>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Learner</th><th>Registered</th><th></th></tr>
                </thead>
                <tbody id="unassignedBody">
                    <?php foreach ($unassigned as $u): ?>
                        <tr data-row-for="<?= (int) $u['id'] ?>">
                            <td>
                                <div class="learner-name"><?= htmlspecialchars($u['name']) ?></div>
                                <div class="learner-email"><?= htmlspecialchars($u['email']) ?></div>
                            </td>
                            <td class="last-active"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <button class="claim-btn" data-learner-id="<?= (int) $u['id'] ?>" onclick="ngClaimLearner(this)">Claim as my learner</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<div class="toast" id="ngToast"></div>

<footer class="ng-footer" style="text-align:center;padding:14px;font-size:12px;font-weight:700;color:var(--text-soft);">
    © <?= date('Y') ?> Number Gear &nbsp;·&nbsp; Developed by <strong>Purretech Solutions</strong>
</footer>

<script>
function ngToast(msg) {
    const t = document.getElementById('ngToast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2400);
}

function ngClaimLearner(btn) {
    const learnerId = btn.getAttribute('data-learner-id');
    btn.disabled = true;
    btn.textContent = 'Claiming…';
    fetch('../api/assign_instructor.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ learner_id: learnerId, instructor_id: <?= (int) $user['id'] ?> })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            ngToast('Learner claimed — reloading…');
            setTimeout(() => location.reload(), 500);
        } else {
            ngToast(data.error || 'Could not claim this learner.');
            btn.disabled = false;
            btn.textContent = 'Claim as my learner';
        }
    })
    .catch(() => {
        ngToast('Network error — please try again.');
        btn.disabled = false;
        btn.textContent = 'Claim as my learner';
    });
}

function ngAssignInstructor(select) {
    const learnerId = select.getAttribute('data-learner-id');
    const instructorId = select.value;
    select.disabled = true;
    fetch('../api/assign_instructor.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ learner_id: learnerId, instructor_id: instructorId || null })
    })
    .then(r => r.json())
    .then(data => {
        select.disabled = false;
        ngToast(data.ok ? 'Assignment updated.' : (data.error || 'Could not update assignment.'));
    })
    .catch(() => {
        select.disabled = false;
        ngToast('Network error — please try again.');
    });
}
</script>

</body>
</html>
