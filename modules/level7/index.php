<?php
// Number Gear — Level 7: Ordinal Numbers
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
    <title>Level 7 — Ordinal Numbers | Number Gear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/accessibility.js"></script>
    <style>

        /* ===== PAGE WRAP ===== */
        .l6-wrap {
            max-width: 860px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* ===== EXPLAINER CARD ===== */
        .explainer-card {
            background: var(--surface);
            border: 2px solid var(--sky);
            border-radius: 18px;
            padding: 14px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .ex-icon { font-size: 32px; flex-shrink: 0; margin-top: 2px; }
        .ex-body  { flex: 1; }
        .ex-body h3 { font-size: 16px; font-weight: 900; color: var(--sky-dark); margin-bottom: 6px; }
        .ex-body p  { font-size: 13px; font-weight: 600; color: var(--text); line-height: 1.65; }
        .ex-body strong { color: var(--sky-dark); }

        /* ===== MODE TABS ===== */
        .mode-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .mode-tab {
            padding: 14px 10px;
            border: 2.5px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
            font-size: 14px; font-weight: 900;
            cursor: pointer; font-family: inherit;
            color: var(--text-soft);
            display: flex; flex-direction: column;
            align-items: center; gap: 5px;
            transition: 0.2s ease;
        }
        .mode-tab .tab-icon { font-size: 26px; }
        .mode-tab.active {
            background: var(--sky);
            border-color: var(--sky);
            color: white;
        }
        .mode-tab:not(.active):hover { border-color: var(--sky); color: var(--sky-dark); }

        /* ===== BATCH TABS ===== */
        .batch-row {
            display: flex; gap: 8px; flex-wrap: wrap;
        }
        .batch-btn {
            padding: 7px 14px;
            border: 2px solid var(--border);
            border-radius: 20px;
            background: var(--surface);
            font-size: 13px; font-weight: 800;
            cursor: pointer; font-family: inherit;
            color: var(--text-soft); transition: 0.18s ease;
        }
        .batch-btn.active { background: var(--sky); border-color: var(--sky); color: white; }
        .batch-btn:not(.active):hover { border-color: var(--sky); color: var(--sky-dark); }

        /* ===== LEARN MODE GRID ===== */
        .ordinal-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }
        .ord-card {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 10px 6px;
            text-align: center;
            cursor: pointer;
            transition: 0.18s ease;
            display: flex; flex-direction: column;
            align-items: center; gap: 3px;
            user-select: none;
        }
        .ord-card:hover { border-color: var(--sky); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(91,168,212,0.2); }
        .ord-card:active { transform: scale(0.96); }
        .ord-card.learned { background: var(--sky-light); border-color: var(--sky); }
        .ord-card.speaking { background: var(--sky); border-color: var(--sky-dark); transform: scale(1.08); }
        .ord-num      { font-size: 22px; font-weight: 900; color: var(--text); line-height: 1; }
        .ord-word     { font-size: 11px; font-weight: 800; color: var(--sky-dark); line-height: 1.2; }
        .ord-suffix   { font-size: 11px; font-weight: 700; color: var(--text-soft); }
        .ord-card.learned .ord-num  { color: var(--sky-dark); }
        .ord-card.learned .ord-word { color: var(--sky-dark); }
        .ord-card.speaking .ord-num  { color: white; }
        .ord-card.speaking .ord-word { color: white; opacity: 0.9; }

        /* ===== LEARNED BANNER ===== */
        .learned-banner-l6 {
            text-align: center;
            font-size: 15px; font-weight: 700;
            color: var(--text-soft); margin-top: 4px;
        }
        .learned-banner-l6 strong { color: var(--sky-dark); }

        /* ===== MATCH MODE ===== */
        .match-wrap {
            display: flex; flex-direction: column; gap: 16px;
        }
        .match-question-card {
            background: linear-gradient(135deg, var(--sky-light), #e8f6ff);
            border: 2.5px solid var(--sky);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
        }
        .match-prompt {
            font-size: 14px; font-weight: 700; color: var(--text-soft); margin-bottom: 8px;
        }
        .match-big {
            font-size: 72px; font-weight: 900; color: var(--sky-dark);
            line-height: 1; margin-bottom: 6px;
            animation: popIn 0.3s ease;
        }
        .match-big-word {
            font-size: 28px; font-weight: 900; color: var(--sky-dark);
            line-height: 1; margin-bottom: 6px;
            animation: popIn 0.3s ease;
        }
        @keyframes popIn { 0%{transform:scale(0.7);opacity:0} 60%{transform:scale(1.15)} 100%{transform:scale(1);opacity:1} }
        .match-sub {
            font-size: 13px; font-weight: 700; color: var(--text-soft);
        }
        .match-replay-btn {
            margin-top: 10px;
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px;
            background: var(--sky-light); border: 2px solid var(--sky);
            border-radius: 20px; font-size: 13px; font-weight: 800;
            cursor: pointer; font-family: inherit; color: var(--sky-dark);
            transition: 0.18s ease;
        }
        .match-replay-btn:hover { background: var(--sky); color: white; }

        /* Match choices */
        .match-choices {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .match-choice {
            padding: 16px 10px;
            border: 2.5px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
            font-size: 16px; font-weight: 900;
            cursor: pointer; font-family: inherit;
            color: var(--text); transition: 0.2s ease;
            text-align: center; line-height: 1.3;
        }
        .match-choice:hover:not(:disabled) {
            border-color: var(--sky);
            background: var(--sky-light);
            color: var(--sky-dark);
            transform: translateY(-2px);
        }
        .match-choice.correct { background: var(--success-bg); border-color: var(--success); color: #276749; animation: glow 0.5s ease; }
        .match-choice.wrong   { background: var(--error-bg);   border-color: var(--error);   color: #9b2335; animation: shake 0.4s ease; }
        .match-choice:disabled { cursor: default; }
        @keyframes glow  { 0%,100%{box-shadow:none} 50%{box-shadow:0 0 0 6px rgba(72,187,120,0.25)} }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)} }

        /* Match progress & score */
        .match-progress-wrap {
            display: flex; align-items: center; gap: 10px;
        }
        .match-progress-track {
            flex: 1; height: 10px; background: var(--border);
            border-radius: 10px; overflow: hidden;
        }
        .match-progress-fill {
            height: 100%; background: var(--sky);
            border-radius: 10px; transition: width 0.4s ease; width: 0%;
        }
        .match-progress-label {
            font-size: 14px; font-weight: 800; color: var(--text-soft); white-space: nowrap;
        }
        .match-score-row {
            display: flex; gap: 8px; flex-wrap: wrap;
        }
        .score-chip-l6 {
            background: var(--bg); border: 2px solid var(--border);
            border-radius: 12px; padding: 7px 14px;
            font-size: 13px; font-weight: 800; color: var(--text-soft);
        }
        .score-chip-l6 span { color: var(--sky-dark); font-size: 15px; font-weight: 900; }

        /* ===== SPELL MODE ===== */
        .spell-wrap {
            display: flex; flex-direction: column; gap: 16px;
        }
        .spell-question-card {
            background: linear-gradient(135deg, var(--peach-light), #fff8f0);
            border: 2.5px solid var(--peach);
            border-radius: 20px;
            padding: 24px 20px;
            text-align: center;
        }
        .spell-prompt { font-size: 14px; font-weight: 700; color: var(--text-soft); margin-bottom: 8px; }
        .spell-big    { font-size: 80px; font-weight: 900; color: var(--peach-dark); line-height: 1; }
        .spell-sub    { font-size: 13px; font-weight: 700; color: var(--text-soft); margin-top: 6px; }

        .spell-choices {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .spell-choice {
            padding: 14px 10px;
            border: 2.5px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
            font-size: 15px; font-weight: 800;
            cursor: pointer; font-family: inherit;
            color: var(--text); transition: 0.2s ease;
            text-align: center; line-height: 1.3;
        }
        .spell-choice:hover:not(:disabled) {
            border-color: var(--peach);
            background: var(--peach-light);
            color: var(--peach-dark);
            transform: translateY(-2px);
        }
        .spell-choice.correct { background: var(--success-bg); border-color: var(--success); color: #276749; animation: glow 0.5s ease; }
        .spell-choice.wrong   { background: var(--error-bg);   border-color: var(--error);   color: #9b2335; animation: shake 0.4s ease; }
        .spell-choice:disabled { cursor: default; }

        /* ===== COMPLETION ===== */
        .completion-card {
            background: linear-gradient(135deg, var(--sky-light), var(--mint-light));
            border: 2.5px solid var(--sky);
            border-radius: 20px;
            padding: 28px 20px;
            text-align: center;
            display: none;
        }
        .completion-card.show { display: block; animation: popIn 0.4s ease; }
        .comp-flowers {
            font-size: 28px; letter-spacing: 4px;
            margin-bottom: 8px; line-height: 1.4;
            animation: popIn 0.5s ease;
        }
        .comp-emoji { font-size: 52px; margin-bottom: 8px; }
        .comp-title { font-size: 24px; font-weight: 900; color: var(--sky-dark); margin-bottom: 6px; }
        .comp-sub   { font-size: 14px; font-weight: 700; color: var(--text-soft); margin-bottom: 16px; }
        .comp-btns  { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }

        @media (max-width: 600px) {
            .mode-tabs       { grid-template-columns: repeat(3, 1fr); gap: 8px; }
            .mode-tab        { font-size: 12px; padding: 12px 6px; }
            .ordinal-grid    { grid-template-columns: repeat(4, 1fr); gap: 6px; }
            .ord-num         { font-size: 18px; }
            .match-big       { font-size: 56px; }
            .match-big-word  { font-size: 22px; }
            .spell-big       { font-size: 60px; }
            .match-choices,
            .spell-choices   { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 400px) {
            .ordinal-grid { grid-template-columns: repeat(4, 1fr); }
        }
    </style>
</head>
<body>
<canvas id="confettiCanvas6" style="position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999;display:none;"></canvas>
<div class="app-shell">

    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">🥇</div>
            <div>
                <h1>Level 7</h1>
                <p>Ordinal Numbers</p>
            </div>
        </div>
        <a href="../../index.php" class="back-btn">← Home</a>
    </header>

    <main class="level-page">
    <div class="l6-wrap">

        <!-- ===== EXPLAINER ===== -->
        <div class="explainer-card">
            <div class="ex-icon">🥇</div>
            <div class="ex-body">
                <h3>What are Ordinal Numbers?</h3>
                <p>
                    <strong>Cardinal numbers</strong> tell us <em>how many</em> — 1, 2, 3…
                    <strong>Ordinal numbers</strong> tell us <em>the position</em> — <strong>1st, 2nd, 3rd</strong>…
                    Like finishing a race: <strong>1st</strong> = first, <strong>2nd</strong> = second, <strong>3rd</strong> = third!
                    Tap any card to hear the ordinal number, then try the quiz!
                </p>
            </div>
        </div>

        <!-- ===== MODE TABS ===== -->
        <div class="mode-tabs">
            <button class="mode-tab active" id="tabLearn" onclick="setMode('learn')">
                <span class="tab-icon">📖</span>
                Learn
            </button>
            <button class="mode-tab" id="tabMatch" onclick="setMode('match')">
                <span class="tab-icon">🔗</span>
                Match
            </button>
            <button class="mode-tab" id="tabSpell" onclick="setMode('spell')">
                <span class="tab-icon">✏️</span>
                Spell It
            </button>
        </div>

        <!-- ===== LEARN MODE ===== -->
        <div id="modeLearn">

            <!-- Batch selector -->
            <div class="batch-row" id="learnBatches" style="margin-bottom:12px;"></div>

            <!-- Ordinal grid -->
            <div class="ordinal-grid" id="ordinalGrid"></div>

            <!-- Learned count -->
            <div class="learned-banner-l6">
                <strong id="learnedCount">0</strong> of 100 ordinal numbers heard ✓
            </div>

        </div>

        <!-- ===== MATCH MODE ===== -->
        <div id="modeMatch" style="display:none;">
            <div class="match-wrap">

                <!-- Batch selector -->
                <div class="batch-row" id="matchBatchRow" style="margin-bottom:4px;"></div>

                <!-- Progress -->
                <div class="match-progress-wrap">
                    <div class="match-progress-track">
                        <div class="match-progress-fill" id="matchFill"></div>
                    </div>
                    <span class="match-progress-label" id="matchLabel">0 / 10</span>
                </div>

                <!-- Question card -->
                <div class="match-question-card" id="matchQuestionCard">
                    <div class="match-prompt" id="matchPrompt">What is the ordinal number for this position?</div>
                    <div class="match-big" id="matchBig">–</div>
                    <div class="match-sub" id="matchSub">Choose the correct ordinal word below</div>
                    <button class="match-replay-btn" onclick="replayMatch()">🔊 Hear it again</button>
                </div>

                <!-- Choices -->
                <div class="match-choices" id="matchChoices"></div>

                <!-- Score -->
                <div class="match-score-row">
                    <div class="score-chip-l6">✅ Correct: <span id="matchCorrect">0</span></div>
                    <div class="score-chip-l6">🔥 Streak: <span id="matchStreak">0</span></div>
                </div>

                <!-- Completion with flowers -->
                <div class="completion-card" id="matchCompletion">
                    <div class="comp-flowers" id="matchCompFlowers">🌸 🌺 🌼</div>
                    <div class="comp-title"   id="matchCompTitle">Well Done!</div>
                    <div class="comp-sub"     id="matchCompSub">You matched all 10 ordinal numbers!</div>
                    <div class="comp-btns">
                        <button class="btn btn-sky" id="matchNextBatchBtn">Next Batch →</button>
                        <button class="btn btn-outline" onclick="startMatch(1)">Start Over 🔁</button>
                    </div>
                </div>

            </div>
        </div>

        <!-- ===== SPELL MODE ===== -->
        <div id="modeSpell" style="display:none;">
            <div class="spell-wrap">

                <!-- Progress -->
                <div class="match-progress-wrap">
                    <div class="match-progress-track">
                        <div class="match-progress-fill" id="spellFill" style="background:var(--peach);"></div>
                    </div>
                    <span class="match-progress-label" id="spellLabel">0 / 10</span>
                </div>

                <!-- Question card -->
                <div class="spell-question-card">
                    <div class="spell-prompt">What is the ordinal word for this number?</div>
                    <div class="spell-big" id="spellBig">–</div>
                    <div class="spell-sub" id="spellSub">e.g. 1 → first, 2 → second</div>
                </div>

                <!-- Choices -->
                <div class="spell-choices" id="spellChoices"></div>

                <!-- Score -->
                <div class="match-score-row">
                    <div class="score-chip-l6" style="border-color:var(--peach);">✅ Correct: <span id="spellCorrect" style="color:var(--peach-dark);">0</span></div>
                    <div class="score-chip-l6" style="border-color:var(--peach);">🔥 Streak: <span id="spellStreak" style="color:var(--peach-dark);">0</span></div>
                </div>

                <!-- Completion -->
                <div class="completion-card" id="spellCompletion" style="border-color:var(--peach);">
                    <div class="comp-emoji">🌟</div>
                    <div class="comp-title" id="spellCompTitle" style="color:var(--peach-dark);">Brilliant!</div>
                    <div class="comp-sub"   id="spellCompSub">You spelled all 10 ordinal words!</div>
                    <div class="comp-btns">
                        <button class="btn btn-peach"   onclick="startSpell()">Play Again 🔁</button>
                        <button class="btn btn-outline" onclick="setMode('match')">Try Match 🔗</button>
                    </div>
                </div>

            </div>
        </div>

    </div>
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
/* ================================================================
   ORDINAL DATA — 1 to 100
================================================================ */
function getOrdinal(n) {
    const words = {
        1:'first', 2:'second', 3:'third', 4:'fourth', 5:'fifth',
        6:'sixth', 7:'seventh', 8:'eighth', 9:'ninth', 10:'tenth',
        11:'eleventh', 12:'twelfth', 13:'thirteenth', 14:'fourteenth', 15:'fifteenth',
        16:'sixteenth', 17:'seventeenth', 18:'eighteenth', 19:'nineteenth', 20:'twentieth',
        21:'twenty-first', 22:'twenty-second', 23:'twenty-third', 24:'twenty-fourth',
        25:'twenty-fifth', 26:'twenty-sixth', 27:'twenty-seventh', 28:'twenty-eighth',
        29:'twenty-ninth', 30:'thirtieth',
        31:'thirty-first', 32:'thirty-second', 33:'thirty-third', 34:'thirty-fourth',
        35:'thirty-fifth', 36:'thirty-sixth', 37:'thirty-seventh', 38:'thirty-eighth',
        39:'thirty-ninth', 40:'fortieth',
        41:'forty-first', 42:'forty-second', 43:'forty-third', 44:'forty-fourth',
        45:'forty-fifth', 46:'forty-sixth', 47:'forty-seventh', 48:'forty-eighth',
        49:'forty-ninth', 50:'fiftieth',
        51:'fifty-first', 52:'fifty-second', 53:'fifty-third', 54:'fifty-fourth',
        55:'fifty-fifth', 56:'fifty-sixth', 57:'fifty-seventh', 58:'fifty-eighth',
        59:'fifty-ninth', 60:'sixtieth',
        61:'sixty-first', 62:'sixty-second', 63:'sixty-third', 64:'sixty-fourth',
        65:'sixty-fifth', 66:'sixty-sixth', 67:'sixty-seventh', 68:'sixty-eighth',
        69:'sixty-ninth', 70:'seventieth',
        71:'seventy-first', 72:'seventy-second', 73:'seventy-third', 74:'seventy-fourth',
        75:'seventy-fifth', 76:'seventy-sixth', 77:'seventy-seventh', 78:'seventy-eighth',
        79:'seventy-ninth', 80:'eightieth',
        81:'eighty-first', 82:'eighty-second', 83:'eighty-third', 84:'eighty-fourth',
        85:'eighty-fifth', 86:'eighty-sixth', 87:'eighty-seventh', 88:'eighty-eighth',
        89:'eighty-ninth', 90:'ninetieth',
        91:'ninety-first', 92:'ninety-second', 93:'ninety-third', 94:'ninety-fourth',
        95:'ninety-fifth', 96:'ninety-sixth', 97:'ninety-seventh', 98:'ninety-eighth',
        99:'ninety-ninth', 100:'one hundredth'
    };
    return words[n] || '';
}

function getSuffix(n) {
    if (n === 11 || n === 12 || n === 13) return 'th';
    const last = n % 10;
    if (last === 1) return 'st';
    if (last === 2) return 'nd';
    if (last === 3) return 'rd';
    return 'th';
}

function getOrdinalLabel(n) {
    return n + getSuffix(n);
}

/* ================================================================
   STATE
================================================================ */
let currentMode    = 'learn';
let learnBatch     = 1;  // 1-based, 10 per batch
let learnedSet     = new Set();

let matchPool      = [];
let matchBatch     = 1;  // 1–10, each batch covers 10 numbers
let matchCurrent   = null;
let matchAnswered  = false;
let matchDone      = 0;
let matchCorrect   = 0;
let matchStreak    = 0;

let spellPool      = [];
let spellCurrent   = null;
let spellAnswered  = false;
let spellDone      = 0;
let spellCorrect   = 0;
let spellStreak    = 0;

let speakingCard   = null;

/* ================================================================
   LEARN MODE
================================================================ */
function buildLearnBatches() {
    const row = document.getElementById('learnBatches');
    row.innerHTML = '';
    for (let b = 1; b <= 10; b++) {
        const btn = document.createElement('button');
        btn.className = 'batch-btn' + (b === learnBatch ? ' active' : '');
        const s = (b - 1) * 10 + 1, e = b * 10;
        btn.textContent = `${getOrdinalLabel(s)}–${getOrdinalLabel(e)}`;
        btn.onclick = () => { learnBatch = b; buildLearnBatches(); renderLearnGrid(); };
        row.appendChild(btn);
    }
}

function renderLearnGrid() {
    const grid  = document.getElementById('ordinalGrid');
    const start = (learnBatch - 1) * 10 + 1;
    const end   = start + 9;
    grid.innerHTML = '';

    for (let n = start; n <= end; n++) {
        const card = document.createElement('div');
        const isLearned = learnedSet.has(n);
        card.className = 'ord-card' + (isLearned ? ' learned' : '');
        card.setAttribute('data-n', n);
        card.innerHTML = `
            <div class="ord-num">${getOrdinalLabel(n)}</div>
            <div class="ord-word">${getOrdinal(n)}</div>
        `;
        card.onclick = () => tapOrdinal(n, card);
        grid.appendChild(card);
    }

    updateLearnedCount();
}

function tapOrdinal(n, card) {
    // Mark learned
    learnedSet.add(n);
    card.classList.add('learned');

    // Visual speaking pulse
    if (speakingCard) speakingCard.classList.remove('speaking');
    speakingCard = card;
    card.classList.add('speaking');
    setTimeout(() => card.classList.remove('speaking'), 1200);

    // Say ordinal word once only — no repetition
    NG_Speech.sayInstruction(getOrdinal(n));

    updateLearnedCount();
    saveProgress();
}

function updateLearnedCount() {
    document.getElementById('learnedCount').textContent = learnedSet.size;
}

/* ================================================================
   MATCH MODE — sequential batches of 10 (1–10, 11–20 … 91–100)
================================================================ */
function startMatch(batch) {
    matchBatch    = batch || 1;
    matchDone     = 0;
    matchCorrect  = 0;
    matchStreak   = 0;
    matchAnswered = false;

    document.getElementById('matchCompletion').classList.remove('show');
    document.getElementById('matchQuestionCard').style.display = '';
    document.getElementById('matchChoices').style.display      = '';
    document.getElementById('matchCorrect').textContent        = 0;
    document.getElementById('matchStreak').textContent         = 0;

    // Build pool from current batch range (sequential, then shuffle)
    const start = (matchBatch - 1) * 10 + 1;
    const nums  = Array.from({length:10}, (_, i) => start + i);
    matchPool   = nums.sort(() => Math.random() - 0.5);

    buildMatchBatchSelector();
    updateMatchProgress();
    nextMatchQuestion();
    NG_Speech.sayInstruction(`Batch ${matchBatch}. Match the ordinal numbers from ${getOrdinalLabel(start)} to ${getOrdinalLabel(start + 9)}!`);
}

function buildMatchBatchSelector() {
    const row = document.getElementById('matchBatchRow');
    if (!row) return;
    row.innerHTML = '';
    for (let b = 1; b <= 10; b++) {
        const btn  = document.createElement('button');
        const s    = (b - 1) * 10 + 1;
        const e    = b * 10;
        btn.className   = 'batch-btn' + (b === matchBatch ? ' active' : '');
        btn.textContent = `${getOrdinalLabel(s)}–${getOrdinalLabel(e)}`;
        btn.onclick     = () => startMatch(b);
        row.appendChild(btn);
    }
}

function nextMatchQuestion() {
    if (matchPool.length === 0) {
        showMatchCompletion();
        return;
    }

    matchAnswered = false;
    matchCurrent  = matchPool.shift();
    matchDone++;

    const numEl = document.getElementById('matchBig');
    numEl.textContent = matchCurrent;
    numEl.style.animation = 'none';
    setTimeout(() => numEl.style.animation = '', 50);

    document.getElementById('matchSub').textContent    = `What is the ordinal word for ${matchCurrent}?`;
    document.getElementById('matchPrompt').textContent = `Position ${matchCurrent} in a race — what do we call it?`;

    updateMatchProgress();
    renderMatchChoices(matchCurrent);

    // Say only the ordinal word — no number repetition
    setTimeout(() => NG_Speech.sayInstruction(getOrdinal(matchCurrent)), 300);
}

function renderMatchChoices(correct) {
    const container = document.getElementById('matchChoices');
    container.innerHTML = '';

    // 4 choices from within the same batch range
    const start = (matchBatch - 1) * 10 + 1;
    const batchNums = Array.from({length:10}, (_, i) => start + i).filter(n => n !== correct);
    const shuffled  = batchNums.sort(() => Math.random() - 0.5).slice(0, 3);
    const options   = [correct, ...shuffled].sort(() => Math.random() - 0.5);

    options.forEach(n => {
        const btn = document.createElement('button');
        btn.className = 'match-choice';
        btn.innerHTML = `<strong>${getOrdinalLabel(n)}</strong><br><small>${getOrdinal(n)}</small>`;
        btn.onclick   = () => checkMatchAnswer(n, btn, correct);
        container.appendChild(btn);
    });
}

function checkMatchAnswer(selected, btn, correct) {
    if (matchAnswered) return;
    matchAnswered = true;
    document.querySelectorAll('.match-choice').forEach(b => b.disabled = true);

    if (selected === correct) {
        btn.classList.add('correct');
        matchCorrect++;
        matchStreak++;
        document.getElementById('matchCorrect').textContent = matchCorrect;
        document.getElementById('matchStreak').textContent  = matchStreak;
        learnedSet.add(correct);
        updateLearnedCount();
        saveProgress();
        NG_Speech.sayInstruction(`Very good! That is ${getOrdinal(correct)}.`);
        showToast(`✓ ${getOrdinal(correct)}`, 'success');
        burstConfetti6();
        setTimeout(nextMatchQuestion, 1500);
    } else {
        btn.classList.add('wrong');
        matchStreak = 0;
        document.getElementById('matchStreak').textContent = 0;
        document.querySelectorAll('.match-choice').forEach(b => {
            if (b.querySelector('strong').textContent === getOrdinalLabel(correct)) b.classList.add('correct');
        });
        NG_Speech.sayInstruction(`Sorry, try again. The answer is ${getOrdinal(correct)}.`);
        showToast(`The answer is ${getOrdinal(correct)}`, 'error');
        setTimeout(nextMatchQuestion, 2000);
    }
}

function updateMatchProgress() {
    const done = matchDone - 1;
    document.getElementById('matchFill').style.width  = (done / 10 * 100) + '%';
    document.getElementById('matchLabel').textContent = `${done} / 10`;
}

function replayMatch() {
    if (matchCurrent) NG_Speech.sayInstruction(getOrdinal(matchCurrent));
}

function showMatchCompletion() {
    document.getElementById('matchQuestionCard').style.display = 'none';
    document.getElementById('matchChoices').style.display      = 'none';
    document.getElementById('matchFill').style.width           = '100%';
    document.getElementById('matchLabel').textContent          = '10 / 10';

    const perfect    = matchCorrect >= 9;
    const isLastBatch = matchBatch >= 10;
    const start      = (matchBatch - 1) * 10 + 1;
    const end        = matchBatch * 10;

    // Flowers emoji row
    const flowers = ['🌸','🌺','🌼','🌻','🌹','💐','🌷','🌸','🌺','🌼'];
    const flowerStr = flowers.slice(0, matchCorrect).join(' ');

    document.getElementById('matchCompFlowers').textContent = flowerStr;
    document.getElementById('matchCompTitle').textContent   = perfect ? 'Perfect! 🌟' : 'Well Done! 🎉';
    document.getElementById('matchCompSub').textContent     =
        `You matched ${matchCorrect} out of 10 ordinals from ${getOrdinalLabel(start)} to ${getOrdinalLabel(end)}!`;

    const nextBtn = document.getElementById('matchNextBatchBtn');
    if (isLastBatch) {
        nextBtn.textContent = '🏆 All 100 done!';
        nextBtn.onclick     = () => startMatch(1);
    } else {
        nextBtn.textContent = `Next: ${getOrdinalLabel(end + 1)}–${getOrdinalLabel(end + 10)} →`;
        nextBtn.onclick     = () => startMatch(matchBatch + 1);
    }

    document.getElementById('matchCompletion').classList.add('show');

    const speech = perfect
        ? `Perfect! You matched all ten! ${getOrdinal(start)} to ${getOrdinal(end)}. Excellent work!`
        : `Well done! You matched ${matchCorrect} out of ten. Keep going!`;
    NG_Speech.sayInstruction(speech);
    launchConfetti6(perfect);
    saveProgress();
}

/* ================================================================
   SPELL MODE — show ordinal label (e.g. "5th"), pick the word
================================================================ */
function startSpell() {
    spellDone     = 0;
    spellCorrect  = 0;
    spellStreak   = 0;
    spellAnswered = false;
    document.getElementById('spellCompletion').classList.remove('show');
    document.getElementById('spellChoices').style.display = '';

    const all  = Array.from({length:100},(_,i)=>i+1).sort(()=>Math.random()-0.5);
    spellPool  = all.slice(0, 10);
    updateSpellProgress();
    nextSpellQuestion();
}

function nextSpellQuestion() {
    if (spellPool.length === 0) {
        showSpellCompletion();
        return;
    }

    spellAnswered = false;
    spellCurrent  = spellPool.shift();
    spellDone++;

    document.getElementById('spellBig').textContent = getOrdinalLabel(spellCurrent);
    document.getElementById('spellBig').style.animation = 'none';
    setTimeout(() => document.getElementById('spellBig').style.animation = '', 50);
    document.getElementById('spellSub').textContent  =
        `Tap the correct ordinal word for ${getOrdinalLabel(spellCurrent)}`;

    updateSpellProgress();
    renderSpellChoices(spellCurrent);

    setTimeout(() => {
        NG_Speech.sayInstruction(getOrdinalLabel(spellCurrent));
    }, 300);
}

function renderSpellChoices(correct) {
    const container = document.getElementById('spellChoices');
    container.innerHTML = '';

    const options = new Set([correct]);
    while (options.size < 4) {
        const r = Math.max(1, Math.min(100, correct + (Math.floor(Math.random()*14)-7)));
        if (r !== correct) options.add(r);
    }
    const shuffled = Array.from(options).sort(() => Math.random() - 0.5);

    shuffled.forEach(n => {
        const btn = document.createElement('button');
        btn.className   = 'spell-choice';
        btn.textContent = getOrdinal(n);
        btn.onclick     = () => checkSpellAnswer(n, btn, correct);
        container.appendChild(btn);
    });
}

function checkSpellAnswer(selected, btn, correct) {
    if (spellAnswered) return;
    spellAnswered = true;
    document.querySelectorAll('.spell-choice').forEach(b => b.disabled = true);

    if (selected === correct) {
        btn.classList.add('correct');
        spellCorrect++;
        spellStreak++;
        document.getElementById('spellCorrect').textContent = spellCorrect;
        document.getElementById('spellStreak').textContent  = spellStreak;
        learnedSet.add(correct);
        updateLearnedCount();
        saveProgress();
        NG_Speech.sayInstruction(`Very good! That is ${getOrdinal(correct)}.`);
        showToast(`✓ ${getOrdinal(correct)}`, 'success');
        burstConfetti6();
        setTimeout(nextSpellQuestion, 1600);
    } else {
        btn.classList.add('wrong');
        spellStreak = 0;
        document.getElementById('spellStreak').textContent = 0;
        document.querySelectorAll('.spell-choice').forEach(b => {
            if (b.textContent === getOrdinal(correct)) b.classList.add('correct');
        });
        NG_Speech.sayInstruction(`Sorry, try again. The answer is ${getOrdinal(correct)}.`);
        showToast(`The answer is ${getOrdinal(correct)}`, 'error');
        setTimeout(nextSpellQuestion, 2200);
    }
}

function updateSpellProgress() {
    const done = spellDone - 1;
    document.getElementById('spellFill').style.width  = (done / 10 * 100) + '%';
    document.getElementById('spellLabel').textContent = `${done} / 10`;
}

function showSpellCompletion() {
    document.getElementById('spellChoices').style.display = 'none';
    document.getElementById('spellFill').style.width      = '100%';
    document.getElementById('spellLabel').textContent     = '10 / 10';

    const perfect = spellCorrect >= 9;
    document.getElementById('spellCompTitle').textContent = perfect ? 'Perfect Spelling! 🌟' : 'Brilliant! 🎉';
    document.getElementById('spellCompSub').textContent   =
        `You got ${spellCorrect} out of 10 correct!`;
    document.getElementById('spellCompletion').classList.add('show');

    NG_Speech.sayInstruction(`Brilliant! You got ${spellCorrect} out of 10!`);
    launchConfetti6(perfect);
    saveProgress();
}

/* ================================================================
   MODE SWITCH
================================================================ */
function setMode(mode) {
    currentMode = mode;
    document.getElementById('tabLearn').classList.toggle('active', mode === 'learn');
    document.getElementById('tabMatch').classList.toggle('active', mode === 'match');
    document.getElementById('tabSpell').classList.toggle('active', mode === 'spell');
    document.getElementById('modeLearn').style.display = mode === 'learn' ? '' : 'none';
    document.getElementById('modeMatch').style.display = mode === 'match' ? '' : 'none';
    document.getElementById('modeSpell').style.display = mode === 'spell' ? '' : 'none';

    NG_Speech.stop();

    if (mode === 'learn') {
        NG_Speech.sayInstruction('Tap any card to hear the ordinal number!');
    } else if (mode === 'match') {
        startMatch(1);
    } else if (mode === 'spell') {
        startSpell();
        NG_Speech.sayInstruction('Spell it mode. See the ordinal label and pick the correct word!');
    }
}

/* ================================================================
   PROGRESS / STORAGE
================================================================ */
function saveProgress() {
    const score = Math.min(100, Math.round((learnedSet.size / 100) * 60 + matchCorrect * 3 + spellCorrect * 3));
    NG_Storage.setLvl7Score(score);
}

/* ================================================================
   TOAST
================================================================ */
let toastTimer = null;
function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = 'feedback-toast show' + (type ? ' ' + type : '');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2200);
}

/* ================================================================
   INIT
================================================================ */
document.addEventListener('DOMContentLoaded', function () {
    buildLearnBatches();
    renderLearnGrid();
    setTimeout(() => {
        NG_Speech.sayInstruction('Welcome to Level 7! Learn ordinal numbers — first, second, third — all the way to one hundredth!');
    }, 500);
});
/* ================================================================
   CONFETTI — emoji shower on correct answer and completion
================================================================ */
(function() {
    const EMOJIS_FULL  = ['🌸','🌺','🌼','⭐','✨','🌟','💛','🌷','🎉','🏆','🎀','🌻','💮','🎊','🥳'];
    const EMOJIS_BURST = ['⭐','✨','🌸','🌺','🌼','💛','🌟','🎉'];
    let timer = null, fc = 0;

    function makeP(emojis, w, h, delay) {
        return {
            x: Math.random() * w, y: -30 - (delay || 0),
            size: 20 + Math.random() * 20,
            emoji: emojis[Math.floor(Math.random() * emojis.length)],
            speed: 2.5 + Math.random() * 3.5,
            drift: (Math.random() - 0.5) * 2.5,
            swing: Math.random() * 0.04, swingOff: Math.random() * Math.PI * 2,
            spin: (Math.random() - 0.5) * 0.1, angle: Math.random() * Math.PI * 2, alpha: 1,
        };
    }

    function run(particles, loop) {
        const cvs = document.getElementById('confettiCanvas6');
        cvs.style.display = 'block';
        cvs.width = window.innerWidth; cvs.height = window.innerHeight;
        const ctx = cvs.getContext('2d');
        fc = 0;
        clearInterval(timer);
        timer = setInterval(() => {
            ctx.clearRect(0, 0, cvs.width, cvs.height);
            fc++;
            let alive = 0;
            for (const p of particles) {
                p.y += p.speed; p.x += p.drift + Math.sin(fc * p.swing + p.swingOff) * 0.8; p.angle += p.spin;
                if (p.y > cvs.height * 0.75) p.alpha = Math.max(0, 1 - (p.y - cvs.height * 0.75) / (cvs.height * 0.28));
                if (p.y < cvs.height + 50 && p.alpha > 0.02) {
                    alive++;
                    ctx.save(); ctx.globalAlpha = p.alpha; ctx.translate(p.x, p.y); ctx.rotate(p.angle);
                    ctx.font = p.size + 'px serif'; ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
                    ctx.fillText(p.emoji, 0, 0); ctx.restore();
                }
            }
            if (loop && fc < 300) {
                for (const p of particles) if (p.y > cvs.height + 40) Object.assign(p, makeP(EMOJIS_FULL, cvs.width, cvs.height, 0));
            }
            if ((!loop || fc > 300) && alive === 0) {
                clearInterval(timer); ctx.clearRect(0, 0, cvs.width, cvs.height); cvs.style.display = 'none';
            }
        }, 16);
    }

    // Full rain on completion (80 particles, recycling)
    window.launchConfetti6 = function(great) {
        const emojis = great ? EMOJIS_FULL : EMOJIS_FULL;
        const ps = Array.from({length: 80}, (_, i) => { const p = makeP(emojis, window.innerWidth, window.innerHeight, i * 8); return p; });
        run(ps, true);
    };

    // Small burst on each correct answer (20 particles, no loop)
    window.burstConfetti6 = function() {
        const w = window.innerWidth, h = window.innerHeight;
        const ps = Array.from({length: 20}, () => {
            const p = makeP(EMOJIS_BURST, w, h, 0);
            p.y = h * 0.3 + Math.random() * h * 0.2;   // start mid-screen
            p.speed = 1.5 + Math.random() * 2;
            return p;
        });
        run(ps, false);
    };
})();
</script>
</body>
</html>
