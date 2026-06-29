<?php
// Number Gear — Level 6: Prime Numbers
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
    <title>Level 6 — Prime Numbers | Number Gear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/accessibility.js"></script>
    <script src="../../assets/js/i18n-common.js"></script>
    <script src="../../assets/js/i18n-level.js"></script>
    <script src="../../assets/js/music.js"></script>
    <style>
        .gear-intro {
            font-size: 14px; font-weight: 600; color: var(--text); line-height: 1.7;
            background: var(--purple-light); border-left: 4px solid var(--purple);
            border-radius: 10px; padding: 14px 18px; margin-bottom: 22px;
        }
        .gear-layout {
            display: flex; flex-direction: row; gap: 32px;
            align-items: flex-start; width: 100%;
        }
        .gear-left {
            flex: 1 1 0; display: flex; flex-direction: column;
            align-items: center; gap: 12px; min-width: 0; order: 1;
        }
        .gear-right {
            flex: 0 0 360px; width: 360px; display: flex;
            flex-direction: column; gap: 13px; order: 2;
        }
        @media (max-width: 900px) {
            .gear-layout { flex-direction: column; align-items: center; }
            .gear-right  { width: 100%; max-width: 560px; flex: none; }
        }
        #primeCanvas {
            display: block; width: 100%; max-width: 560px; height: auto;
            cursor: pointer; touch-action: manipulation;
            border-radius: 50%; box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }
        /* Shared card styles */
        .ctrl-card { background: var(--surface); border: 2px solid var(--border); border-radius: 16px; padding: 14px 16px; }
        .card-title { font-size: 11px; font-weight: 800; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 11px; }
        .ring-display { display: flex; align-items: center; gap: 12px; background: var(--peach-light); border: 2px solid var(--peach); border-radius: 14px; padding: 12px 14px; }
        .ring-big-num { font-size: 44px; font-weight: 900; color: var(--peach-dark); line-height: 1; min-width: 70px; text-align: center; animation: popIn 0.3s ease; }
        @keyframes popIn { 0%{transform:scale(0.8);opacity:0} 60%{transform:scale(1.12)} 100%{transform:scale(1);opacity:1} }
        .ring-meta { flex: 1; }
        .rm-label  { font-size: 11px; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.4px; }
        .rm-detail { font-size: 13px; font-weight: 800; color: var(--peach-dark); margin-top: 2px; }
        .rm-table  { font-size: 11px; color: var(--text-soft); font-weight: 600; margin-top: 3px; line-height: 1.5; }
        .hear-btn  { padding: 10px 12px; background: var(--peach); border: none; border-radius: 11px; color: white; font-size: 18px; cursor: pointer; font-family: inherit; transition: 0.18s ease; flex-shrink: 0; }
        .hear-btn:hover { background: var(--peach-dark); }
        .ring-btn  { padding: 7px 3px; border: 2px solid var(--border); border-radius: 10px; background: var(--surface); font-size: 11px; font-weight: 800; cursor: pointer; color: var(--text-soft); font-family: inherit; text-align: center; transition: 0.18s ease; line-height: 1.3; }
        .ring-btn:hover  { border-color: var(--peach); color: var(--peach-dark); }
        .ring-btn.active { background: var(--peach); border-color: var(--peach); color: white; }
        .ring-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; }
        .rot-row { display: flex; gap: 8px; }
        .rot-btn  { flex: 1; padding: 13px 6px; border: 2.5px solid; border-radius: 13px; background: var(--surface); font-size: 12px; font-weight: 800; cursor: pointer; font-family: inherit; display: flex; flex-direction: column; align-items: center; gap: 4px; transition: 0.18s ease; line-height: 1.2; }
        .rot-btn .rot-icon { font-size: 24px; line-height: 1; }
        .rot-btn.ccw { border-color: var(--purple); color: var(--purple-dark); background: var(--purple-light); }
        .rot-btn.cw  { border-color: var(--mint);   color: var(--mint-dark);   background: var(--mint-light);  }
        .rot-btn.ccw:hover { background: var(--purple); color: white; }
        .rot-btn.cw:hover  { background: var(--mint);   color: white; }
        .rot-btn:active    { transform: scale(0.96); }
        .sub-row { display: flex; gap: 8px; margin-top: 8px; }
        .spin-btn { flex: 1; padding: 9px 8px; border: 2px solid var(--sky); border-radius: 10px; background: var(--sky-light); color: var(--sky-dark); font-size: 12px; font-weight: 800; cursor: pointer; font-family: inherit; transition: 0.18s ease; text-align: center; }
        .spin-btn.spinning { background: var(--sky); color: white; }
        .reset-ring-btn { padding: 9px 13px; border: 2px solid var(--border); border-radius: 10px; background: var(--surface); font-size: 13px; cursor: pointer; font-family: inherit; font-weight: 800; color: var(--text-soft); transition: 0.18s ease; white-space: nowrap; }
        .reset-ring-btn:hover { background: var(--peach-light); border-color: var(--peach); color: var(--peach-dark); }
        .action-row { display: flex; gap: 8px; }
        .action-btn { flex: 1; padding: 11px 8px; border: 2px solid var(--border); border-radius: 12px; background: var(--surface); font-size: 12px; font-weight: 800; cursor: pointer; font-family: inherit; color: var(--text-soft); transition: 0.18s ease; text-align: center; }
        .action-btn.reset { border-color: var(--mint); color: var(--mint-dark); }
        .action-btn.reset:hover { background: var(--mint-light); }
        .stats-row { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; }
        .stat-chip-sm { background: var(--surface); border: 2px solid var(--border); border-radius: 10px; padding: 6px 12px; font-size: 12px; font-weight: 800; color: var(--text-soft); }
        .stat-chip-sm span { color: var(--peach-dark); font-size: 15px; }

        /* Mix card */
        .mix-card {
            background: linear-gradient(135deg, var(--purple-light) 0%, #fde8d8 100%);
            border: 2px solid var(--purple); border-radius: 16px; padding: 14px 16px;
            display: none;
        }
        .mix-card.show { display: block; animation: popIn 0.35s ease; }
        .mix-card-title { font-size: 14px; font-weight: 900; color: var(--purple-dark); margin-bottom: 7px; }
        .mix-card-desc  { font-size: 12px; font-weight: 600; color: var(--text); line-height: 1.55; margin-bottom: 10px; }
        .mix-prog-label { font-size: 12px; font-weight: 800; color: var(--purple-dark); margin-bottom: 5px; }
        .mix-prog-track { height: 8px; background: rgba(0,0,0,0.1); border-radius: 6px; overflow: hidden; margin-bottom: 10px; }
        .mix-prog-fill  { height: 100%; background: var(--mint); border-radius: 6px; width: 0%; transition: width 0.4s ease; }
        .mix-hint       { font-size: 11px; font-weight: 700; color: var(--text-soft); background: rgba(255,255,255,0.65); border-radius: 8px; padding: 6px 10px; min-height: 28px; line-height: 1.5; }
        .mix-hint.aligned { color: var(--mint-dark); }

        /* Tabs */
        .lvl6-tabs { display: flex; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; }
        .lvl6-tab {
            padding: 9px 20px; border: 2px solid var(--border); border-radius: 20px;
            background: var(--surface); font-size: 14px; font-weight: 800;
            cursor: pointer; font-family: inherit; color: var(--text-soft);
            transition: 0.18s ease;
        }
        .lvl6-tab:hover  { border-color: var(--purple); color: var(--purple-dark); }
        .lvl6-tab.active { background: var(--purple); border-color: var(--purple); color: white; }

        /* Sort quiz */
        .sort-intro {
            font-size: 14px; font-weight: 700; color: var(--text);
            background: var(--purple-light); border-left: 4px solid var(--purple);
            border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; line-height: 1.6;
        }
        .sort-pool-label {
            font-size: 11px; font-weight: 800; color: var(--text-soft);
            text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 8px;
        }
        .sort-pool {
            display: flex; flex-wrap: wrap; gap: 8px;
            min-height: 52px; padding: 10px;
            border: 2px dashed var(--border); border-radius: 14px;
            background: var(--bg); margin-bottom: 14px;
            transition: border-color 0.2s;
        }
        .sort-pool.drag-over { border-color: var(--purple); background: var(--purple-light); }
        .sort-answer-label {
            font-size: 11px; font-weight: 800; color: var(--text-soft);
            text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 8px;
        }
        .sort-answer {
            display: flex; flex-wrap: wrap; gap: 8px;
            min-height: 52px; padding: 10px;
            border: 2px dashed var(--mint); border-radius: 14px;
            background: var(--mint-light); margin-bottom: 16px;
        }
        .sort-answer.drag-over { border-color: var(--mint-dark); background: #b8f0da; }
        .prime-tile {
            padding: 10px 14px; border-radius: 12px;
            font-size: 18px; font-weight: 900;
            cursor: grab; user-select: none;
            background: var(--surface); border: 2px solid var(--border);
            color: var(--text); transition: 0.15s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .prime-tile:hover  { border-color: var(--purple); color: var(--purple-dark); transform: scale(1.06); }
        .prime-tile.dragging { opacity: 0.45; transform: scale(0.96); }
        .prime-tile.correct  { background: var(--mint-light); border-color: var(--mint); color: var(--mint-dark); }
        .prime-tile.wrong    { background: #fde8e8; border-color: #e05050; color: #c03030; }
        .prime-tile.selected-prime { background: var(--purple-light); border-color: var(--purple); color: var(--purple-dark); transform: scale(1.05); }
        .sort-check-btn {
            width: 100%; padding: 13px; background: var(--purple); color: white;
            border: none; border-radius: 13px; font-size: 15px; font-weight: 900;
            font-family: inherit; cursor: pointer; transition: 0.18s ease; margin-bottom: 8px;
        }
        .sort-check-btn:hover  { background: var(--purple-dark); }
        .sort-check-btn:active { transform: scale(0.97); }
        .sort-new-btn {
            width: 100%; padding: 11px; background: transparent; color: var(--mint-dark);
            border: 2px solid var(--mint); border-radius: 13px; font-size: 14px; font-weight: 800;
            font-family: inherit; cursor: pointer; transition: 0.18s ease;
        }
        .sort-new-btn:hover { background: var(--mint-light); }
        .sort-result {
            padding: 14px 16px; border-radius: 14px; font-size: 14px; font-weight: 800;
            text-align: center; line-height: 1.6; margin-top: 8px; display: none;
        }
        .sort-result.correct { background: var(--mint-light); border: 2px solid var(--mint); color: var(--mint-dark); }
        .sort-result.wrong   { background: #fde8e8; border: 2px solid #e05050; color: #c03030; }
        .sort-score-row { display: flex; gap: 8px; margin-bottom: 12px; }

        /* Prime fact card */
        /* Prime fact card */
        .prime-fact-card {
            background: linear-gradient(135deg, var(--purple-light) 0%, #fde8d8 100%);
            border: 2px solid var(--purple); border-radius: 16px; padding: 14px 16px;
            display: none;
        }
        .prime-fact-card.show { display: block; animation: popIn 0.35s ease; }
        .pf-title { font-size: 12px; font-weight: 800; color: var(--purple-dark); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .pf-body  { font-size: 13px; font-weight: 600; color: var(--text); line-height: 1.65; }
        .pf-body strong { color: var(--purple-dark); }

        /* Ring selector tabs (batches) */
        .batch-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
        .batch-btn { padding: 5px 11px; border: 2px solid var(--border); border-radius: 20px; background: var(--surface); font-size: 12px; font-weight: 800; cursor: pointer; color: var(--text-soft); font-family: inherit; transition: 0.18s ease; }
        .batch-btn:hover  { border-color: var(--purple); color: var(--purple-dark); }
        .batch-btn.active { background: var(--purple); border-color: var(--purple); color: white; }
    </style>
    <script>document.addEventListener('DOMContentLoaded', function () { if (window.NG_I18nCommon) NG_I18nCommon.apply(6); });</script>
</head>
<body>
<div class="app-shell">

    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">🔵</div>
            <div>
                <h1 id="lvlHeading">Level 6</h1>
                <p data-i18n="subtitle">Prime Numbers Gear</p>
            </div>
        </div>
        <a href="../../index.php" class="back-btn" id="lvlBackLink">← Home</a>
    </header>

    <main class="level-page">

        <!-- Mode tabs -->
        <div class="lvl6-tabs">
            <button class="lvl6-tab active" id="tab-explorer" onclick="lvl6SetMode('explorer')" data-i18n="tabExplorer">⚙️ Gear Explorer</button>
            <button class="lvl6-tab"        id="tab-sort"     onclick="lvl6SetMode('sort')" data-i18n="tabSort">🔍 Prime Quiz</button>
        </div>

        <!-- GEAR EXPLORER section -->
        <div id="lvl6-explorer">

        <p class="gear-intro" data-i18n-html="gearIntroHtml">
            A <strong>prime number</strong> is a number greater than 1 that can only be divided exactly by <strong>1</strong> and <strong>itself</strong> — no other number divides it evenly.
            For example, <strong>7</strong> is prime because only 1 and 7 divide it. But 6 is <em>not</em> prime because 2 and 3 also divide it.
            <br><br>
            The Number Gear below shows the first <strong>100 prime numbers</strong> arranged across <strong>10 rings</strong> of 10 primes each.
            Spin a ring to explore its primes, click any prime to hear it and learn a fact about it!
        </p>

        <div class="gear-layout">

            <!-- LEFT — Canvas -->
            <div class="gear-left">
                <div class="stats-row">
                    <div class="stat-chip-sm"><span data-i18n="primesExploredLabel">Primes explored:</span> <span id="primesExplored">0</span>/100</div>
                    <div class="stat-chip-sm"><span data-i18n="currentRingLabelText">Current ring:</span> <span id="currentRingLabel">1 (2–29)</span></div>
                </div>
                <canvas id="primeCanvas" width="560" height="560"></canvas>
            </div>

            <!-- RIGHT — Controls -->
            <div class="gear-right">

                <!-- Prime at pointer -->
                <div class="ring-display">
                    <div class="ring-meta">
                        <div class="rm-label" data-i18n="primeAtPointerLabel">Prime at pointer</div>
                        <div class="ring-big-num" id="primeAtPointer">2</div>
                        <div class="rm-detail"    id="primeRingDetail">Ring 1 · position 1</div>
                        <div class="rm-table"     id="primeOrdinal">The 1st prime number</div>
                    </div>
                    <button class="hear-btn" onclick="hearCurrentPrime()">🔊</button>
                </div>

                <!-- Select ring batch -->
                <div class="ctrl-card">
                    <div class="card-title" data-i18n="selectRingTitle">Select a ring</div>
                    <div class="ring-grid" id="ringGrid"></div>
                </div>

                <!-- Rotate -->
                <div class="ctrl-card">
                    <div class="card-title" data-i18n="rotateRingTitle">Rotate selected ring</div>
                    <div class="rot-row">
                        <button class="rot-btn ccw" onclick="rotateCCW()">
                            <span class="rot-icon">↺</span>
                            <span data-i18n="antiClockwise">Anti-clockwise</span>
                        </button>
                        <button class="rot-btn cw" onclick="rotateCW()">
                            <span class="rot-icon">↻</span>
                            <span data-i18n="clockwise">Clockwise</span>
                        </button>
                    </div>
                    <div class="sub-row">
                        <button class="spin-btn" id="spinBtn" onclick="toggleSpin()" data-i18n="autoSpin">▶ Auto-Spin</button>
                        <button class="reset-ring-btn" onclick="resetRing()" data-i18n="resetThisRing">↺ This ring</button>
                    </div>
                </div>

                <!-- Gear actions -->
                <div class="ctrl-card">
                    <div class="card-title" data-i18n="gearActionsTitle">Gear actions</div>
                    <div class="action-row">
                        <button class="action-btn reset" onclick="resetAll()" data-i18n="resetAllBtn">↺ Reset All</button>
                        <button class="action-btn" id="highlightBtn"
                            style="border-color:var(--peach);color:var(--peach-dark);"
                            onclick="toggleHighlight()" data-i18n="highlightOff">✨ Highlight Primes</button>
                    </div>
                    <div class="action-row" style="margin-top:8px;">
                        <button class="action-btn" id="mixBtn"
                            style="border-color:var(--purple);color:var(--purple-dark);"
                            onclick="toggleMix()" data-i18n="mixChallengeBtn">🎲 Mix Challenge</button>
                    </div>
                </div>

                <!-- Mix challenge card -->
                <div class="mix-card" id="mixCard">
                    <div class="mix-card-title" data-i18n="mixCardTitle">🎯 Mix Challenge</div>
                    <div class="mix-card-desc" data-i18n-html="mixCardDescHtml">All rings are shuffled! Select a ring, then use <strong>↺ Anti-clockwise</strong> or <strong>↻ Clockwise</strong> to rotate it until its first prime lines up with the <strong>orange arrow ▼</strong>.</div>
                    <div class="mix-prog-label" id="mixProgLabel">0 / 10 rings aligned</div>
                    <div class="mix-prog-track"><div class="mix-prog-fill" id="mixProgFill"></div></div>
                    <div class="mix-hint" id="mixHint" data-i18n="mixHintDefault">Select a ring to begin</div>
                </div>

                <!-- Prime fact -->
                <div class="prime-fact-card" id="primeFactCard">
                    <div class="pf-title" data-i18n="primeFactTitle">📘 Prime Fact</div>
                    <div class="pf-body" id="primeFactBody"></div>
                </div>

            </div>
        </div>
        </div><!-- /lvl6-explorer -->

        <!-- SORT QUIZ section -->
        <div id="lvl6-sort" style="display:none;">
            <p class="sort-intro" data-i18n-html="sortIntroHtml">
                🔍 <strong>Which ones are prime?</strong> Each question shows you a mix of numbers.
                Some are <strong>prime</strong> (only divisible by 1 and themselves) and some are <strong>not prime</strong>.
                Tap every number you think is prime, then check your answer!
            </p>

            <!-- Score chips -->
            <div class="sort-score-row">
                <div class="stat-chip-sm"><span data-i18n="correctChip">✅ Correct:</span> <span id="sortCorrect">0</span></div>
                <div class="stat-chip-sm"><span data-i18n="wrongChip">❌ Wrong:</span> <span id="sortWrong">0</span></div>
                <div class="stat-chip-sm"><span data-i18n="questionsChip">🔢 Questions:</span> <span id="sortAttempts">0</span></div>
            </div>

            <!-- Question instruction -->
            <div class="sort-pool-label" id="sortInstruction" data-i18n="sortInstruction">Tap all the PRIME numbers below:</div>

            <!-- Number tiles -->
            <div class="sort-pool" id="sortPool" style="min-height:70px;border-style:solid;border-color:var(--purple);"></div>

            <!-- Selected primes -->
            <div class="sort-answer-label" data-i18n="sortAnswerLabel">Your selected primes:</div>
            <div class="sort-answer" id="sortAnswer" style="min-height:52px;"></div>

            <!-- Buttons -->
            <button class="sort-check-btn" onclick="checkSortAnswer()" data-i18n="checkAnswerBtn">✓ Check my answer</button>
            <button class="sort-new-btn"   onclick="newSortQuestion()" data-i18n="nextQuestionBtn">🔄 Next question</button>

            <!-- Result -->
            <div class="sort-result" id="sortResult"></div>

            <!-- Answer reveal -->
            <div id="sortReveal" style="display:none;margin-top:10px;padding:12px 14px;background:var(--purple-light);border:2px solid var(--purple);border-radius:12px;font-size:13px;font-weight:700;color:var(--purple-dark);line-height:1.7;"></div>
        </div><!-- /lvl6-sort -->

    </main>
</div>

<div class="feedback-toast" id="toast"></div>

<script src="../../assets/js/speech.js"></script>
<script>
    window.NG_USER_ID  = <?= json_encode($ng_current_user['id']) ?>;
    window.NG_API_BASE = '../../api/';
</script>
<script src="../../assets/js/storage.js"></script>
<script>
/* ============================================================
   FIRST 100 PRIME NUMBERS
============================================================ */
const PRIMES = [
      2,   3,   5,   7,  11,  13,  17,  19,  23,  29,
     31,  37,  41,  43,  47,  53,  59,  61,  67,  71,
     73,  79,  83,  89,  97, 101, 103, 107, 109, 113,
    127, 131, 137, 139, 149, 151, 157, 163, 167, 173,
    179, 181, 191, 193, 197, 199, 211, 223, 227, 229,
    233, 239, 241, 251, 257, 263, 269, 271, 277, 281,
    283, 293, 307, 311, 313, 317, 331, 337, 347, 349,
    353, 359, 367, 373, 379, 383, 389, 397, 401, 409,
    419, 421, 431, 433, 439, 443, 449, 457, 461, 463,
    467, 479, 487, 491, 499, 503, 509, 521, 523, 541
];

// 10 rings of 10 primes each
const NUM_RINGS  = 10;
const RING_SIZE  = 10;
const SLOTS      = 10;
const SLOT_DEG   = 360 / SLOTS;

// Ring colours (pastel fills + accents)
const RING_FILLS = [
    '#fff8f0','#edfaf4','#edf4fb','#fef0f5',
    '#f5eeff','#e8fcfe','#fffee8','#f0fbe8',
    '#eeebff','#e6f8f6'
];
const RING_ACCENTS = [
    '#b84800','#1a7a50','#1a5fa0','#a0184a',
    '#5b1fa0','#0a6e72','#9a7000','#3d7200',
    '#3d1fa0','#006660'
];

/* ============================================================
   L6 — translations (static text + dynamic message templates)
============================================================ */
const L6 = {
    en: {
        subtitle: 'Prime Numbers Gear',
        tabExplorer: '⚙️ Gear Explorer', tabSort: '🔍 Prime Quiz',
        gearIntroHtml: 'A <strong>prime number</strong> is a number greater than 1 that can only be divided exactly by <strong>1</strong> and <strong>itself</strong> — no other number divides it evenly. For example, <strong>7</strong> is prime because only 1 and 7 divide it. But 6 is <em>not</em> prime because 2 and 3 also divide it.<br><br>The Number Gear below shows the first <strong>100 prime numbers</strong> arranged across <strong>10 rings</strong> of 10 primes each. Spin a ring to explore its primes, click any prime to hear it and learn a fact about it!',
        primesExploredLabel: 'Primes explored:', currentRingLabelText: 'Current ring:',
        primeAtPointerLabel: 'Prime at pointer',
        ringPositionLabel: 'Ring {ring} · position {pos}', primeOrdinalMsg: 'The {ord} prime number',
        selectRingTitle: 'Select a ring', rotateRingTitle: 'Rotate selected ring',
        antiClockwise: 'Anti-clockwise', clockwise: 'Clockwise',
        autoSpin: '▶ Auto-Spin', stopSpin: '⏸ Stop', resetThisRing: '↺ This ring',
        gearActionsTitle: 'Gear actions', resetAllBtn: '↺ Reset All',
        highlightOn: '✨ Highlighted', highlightOff: '✨ Highlight Primes',
        mixChallengeBtn: '🎲 Mix Challenge', exitChallengeBtn: '✕ Exit Challenge',
        mixCardTitle: '🎯 Mix Challenge',
        mixCardDescHtml: 'All rings are shuffled! Select a ring, then use <strong>↺ Anti-clockwise</strong> or <strong>↻ Clockwise</strong> to rotate it until its first prime lines up with the <strong>orange arrow ▼</strong>.',
        mixProgLabel: '{aligned} / 10 rings aligned', mixHintDefault: 'Select a ring to begin',
        mixHintAligned: '✓ Ring {ring} aligned! Select another ring.',
        mixHintCW: 'Ring {ring}: rotate ↻ clockwise {n} steps.',
        mixHintCCW: 'Ring {ring}: rotate ↺ anti-clockwise {n} steps.',
        primeFactTitle: '📘 Prime Fact',
        sortIntroHtml: '🔍 <strong>Which ones are prime?</strong> Each question shows you a mix of numbers. Some are <strong>prime</strong> (only divisible by 1 and themselves) and some are <strong>not prime</strong>. Tap every number you think is prime, then check your answer!',
        correctChip: '✅ Correct:', wrongChip: '❌ Wrong:', questionsChip: '🔢 Questions:',
        sortInstruction: 'Tap all the PRIME numbers below:', sortAnswerLabel: 'Your selected primes:',
        sortAnswerPlaceholder: 'Tap numbers above to select them…',
        checkAnswerBtn: '✓ Check my answer', nextQuestionBtn: '🔄 Next question',
        clickToDeselect: 'Click to deselect', selectAtLeastOne: 'Tap at least one number you think is prime!',
        ringResetToast: 'Ring {n} reset ↺', allRingsResetToast: 'All rings reset ↺',
        highlightOnToast: 'Explored primes highlighted ✨', highlightOffToast: 'Highlight off',
        ringsMixedToast: 'Rings mixed! 🎲 Align them all!', allAlignedToast: '🎉 All rings aligned! Brilliant!',
        brilliantMsg: '🌟 <strong>Brilliant!</strong> You found all the prime numbers!',
        primesWereLabel: '✅ The primes were: <strong>{list}</strong>',
        notPrimeLabel: '🔵 Not prime: {list}',
        notPrimeFactorsLabel: '🔴 Not prime (these have other factors): {list}',
        notQuiteLabel: '❌ <strong>Not quite!</strong> ',
        notPrimeSuffix: ' <strong>not prime</strong>. ',
        youMissedLabel: 'You missed: <strong>{list}</strong>.',
        genericPrimeFact: '<strong>{p}</strong> is the {ord} prime number. It can only be divided by 1 and {p}.'
    },
    de: {
        subtitle: 'Primzahlen-Zahnrad',
        tabExplorer: '⚙️ Zahnrad-Entdecker', tabSort: '🔍 Primzahlen-Quiz',
        gearIntroHtml: 'Eine <strong>Primzahl</strong> ist eine Zahl größer als 1, die sich nur genau durch <strong>1</strong> und <strong>sich selbst</strong> teilen lässt — keine andere Zahl teilt sie ohne Rest. Zum Beispiel ist <strong>7</strong> eine Primzahl, weil sie nur durch 1 und 7 teilbar ist. Aber 6 ist <em>keine</em> Primzahl, weil sie auch durch 2 und 3 teilbar ist.<br><br>Das Zahnrad unten zeigt die ersten <strong>100 Primzahlen</strong>, verteilt auf <strong>10 Ringe</strong> mit je 10 Primzahlen. Drehe einen Ring, um seine Primzahlen zu entdecken, und klicke auf eine Primzahl, um sie zu hören und einen Fakt darüber zu erfahren!',
        primesExploredLabel: 'Erforschte Primzahlen:', currentRingLabelText: 'Aktueller Ring:',
        primeAtPointerLabel: 'Primzahl am Zeiger',
        ringPositionLabel: 'Ring {ring} · Position {pos}', primeOrdinalMsg: 'Die {ord} Primzahl',
        selectRingTitle: 'Ring auswählen', rotateRingTitle: 'Ausgewählten Ring drehen',
        antiClockwise: 'Gegen den Uhrzeigersinn', clockwise: 'Im Uhrzeigersinn',
        autoSpin: '▶ Auto-Drehung', stopSpin: '⏸ Stopp', resetThisRing: '↺ Diesen Ring',
        gearActionsTitle: 'Zahnrad-Aktionen', resetAllBtn: '↺ Alles zurücksetzen',
        highlightOn: '✨ Hervorgehoben', highlightOff: '✨ Primzahlen hervorheben',
        mixChallengeBtn: '🎲 Mix-Herausforderung', exitChallengeBtn: '✕ Herausforderung verlassen',
        mixCardTitle: '🎯 Mix-Herausforderung',
        mixCardDescHtml: 'Alle Ringe sind durchmischt! Wähle einen Ring und drehe ihn mit <strong>↺ gegen den Uhrzeigersinn</strong> oder <strong>↻ im Uhrzeigersinn</strong>, bis seine erste Primzahl mit dem <strong>orangen Pfeil ▼</strong> übereinstimmt.',
        mixProgLabel: '{aligned} / 10 Ringe ausgerichtet', mixHintDefault: 'Wähle einen Ring, um zu beginnen',
        mixHintAligned: '✓ Ring {ring} ausgerichtet! Wähle einen anderen Ring.',
        mixHintCW: 'Ring {ring}: drehe ↻ im Uhrzeigersinn um {n} Schritte.',
        mixHintCCW: 'Ring {ring}: drehe ↺ gegen den Uhrzeigersinn um {n} Schritte.',
        primeFactTitle: '📘 Primzahl-Fakt',
        sortIntroHtml: '🔍 <strong>Welche sind Primzahlen?</strong> Jede Frage zeigt dir eine Mischung von Zahlen. Manche sind <strong>Primzahlen</strong> (nur durch 1 und sich selbst teilbar) und manche sind <strong>keine Primzahlen</strong>. Tippe jede Zahl an, die du für eine Primzahl hältst, und prüfe dann deine Antwort!',
        correctChip: '✅ Richtig:', wrongChip: '❌ Falsch:', questionsChip: '🔢 Fragen:',
        sortInstruction: 'Tippe alle PRIMZAHLEN unten an:', sortAnswerLabel: 'Deine ausgewählten Primzahlen:',
        sortAnswerPlaceholder: 'Tippe oben Zahlen an, um sie auszuwählen …',
        checkAnswerBtn: '✓ Antwort prüfen', nextQuestionBtn: '🔄 Nächste Frage',
        clickToDeselect: 'Klicken zum Abwählen', selectAtLeastOne: 'Tippe mindestens eine Zahl an, die du für eine Primzahl hältst!',
        ringResetToast: 'Ring {n} zurückgesetzt ↺', allRingsResetToast: 'Alle Ringe zurückgesetzt ↺',
        highlightOnToast: 'Erforschte Primzahlen hervorgehoben ✨', highlightOffToast: 'Hervorhebung aus',
        ringsMixedToast: 'Ringe gemischt! 🎲 Richte sie alle aus!', allAlignedToast: '🎉 Alle Ringe ausgerichtet! Großartig!',
        brilliantMsg: '🌟 <strong>Großartig!</strong> Du hast alle Primzahlen gefunden!',
        primesWereLabel: '✅ Die Primzahlen waren: <strong>{list}</strong>',
        notPrimeLabel: '🔵 Keine Primzahl: {list}',
        notPrimeFactorsLabel: '🔴 Keine Primzahl (haben weitere Teiler): {list}',
        notQuiteLabel: '❌ <strong>Nicht ganz!</strong> ',
        notPrimeSuffix: ' <strong>ist/sind keine Primzahl(en)</strong>. ',
        youMissedLabel: 'Du hast übersehen: <strong>{list}</strong>.',
        genericPrimeFact: '<strong>{p}</strong> ist die {ord} Primzahl. Sie lässt sich nur durch 1 und {p} teilen.'
    },
    fr: {
        subtitle: "Engrenage des nombres premiers",
        tabExplorer: "⚙️ Explorateur d'engrenage", tabSort: '🔍 Quiz des nombres premiers',
        gearIntroHtml: "Un <strong>nombre premier</strong> est un nombre supérieur à 1 qui ne peut être divisé exactement que par <strong>1</strong> et <strong>lui-même</strong> — aucun autre nombre ne le divise sans reste. Par exemple, <strong>7</strong> est premier car seuls 1 et 7 le divisent. Mais 6 n'est <em>pas</em> premier car 2 et 3 le divisent aussi.<br><br>L'engrenage ci-dessous montre les 100 premiers <strong>nombres premiers</strong> répartis sur <strong>10 anneaux</strong> de 10 nombres premiers chacun. Fais tourner un anneau pour explorer ses nombres premiers, clique sur un nombre premier pour l'entendre et apprendre un fait à son sujet !",
        primesExploredLabel: 'Nombres premiers explorés :', currentRingLabelText: 'Anneau actuel :',
        primeAtPointerLabel: 'Nombre premier à la flèche',
        ringPositionLabel: 'Anneau {ring} · position {pos}', primeOrdinalMsg: 'Le {ord} nombre premier',
        selectRingTitle: 'Choisis un anneau', rotateRingTitle: "Fais tourner l'anneau sélectionné",
        antiClockwise: 'Sens anti-horaire', clockwise: 'Sens horaire',
        autoSpin: '▶ Rotation auto', stopSpin: '⏸ Arrêter', resetThisRing: '↺ Cet anneau',
        gearActionsTitle: "Actions de l'engrenage", resetAllBtn: '↺ Tout réinitialiser',
        highlightOn: '✨ Surligné', highlightOff: '✨ Surligner les nombres premiers',
        mixChallengeBtn: '🎲 Défi mélange', exitChallengeBtn: '✕ Quitter le défi',
        mixCardTitle: '🎯 Défi mélange',
        mixCardDescHtml: "Tous les anneaux sont mélangés ! Choisis un anneau, puis utilise <strong>↺ Sens anti-horaire</strong> ou <strong>↻ Sens horaire</strong> pour le faire tourner jusqu'à ce que son premier nombre premier s'aligne avec la <strong>flèche orange ▼</strong>.",
        mixProgLabel: '{aligned} / 10 anneaux alignés', mixHintDefault: 'Choisis un anneau pour commencer',
        mixHintAligned: '✓ Anneau {ring} aligné ! Choisis un autre anneau.',
        mixHintCW: 'Anneau {ring} : tourne ↻ dans le sens horaire de {n} pas.',
        mixHintCCW: 'Anneau {ring} : tourne ↺ dans le sens anti-horaire de {n} pas.',
        primeFactTitle: '📘 Le savais-tu ?',
        sortIntroHtml: '🔍 <strong>Lesquels sont premiers ?</strong> Chaque question te montre un mélange de nombres. Certains sont <strong>premiers</strong> (divisibles uniquement par 1 et eux-mêmes) et d\'autres ne le sont <strong>pas</strong>. Touche chaque nombre que tu penses être premier, puis vérifie ta réponse !',
        correctChip: '✅ Correct :', wrongChip: '❌ Faux :', questionsChip: '🔢 Questions :',
        sortInstruction: 'Touche tous les nombres PREMIERS ci-dessous :', sortAnswerLabel: 'Tes nombres premiers sélectionnés :',
        sortAnswerPlaceholder: 'Touche les nombres ci-dessus pour les sélectionner…',
        checkAnswerBtn: '✓ Vérifier ma réponse', nextQuestionBtn: '🔄 Question suivante',
        clickToDeselect: 'Clique pour désélectionner', selectAtLeastOne: 'Touche au moins un nombre que tu penses être premier !',
        ringResetToast: 'Anneau {n} réinitialisé ↺', allRingsResetToast: 'Tous les anneaux réinitialisés ↺',
        highlightOnToast: 'Nombres premiers explorés surlignés ✨', highlightOffToast: 'Surlignage désactivé',
        ringsMixedToast: 'Anneaux mélangés ! 🎲 Aligne-les tous !', allAlignedToast: '🎉 Tous les anneaux sont alignés ! Magnifique !',
        brilliantMsg: '🌟 <strong>Magnifique !</strong> Tu as trouvé tous les nombres premiers !',
        primesWereLabel: '✅ Les nombres premiers étaient : <strong>{list}</strong>',
        notPrimeLabel: '🔵 Pas premiers : {list}',
        notPrimeFactorsLabel: '🔴 Pas premiers (ils ont d\'autres diviseurs) : {list}',
        notQuiteLabel: '❌ <strong>Pas tout à fait !</strong> ',
        notPrimeSuffix: ' ne sont <strong>pas premiers</strong>. ',
        youMissedLabel: 'Tu as manqué : <strong>{list}</strong>.',
        genericPrimeFact: '<strong>{p}</strong> est le {ord} nombre premier. Il ne peut être divisé que par 1 et {p}.'
    },
    ar: {
        subtitle: 'تروس الأعداد الأولية',
        tabExplorer: '⚙️ مستكشف التروس', tabSort: '🔍 اختبار الأعداد الأولية',
        gearIntroHtml: '<strong>العدد الأولي</strong> هو عدد أكبر من 1 لا يقبل القسمة إلا على <strong>1</strong> و<strong>نفسه</strong> — لا يقسمه أي عدد آخر بالتمام. على سبيل المثال، <strong>7</strong> عدد أولي لأنه لا يقبل القسمة إلا على 1 و7. لكن 6 <em>ليس</em> أوليًا لأنه يقبل القسمة أيضًا على 2 و3.<br><br>يُظهر ترس الأرقام أدناه أول <strong>100 عدد أولي</strong> موزعة على <strong>10 حلقات</strong>، كل حلقة بها 10 أعداد أولية. أدر إحدى الحلقات لاستكشاف أعدادها الأولية، واضغط على أي عدد أولي لسماعه وتعلّم معلومة عنه!',
        primesExploredLabel: 'الأعداد الأولية المستكشَفة:', currentRingLabelText: 'الحلقة الحالية:',
        primeAtPointerLabel: 'العدد الأولي عند المؤشر',
        ringPositionLabel: 'الحلقة {ring} · الموضع {pos}', primeOrdinalMsg: 'العدد الأولي رقم {ord}',
        selectRingTitle: 'اختر حلقة', rotateRingTitle: 'تدوير الحلقة المختارة',
        antiClockwise: 'عكس عقارب الساعة', clockwise: 'مع عقارب الساعة',
        autoSpin: '▶ دوران تلقائي', stopSpin: '⏸ إيقاف', resetThisRing: '↺ هذه الحلقة',
        gearActionsTitle: 'إجراءات الترس', resetAllBtn: '↺ إعادة ضبط الكل',
        highlightOn: '✨ تم التمييز', highlightOff: '✨ تمييز الأعداد الأولية',
        mixChallengeBtn: '🎲 تحدي المزج', exitChallengeBtn: '✕ الخروج من التحدي',
        mixCardTitle: '🎯 تحدي المزج',
        mixCardDescHtml: 'تم خلط جميع الحلقات! اختر حلقة، ثم استخدم <strong>↺ عكس عقارب الساعة</strong> أو <strong>↻ مع عقارب الساعة</strong> لتدويرها حتى يتوافق أول عدد أولي فيها مع <strong>السهم البرتقالي ▼</strong>.',
        mixProgLabel: '{aligned} / 10 حلقات متوافقة', mixHintDefault: 'اختر حلقة للبدء',
        mixHintAligned: '✓ الحلقة {ring} متوافقة! اختر حلقة أخرى.',
        mixHintCW: 'الحلقة {ring}: أدرها ↻ مع عقارب الساعة {n} خطوات.',
        mixHintCCW: 'الحلقة {ring}: أدرها ↺ عكس عقارب الساعة {n} خطوات.',
        primeFactTitle: '📘 معلومة عن الأعداد الأولية',
        sortIntroHtml: '🔍 <strong>أي منها أولي؟</strong> يعرض لك كل سؤال مجموعة مختلطة من الأعداد. بعضها <strong>أولي</strong> (لا يقبل القسمة إلا على 1 وعلى نفسه) وبعضها <strong>ليس أوليًا</strong>. اضغط على كل عدد تعتقد أنه أولي، ثم تحقق من إجابتك!',
        correctChip: '✅ صحيح:', wrongChip: '❌ خطأ:', questionsChip: '🔢 الأسئلة:',
        sortInstruction: 'اضغط على جميع الأعداد الأولية أدناه:', sortAnswerLabel: 'الأعداد الأولية التي اخترتها:',
        sortAnswerPlaceholder: 'اضغط على الأعداد أعلاه لاختيارها…',
        checkAnswerBtn: '✓ تحقق من إجابتي', nextQuestionBtn: '🔄 السؤال التالي',
        clickToDeselect: 'اضغط لإلغاء التحديد', selectAtLeastOne: 'اضغط على عدد واحد على الأقل تعتقد أنه أولي!',
        ringResetToast: 'تمت إعادة ضبط الحلقة {n} ↺', allRingsResetToast: 'تمت إعادة ضبط جميع الحلقات ↺',
        highlightOnToast: 'تم تمييز الأعداد الأولية المستكشَفة ✨', highlightOffToast: 'تم إيقاف التمييز',
        ringsMixedToast: 'تم خلط الحلقات! 🎲 وافِقها جميعًا!', allAlignedToast: '🎉 جميع الحلقات متوافقة! رائع!',
        brilliantMsg: '🌟 <strong>رائع!</strong> وجدت جميع الأعداد الأولية!',
        primesWereLabel: '✅ الأعداد الأولية كانت: <strong>{list}</strong>',
        notPrimeLabel: '🔵 ليست أولية: {list}',
        notPrimeFactorsLabel: '🔴 ليست أولية (لها قواسم أخرى): {list}',
        notQuiteLabel: '❌ <strong>ليس تمامًا!</strong> ',
        notPrimeSuffix: ' <strong>ليست أولية</strong>. ',
        youMissedLabel: 'فاتك: <strong>{list}</strong>.',
        genericPrimeFact: '<strong>{p}</strong> هو العدد الأولي رقم {ord}. لا يقبل القسمة إلا على 1 و{p}.'
    },
    zh: {
        subtitle: '质数齿轮',
        tabExplorer: '⚙️ 齿轮探索', tabSort: '🔍 质数测验',
        gearIntroHtml: '<strong>质数</strong>是大于1且只能被<strong>1</strong>和<strong>它自己</strong>整除的数——没有其他数能整除它。例如，<strong>7</strong>是质数，因为只有1和7能整除它。但6<em>不是</em>质数，因为2和3也能整除它。<br><br>下面的数字齿轮展示了前<strong>100个质数</strong>，分布在<strong>10个环</strong>上，每个环10个质数。转动一个环来探索它的质数，点击任意质数即可听到它并了解一个小知识！',
        primesExploredLabel: '已探索质数：', currentRingLabelText: '当前环：',
        primeAtPointerLabel: '指针处的质数',
        ringPositionLabel: '环 {ring} · 位置 {pos}', primeOrdinalMsg: '第{ord}个质数',
        selectRingTitle: '选择一个环', rotateRingTitle: '旋转所选环',
        antiClockwise: '逆时针', clockwise: '顺时针',
        autoSpin: '▶ 自动旋转', stopSpin: '⏸ 停止', resetThisRing: '↺ 重置此环',
        gearActionsTitle: '齿轮操作', resetAllBtn: '↺ 全部重置',
        highlightOn: '✨ 已高亮', highlightOff: '✨ 高亮质数',
        mixChallengeBtn: '🎲 混合挑战', exitChallengeBtn: '✕ 退出挑战',
        mixCardTitle: '🎯 混合挑战',
        mixCardDescHtml: '所有环都被打乱了！选择一个环，然后用<strong>↺ 逆时针</strong>或<strong>↻ 顺时针</strong>旋转它，直到它的第一个质数对准<strong>橙色箭头 ▼</strong>。',
        mixProgLabel: '{aligned} / 10 个环已对齐', mixHintDefault: '选择一个环开始',
        mixHintAligned: '✓ 环 {ring} 已对齐！选择另一个环。',
        mixHintCW: '环 {ring}：顺时针↻旋转 {n} 步。',
        mixHintCCW: '环 {ring}：逆时针↺旋转 {n} 步。',
        primeFactTitle: '📘 质数小知识',
        sortIntroHtml: '🔍 <strong>哪些是质数？</strong>每个问题会显示一组混合数字。有些是<strong>质数</strong>（只能被1和自己整除），有些<strong>不是质数</strong>。点击你认为是质数的每一个数字，然后检查你的答案！',
        correctChip: '✅ 正确：', wrongChip: '❌ 错误：', questionsChip: '🔢 题目：',
        sortInstruction: '点击下面所有的质数：', sortAnswerLabel: '你选择的质数：',
        sortAnswerPlaceholder: '点击上方数字进行选择…',
        checkAnswerBtn: '✓ 检查我的答案', nextQuestionBtn: '🔄 下一题',
        clickToDeselect: '点击以取消选择', selectAtLeastOne: '请点击至少一个你认为是质数的数字！',
        ringResetToast: '环 {n} 已重置 ↺', allRingsResetToast: '所有环已重置 ↺',
        highlightOnToast: '已探索的质数已高亮 ✨', highlightOffToast: '已关闭高亮',
        ringsMixedToast: '环已打乱！🎲 把它们全部对齐！', allAlignedToast: '🎉 所有环都已对齐！太棒了！',
        brilliantMsg: '🌟 <strong>太棒了！</strong>你找到了所有的质数！',
        primesWereLabel: '✅ 质数是：<strong>{list}</strong>',
        notPrimeLabel: '🔵 不是质数：{list}',
        notPrimeFactorsLabel: '🔴 不是质数（它们还有其他因数）：{list}',
        notQuiteLabel: '❌ <strong>差一点！</strong> ',
        notPrimeSuffix: ' <strong>不是质数</strong>。',
        youMissedLabel: '你漏掉了：<strong>{list}</strong>。',
        genericPrimeFact: '<strong>{p}</strong> 是第{ord}个质数。它只能被 1 和 {p} 整除。'
    }
};

/* ============================================================
   PRIME FACTS (concise, kid-friendly) — per language
============================================================ */
const PRIME_FACTS = {
    en: {
        2:   'The only <strong>even</strong> prime! Every other even number can be divided by 2.',
        3:   'The sum of its digits (3) is divisible by 3. It\'s the first <strong>odd prime</strong>!',
        5:   'Any number ending in 0 or 5 is divisible by 5. Prime 5 is special!',
        7:   '7 is lucky! There are 7 days in a week and 7 colours in a rainbow.',
        11:  'A <strong>palindrome prime</strong> — reads the same forwards and backwards!',
        13:  'Some call it unlucky, but 13 is a proud prime!',
        17:  'A <strong>Fermat prime</strong> — one of only five known of its kind.',
        19:  '19 is a prime and also a <strong>happy number</strong>!',
        23:  'The angle the Earth tilts is about 23°. A cosmic prime!',
        29:  'The last prime in the 20s. After 29, next prime is 31.',
        31:  'There are 31 days in 7 months of the year. A monthly prime!',
        37:  '37 is a <strong>lucky prime</strong> and appears in many puzzles.',
        41:  'The formula n² + n + 41 gives primes for n = 0 to 39!',
        43:  '43 is a prime and a <strong>lucky number</strong> in Chinese culture.',
        47:  '47 appears more than any prime in Star Trek episodes!',
        53:  '53 is the 16th prime. Multiply its digits: 5×3 = 15.',
        59:  'The last prime before 60. Next is 61 — twin primes!',
        61:  '<strong>Twin prime</strong> with 59. They differ by just 2.',
        67:  '67 is a <strong>lucky prime</strong> — both prime and lucky!',
        71:  '<strong>Mirror prime</strong> — reverse the digits and get 17, also prime!',
        73:  'Sheldon Cooper\'s favourite number in The Big Bang Theory!',
        79:  'The last prime of the 70s. 7 and 9 are both odd — prime combo!',
        83:  '83 is prime and also the atomic number of Bismuth.',
        89:  'Part of a <strong>Fibonacci prime</strong> — it appears in the Fibonacci sequence!',
        97:  'The largest prime under 100. The champion of the first century!',
        101: 'The first <strong>3-digit prime</strong> — also a palindrome prime!',
        103: '103 is prime. Add its digits: 1+0+3 = 4. Not prime, but 103 is!',
        107: '107 is a <strong>safe prime</strong> — (107−1)÷2 = 53, also prime!',
        109: 'Rotate 109 upside down and you get 601 — also prime!',
        113: 'The 30th prime. 1×1×3 = 3. Its digits multiply to a prime!',
    },
    de: {
        2:   'Die einzige <strong>gerade</strong> Primzahl! Jede andere gerade Zahl lässt sich durch 2 teilen.',
        3:   'Die Quersumme (3) ist durch 3 teilbar. Sie ist die erste <strong>ungerade Primzahl</strong>!',
        5:   'Jede Zahl, die auf 0 oder 5 endet, ist durch 5 teilbar. Die Primzahl 5 ist etwas Besonderes!',
        7:   '7 ist eine Glückszahl! Eine Woche hat 7 Tage und ein Regenbogen 7 Farben.',
        11:  'Eine <strong>Palindrom-Primzahl</strong> — vorwärts und rückwärts gelesen gleich!',
        13:  'Manche nennen sie unglücklich, aber 13 ist eine stolze Primzahl!',
        17:  'Eine <strong>Fermat-Primzahl</strong> — eine von nur fünf bekannten dieser Art.',
        19:  '19 ist eine Primzahl und auch eine <strong>glückliche Zahl</strong>!',
        23:  'Die Erde ist um etwa 23° geneigt. Eine kosmische Primzahl!',
        29:  'Die letzte Primzahl der Zwanziger. Nach 29 kommt die 31.',
        31:  'Sieben Monate im Jahr haben 31 Tage. Eine Monats-Primzahl!',
        37:  '37 ist eine <strong>Glücks-Primzahl</strong> und taucht in vielen Rätseln auf.',
        41:  'Die Formel n² + n + 41 ergibt Primzahlen für n = 0 bis 39!',
        43:  '43 ist eine Primzahl und in der chinesischen Kultur eine <strong>Glückszahl</strong>.',
        47:  '47 kommt in Star-Trek-Episoden häufiger vor als jede andere Primzahl!',
        53:  '53 ist die 16. Primzahl. Ihre Ziffern multipliziert: 5×3 = 15.',
        59:  'Die letzte Primzahl vor 60. Die nächste ist 61 — Primzahlzwillinge!',
        61:  '<strong>Primzahlzwilling</strong> mit 59. Sie unterscheiden sich nur um 2.',
        67:  '67 ist eine <strong>Glücks-Primzahl</strong> — prim und glücklich zugleich!',
        71:  '<strong>Spiegel-Primzahl</strong> — die Ziffern umgedreht ergeben 17, ebenfalls eine Primzahl!',
        73:  'Sheldon Coopers Lieblingszahl in The Big Bang Theory!',
        79:  'Die letzte Primzahl der Siebziger. 7 und 9 sind beide ungerade — eine Primkombination!',
        83:  '83 ist eine Primzahl und auch die Ordnungszahl von Bismut.',
        89:  'Teil einer <strong>Fibonacci-Primzahl</strong> — sie kommt in der Fibonacci-Folge vor!',
        97:  'Die größte Primzahl unter 100. Die Meisterin des ersten Hunderts!',
        101: 'Die erste <strong>dreistellige Primzahl</strong> — auch eine Palindrom-Primzahl!',
        103: '103 ist eine Primzahl. Ziffern addiert: 1+0+3 = 4. Das ist keine Primzahl, aber 103 ist eine!',
        107: '107 ist eine <strong>sichere Primzahl</strong> — (107−1)÷2 = 53, auch eine Primzahl!',
        109: 'Dreht man die 109 auf den Kopf, ergibt sich 601 — ebenfalls eine Primzahl!',
        113: 'Die 30. Primzahl. 1×1×3 = 3. Ihre Ziffern multipliziert ergeben eine Primzahl!',
    },
    fr: {
        2:   "Le seul nombre premier <strong>pair</strong> ! Tout autre nombre pair est divisible par 2.",
        3:   "La somme de ses chiffres (3) est divisible par 3. C'est le premier <strong>nombre premier impair</strong> !",
        5:   "Tout nombre qui finit par 0 ou 5 est divisible par 5. Le nombre premier 5 est spécial !",
        7:   "7 est un chiffre porte-bonheur ! Il y a 7 jours dans une semaine et 7 couleurs dans un arc-en-ciel.",
        11:  "Un <strong>nombre premier palindrome</strong> — se lit pareil dans les deux sens !",
        13:  "Certains le trouvent malchanceux, mais 13 est un fier nombre premier !",
        17:  "Un <strong>nombre premier de Fermat</strong> — l'un des cinq seuls connus de ce type.",
        19:  "19 est premier et aussi un <strong>nombre heureux</strong> !",
        23:  "L'inclinaison de la Terre est d'environ 23°. Un nombre premier cosmique !",
        29:  "Le dernier nombre premier des années vingt. Après 29 vient 31.",
        31:  "Sept mois de l'année comptent 31 jours. Un nombre premier mensuel !",
        37:  "37 est un <strong>nombre premier porte-bonheur</strong> et apparaît dans de nombreuses énigmes.",
        41:  "La formule n² + n + 41 donne des nombres premiers pour n = 0 à 39 !",
        43:  "43 est premier et aussi un <strong>chiffre porte-bonheur</strong> dans la culture chinoise.",
        47:  "47 apparaît plus que tout autre nombre premier dans les épisodes de Star Trek !",
        53:  "53 est le 16e nombre premier. Multiplie ses chiffres : 5×3 = 15.",
        59:  "Le dernier nombre premier avant 60. Le suivant est 61 — des nombres premiers jumeaux !",
        61:  "<strong>Nombre premier jumeau</strong> avec 59. Ils ne diffèrent que de 2.",
        67:  "67 est un <strong>nombre premier porte-bonheur</strong> — premier et chanceux à la fois !",
        71:  "<strong>Nombre premier miroir</strong> — inverse ses chiffres et obtiens 17, aussi premier !",
        73:  "Le nombre préféré de Sheldon Cooper dans The Big Bang Theory !",
        79:  "Le dernier nombre premier des années soixante-dix. 7 et 9 sont tous deux impairs — une combinaison première !",
        83:  "83 est premier et aussi le numéro atomique du bismuth.",
        89:  "Fait partie des <strong>nombres premiers de Fibonacci</strong> — il apparaît dans la suite de Fibonacci !",
        97:  "Le plus grand nombre premier sous 100. Le champion du premier siècle !",
        101: "Le premier <strong>nombre premier à 3 chiffres</strong> — aussi un palindrome !",
        103: "103 est premier. Additionne ses chiffres : 1+0+3 = 4. Ce n'est pas premier, mais 103 l'est !",
        107: "107 est un <strong>nombre premier sûr</strong> — (107−1)÷2 = 53, aussi premier !",
        109: "Retourne 109 à l'envers et tu obtiens 601 — aussi premier !",
        113: "Le 30e nombre premier. 1×1×3 = 3. Ses chiffres multipliés donnent un nombre premier !",
    },
    ar: {
        2:   'العدد الأولي <strong>الزوجي</strong> الوحيد! كل عدد زوجي آخر يقبل القسمة على 2.',
        3:   'مجموع رقميه (3) يقبل القسمة على 3. وهو أول <strong>عدد أولي فردي</strong>!',
        5:   'كل عدد ينتهي بـ 0 أو 5 يقبل القسمة على 5. العدد الأولي 5 مميز!',
        7:   '7 عدد محظوظ! في الأسبوع 7 أيام وفي قوس قزح 7 ألوان.',
        11:  '<strong>عدد أولي متناظر</strong> — يُقرأ بنفس الشكل من الاتجاهين!',
        13:  'يعتبره بعضهم نحس الحظ، لكن 13 عدد أولي فخور!',
        17:  '<strong>عدد فيرما الأولي</strong> — واحد من خمسة فقط معروفة من نوعه.',
        19:  '19 عدد أولي وهو أيضًا <strong>عدد سعيد</strong>!',
        23:  'تميل الأرض بزاوية حوالي 23°. عدد أولي كوني!',
        29:  'آخر عدد أولي في العشرينيات. بعد 29 يأتي 31.',
        31:  'سبعة أشهر في السنة بها 31 يومًا. عدد أولي شهري!',
        37:  '37 <strong>عدد أولي محظوظ</strong> ويظهر في الكثير من الألغاز.',
        41:  'الصيغة n² + n + 41 تعطي أعدادًا أولية لـ n من 0 إلى 39!',
        43:  '43 عدد أولي وأيضًا <strong>عدد محظوظ</strong> في الثقافة الصينية.',
        47:  '47 يظهر أكثر من أي عدد أولي آخر في حلقات ستار تريك!',
        53:  '53 هو العدد الأولي رقم 16. اضرب رقميه: 5×3 = 15.',
        59:  'آخر عدد أولي قبل 60. التالي هو 61 — أعداد أولية توأم!',
        61:  '<strong>عدد أولي توأم</strong> مع 59. يفرقهما 2 فقط.',
        67:  '67 <strong>عدد أولي محظوظ</strong> — أولي ومحظوظ في الوقت نفسه!',
        71:  '<strong>عدد أولي مرآة</strong> — اعكس رقميه فتحصل على 17، وهو أولي أيضًا!',
        73:  'العدد المفضل لشيلدون كوبر في مسلسل The Big Bang Theory!',
        79:  'آخر عدد أولي في السبعينيات. 7 و 9 كلاهما فردي — توافق أولي!',
        83:  '83 عدد أولي وهو أيضًا العدد الذري للبزموت.',
        89:  'جزء من <strong>عدد أولي فيبوناتشي</strong> — يظهر في متتالية فيبوناتشي!',
        97:  'أكبر عدد أولي أقل من 100. بطل المئة الأولى!',
        101: 'أول <strong>عدد أولي من 3 أرقام</strong> — وهو أيضًا متناظر!',
        103: '103 عدد أولي. اجمع رقميه: 1+0+3 = 4. هذا ليس أوليًا، لكن 103 أولي!',
        107: '107 <strong>عدد أولي آمن</strong> — (107−1)÷2 = 53، وهو أولي أيضًا!',
        109: 'اقلب 109 رأسًا على عقب فتحصل على 601 — وهو أولي أيضًا!',
        113: 'العدد الأولي رقم 30. 1×1×3 = 3. حاصل ضرب رقميه عدد أولي!',
    },
    zh: {
        2:   '唯一的<strong>偶数</strong>质数！其他所有偶数都能被2整除。',
        3:   '它的数字之和（3）能被3整除。它是第一个<strong>奇数质数</strong>！',
        5:   '任何以0或5结尾的数都能被5整除。质数5很特别！',
        7:   '7是个幸运数字！一周有7天，彩虹有7种颜色。',
        11:  '一个<strong>回文质数</strong>——正着读和倒着读都一样！',
        13:  '有人说它不吉利，但13是个自豪的质数！',
        17:  '一个<strong>费马质数</strong>——已知仅有五个这种类型。',
        19:  '19是质数，也是一个<strong>快乐数</strong>！',
        23:  '地球倾斜角度大约是23°。一个宇宙质数！',
        29:  '20多岁数字中的最后一个质数。29之后是31。',
        31:  '一年中有7个月有31天。一个月份质数！',
        37:  '37是个<strong>幸运质数</strong>，常出现在各种谜题中。',
        41:  '公式 n² + n + 41 在 n 为0到39时都能得到质数！',
        43:  '43是质数，在中国文化中也是<strong>吉利数字</strong>。',
        47:  '47在《星际迷航》各集中出现的次数比任何质数都多！',
        53:  '53是第16个质数。把它的数字相乘：5×3 = 15。',
        59:  '60之前的最后一个质数。下一个是61——孪生质数！',
        61:  '与59组成<strong>孪生质数</strong>。它们只相差2。',
        67:  '67是<strong>幸运质数</strong>——既是质数又是吉利数字！',
        71:  '<strong>镜像质数</strong>——把数字反过来就是17，也是质数！',
        73:  '《生活大爆炸》中谢尔顿·库珀最喜欢的数字！',
        79:  '70多岁数字中的最后一个质数。7和9都是奇数——质数组合！',
        83:  '83是质数，也是铋的原子序数。',
        89:  '<strong>斐波那契质数</strong>的一员——它出现在斐波那契数列中！',
        97:  '100以内最大的质数。第一个百数区间的冠军！',
        101: '第一个<strong>三位数质数</strong>——也是一个回文质数！',
        103: '103是质数。把数字相加：1+0+3 = 4，不是质数，但103本身是！',
        107: '107是<strong>安全质数</strong>——(107−1)÷2 = 53，也是质数！',
        109: '把109倒过来看就是601——也是质数！',
        113: '第30个质数。1×1×3 = 3，它的数字相乘正好是个质数！',
    }
};

function getPrimeFact(p) {
    const lang = NG_LevelI18n.lang();
    const facts = PRIME_FACTS[lang] || PRIME_FACTS.en;
    if (facts[p]) return facts[p];
    // Generic fact for primes beyond the table
    const idx = PRIMES.indexOf(p) + 1;
    return NG_LevelI18n.t(L6, 'genericPrimeFact', { p: p, ord: localOrdinal(idx, lang) });
}

function ordinal(n) {
    const s = ['th','st','nd','rd'], v = n % 100;
    return n + (s[(v-20)%10] || s[v] || s[0]);
}

// Localised ordinal label used for "the {ord} prime number" across languages.
function localOrdinal(n, lang) {
    if (lang === 'de') return n + '.';
    if (lang === 'fr') return n === 1 ? '1er' : n + 'e';
    if (lang === 'ar' || lang === 'zh') return String(n);
    return ordinal(n);
}

/* ============================================================
   STATE
============================================================ */
let ringOffsets    = new Array(NUM_RINGS).fill(0);
let activeRing     = 0;
let exploredPrimes = new Set();
let spinInterval   = null;
let highlightMode  = false;
let mixMode        = false;

/* ============================================================
   CANVAS
============================================================ */
const canvas = document.getElementById('primeCanvas');
const ctx    = canvas.getContext('2d');
const CS = 560, CX = 280, CY = 280;
const HUB_R  = 28;
const REF_R  = 50;   // inner reference ring outer radius
const RING_W = 22;   // each ring width
const GEAR_OUTER = REF_R + NUM_RINGS * RING_W;  // 50 + 220 = 270

function drawGear() {
    ctx.clearRect(0, 0, CS, CS);

    // Background disc
    ctx.beginPath();
    ctx.arc(CX, CY, GEAR_OUTER + 6, 0, Math.PI * 2);
    ctx.fillStyle = '#dde4ee';
    ctx.fill();
    ctx.strokeStyle = '#bcc8d8';
    ctx.lineWidth = 2;
    ctx.stroke();

    // Rings (outermost first)
    for (let i = NUM_RINGS - 1; i >= 0; i--) {
        const rIn  = REF_R + i * RING_W;
        const rOut = REF_R + (i + 1) * RING_W;
        const isActive = (i === activeRing);

        ctx.beginPath();
        ctx.arc(CX, CY, rOut, 0, Math.PI * 2);
        ctx.arc(CX, CY, rIn,  0, Math.PI * 2, true);
        ctx.fillStyle = isActive ? lightenHex(RING_FILLS[i], -18) : RING_FILLS[i];
        ctx.fill();

        ctx.beginPath();
        ctx.arc(CX, CY, rOut, 0, Math.PI * 2);
        ctx.strokeStyle = isActive ? '#f4a571' : '#c4cedd';
        ctx.lineWidth = isActive ? 2 : 1;
        ctx.stroke();
    }

    // Pointer-slot highlight (top slot on each ring)
    for (let i = 0; i < NUM_RINGS; i++) {
        const rIn  = REF_R + i * RING_W;
        const rOut = REF_R + (i + 1) * RING_W;
        const half = (Math.PI * 2 / SLOTS) / 2 * 0.82;
        const top  = -Math.PI / 2;
        ctx.beginPath();
        ctx.arc(CX, CY, rOut - 0.5, top - half, top + half);
        ctx.arc(CX, CY, rIn  + 0.5, top + half, top - half, true);
        ctx.closePath();
        ctx.fillStyle = (i === activeRing) ? 'rgba(244,165,113,0.52)' : 'rgba(0,0,0,0.04)';
        ctx.fill();
    }

    // Numbers
    for (let i = 0; i < NUM_RINGS; i++) {
        const rIn    = REF_R + i * RING_W;
        const rOut   = REF_R + (i + 1) * RING_W;
        const rMid   = (rIn + rOut) / 2;
        const isActive = (i === activeRing);
        const ringStart = i * RING_SIZE;
        const fs = Math.max(9, Math.floor(RING_W / 2.1));
        ctx.font = `${isActive ? 900 : 700} ${fs}px Nunito, Segoe UI, sans-serif`;
        ctx.textAlign    = 'center';
        ctx.textBaseline = 'middle';

        for (let j = 0; j < SLOTS; j++) {
            const prime    = PRIMES[ringStart + j];
            const atPointer = (j === ringOffsets[i]);
            const angleDeg  = 270 + (j - ringOffsets[i]) * SLOT_DEG;
            const angleRad  = angleDeg * Math.PI / 180;
            const x = CX + rMid * Math.cos(angleRad);
            const y = CY + rMid * Math.sin(angleRad);

            ctx.save();
            ctx.translate(x, y);

            const label   = String(prime);
            const metrics = ctx.measureText(label);
            const pw = metrics.width + 4, ph = fs + 4, pr = ph / 2;

            // Pill background
            ctx.beginPath();
            ctx.roundRect(-pw/2, -ph/2, pw, ph, pr);
            if (atPointer && isActive) {
                ctx.fillStyle = '#f4a571';
            } else if (highlightMode && exploredPrimes.has(prime)) {
                ctx.fillStyle = '#c8f5e8';
            } else {
                ctx.fillStyle = 'rgba(255,255,255,0.93)';
            }
            ctx.fill();

            // Text — always readable
            ctx.fillStyle = (atPointer && isActive) ? 'white'
                           : isActive ? RING_ACCENTS[i]
                           : '#4a5568';
            ctx.fillText(label, 0, 0);
            ctx.restore();
        }
    }

    // Reference ring (inner purple ring)
    ctx.beginPath();
    ctx.arc(CX, CY, REF_R, 0, Math.PI * 2);
    ctx.arc(CX, CY, HUB_R, 0, Math.PI * 2, true);
    ctx.fillStyle = '#ede8ff';
    ctx.fill();
    ctx.beginPath();
    ctx.arc(CX, CY, REF_R, 0, Math.PI * 2);
    ctx.strokeStyle = '#b4a8e0';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    // Hub
    const g = ctx.createRadialGradient(CX, CY - 4, 2, CX, CY, HUB_R);
    g.addColorStop(0, '#9d8fe0');
    g.addColorStop(1, '#5a4fb5');
    ctx.beginPath();
    ctx.arc(CX, CY, HUB_R, 0, Math.PI * 2);
    ctx.fillStyle = g;
    ctx.fill();
    ctx.font = 'bold 11px Nunito, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillStyle = 'white';
    ctx.fillText('P', CX, CY);

    // Reference arrow at top
    const arrowTipY = CY - GEAR_OUTER - 2;
    ctx.beginPath();
    ctx.moveTo(CX, arrowTipY);
    ctx.lineTo(CX - 9, arrowTipY - 14);
    ctx.lineTo(CX + 9, arrowTipY - 14);
    ctx.closePath();
    ctx.fillStyle = '#f4a571';
    ctx.fill();
    ctx.strokeStyle = '#d4824a';
    ctx.lineWidth = 1.5;
    ctx.stroke();
}

function lightenHex(hex, amt) {
    const n = parseInt(hex.replace('#',''), 16);
    const r = Math.min(255, Math.max(0, (n >> 16) + amt));
    const gg = Math.min(255, Math.max(0, ((n >> 8) & 0xff) + amt));
    const b = Math.min(255, Math.max(0, (n & 0xff) + amt));
    return '#' + ((r << 16) | (gg << 8) | b).toString(16).padStart(6,'0');
}

/* ============================================================
   CANVAS CLICK — select ring
============================================================ */
canvas.addEventListener('click', function(e) {
    const rect = canvas.getBoundingClientRect();
    const sx = CS / rect.width, sy = CS / rect.height;
    const mx = (e.clientX - rect.left) * sx;
    const my = (e.clientY - rect.top)  * sy;
    const dist = Math.hypot(mx - CX, my - CY);
    if (dist < HUB_R) return;
    for (let i = 0; i < NUM_RINGS; i++) {
        const rIn  = REF_R + i * RING_W;
        const rOut = REF_R + (i + 1) * RING_W;
        if (dist >= rIn && dist < rOut) { setActiveRing(i); return; }
    }
});

/* ============================================================
   RING GRID
============================================================ */
function buildRingGrid() {
    const grid = document.getElementById('ringGrid');
    grid.innerHTML = '';
    for (let i = 0; i < NUM_RINGS; i++) {
        const start   = PRIMES[i * RING_SIZE];
        const end     = PRIMES[i * RING_SIZE + RING_SIZE - 1];
        const aligned = mixMode && ringOffsets[i] === 0;
        const btn     = document.createElement('button');
        btn.className = 'ring-btn'
            + (i === activeRing ? ' active' : '')
            + (aligned ? ' aligned' : '');
        btn.innerHTML = `<strong>R${i+1}</strong><br><small>${start}–${end}</small>`;
        if (aligned) {
            const chk = document.createElement('div');
            chk.style.cssText = 'position:absolute;top:-5px;right:-5px;width:14px;height:14px;background:var(--mint);border-radius:50%;font-size:8px;display:flex;align-items:center;justify-content:center;color:white;font-weight:900;pointer-events:none;';
            chk.textContent = '✓';
            btn.style.position = 'relative';
            btn.appendChild(chk);
        }
        btn.onclick = () => setActiveRing(i);
        grid.appendChild(btn);
    }
}

function setActiveRing(i) {
    if (spinInterval) stopSpin();
    activeRing = i;
    buildRingGrid();
    updatePointerDisplay();
    updateStats();
    drawGear();
    if (mixMode) updateMixHint();
    const start = PRIMES[i * RING_SIZE];
    const end   = PRIMES[i * RING_SIZE + RING_SIZE - 1];
    document.getElementById('currentRingLabel').textContent = `${i+1} (${start}–${end})`;
    NG_Speech.sayInstruction(`Ring ${i+1}. Primes ${start} to ${end}.`);
}

/* ============================================================
   POINTER DISPLAY
============================================================ */
function updatePointerDisplay() {
    const idx   = activeRing * RING_SIZE + ringOffsets[activeRing];
    const prime = PRIMES[idx];
    exploredPrimes.add(prime);

    const el = document.getElementById('primeAtPointer');
    el.style.animation = 'none'; void el.offsetWidth; el.style.animation = '';
    el.textContent = prime;

    document.getElementById('primeRingDetail').textContent =
        NG_LevelI18n.t(L6, 'ringPositionLabel', { ring: activeRing + 1, pos: ringOffsets[activeRing] + 1 });
    document.getElementById('primeOrdinal').textContent =
        NG_LevelI18n.t(L6, 'primeOrdinalMsg', { ord: localOrdinal(idx + 1, NG_LevelI18n.lang()) });

    // Show fact card
    const factCard = document.getElementById('primeFactCard');
    document.getElementById('primeFactBody').innerHTML = getPrimeFact(prime);
    factCard.classList.add('show');

    updateStats();
}

/* ============================================================
   ROTATE
============================================================ */
function rotateCW() {
    ringOffsets[activeRing] = (ringOffsets[activeRing] + 1) % SLOTS;
    updatePointerDisplay();
    buildRingGrid();
    drawGear();
    if (mixMode) checkMixProgress();
    NG_Speech.sayNumber(PRIMES[activeRing * RING_SIZE + ringOffsets[activeRing]]);
}

function rotateCCW() {
    ringOffsets[activeRing] = (ringOffsets[activeRing] + SLOTS - 1) % SLOTS;
    updatePointerDisplay();
    buildRingGrid();
    drawGear();
    if (mixMode) checkMixProgress();
    NG_Speech.sayNumber(PRIMES[activeRing * RING_SIZE + ringOffsets[activeRing]]);
}

function resetRing() {
    if (spinInterval) stopSpin();
    ringOffsets[activeRing] = 0;
    updatePointerDisplay();
    drawGear();
    showToast(NG_LevelI18n.t(L6, 'ringResetToast', { n: activeRing + 1 }), '');
}

function resetAll() {
    if (spinInterval) stopSpin();
    exitMixMode();
    ringOffsets.fill(0);
    activeRing = 0;
    buildRingGrid();
    updatePointerDisplay();
    updateStats();
    drawGear();
    showToast(NG_LevelI18n.t(L6, 'allRingsResetToast'), '');
}

/* ============================================================
   SPIN
============================================================ */
function toggleSpin() {
    if (spinInterval) stopSpin(); else startSpin();
}
function startSpin() {
    document.getElementById('spinBtn').textContent = NG_LevelI18n.t(L6, 'stopSpin');
    document.getElementById('spinBtn').classList.add('spinning');
    spinInterval = setInterval(rotateCW, 700);
}
function stopSpin() {
    clearInterval(spinInterval);
    spinInterval = null;
    document.getElementById('spinBtn').textContent = NG_LevelI18n.t(L6, 'autoSpin');
    document.getElementById('spinBtn').classList.remove('spinning');
}

/* ============================================================
   HIGHLIGHT
============================================================ */
function toggleHighlight() {
    highlightMode = !highlightMode;
    const btn = document.getElementById('highlightBtn');
    btn.style.background = highlightMode ? 'var(--peach-light)' : '';
    btn.textContent = NG_LevelI18n.t(L6, highlightMode ? 'highlightOn' : 'highlightOff');
    drawGear();
    showToast(NG_LevelI18n.t(L6, highlightMode ? 'highlightOnToast' : 'highlightOffToast'), '');
}

/* ============================================================
   HEAR
============================================================ */
function hearCurrentPrime() {
    const prime = PRIMES[activeRing * RING_SIZE + ringOffsets[activeRing]];
    NG_Speech.sayInstruction(`${prime}. ${prime} is a prime number.`);
}

/* ============================================================
   STATS
============================================================ */
function updateStats() {
    document.getElementById('primesExplored').textContent = exploredPrimes.size;
    NG_Storage.setLvl6Score(Math.round((exploredPrimes.size / 100) * 100));
}

/* ============================================================
   TOAST
============================================================ */
let _toastTimer = null;
function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'feedback-toast show' + (type ? ' ' + type : '');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => t.classList.remove('show'), 2500);
}

/* ============================================================
   MIX CHALLENGE
============================================================ */
function toggleMix() {
    if (mixMode) resetAll();
    else startMix();
}

function startMix() {
    if (spinInterval) stopSpin();
    mixMode = true;
    document.getElementById('mixBtn').textContent  = NG_LevelI18n.t(L6, 'exitChallengeBtn');
    document.getElementById('mixBtn').style.background = 'var(--purple-light)';
    document.getElementById('mixCard').classList.add('show');

    // Shuffle all rings to a non-zero offset
    for (let i = 0; i < NUM_RINGS; i++) {
        let off;
        do { off = Math.floor(Math.random() * SLOTS); } while (off === 0);
        ringOffsets[i] = off;
    }

    activeRing = 0;
    buildRingGrid();
    updatePointerDisplay();
    updateStats();
    drawGear();
    checkMixProgress();
    showToast(NG_LevelI18n.t(L6, 'ringsMixedToast'), '');
    NG_Speech.sayInstruction('Mix challenge! Rotate each ring until its first prime lines up with the reference arrow.');
}

function exitMixMode() {
    mixMode = false;
    document.getElementById('mixBtn').textContent = NG_LevelI18n.t(L6, 'mixChallengeBtn');
    document.getElementById('mixBtn').style.background = '';
    document.getElementById('mixCard').classList.remove('show');
}

function checkMixProgress() {
    const aligned = ringOffsets.filter(o => o === 0).length;
    document.getElementById('mixProgLabel').textContent = NG_LevelI18n.t(L6, 'mixProgLabel', { aligned: aligned });
    document.getElementById('mixProgFill').style.width  = (aligned * 10) + '%';
    updateMixHint();
    if (aligned === NUM_RINGS) onMixComplete();
}

function updateMixHint() {
    const hintEl = document.getElementById('mixHint');
    const offset = ringOffsets[activeRing];
    if (offset === 0) {
        hintEl.className = 'mix-hint aligned';
        hintEl.textContent = NG_LevelI18n.t(L6, 'mixHintAligned', { ring: activeRing + 1 });
    } else {
        hintEl.className = 'mix-hint';
        const cwSteps  = SLOTS - offset;
        const ccwSteps = offset;
        if (cwSteps <= ccwSteps) {
            hintEl.textContent = NG_LevelI18n.t(L6, 'mixHintCW', { ring: activeRing + 1, n: cwSteps });
        } else {
            hintEl.textContent = NG_LevelI18n.t(L6, 'mixHintCCW', { ring: activeRing + 1, n: ccwSteps });
        }
    }
}

function onMixComplete() {
    if (spinInterval) stopSpin();
    exitMixMode();
    buildRingGrid();
    drawGear();
    showToast(NG_LevelI18n.t(L6, 'allAlignedToast'), 'success');
    NG_Speech.sayInstruction('Fantastic! All rings are aligned. You solved the prime number mix challenge!');
    NG_Storage.setLvl6Score(Math.min(100, (NG_Storage.getLvl6Score ? NG_Storage.getLvl6Score() : 0) + 20));
}

/* ============================================================
   INIT
============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    NG_LevelI18n.applyStatic(L6);
    buildRingGrid();
    updatePointerDisplay();
    drawGear();
    NG_Music.init();
    setTimeout(() => NG_Speech.sayInstruction(
        'Welcome to Level 6 Prime Numbers Gear! Explore the first one hundred prime numbers. Spin any ring to discover them!'
    ), 600);
});

/* ============================================================
   MODE SWITCHER
============================================================ */
function lvl6SetMode(mode) {
    document.getElementById('tab-explorer').classList.toggle('active', mode === 'explorer');
    document.getElementById('tab-sort').classList.toggle('active', mode === 'sort');
    document.getElementById('lvl6-explorer').style.display = mode === 'explorer' ? '' : 'none';
    document.getElementById('lvl6-sort').style.display     = mode === 'sort'     ? '' : 'none';
    if (mode === 'sort' && sortPool === null) newSortQuestion();
}

/* ============================================================
   PRIME IDENTIFIER QUIZ — mix of primes and non-primes
============================================================ */
const PRIME_SET = new Set(PRIMES);   // fast lookup

// Generate non-prime composites for mixing in
function getNonPrimes(count, min, max) {
    const pool = [];
    for (let n = min; n <= max; n++) {
        if (!PRIME_SET.has(n) && n > 1) pool.push(n);
    }
    pool.sort(() => Math.random() - 0.5);
    return pool.slice(0, count);
}

let sortPool         = null;   // all numbers shown (primes + non-primes mixed)
let sortSelected     = new Set(); // indices the learner has tapped as "prime"
let sortCorrectN     = 0;
let sortWrongN       = 0;
let sortAttempts     = 0;
let sortAnswered     = false;

function newSortQuestion() {
    sortAnswered = false;
    document.getElementById('sortResult').style.display  = 'none';
    document.getElementById('sortReveal').style.display  = 'none';
    sortSelected.clear();

    // Pick 5 random primes
    const primes = [...PRIMES].sort(() => Math.random() - 0.5).slice(0, 5);
    // Pick 4 non-primes from a similar range
    const minP = Math.min(...primes), maxP = Math.max(...primes);
    const nonPrimes = getNonPrimes(4, Math.max(2, minP - 10), maxP + 20);

    // Mix and shuffle
    sortPool = [...primes, ...nonPrimes].sort(() => Math.random() - 0.5);

    renderSortPool();
    renderSortAnswer();

    NG_Speech.sayInstruction('Tap all the prime numbers in the list!');
}

function renderSortPool() {
    const poolEl = document.getElementById('sortPool');
    poolEl.innerHTML = '';
    sortPool.forEach((n, idx) => {
        const tile = document.createElement('div');
        tile.className = 'prime-tile' + (sortSelected.has(idx) ? ' selected-prime' : '');
        tile.textContent = n;
        tile.style.cursor = 'pointer';
        if (sortAnswered) {
            // Show correct/wrong after checking
            const isPrime = PRIME_SET.has(n);
            const wasSelected = sortSelected.has(idx);
            if (isPrime && wasSelected)  tile.classList.add('correct');
            else if (!isPrime && wasSelected) tile.classList.add('wrong');
            else if (isPrime && !wasSelected) tile.classList.add('wrong'); // missed
        }
        tile.addEventListener('click', () => {
            if (sortAnswered) return;
            if (sortSelected.has(idx)) sortSelected.delete(idx);
            else sortSelected.add(idx);
            document.getElementById('sortResult').style.display = 'none';
            renderSortPool();
            renderSortAnswer();
        });
        poolEl.appendChild(tile);
    });
}

function renderSortAnswer() {
    const ansEl = document.getElementById('sortAnswer');
    if (sortSelected.size === 0) {
        ansEl.innerHTML = '<span style="color:var(--text-soft);font-size:13px;font-weight:700;padding:8px;">' +
            NG_LevelI18n.t(L6, 'sortAnswerPlaceholder') + '</span>';
        return;
    }
    ansEl.innerHTML = '';
    sortPool.forEach((n, idx) => {
        if (!sortSelected.has(idx)) return;
        const tile = document.createElement('div');
        tile.className = 'prime-tile';
        tile.textContent = n;
        tile.style.background = 'var(--purple-light)';
        tile.style.borderColor = 'var(--purple)';
        tile.style.color = 'var(--purple-dark)';
        tile.style.cursor = 'pointer';
        tile.title = NG_LevelI18n.t(L6, 'clickToDeselect');
        tile.addEventListener('click', () => {
            if (sortAnswered) return;
            sortSelected.delete(idx);
            renderSortPool();
            renderSortAnswer();
        });
        ansEl.appendChild(tile);
    });
}

function checkSortAnswer() {
    if (sortSelected.size === 0) {
        showToast(NG_LevelI18n.t(L6, 'selectAtLeastOne'), '');
        return;
    }

    sortAnswered = true;
    sortAttempts++;
    document.getElementById('sortAttempts').textContent = sortAttempts;

    // Evaluate
    const correctPrimes  = sortPool.filter(n => PRIME_SET.has(n));
    const selectedNums   = [...sortSelected].map(i => sortPool[i]);
    const truePositives  = selectedNums.filter(n => PRIME_SET.has(n));
    const falsePositives = selectedNums.filter(n => !PRIME_SET.has(n));
    const missedPrimes   = correctPrimes.filter(n => !selectedNums.includes(n));
    const isRight        = truePositives.length === correctPrimes.length && falsePositives.length === 0;

    // Re-render with colour coding
    renderSortPool();
    renderSortAnswer();

    const resultEl = document.getElementById('sortResult');
    const revealEl = document.getElementById('sortReveal');
    resultEl.style.display = 'block';
    revealEl.style.display = 'block';

    if (isRight) {
        sortCorrectN++;
        document.getElementById('sortCorrect').textContent = sortCorrectN;
        resultEl.className = 'sort-result correct';
        resultEl.innerHTML = NG_LevelI18n.t(L6, 'brilliantMsg');
        revealEl.innerHTML = NG_LevelI18n.t(L6, 'primesWereLabel', { list: correctPrimes.sort((a,b)=>a-b).join(', ') }) + '<br>' +
            NG_LevelI18n.t(L6, 'notPrimeLabel', { list: sortPool.filter(n => !PRIME_SET.has(n)).sort((a,b)=>a-b).join(', ') });
        NG_Speech.sayInstruction('Brilliant! You found all the prime numbers!');
        NG_Storage.setLvl6Score(Math.min(100, (NG_Storage.getLvl6Score ? NG_Storage.getLvl6Score() : 0) + 8));
    } else {
        sortWrongN++;
        document.getElementById('sortWrong').textContent = sortWrongN;
        resultEl.className = 'sort-result wrong';
        let msg = NG_LevelI18n.t(L6, 'notQuiteLabel');
        if (falsePositives.length > 0)
            msg += falsePositives.join(', ') + NG_LevelI18n.t(L6, 'notPrimeSuffix');
        if (missedPrimes.length > 0)
            msg += NG_LevelI18n.t(L6, 'youMissedLabel', { list: missedPrimes.join(', ') });
        resultEl.innerHTML = msg;
        revealEl.innerHTML = NG_LevelI18n.t(L6, 'primesWereLabel', { list: correctPrimes.sort((a,b)=>a-b).join(', ') }) + '<br>' +
            NG_LevelI18n.t(L6, 'notPrimeFactorsLabel', { list: sortPool.filter(n => !PRIME_SET.has(n)).sort((a,b)=>a-b).join(', ') });
        NG_Speech.sayInstruction('Not quite! Check which numbers are prime and try the next question.');
    }
}
</script>
</body>
</html>
