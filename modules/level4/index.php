<?php
// Number Gear — Level 4: Multiply & Divide
require_once __DIR__ . '/../../auth/auth.php';
ng_session_start();
$ng_current_user = ng_current_user();
if (!$ng_current_user) { header('Location: ../../auth/login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level 4 — Multiply & Divide | Number Gear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/accessibility.js"></script>
    <script src="../../assets/js/i18n-common.js"></script>
    <style>
        .gear-intro {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.7;
            background: var(--purple-light);
            border-left: 4px solid var(--purple);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 22px;
        }

        /* Level 3–style two-column layout */
        .gear-layout {
            display: flex;
            flex-direction: row;
            gap: 32px;
            align-items: flex-start;
            width: 100%;
        }
        .gear-left {
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            min-width: 0;
            order: 1;
        }
        .gear-right {
            flex: 0 0 360px;
            width: 360px;
            display: flex;
            flex-direction: column;
            gap: 13px;
            order: 2;
        }
        @media (max-width: 900px) {
            .gear-layout { flex-direction: column; align-items: center; }
            .gear-right  { width: 100%; max-width: 560px; flex: none; }
        }
        #gearCanvas4 {
            display: block;
            width: 100%;
            max-width: 560px;
            height: auto;
            cursor: pointer;
            touch-action: manipulation;
            border-radius: 50%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }

        .gear-canvas-wrap {
            position: relative;
            display: flex;
            justify-content: center;
            width: 100%;
        }



        /* Table selector */
        .table-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 16px;
        }

        .table-btn {
            padding: 10px 6px;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            color: var(--text-soft);
            font-family: inherit;
            text-align: center;
            transition: 0.18s ease;
        }
        .table-btn:hover  { border-color: var(--peach); color: var(--peach-dark); }
        .table-btn.active { background: var(--peach); border-color: var(--peach); color: white; }

        /* Rotate row */


        /* ── Shared card styles matching Level 3 ── */
        .ctrl-card {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 14px 16px;
        }
        .card-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-soft);
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 11px;
        }
        .ring-display {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--peach-light);
            border: 2px solid var(--peach);
            border-radius: 14px;
            padding: 12px 14px;
        }
        .ring-big-num {
            font-size: 32px;
            font-weight: 900;
            color: var(--peach-dark);
            line-height: 1;
            min-width: 60px;
            text-align: center;
            animation: popIn 0.3s ease;
        }
        @keyframes popIn { 0%{transform:scale(0.8);opacity:0} 60%{transform:scale(1.12)} 100%{transform:scale(1);opacity:1} }
        .ring-meta { flex: 1; }
        .ring-meta .rm-label {
            font-size: 11px; font-weight: 700; color: var(--text-soft);
            text-transform: uppercase; letter-spacing: 0.4px;
        }
        .ring-meta .rm-detail {
            font-size: 13px; font-weight: 800; color: var(--peach-dark); margin-top: 2px;
        }
        .ring-meta .rm-table {
            font-size: 11px; color: var(--text-soft); font-weight: 600;
            margin-top: 3px; line-height: 1.4;
        }
        .hear-btn {
            padding: 10px 12px;
            background: var(--peach);
            border: none; border-radius: 11px;
            color: white; font-size: 18px;
            cursor: pointer; font-family: inherit;
            transition: 0.18s ease; flex-shrink: 0;
        }
        .hear-btn:hover  { background: var(--peach-dark); }
        .hear-btn:active { transform: scale(0.94); }
        .ring-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
        }
        .ring-btn {
            padding: 7px 3px;
            border: 2px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            font-size: 11px; font-weight: 800;
            cursor: pointer; color: var(--text-soft);
            font-family: inherit; text-align: center;
            transition: 0.18s ease; line-height: 1.3;
        }
        .ring-btn:hover  { border-color: var(--peach); color: var(--peach-dark); }
        .ring-btn.active { background: var(--peach); border-color: var(--peach); color: white; }
        .rot-row { display: flex; gap: 8px; }
        .rot-btn {
            flex: 1; padding: 13px 6px;
            border: 2.5px solid; border-radius: 13px;
            background: var(--surface);
            font-size: 12px; font-weight: 800;
            cursor: pointer; font-family: inherit;
            display: flex; flex-direction: column;
            align-items: center; gap: 4px;
            transition: 0.18s ease; line-height: 1.2;
        }
        .rot-btn .rot-icon { font-size: 24px; line-height: 1; }
        .rot-btn.ccw { border-color: var(--purple); color: var(--purple-dark); background: var(--purple-light); }
        .rot-btn.cw  { border-color: var(--mint);   color: var(--mint-dark);   background: var(--mint-light);  }
        .rot-btn.ccw:hover { background: var(--purple); color: white; }
        .rot-btn.cw:hover  { background: var(--mint);   color: white; }
        .rot-btn:active    { transform: scale(0.96); }
        .sub-row { display: flex; gap: 8px; margin-top: 8px; }
        .spin-btn {
            flex: 1; padding: 9px 8px;
            border: 2px solid var(--peach);
            border-radius: 10px;
            background: var(--peach-light); color: var(--peach-dark);
            font-size: 12px; font-weight: 800;
            cursor: pointer; font-family: inherit;
            transition: 0.18s ease; text-align: center;
        }
        .spin-btn.spinning { background: var(--peach); color: white; border-color: var(--peach); }
        .reset-ring-btn {
            padding: 9px 13px;
            border: 2px solid var(--border);
            border-radius: 10px; background: var(--surface);
            font-size: 13px; cursor: pointer;
            font-family: inherit; font-weight: 800;
            color: var(--text-soft); transition: 0.18s ease; white-space: nowrap;
        }
        .reset-ring-btn:hover { background: var(--peach-light); border-color: var(--peach); color: var(--peach-dark); }
        .action-row { display: flex; gap: 8px; }
        .action-btn {
            flex: 1; padding: 11px 8px;
            border: 2px solid var(--border); border-radius: 12px;
            background: var(--surface); font-size: 12px; font-weight: 800;
            cursor: pointer; font-family: inherit;
            color: var(--text-soft); transition: 0.18s ease; text-align: center;
        }
        .action-btn:hover { background: var(--bg); }
        .action-btn.reset { border-color: var(--mint); color: var(--mint-dark); }
        .action-btn.reset:hover { background: var(--mint-light); }
        .stats-row { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; }
        .stat-chip-sm {
            background: var(--surface); border: 2px solid var(--border);
            border-radius: 10px; padding: 6px 12px;
            font-size: 12px; font-weight: 800; color: var(--text-soft);
        }
        .stat-chip-sm span { color: var(--peach-dark); font-size: 15px; }

        /* Challenge — example item */
        .ex-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            cursor: pointer;
            transition: 0.18s ease;
            font-family: inherit;
            text-align: left;
            width: 100%;
        }
        .ex-item:hover { border-color: var(--peach); background: var(--peach-light); }
        .ex-item.selected { border-color: var(--peach); background: var(--peach-light); }
        .ex-item-eq {
            font-size: 20px;
            font-weight: 900;
            color: var(--peach-dark);
            min-width: 90px;
        }
        .ex-item-desc {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-soft);
            line-height: 1.4;
            flex: 1;
        }
        /* Single example card styles */
        .ex-badge {
            font-size: 11px; font-weight: 800; color: var(--text-soft);
            text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;
        }
        .ex-sum4 {
            font-size: 34px; font-weight: 900; color: var(--peach-dark);
            line-height: 1; letter-spacing: -1px; margin-bottom: 8px;
        }
        .ex-ring-hint {
            display: inline-block; font-size: 12px; font-weight: 800;
            color: var(--purple-dark); background: var(--purple-light);
            border: 1.5px solid var(--purple); border-radius: 20px;
            padding: 3px 10px; width: fit-content; margin-bottom: 8px;
        }
        .ex-desc4 {
            font-size: 13px; font-weight: 600; color: var(--text); line-height: 1.6;
        }
        .ex-next-btn {
            width: 100%; padding: 12px;
            background: var(--mint); color: white;
            border: none; border-radius: 13px;
            font-size: 14px; font-weight: 800;
            font-family: inherit; cursor: pointer;
            transition: 0.18s ease;
        }
        .ex-next-btn:hover { background: var(--mint-dark); }

        .ex-go-btn {
            width: 100%;
            padding: 14px;
            background: var(--peach);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 900;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.18s ease;
        }
        .ex-go-btn:not(:disabled):hover { background: var(--peach-dark); }
        .ex-go-btn:active { transform: scale(0.97); }
        .ex-go-btn:disabled { opacity: 0.45; cursor: not-allowed; }
        .ex-go-icon { font-size: 20px; }

        .challenge-q {
            font-size: 28px;
            font-weight: 900;
            color: var(--text);
            margin-bottom: 14px;
            text-align: center;
        }

        .challenge-choices {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .challenge-choice {
            min-width: 64px;
            padding: 12px 10px;
            border: 2px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            font-size: 20px;
            font-weight: 900;
            cursor: pointer;
            transition: 0.18s ease;
            font-family: inherit;
            color: var(--text);
        }
        .challenge-choice:hover:not(:disabled) { border-color: var(--peach); background: var(--peach-light); }
        .challenge-choice.correct { background: var(--success-bg); border-color: var(--success); color: #276749; animation: glow 0.6s ease; }
        .challenge-choice.wrong   { background: var(--error-bg);   border-color: var(--error);   animation: shake 0.4s ease; }
        .challenge-choice:disabled { cursor: default; }

        .challenge-score {
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-soft);
        }

        .challenge-score span { color: var(--peach-dark); font-weight: 900; }

        /* Quiz mode */
        .ex-next-btn {
            width: 100%; padding: 12px;
            background: var(--mint); color: white;
            border: none; border-radius: 13px;
            font-size: 14px; font-weight: 800;
            font-family: inherit; cursor: pointer;
            transition: 0.18s ease;
        }
        .ex-next-btn:hover { background: var(--mint-dark); }
        #gearCanvasQz {
            display: block; width: 100%; max-width: 560px; height: auto;
            cursor: pointer; touch-action: manipulation;
            border-radius: 50%; box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }



        @media (max-width: 560px) {
            .table-grid { grid-template-columns: repeat(5, 1fr); gap: 5px; }
            .table-btn  { font-size: 12px; padding: 8px 3px; }
            .eq-display { font-size: 20px; }
        }
    </style>
    <script>document.addEventListener('DOMContentLoaded', function () { if (window.NG_I18nCommon) NG_I18nCommon.apply(4); });</script>
</head>
<body>
<div class="app-shell">

    <!-- Header -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">✖️</div>
            <div>
                <h1 id="lvlHeading">Level 4</h1>
                <p>Multiply &amp; Divide</p>
            </div>
        </div>
        <a href="../../index.php" class="back-btn" id="lvlBackLink">← Home</a>
    </header>

    <main class="level-page">

        <div class="instruction-banner">
            ✖️ Watch how multiplying and dividing work on the gear — then try the quiz yourself!
        </div>

        <!-- Explainer -->
        <p class="gear-intro">
            Both multiplication and division work the <strong>same way</strong> on this gear: start at <strong>0</strong> on the reference arrow ▼ and spin <strong>clockwise ↻</strong>, counting each spin.
            🟢 <strong>Multiply:</strong> count a fixed number of spins — the number at the arrow is your answer.
            🟣 <strong>Divide:</strong> keep spinning until your number appears at the arrow — the spins you counted is your answer.
        </p>

        <!-- Mode tabs -->
        <div class="tab-bar" style="margin-bottom:16px;">
            <button class="tab-btn active" id="modeChalBtn"  onclick="setMode('challenge')" >🎯 Illustrated</button>
            <button class="tab-btn"        id="modeQuizBtn"  onclick="setMode('quiz')"      >🧮 Quiz</button>
        </div>

        <!-- ===== CHALLENGE MODE ===== -->
        <div id="chalMode" style="display:none;width:100%;">
            <div class="gear-layout">

                <!-- LEFT — gear canvas (reused) -->
                <div class="gear-left">
                    <div class="stats-row">
                        <div class="stat-chip-sm">Planes explored: <span id="tablesExplored2">0</span>/10</div>
                        <div class="stat-chip-sm">Score: <span id="quizScore2">0</span></div>
                    </div>
                    <canvas id="gearCanvas4b" width="560" height="560"></canvas>

                </div>

                <!-- RIGHT — challenge controls -->
                <div class="gear-right">

                    <!-- Operation picker -->
                    <div class="ctrl-card">
                        <div class="card-title">Choose an operation</div>
                        <div class="rot-row">
                            <button class="rot-btn cw" id="opMultBtn" onclick="selectOp('multiply')">
                                <span class="rot-icon">✖️</span>
                                <span>Multiply</span>
                            </button>
                            <button class="rot-btn cw" id="opDivBtn" onclick="selectOp('divide')" style="border-color:var(--purple);color:var(--purple-dark);background:var(--purple-light);">
                                <span class="rot-icon">➗</span>
                                <span>Divide</span>
                            </button>
                        </div>
                    </div>

                    <!-- Single example card -->
                    <div class="ctrl-card" id="exampleCard4">
                        <div class="ex-badge" id="exBadge4">Example 1 of 10</div>
                        <div class="ex-sum4"  id="exSum4">3 × 4 = ?</div>
                        <div class="ex-ring-hint" id="exHint4">Use Plane 3</div>
                        <div class="ex-desc4" id="exDesc4">Start at 0 on the reference arrow. Rotate clockwise ↻ and count 4 spins — the number at the arrow is your answer!</div>
                    </div>

                    <!-- Show on gear -->
                    <button class="ex-go-btn" id="showOnGearBtn" onclick="showOnGear()">
                        <span class="ex-go-icon">⚙️</span> Show on Gear!
                    </button>

                    <!-- How it works -->
                    <div id="chalStepCard" style="display:none;" class="ctrl-card">
                        <div class="card-title">How it works</div>
                        <div id="chalStepText" style="font-size:14px;font-weight:700;color:var(--text);line-height:1.8;"></div>
                    </div>

                    <!-- Result -->
                    <div id="chalIllustResult" style="display:none;" class="ctrl-card">
                        <div id="chalIllustEq"  style="font-size:28px;font-weight:900;color:var(--mint-dark);text-align:center;"></div>
                        <div id="chalIllustSub" style="font-size:13px;font-weight:700;color:var(--text-soft);text-align:center;margin-top:4px;"></div>
                        <button class="ex-next-btn" onclick="nextExample4()" style="margin-top:10px;">Next Example →</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- ===== QUIZ MODE ===== -->
        <div id="quizMode" style="display:none;width:100%;">
            <div class="gear-layout">

                <!-- LEFT — quiz gear canvas + rotate controls -->
                <div class="gear-left">
                    <div class="stats-row">
                        <div class="stat-chip-sm">Correct: <span id="qzCorrect">0</span></div>
                        <div class="stat-chip-sm">Streak: <span id="qzStreak">0</span></div>
                        <div class="stat-chip-sm">Score: <span id="qzScore">0</span></div>
                    </div>
                    <canvas id="gearCanvasQz" width="560" height="560"></canvas>
                    <div style="width:100%;max-width:560px;">
                        <div class="ctrl-card" style="margin-top:10px;">
                            <div class="card-title">Rotate the gear to find your answer</div>
                            <div class="rot-row">
                                <button class="rot-btn ccw" onclick="qzRotateCCW()">
                                    <span class="rot-icon">↺</span><span>Anti-clockwise</span>
                                </button>
                                <button class="rot-btn cw" onclick="qzRotateCW()">
                                    <span class="rot-icon">↻</span><span>Clockwise</span>
                                </button>
                            </div>
                            <div class="sub-row">
                                <button class="spin-btn" id="qzSpinBtn" onclick="qzToggleSpin()">▶ Auto-Spin</button>
                                <button class="reset-ring-btn" onclick="qzResetPlane()">↺ Reset plane</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT — quiz controls -->
                <div class="gear-right">

                    <!-- Operation selector -->
                    <div class="ctrl-card">
                        <div class="card-title">Choose operation</div>
                        <div class="rot-row">
                            <button class="rot-btn cw"  id="qzMultBtn" onclick="qzSelectOp('multiply')">
                                <span class="rot-icon">✖️</span><span>Multiply</span>
                            </button>
                            <button class="rot-btn cw" id="qzDivBtn"  onclick="qzSelectOp('divide')" style="border-color:var(--purple);color:var(--purple-dark);background:var(--purple-light);">
                                <span class="rot-icon">➗</span><span>Divide</span>
                            </button>
                        </div>
                    </div>

                    <!-- Current question -->
                    <div class="ring-display" id="qzQuestionCard">
                        <div class="ring-meta">
                            <div class="rm-label"    id="qzOpLabel">Multiplication</div>
                            <div class="ring-big-num" id="qzQuestion" style="font-size:28px;min-width:auto;">–</div>
                            <div class="rm-detail"   id="qzHint">Start at 0 on the reference arrow. Select a plane, then rotate clockwise to find the answer.</div>
                            <div class="rm-table"    id="qzPointerVal">Number at arrow: –</div>
                        </div>
                        <button class="hear-btn" onclick="qzHear()">🔊</button>
                    </div>

                    <!-- Plane selector -->
                    <div class="ctrl-card">
                        <div class="card-title">Select a plane</div>
                        <div class="ring-grid" id="qzPlaneGrid"></div>
                    </div>

                    <!-- Submit -->
                    <button class="ex-go-btn" id="qzSubmitBtn" onclick="qzSubmit()">
                        <span class="ex-go-icon">✓</span> Submit Answer
                    </button>

                    <!-- Feedback -->
                    <div id="qzFeedback" style="display:none;" class="ctrl-card">
                        <div class="card-title" id="qzFeedbackTitle">Result</div>
                        <div id="qzFeedbackBody" style="font-size:14px;font-weight:700;color:var(--text);line-height:1.8;"></div>
                        <button class="ex-next-btn" onclick="qzNext()" style="margin-top:10px;">Next Question →</button>
                    </div>

                </div>
            </div>
        </div>

    </main>
</div>

<!-- Toast -->
<div class="feedback-toast" id="toast"></div>

<script src="../../assets/js/speech.js"></script>
<script>
    window.NG_USER_ID  = <?= json_encode($ng_current_user['id']) ?>;
    window.NG_API_BASE = '../../api/';
</script>
<script src="../../assets/js/storage.js"></script>
<script>
/* ================================================================
   CONSTANTS
================================================================ */
const CS4  = 540;
const CX4  = CS4 / 2; // 270
const CY4  = CS4 / 2; // 270
const HUB4 = 28;
const RW4  = 24;
// Ring i: inner = HUB4 + i*RW4, outer = HUB4 + (i+1)*RW4
// Ring i represents ×(i+1) table: numbers = (i+1)*0, (i+1)*1, ..., (i+1)*9

const SKY_LIGHT   = '#ffe0c8';
const SKY_COLOR   = '#f4a571';
const RING_BG     = ['#fff8f0','#edfaf4','#edf4fb','#fef0f5','#f5eeff','#e8fcfe','#fffee8','#f0fbe8','#eeebff','#e6f8f6'];
const RING_BG_ACT = '#fde8d8';

/* ================================================================
   STATE
================================================================ */
// Progress tracking (based on Illustrated + Quiz activity, not free exploration)
let illustratedExplored = new Set();
let chalCorrect = 0;
let chalStreak  = 0;

/* ================================================================
/* ================================================================
   MODE SWITCH
================================================================ */
function setMode(mode) {
    document.getElementById('modeChalBtn').classList.toggle('active', mode === 'challenge');
    document.getElementById('modeQuizBtn').classList.toggle('active', mode === 'quiz');
    document.getElementById('chalMode').style.display  = mode === 'challenge'  ? 'block' : 'none';
    document.getElementById('quizMode').style.display  = mode === 'quiz'       ? 'block' : 'none';
    if (qzSpinInterval) qzStopSpin();
    if (mode === 'challenge') initChallenge();
    if (mode === 'quiz')      initQuiz();
}

/* ================================================================
   CHALLENGE MODE — one example at a time with pointer animation
================================================================ */
const MULT_EXAMPLES = [
    { plane:1, steps:5,  result:5  },
    { plane:2, steps:4,  result:8  },
    { plane:3, steps:4,  result:12 },
    { plane:4, steps:3,  result:12 },
    { plane:5, steps:6,  result:30 },
    { plane:6, steps:5,  result:30 },
    { plane:7, steps:4,  result:28 },
    { plane:8, steps:3,  result:24 },
    { plane:9, steps:3,  result:27 },
    { plane:10,steps:5,  result:50 },
];
// Division: dividend ÷ plane = quotient
// Start at 0 on the plane and move clockwise — the dividend appears
// at the arrow after exactly 'quotient' steps (same direction as multiply)
// dividend = plane * quotient  (always a clean multiple on the plane)
const DIV_EXAMPLES = [
    { plane:4, dividend:12, quotient:3  },   // 12 ÷ 4 = 3
    { plane:2, dividend:8,  quotient:4  },   // 8  ÷ 2 = 4
    { plane:3, dividend:12, quotient:4  },   // 12 ÷ 3 = 4
    { plane:5, dividend:20, quotient:4  },   // 20 ÷ 5 = 4
    { plane:6, dividend:30, quotient:5  },   // 30 ÷ 6 = 5
    { plane:7, dividend:21, quotient:3  },   // 21 ÷ 7 = 3
    { plane:8, dividend:24, quotient:3  },   // 24 ÷ 8 = 3
    { plane:9, dividend:27, quotient:3  },   // 27 ÷ 9 = 3
    { plane:10,dividend:40, quotient:4  },   // 40 ÷ 10= 4
    { plane:3, dividend:27, quotient:9  },   // 27 ÷ 3 = 9
];

let chalOp         = 'multiply';
let chalExIdx      = 0;
let chalAnimTimer4 = null;
let chalPointer    = null;  // { plane, stepsTotal, stepsDone, op }

let canvas4b, ctx4b;
let activeTable4b   = 0;
let tableOffsets4b  = new Array(10).fill(0);  // rotation offsets for challenge gear

function initChallenge() {
    if (!canvas4b) {
        canvas4b = document.getElementById('gearCanvas4b');
        ctx4b    = canvas4b.getContext('2d');
    }
    chalExIdx = 0;
    chalPointer = null;
    selectOp('multiply');
}

function selectOp(op) {
    chalOp    = op;
    chalExIdx = 0;
    clearInterval(chalAnimTimer4);
    chalPointer = null;

    const mBtn = document.getElementById('opMultBtn');
    const dBtn = document.getElementById('opDivBtn');
    if (op === 'multiply') {
        mBtn.style.opacity = '1';   mBtn.style.fontWeight = '900';
        dBtn.style.opacity = '0.5'; dBtn.style.fontWeight = '700';
    } else {
        dBtn.style.opacity = '1';   dBtn.style.fontWeight = '900';
        mBtn.style.opacity = '0.5'; mBtn.style.fontWeight = '700';
    }

    loadExample4();
}

function loadExample4() {
    const examples = chalOp === 'multiply' ? MULT_EXAMPLES : DIV_EXAMPLES;
    const ex       = examples[chalExIdx];
    const total    = examples.length;
    const sym  = chalOp === 'multiply' ? '×' : '÷';
    const a    = chalOp === 'multiply' ? ex.plane    : ex.dividend;
    const b    = chalOp === 'multiply' ? ex.steps    : ex.plane;
    const ans  = chalOp === 'multiply' ? ex.result   : ex.quotient;

    document.getElementById('exBadge4').textContent = `Example ${chalExIdx + 1} of ${total}`;
    document.getElementById('exSum4').textContent   = `${a} ${sym} ${b} = ?`;
    document.getElementById('exHint4').textContent  = `Use Plane ${ex.plane} (counts in ${ex.plane}s)`;

    // Uniform explainer structure for both operations:
    // 1) Where we start  2) Direction + what to count  3) How to read the answer
    if (chalOp === 'multiply') {
        document.getElementById('exDesc4').textContent =
            `Start at 0 on the reference arrow. Rotate clockwise ↻ and count ${ex.steps} spins — the number at the arrow is your answer!`;
        document.getElementById('chalStepText').innerHTML =
            `Step 1: <strong>Plane ${ex.plane}</strong> counts in ${ex.plane}s. Start with <strong>0</strong> at the reference arrow ▼.<br>` +
            `Step 2: Rotate <strong>clockwise ↻</strong>, one spin at a time, counting as you go: 1, 2, 3… up to <strong>${ex.steps} spins</strong>.<br>` +
            `Step 3: Read the number now sitting at the arrow — that's your answer.<br>` +
            `${ex.plane} × ${ex.steps} = <strong>${ex.result}</strong>`;
    } else {
        document.getElementById('exDesc4').textContent =
            `Start at 0 on the reference arrow. Rotate clockwise ↻ and count the spins until ${ex.dividend} reaches the arrow — the spins counted is your answer!`;
        document.getElementById('chalStepText').innerHTML =
            `Step 1: <strong>Plane ${ex.plane}</strong> counts in ${ex.plane}s. Start with <strong>0</strong> at the reference arrow ▼.<br>` +
            `Step 2: Rotate <strong>clockwise ↻</strong>, one spin at a time, counting as you go, until <strong>${ex.dividend}</strong> lands on the arrow.<br>` +
            `Step 3: Count how many spins you took to get there — that's your answer.<br>` +
            `${ex.dividend} ÷ ${ex.plane} = <strong>${ex.quotient}</strong>`;
    }

    document.getElementById('chalStepCard').style.display     = 'none';
    document.getElementById('chalIllustResult').style.display = 'none';

    // Reset canvas
    chalPointer    = null;
    activeTable4b  = ex.plane - 1;
    drawGear4b();
}

function nextExample4() {
    const examples = chalOp === 'multiply' ? MULT_EXAMPLES : DIV_EXAMPLES;
    chalExIdx = (chalExIdx + 1) % examples.length;
    clearInterval(chalAnimTimer4);
    chalPointer = null;
    loadExample4();
}

function showOnGear() {
    const examples = chalOp === 'multiply' ? MULT_EXAMPLES : DIV_EXAMPLES;
    const ex       = examples[chalExIdx];
    clearInterval(chalAnimTimer4);

    document.getElementById('chalStepCard').style.display     = 'block';
    document.getElementById('chalIllustResult').style.display = 'none';

    activeTable4b = ex.plane - 1;
    tableOffsets4b.fill(0);

    illustratedExplored.add(ex.plane);
    document.getElementById('tablesExplored2').textContent = illustratedExplored.size;
    document.getElementById('quizScore2').textContent      = Math.min(100, illustratedExplored.size * 10);

    // Both operations now rotate the SAME way: start at 0, go CLOCKWISE,
    // counting steps as the gear turns — only what we count for differs.
    const totalSteps = chalOp === 'multiply' ? ex.steps : ex.quotient;

    tableOffsets4b[activeTable4b] = 0;
    chalPointer = { op: chalOp, stepsTotal: totalSteps, stepsDone: 0, startOffset: 0 };
    drawGear4b();

    let done = 0;
    chalAnimTimer4 = setInterval(() => {
        done++;
        tableOffsets4b[activeTable4b] = done % 10;
        chalPointer.stepsDone = done;
        drawGear4b();
        if (done >= totalSteps) {
            clearInterval(chalAnimTimer4);
            if (chalOp === 'multiply') {
                document.getElementById('chalIllustEq').textContent  = `${ex.plane} × ${ex.steps} = ${ex.result}`;
                document.getElementById('chalIllustSub').textContent = `${ex.plane} jumped ${ex.steps} spins clockwise to land on ${ex.result}`;
                NG_Speech.sayInstruction(`${ex.plane} times ${ex.steps} equals ${ex.result}.`);
            } else {
                document.getElementById('chalIllustEq').textContent  = `${ex.dividend} ÷ ${ex.plane} = ${ex.quotient}`;
                document.getElementById('chalIllustSub').textContent = `Rotated clockwise ${ex.quotient} spins until ${ex.dividend} reached the arrow — counted ${ex.quotient} spins!`;
                NG_Speech.sayInstruction(`${ex.dividend} divided by ${ex.plane} equals ${ex.quotient}.`);
            }
            document.getElementById('chalIllustResult').style.display = 'block';
        }
    }, 550);

}
/* ================================================================
   SECOND CANVAS — challenge gear (rotates for illustration)
================================================================ */
function drawGear4b() {
    if (!ctx4b) return;
    const CS = 560, CX = 280, CY = 280;
    const SLOTS = 10, SLOT_DEG = 36;
    ctx4b.clearRect(0, 0, CS, CS);

    const GEAR_OUTER = HUB4 + 10 * RW4;
    const isDivide   = chalPointer && chalPointer.op === 'divide';
    const isMultiply = chalPointer && chalPointer.op === 'multiply';

    // Background disc
    ctx4b.beginPath();
    ctx4b.arc(CX, CY, GEAR_OUTER + 6, 0, Math.PI * 2);
    ctx4b.fillStyle = '#dde4ee';
    ctx4b.fill();

    // All rings — dim non-active rings heavily during animation so active ring pops
    for (let i = 9; i >= 0; i--) {
        const rIn  = HUB4 + i * RW4;
        const rOut = HUB4 + (i + 1) * RW4;
        const isActive = (i === activeTable4b);
        const dimmed   = chalPointer && !isActive;
        ctx4b.beginPath();
        ctx4b.arc(CX, CY, rOut, 0, Math.PI * 2);
        ctx4b.arc(CX, CY, rIn, 0, Math.PI * 2, true);
        if (isActive) {
            ctx4b.fillStyle = isDivide ? '#f3eeff' : '#edf9f4';
        } else {
            ctx4b.fillStyle = dimmed ? '#f4f6f8' : (i % 2 === 0 ? '#f8f9ff' : '#ffffff');
        }
        ctx4b.fill();
        ctx4b.beginPath();
        ctx4b.arc(CX, CY, rOut, 0, Math.PI * 2);
        ctx4b.strokeStyle = isActive ? (isDivide ? '#8250c8' : '#52c4a0') : '#dde3ea';
        ctx4b.lineWidth = isActive ? 2.5 : 0.8;
        ctx4b.stroke();
    }

    // ── ARC HIGHLIGHT — sweeps from the start (0) to the current step,
    //    showing the exact path traced on the active ring ──────────────────
    if (chalPointer && chalPointer.stepsDone > 0) {
        const i      = activeTable4b;
        const rIn    = HUB4 + i * RW4;
        const rOut   = HUB4 + (i + 1) * RW4;
        const rMid   = (rIn + rOut) / 2;
        const arcColor = isDivide ? '#8250c8' : '#52c4a0';

        // The reference arrow sits at angle -90° (270°) on screen.
        // The gear rotates clockwise, so as offset increases, slot 0's
        // position angle moves further clockwise from the arrow.
        // The swept path covers from the arrow position backwards to
        // where slot 0 currently sits — i.e. stepsDone slots' worth of arc.
        const arrowAngle   = -90; // degrees, pointing up
        const sweepDeg     = chalPointer.stepsDone * SLOT_DEG;
        const startAngle   = (arrowAngle - sweepDeg) * Math.PI / 180;
        const endAngle     = arrowAngle * Math.PI / 180;

        ctx4b.save();
        ctx4b.beginPath();
        ctx4b.arc(CX, CY, rOut + 3, startAngle, endAngle);
        ctx4b.arc(CX, CY, rIn  - 3, endAngle, startAngle, true);
        ctx4b.closePath();
        ctx4b.fillStyle = isDivide ? 'rgba(130,80,200,0.20)' : 'rgba(82,196,160,0.20)';
        ctx4b.fill();

        // Outline the swept arc edges for a clear "highlighted path" look
        ctx4b.beginPath();
        ctx4b.arc(CX, CY, rOut + 3, startAngle, endAngle);
        ctx4b.strokeStyle = arcColor;
        ctx4b.lineWidth = 3;
        ctx4b.setLineDash([5, 4]);
        ctx4b.stroke();
        ctx4b.setLineDash([]);
        ctx4b.restore();
    }

    // Numbers — rotate active plane using tableOffsets4b, dim others
    for (let i = 0; i < 10; i++) {
        const rIn  = HUB4 + i * RW4;
        const rOut = HUB4 + (i + 1) * RW4;
        const rMid = (rIn + rOut) / 2;
        const mult     = i + 1;
        const isActive = (i === activeTable4b);
        const offset   = tableOffsets4b[i] || 0;
        const dimmed   = chalPointer && !isActive;
        const fs = isActive ? Math.max(11, Math.floor(RW4 / 1.9)) : Math.max(8, Math.floor(RW4 / 2.5));
        ctx4b.font = `${isActive ? 900 : 600} ${fs}px Nunito, Segoe UI, sans-serif`;
        ctx4b.textAlign    = 'center';
        ctx4b.textBaseline = 'middle';

        // Track which slots have already swept past the arrow (counted so far)
        const stepsDone = chalPointer ? chalPointer.stepsDone : 0;

        for (let j = 0; j < SLOTS; j++) {
            const value    = mult * j;
            const atArrow  = (j === offset);
            const angleDeg = 270 + (j - offset) * SLOT_DEG;
            const angleRad = angleDeg * Math.PI / 180;
            const x = CX + rMid * Math.cos(angleRad);
            const y = CY + rMid * Math.sin(angleRad);

            ctx4b.save();
            ctx4b.translate(x, y);
            const label   = String(value);
            const metrics = ctx4b.measureText(label);
            const pw = metrics.width + 5, ph = fs + 5, pr = ph / 2;

            // Slots already swept past the arrow on the way from 0 to the
            // current offset (both ops now start at 0 and move clockwise)
            const alreadyPassed = isActive && stepsDone > 0 && j >= 0 && j < offset;

            ctx4b.beginPath();
            ctx4b.roundRect(-pw/2, -ph/2, pw, ph, pr);
            if (atArrow && isActive)      ctx4b.fillStyle = isDivide ? '#8250c8' : '#f4a571';
            else if (alreadyPassed)       ctx4b.fillStyle = isDivide ? 'rgba(130,80,200,0.22)' : 'rgba(82,196,160,0.22)';
            else if (dimmed)              ctx4b.fillStyle = 'rgba(255,255,255,0.5)';
            else                          ctx4b.fillStyle = 'rgba(255,255,255,0.95)';
            ctx4b.fill();

            ctx4b.globalAlpha = dimmed ? 0.35 : 1;
            ctx4b.fillStyle   = (atArrow && isActive) ? 'white'
                              : alreadyPassed         ? (isDivide ? '#8250c8' : '#1a7a50')
                              : isActive              ? '#d4824a'
                              :                         '#4a5568';
            ctx4b.fillText(label, 0, 0);
            ctx4b.globalAlpha = 1;
            ctx4b.restore();
        }
    }

    // ── Large step counter — centred, clear, prominent ────────────────────────
    if (chalPointer && chalPointer.stepsDone > 0) {
        const stepsDone = chalPointer.stepsDone;
        const op        = chalPointer.op;
        const color     = op === 'multiply' ? '#2eaa86' : '#8250c8';
        const label     = `↻ ${stepsDone}`;

        // Large badge at the hub
        ctx4b.beginPath();
        ctx4b.arc(CX, CY, HUB4 + 4, 0, Math.PI * 2);
        ctx4b.fillStyle = color;
        ctx4b.fill();
        ctx4b.font = 'bold 16px Nunito, sans-serif';
        ctx4b.textAlign = 'center';
        ctx4b.textBaseline = 'middle';
        ctx4b.fillStyle = 'white';
        ctx4b.fillText(String(stepsDone), CX, CY);

        // Direction label above the gear
        ctx4b.font = 'bold 13px Nunito, sans-serif';
        ctx4b.fillStyle = color;
        ctx4b.textAlign = 'center';
        ctx4b.fillText(label + ' spin' + (stepsDone > 1 ? 's' : ''), CX, CY - GEAR_OUTER - 22);

        // Current value at arrow — large label just inside the outer edge
        const atArrowVal = (activeTable4b + 1) * (tableOffsets4b[activeTable4b] || 0);
        ctx4b.font = 'bold 11px Nunito, sans-serif';
        ctx4b.fillStyle = color;
        ctx4b.textAlign = 'center';
        ctx4b.fillText('▼ ' + atArrowVal, CX, CY - GEAR_OUTER + 18);
    } else {
        // Hub label when idle
        ctx4b.beginPath();
        ctx4b.arc(CX, CY, HUB4, 0, Math.PI * 2);
        const g4b = ctx4b.createRadialGradient(CX, CY - 4, 2, CX, CY, HUB4);
        g4b.addColorStop(0, '#9d8fe0');
        g4b.addColorStop(1, '#5a4fb5');
        ctx4b.fillStyle = g4b;
        ctx4b.fill();
        ctx4b.font = 'bold 14px Nunito, sans-serif';
        ctx4b.textAlign = 'center';
        ctx4b.textBaseline = 'middle';
        ctx4b.fillStyle = 'white';
        ctx4b.fillText(chalOp === 'multiply' ? '×' : '÷', CX, CY);
    }

    // Reference arrow at top
    const arrowTipY  = CY - GEAR_OUTER - 1;
    ctx4b.beginPath();
    ctx4b.moveTo(CX, arrowTipY);
    ctx4b.lineTo(CX - 9, arrowTipY - 14);
    ctx4b.lineTo(CX + 9, arrowTipY - 14);
    ctx4b.closePath();
    ctx4b.fillStyle = '#f4a571';
    ctx4b.fill();
}

/* ================================================================
   UTILS
================================================================ */
function randInt(min, max) { return Math.floor(Math.random() * (max - min + 1)) + min; }

let _toast4Timer = null;
function showToast4(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = 'feedback-toast show' + (type ? ' ' + type : '');
    clearTimeout(_toast4Timer);
    _toast4Timer = setTimeout(() => t.classList.remove('show'), 2200);
}

/* ================================================================
   QUIZ MODE — use the gear to answer multiply & divide questions
================================================================ */
let qzCanvas, qzCtx;
let qzOp          = 'multiply';
let qzActivePlane = 0;
let qzOffset      = 0;        // current rotation of active plane
let qzQuestion    = null;     // { op, a, b, answer }
let qzDivPointerRad = null;   // fixed angle where dividend sat at question start
let qzSpinInterval = null;
let qzCorrectCount = 0;
let qzStreakCount  = 0;
let qzScoreCount  = 0;
let qzAnswered    = false;

function initQuiz() {
    if (!qzCanvas) {
        qzCanvas = document.getElementById('gearCanvasQz');
        qzCtx    = qzCanvas.getContext('2d');
    }
    qzOffset = 0;
    qzActivePlane = 0;
    qzAnswered = false;
    qzSelectOp('multiply');
}

/* ── Operation select ──────────────────────────────────────────────────── */
function qzSelectOp(op) {
    qzOp = op;
    const mBtn = document.getElementById('qzMultBtn');
    const dBtn = document.getElementById('qzDivBtn');
    if (op === 'multiply') {
        mBtn.style.opacity = '1'; mBtn.style.fontWeight = '900';
        dBtn.style.opacity = '0.5'; dBtn.style.fontWeight = '700';
    } else {
        dBtn.style.opacity = '1'; dBtn.style.fontWeight = '900';
        mBtn.style.opacity = '0.5'; mBtn.style.fontWeight = '700';
    }
    document.getElementById('qzOpLabel').textContent =
        op === 'multiply' ? 'Multiplication' : 'Division';
    qzGenerateQuestion();
}

/* ── Generate question ─────────────────────────────────────────────────── */
function qzGenerateQuestion() {
    qzOffset         = 0;
    qzDivPointerRad = null;
    qzAnswered      = false;
    document.getElementById('qzFeedback').style.display = 'none';
    document.getElementById('qzSubmitBtn').disabled     = false;
    document.getElementById('qzSubmitBtn').style.opacity = '1';

    let a, b, answer;
    if (qzOp === 'multiply') {
        a      = randInt4(1, 10);   // plane
        b      = randInt4(1, 10);   // steps
        answer = a * b;
        qzQuestion = { op:'multiply', a, b, answer };
        document.getElementById('qzQuestion').textContent = `${a} × ${b} = ?`;
        document.getElementById('qzHint').innerHTML =
            `Start at <strong>0</strong> on the reference arrow ▼. Use <strong>Plane ${a}</strong> and rotate <strong>clockwise ↻</strong>, counting ${b} spins — then read the number at the arrow.`;
        // Pre-select the right plane
        qzActivePlane = a - 1;
    } else {
        // Pick clean division: dividend = plane × quotient
        a      = randInt4(1, 10);         // divisor (plane)
        b      = randInt4(1, 10);         // quotient (answer)
        answer = a * b;                   // dividend
        qzQuestion = { op:'divide', a, b, answer };
        // Record the fixed canvas angle where the dividend lives at start (offset=0)
        // Dividend is at slot b on the plane. Slot j at offset 0 → angle 270 + j*36
        qzDivPointerRad = (270 + b * 36) * Math.PI / 180;
        document.getElementById('qzQuestion').textContent = `${answer} ÷ ${a} = ?`;
        document.getElementById('qzHint').innerHTML =
            `Start at <strong>0</strong> on the reference arrow ▼. Use <strong>Plane ${a}</strong> and rotate <strong>clockwise ↻</strong>, counting your spins, until <strong>${answer}</strong> reaches the arrow — the spins you counted is your answer.`;
        qzActivePlane = a - 1;
    }

    buildQzPlaneGrid();
    qzDrawGear();
    updateQzPointer();
    NG_Speech.sayInstruction(
        qzOp === 'multiply'
            ? `What is ${a} times ${b}?`
            : `What is ${answer} divided by ${a}?`
    );
}

/* ── Plane grid ────────────────────────────────────────────────────────── */
function buildQzPlaneGrid() {
    const grid = document.getElementById('qzPlaneGrid');
    grid.innerHTML = '';
    for (let i = 0; i < 10; i++) {
        const btn = document.createElement('button');
        btn.className = 'ring-btn' + (i === qzActivePlane ? ' active' : '');
        btn.innerHTML = `<strong>×${i+1}</strong><br><small>${i+1}–${(i+1)*10}</small>`;
        btn.onclick = () => {
            qzActivePlane = i;
            qzOffset = 0;
            buildQzPlaneGrid();
            qzDrawGear();
            updateQzPointer();
        };
        grid.appendChild(btn);
    }
}

/* ── Rotate controls ───────────────────────────────────────────────────── */
function qzRotateCW() {
    qzOffset = (qzOffset + 1) % 10;
    qzDrawGear();
    updateQzPointer();
}
function qzRotateCCW() {
    qzOffset = (qzOffset + 9) % 10;
    qzDrawGear();
    updateQzPointer();
}
function qzResetPlane() {
    if (qzSpinInterval) qzStopSpin();
    qzOffset = 0;
    qzDrawGear();
    updateQzPointer();
}
function qzToggleSpin() {
    if (qzSpinInterval) qzStopSpin(); else qzStartSpin();
}
function qzStartSpin() {
    document.getElementById('qzSpinBtn').textContent = '⏸ Stop';
    document.getElementById('qzSpinBtn').classList.add('spinning');
    qzSpinInterval = setInterval(qzRotateCW, 600);
}
function qzStopSpin() {
    clearInterval(qzSpinInterval);
    qzSpinInterval = null;
    document.getElementById('qzSpinBtn').textContent = '▶ Auto-Spin';
    document.getElementById('qzSpinBtn').classList.remove('spinning');
}

function updateQzPointer() {
    const val = (qzActivePlane + 1) * qzOffset;
    document.getElementById('qzPointerVal').textContent = `Number at arrow: ${val}`;
}

function qzHear() {
    if (qzQuestion) {
        const q = qzQuestion;
        NG_Speech.sayInstruction(
            q.op === 'multiply' ? `What is ${q.a} times ${q.b}?` : `What is ${q.answer} divided by ${q.a}?`
        );
    }
}

/* ── Submit answer ─────────────────────────────────────────────────────── */
function qzSubmit() {
    if (qzAnswered || !qzQuestion) return;
    if (qzSpinInterval) qzStopSpin();
    qzAnswered = true;

    const q          = qzQuestion;
    const pointerVal = (qzActivePlane + 1) * qzOffset;
    const planeMatch = (qzActivePlane === q.a - 1);

    let correct = false;

    if (q.op === 'multiply') {
        // Correct if: right plane selected AND pointer shows the right answer
        correct = planeMatch && (pointerVal === q.answer);
    } else {
        // Divide: correct if right plane AND they rotated to show the dividend,
        // then counted back — we check: pointer currently shows the dividend
        // OR offset equals the quotient (they've counted correctly)
        correct = planeMatch && (qzOffset === q.b || pointerVal === q.answer);
    }

    document.getElementById('qzSubmitBtn').disabled    = true;
    document.getElementById('qzSubmitBtn').style.opacity = '0.5';
    document.getElementById('qzFeedback').style.display = 'block';

    if (correct) {
        qzCorrectCount++;
        qzStreakCount++;
        qzScoreCount += 10 + (qzStreakCount > 2 ? 5 : 0);
        document.getElementById('qzFeedbackTitle').textContent = '✅ Correct!';
        document.getElementById('qzFeedbackTitle').style.color  = 'var(--mint-dark)';
        document.getElementById('qzFeedbackBody').innerHTML =
            q.op === 'multiply'
                ? `Well done! <strong>${q.a} × ${q.b} = ${q.answer}</strong>.<br>` +
                  `You selected Plane ${q.a} and spun ${q.b} spins clockwise to land on ${q.answer}. ✓`
                : `Well done! <strong>${q.answer} ÷ ${q.a} = ${q.b}</strong>.<br>` +
                  `You found ${q.answer} on Plane ${q.a} — it is ${q.b} spins from 0. ✓`;
        NG_Speech.sayInstruction(
            q.op === 'multiply'
                ? `Correct! ${q.a} times ${q.b} equals ${q.answer}.`
                : `Correct! ${q.answer} divided by ${q.a} equals ${q.b}.`
        );
    } else {
        qzStreakCount = 0;
        document.getElementById('qzFeedbackTitle').textContent = '❌ Not quite!';
        document.getElementById('qzFeedbackTitle').style.color  = 'var(--peach-dark)';
        const expected = q.op === 'multiply'
            ? `Select Plane ${q.a}, spin clockwise ${q.b} spins — the arrow shows <strong>${q.answer}</strong>.`
            : `Select Plane ${q.a}, rotate until <strong>${q.answer}</strong> is at the arrow — that is ${q.b} spins from 0.`;
        document.getElementById('qzFeedbackBody').innerHTML =
            `The answer is <strong>${q.op === 'multiply' ? q.answer : q.b}</strong>.<br>${expected}`;
        NG_Speech.sayInstruction(
            q.op === 'multiply'
                ? `Not quite. ${q.a} times ${q.b} equals ${q.answer}.`
                : `Not quite. ${q.answer} divided by ${q.a} equals ${q.b}.`
        );
    }

    document.getElementById('qzCorrect').textContent = qzCorrectCount;
    document.getElementById('qzStreak').textContent  = qzStreakCount;
    document.getElementById('qzScore').textContent   = qzScoreCount;
    NG_Storage.setLvl4Score(Math.min(100, qzScoreCount));

    // Highlight the gear to show the correct answer
    qzActivePlane = q.a - 1;
    qzOffset      = q.op === 'multiply' ? q.b % 10 : q.b % 10;
    buildQzPlaneGrid();
    qzDrawGear();
    updateQzPointer();
}

function qzNext() {
    qzGenerateQuestion();
}

function randInt4(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/* ── Quiz gear canvas drawing ──────────────────────────────────────────── */
function qzDrawGear() {
    if (!qzCtx) return;
    const CS = 560, CX = 280, CY = 280;
    const SLOTS = 10, SLOT_DEG = 36;
    qzCtx.clearRect(0, 0, CS, CS);

    const GEAR_OUT = HUB4 + 10 * RW4;

    // Background disc
    qzCtx.beginPath();
    qzCtx.arc(CX, CY, GEAR_OUT + 6, 0, Math.PI * 2);
    qzCtx.fillStyle = '#dde4ee';
    qzCtx.fill();

    // Rings — active plane uses question colour
    const qIsDiv = qzQuestion && qzQuestion.op === 'divide';
    for (let i = 9; i >= 0; i--) {
        const rIn  = HUB4 + i * RW4;
        const rOut = HUB4 + (i + 1) * RW4;
        const isAct = (i === qzActivePlane);
        qzCtx.beginPath();
        qzCtx.arc(CX, CY, rOut, 0, Math.PI * 2);
        qzCtx.arc(CX, CY, rIn,  0, Math.PI * 2, true);
        qzCtx.fillStyle = isAct
            ? (qIsDiv ? '#f3eeff' : '#edf9f4')
            : (i % 2 === 0 ? '#f8f9ff' : '#ffffff');
        qzCtx.fill();
        qzCtx.beginPath();
        qzCtx.arc(CX, CY, rOut, 0, Math.PI * 2);
        qzCtx.strokeStyle = isAct ? (qIsDiv ? '#8250c8' : '#2eaa86') : '#c4cedd';
        qzCtx.lineWidth   = isAct ? 2.5 : 1;
        qzCtx.stroke();
    }

    // Numbers
    // For division: dividend sits at slot = quotient (b) on plane = divisor-1 (a-1)
    const divDividendSlot = (qIsDiv && qzQuestion) ? qzQuestion.b : -1;

    for (let i = 0; i < 10; i++) {
        const rIn    = HUB4 + i * RW4;
        const rOut   = HUB4 + (i + 1) * RW4;
        const rMid   = (rIn + rOut) / 2;
        const mult   = i + 1;
        const isAct  = (i === qzActivePlane);
        const offset = (i === qzActivePlane) ? qzOffset : 0;
        const fs     = isAct ? Math.max(11, Math.floor(RW4 / 1.9)) : Math.max(9, Math.floor(RW4 / 2.5));
        qzCtx.font = `${isAct ? 900 : 600} ${fs}px Nunito, Segoe UI, sans-serif`;
        qzCtx.textAlign    = 'center';
        qzCtx.textBaseline = 'middle';

        for (let j = 0; j < SLOTS; j++) {
            const value      = mult * j;
            const atArrow    = (j === offset);
            // Is this slot the dividend slot on the active division plane?
            const isDividend = qIsDiv && isAct && (j === divDividendSlot);
            const angleDeg   = 270 + (j - offset) * SLOT_DEG;
            const angleRad   = angleDeg * Math.PI / 180;
            const x = CX + rMid * Math.cos(angleRad);
            const y = CY + rMid * Math.sin(angleRad);

            qzCtx.save();
            qzCtx.translate(x, y);
            const label   = String(value);
            const metrics = qzCtx.measureText(label);
            const pw = metrics.width + 5, ph = fs + 5, pr = ph / 2;

            // If this is the dividend slot, draw a larger glowing ring behind
            if (isDividend && !atArrow) {
                qzCtx.beginPath();
                qzCtx.arc(0, 0, Math.max(pw, ph) / 2 + 7, 0, Math.PI * 2);
                qzCtx.fillStyle = 'rgba(130,80,200,0.18)';
                qzCtx.fill();
                qzCtx.beginPath();
                qzCtx.arc(0, 0, Math.max(pw, ph) / 2 + 7, 0, Math.PI * 2);
                qzCtx.strokeStyle = '#8250c8';
                qzCtx.lineWidth = 2;
                qzCtx.setLineDash([3, 2]);
                qzCtx.stroke();
                qzCtx.setLineDash([]);
            }

            qzCtx.beginPath();
            qzCtx.roundRect(-pw/2, -ph/2, pw, ph, pr);
            if (atArrow && isAct)
                qzCtx.fillStyle = qIsDiv ? '#8250c8' : '#f4a571';
            else if (isDividend)
                qzCtx.fillStyle = '#e8d4ff';   // light purple pill for dividend
            else if (!isAct)
                qzCtx.fillStyle = 'rgba(255,255,255,0.6)';
            else
                qzCtx.fillStyle = 'rgba(255,255,255,0.95)';
            qzCtx.fill();

            qzCtx.globalAlpha = isAct ? 1 : 0.4;
            qzCtx.fillStyle   = (atArrow && isAct) ? 'white'
                              : isDividend          ? '#5b10a0'
                              : isAct               ? '#d4824a'
                              :                       '#4a5568';
            qzCtx.font = isDividend
                ? `900 ${fs}px Nunito, Segoe UI, sans-serif`
                : `${isAct ? 900 : 600} ${fs}px Nunito, Segoe UI, sans-serif`;
            qzCtx.fillText(label, 0, 0);
            qzCtx.globalAlpha = 1;
            ctx4b && (qzCtx.font = `${isAct ? 900 : 600} ${fs}px Nunito, Segoe UI, sans-serif`);
            qzCtx.restore();
        }
    }

    // ── Division: fixed needle pointing at where the dividend was at start ──────
    // This pointer is planted at the dividend's original angle and NEVER moves.
    // As the learner rotates the gear the dividend moves away; they count the
    // steps from the pointer to the reference arrow (▼) to get the answer.
    if (qIsDiv && qzDivPointerRad !== null) {
        const rIn  = HUB4 + qzActivePlane * RW4;
        const rOut = HUB4 + (qzActivePlane + 1) * RW4;
        const pr   = qzDivPointerRad;   // fixed — never changes

        // Full-height radial needle across the ring
        const nInX  = CX + rIn  * Math.cos(pr);
        const nInY  = CY + rIn  * Math.sin(pr);
        const nOutX = CX + rOut * Math.cos(pr);
        const nOutY = CY + rOut * Math.sin(pr);

        qzCtx.beginPath();
        qzCtx.moveTo(nInX, nInY);
        qzCtx.lineTo(nOutX, nOutY);
        qzCtx.strokeStyle = '#8250c8';
        qzCtx.lineWidth   = 3;
        qzCtx.lineCap     = 'round';
        qzCtx.stroke();

        // Arrowhead outside the ring, pointing inward along the needle
        const arrowTipX  = CX + (rOut + 2)  * Math.cos(pr);
        const arrowTipY  = CY + (rOut + 2)  * Math.sin(pr);
        const arrowMidX  = CX + (rOut + 14) * Math.cos(pr);
        const arrowMidY  = CY + (rOut + 14) * Math.sin(pr);
        const perpR = pr + Math.PI / 2;
        const wing  = 7;

        qzCtx.beginPath();
        qzCtx.moveTo(arrowTipX, arrowTipY);
        qzCtx.lineTo(arrowMidX + wing * Math.cos(perpR), arrowMidY + wing * Math.sin(perpR));
        qzCtx.lineTo(arrowMidX - wing * Math.cos(perpR), arrowMidY - wing * Math.sin(perpR));
        qzCtx.closePath();
        qzCtx.fillStyle   = '#8250c8';
        qzCtx.fill();
        qzCtx.strokeStyle = '#5b10a0';
        qzCtx.lineWidth   = 1;
        qzCtx.stroke();

        // Small label "÷ START" beside the pointer
        const lblR = rOut + 28;
        const lblX = CX + lblR * Math.cos(pr);
        const lblY = CY + lblR * Math.sin(pr);
        qzCtx.font = 'bold 9px Nunito, sans-serif';
        qzCtx.fillStyle = '#5b10a0';
        qzCtx.textAlign = 'center';
        qzCtx.textBaseline = 'middle';
        qzCtx.fillText('÷ START', lblX, lblY);
    }

    // Hub
    qzCtx.beginPath();
    qzCtx.arc(CX, CY, HUB4, 0, Math.PI * 2);
    const gQz = qzCtx.createRadialGradient(CX, CY - 4, 2, CX, CY, HUB4);
    gQz.addColorStop(0, '#9d8fe0');
    gQz.addColorStop(1, '#5a4fb5');
    qzCtx.fillStyle = gQz;
    qzCtx.fill();
    qzCtx.font = 'bold 14px Nunito, sans-serif';
    qzCtx.textAlign = 'center';
    qzCtx.textBaseline = 'middle';
    qzCtx.fillStyle = 'white';
    qzCtx.fillText('0', CX, CY);

    // Reference arrow
    const arrowTipY = CY - GEAR_OUT - 1;
    qzCtx.beginPath();
    qzCtx.moveTo(CX, arrowTipY);
    qzCtx.lineTo(CX - 9, arrowTipY - 14);
    qzCtx.lineTo(CX + 9, arrowTipY - 14);
    qzCtx.closePath();
    qzCtx.fillStyle   = '#f4a571';
    qzCtx.fill();
    qzCtx.strokeStyle = '#d4824a';
    qzCtx.lineWidth   = 1.5;
    qzCtx.stroke();
}

/* ================================================================
   INIT
================================================================ */
document.addEventListener('DOMContentLoaded', function () {
    initChallenge();

    setTimeout(() => {
        NG_Speech.sayInstruction('Welcome to Level 4! See how multiplication and division work on the gear, then try the quiz!');
    }, 500);
});
</script>
</body>
</html>
