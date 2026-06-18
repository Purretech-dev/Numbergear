<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level 5 — Prime Numbers | Number Gear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
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
        .lvl5-tabs { display: flex; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; }
        .lvl5-tab {
            padding: 9px 20px; border: 2px solid var(--border); border-radius: 20px;
            background: var(--surface); font-size: 14px; font-weight: 800;
            cursor: pointer; font-family: inherit; color: var(--text-soft);
            transition: 0.18s ease;
        }
        .lvl5-tab:hover  { border-color: var(--purple); color: var(--purple-dark); }
        .lvl5-tab.active { background: var(--purple); border-color: var(--purple); color: white; }

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
</head>
<body>
<div class="app-shell">

    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">🔵</div>
            <div>
                <h1>Level 5</h1>
                <p>Prime Numbers Gear</p>
            </div>
        </div>
        <a href="../../index.php" class="back-btn">← Home</a>
    </header>

    <main class="level-page">

        <!-- Mode tabs -->
        <div class="lvl5-tabs">
            <button class="lvl5-tab active" id="tab-explorer" onclick="lvl5SetMode('explorer')">⚙️ Gear Explorer</button>
            <button class="lvl5-tab"        id="tab-sort"     onclick="lvl5SetMode('sort')">🔍 Prime Quiz</button>
        </div>

        <!-- GEAR EXPLORER section -->
        <div id="lvl5-explorer">

        <p class="gear-intro">
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
                    <div class="stat-chip-sm">Primes explored: <span id="primesExplored">0</span>/100</div>
                    <div class="stat-chip-sm">Current ring: <span id="currentRingLabel">1 (2–29)</span></div>
                </div>
                <canvas id="primeCanvas" width="560" height="560"></canvas>
            </div>

            <!-- RIGHT — Controls -->
            <div class="gear-right">

                <!-- Prime at pointer -->
                <div class="ring-display">
                    <div class="ring-meta">
                        <div class="rm-label">Prime at pointer</div>
                        <div class="ring-big-num" id="primeAtPointer">2</div>
                        <div class="rm-detail"    id="primeRingDetail">Ring 1 · position 1</div>
                        <div class="rm-table"     id="primeOrdinal">The 1st prime number</div>
                    </div>
                    <button class="hear-btn" onclick="hearCurrentPrime()">🔊</button>
                </div>

                <!-- Select ring batch -->
                <div class="ctrl-card">
                    <div class="card-title">Select a ring</div>
                    <div class="ring-grid" id="ringGrid"></div>
                </div>

                <!-- Rotate -->
                <div class="ctrl-card">
                    <div class="card-title">Rotate selected ring</div>
                    <div class="rot-row">
                        <button class="rot-btn ccw" onclick="rotateCCW()">
                            <span class="rot-icon">↺</span>
                            <span>Anti-clockwise</span>
                        </button>
                        <button class="rot-btn cw" onclick="rotateCW()">
                            <span class="rot-icon">↻</span>
                            <span>Clockwise</span>
                        </button>
                    </div>
                    <div class="sub-row">
                        <button class="spin-btn" id="spinBtn" onclick="toggleSpin()">▶ Auto-Spin</button>
                        <button class="reset-ring-btn" onclick="resetRing()">↺ This ring</button>
                    </div>
                </div>

                <!-- Gear actions -->
                <div class="ctrl-card">
                    <div class="card-title">Gear actions</div>
                    <div class="action-row">
                        <button class="action-btn reset" onclick="resetAll()">↺ Reset All</button>
                        <button class="action-btn" id="highlightBtn"
                            style="border-color:var(--peach);color:var(--peach-dark);"
                            onclick="toggleHighlight()">✨ Highlight Primes</button>
                    </div>
                    <div class="action-row" style="margin-top:8px;">
                        <button class="action-btn" id="mixBtn"
                            style="border-color:var(--purple);color:var(--purple-dark);"
                            onclick="toggleMix()">🎲 Mix Challenge</button>
                    </div>
                </div>

                <!-- Mix challenge card -->
                <div class="mix-card" id="mixCard">
                    <div class="mix-card-title">🎯 Mix Challenge</div>
                    <div class="mix-card-desc">All rings are shuffled! Select a ring, then use <strong>↺ Anti-clockwise</strong> or <strong>↻ Clockwise</strong> to rotate it until its first prime lines up with the <strong>orange arrow ▼</strong>.</div>
                    <div class="mix-prog-label" id="mixProgLabel">0 / 10 rings aligned</div>
                    <div class="mix-prog-track"><div class="mix-prog-fill" id="mixProgFill"></div></div>
                    <div class="mix-hint" id="mixHint">Select a ring to begin</div>
                </div>

                <!-- Prime fact -->
                <div class="prime-fact-card" id="primeFactCard">
                    <div class="pf-title">📘 Prime Fact</div>
                    <div class="pf-body" id="primeFactBody"></div>
                </div>

            </div>
        </div>
        </div><!-- /lvl5-explorer -->

        <!-- SORT QUIZ section -->
        <div id="lvl5-sort" style="display:none;">
            <p class="sort-intro">
                🔍 <strong>Which ones are prime?</strong> Each question shows you a mix of numbers.
                Some are <strong>prime</strong> (only divisible by 1 and themselves) and some are <strong>not prime</strong>.
                Tap every number you think is prime, then check your answer!
            </p>

            <!-- Score chips -->
            <div class="sort-score-row">
                <div class="stat-chip-sm">✅ Correct: <span id="sortCorrect">0</span></div>
                <div class="stat-chip-sm">❌ Wrong: <span id="sortWrong">0</span></div>
                <div class="stat-chip-sm">🔢 Questions: <span id="sortAttempts">0</span></div>
            </div>

            <!-- Question instruction -->
            <div class="sort-pool-label" id="sortInstruction">Tap all the PRIME numbers below:</div>

            <!-- Number tiles -->
            <div class="sort-pool" id="sortPool" style="min-height:70px;border-style:solid;border-color:var(--purple);"></div>

            <!-- Selected primes -->
            <div class="sort-answer-label">Your selected primes:</div>
            <div class="sort-answer" id="sortAnswer" style="min-height:52px;"></div>

            <!-- Buttons -->
            <button class="sort-check-btn" onclick="checkSortAnswer()">✓ Check my answer</button>
            <button class="sort-new-btn"   onclick="newSortQuestion()">🔄 Next question</button>

            <!-- Result -->
            <div class="sort-result" id="sortResult"></div>

            <!-- Answer reveal -->
            <div id="sortReveal" style="display:none;margin-top:10px;padding:12px 14px;background:var(--purple-light);border:2px solid var(--purple);border-radius:12px;font-size:13px;font-weight:700;color:var(--purple-dark);line-height:1.7;"></div>
        </div><!-- /lvl5-sort -->

    </main>
</div>

<div class="feedback-toast" id="toast"></div>

<script src="../../assets/js/speech.js"></script>
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
   PRIME FACTS (concise, kid-friendly)
============================================================ */
const PRIME_FACTS = {
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
};

function getPrimeFact(p) {
    if (PRIME_FACTS[p]) return PRIME_FACTS[p];
    // Generic fact for primes beyond the table
    const idx = PRIMES.indexOf(p) + 1;
    return `<strong>${p}</strong> is the ${ordinal(idx)} prime number. It can only be divided by 1 and ${p}.`;
}

function ordinal(n) {
    const s = ['th','st','nd','rd'], v = n % 100;
    return n + (s[(v-20)%10] || s[v] || s[0]);
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
        `Ring ${activeRing + 1} · position ${ringOffsets[activeRing] + 1}`;
    document.getElementById('primeOrdinal').textContent =
        `The ${ordinal(idx + 1)} prime number`;

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
    showToast('Ring ' + (activeRing + 1) + ' reset ↺', '');
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
    showToast('All rings reset ↺', '');
}

/* ============================================================
   SPIN
============================================================ */
function toggleSpin() {
    if (spinInterval) stopSpin(); else startSpin();
}
function startSpin() {
    document.getElementById('spinBtn').textContent = '⏸ Stop';
    document.getElementById('spinBtn').classList.add('spinning');
    spinInterval = setInterval(rotateCW, 700);
}
function stopSpin() {
    clearInterval(spinInterval);
    spinInterval = null;
    document.getElementById('spinBtn').textContent = '▶ Auto-Spin';
    document.getElementById('spinBtn').classList.remove('spinning');
}

/* ============================================================
   HIGHLIGHT
============================================================ */
function toggleHighlight() {
    highlightMode = !highlightMode;
    const btn = document.getElementById('highlightBtn');
    btn.style.background = highlightMode ? 'var(--peach-light)' : '';
    btn.textContent = highlightMode ? '✨ Highlighted' : '✨ Highlight Primes';
    drawGear();
    showToast(highlightMode ? 'Explored primes highlighted ✨' : 'Highlight off', '');
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
    NG_Storage.setLvl5Score(Math.round((exploredPrimes.size / 100) * 100));
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
    document.getElementById('mixBtn').textContent  = '✕ Exit Challenge';
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
    showToast('Rings mixed! 🎲 Align them all!', '');
    NG_Speech.sayInstruction('Mix challenge! Rotate each ring until its first prime lines up with the reference arrow.');
}

function exitMixMode() {
    mixMode = false;
    document.getElementById('mixBtn').textContent = '🎲 Mix Challenge';
    document.getElementById('mixBtn').style.background = '';
    document.getElementById('mixCard').classList.remove('show');
}

function checkMixProgress() {
    const aligned = ringOffsets.filter(o => o === 0).length;
    document.getElementById('mixProgLabel').textContent = `${aligned} / 10 rings aligned`;
    document.getElementById('mixProgFill').style.width  = (aligned * 10) + '%';
    updateMixHint();
    if (aligned === NUM_RINGS) onMixComplete();
}

function updateMixHint() {
    const hintEl = document.getElementById('mixHint');
    const offset = ringOffsets[activeRing];
    if (offset === 0) {
        hintEl.className = 'mix-hint aligned';
        hintEl.textContent = `✓ Ring ${activeRing + 1} aligned! Select another ring.`;
    } else {
        hintEl.className = 'mix-hint';
        const cwSteps  = SLOTS - offset;
        const ccwSteps = offset;
        if (cwSteps <= ccwSteps) {
            hintEl.textContent = `Ring ${activeRing + 1}: rotate ↻ clockwise ${cwSteps} step${cwSteps > 1 ? 's' : ''}.`;
        } else {
            hintEl.textContent = `Ring ${activeRing + 1}: rotate ↺ anti-clockwise ${ccwSteps} step${ccwSteps > 1 ? 's' : ''}.`;
        }
    }
}

function onMixComplete() {
    if (spinInterval) stopSpin();
    exitMixMode();
    buildRingGrid();
    drawGear();
    showToast('🎉 All rings aligned! Brilliant!', 'success');
    NG_Speech.sayInstruction('Fantastic! All rings are aligned. You solved the prime number mix challenge!');
    NG_Storage.setLvl5Score(Math.min(100, (NG_Storage.getLvl5Score ? NG_Storage.getLvl5Score() : 0) + 20));
}

/* ============================================================
   INIT
============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    buildRingGrid();
    updatePointerDisplay();
    drawGear();
    setTimeout(() => NG_Speech.sayInstruction(
        'Welcome to the Prime Numbers Gear! Explore the first one hundred prime numbers. Spin any ring to discover them!'
    ), 600);
});

/* ============================================================
   MODE SWITCHER
============================================================ */
function lvl5SetMode(mode) {
    document.getElementById('tab-explorer').classList.toggle('active', mode === 'explorer');
    document.getElementById('tab-sort').classList.toggle('active', mode === 'sort');
    document.getElementById('lvl5-explorer').style.display = mode === 'explorer' ? '' : 'none';
    document.getElementById('lvl5-sort').style.display     = mode === 'sort'     ? '' : 'none';
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
        ansEl.innerHTML = '<span style="color:var(--text-soft);font-size:13px;font-weight:700;padding:8px;">Tap numbers above to select them…</span>';
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
        tile.title = 'Click to deselect';
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
        showToast('Tap at least one number you think is prime!', '');
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
        resultEl.innerHTML = `🌟 <strong>Brilliant!</strong> You found all ${correctPrimes.length} prime${correctPrimes.length > 1 ? 's' : ''}!`;
        revealEl.innerHTML = `✅ The primes were: <strong>${correctPrimes.sort((a,b)=>a-b).join(', ')}</strong><br>` +
            `🔵 Not prime: ${sortPool.filter(n => !PRIME_SET.has(n)).sort((a,b)=>a-b).join(', ')}`;
        NG_Speech.sayInstruction('Brilliant! You found all the prime numbers!');
        NG_Storage.setLvl5Score(Math.min(100, (NG_Storage.getLvl5Score ? NG_Storage.getLvl5Score() : 0) + 8));
    } else {
        sortWrongN++;
        document.getElementById('sortWrong').textContent = sortWrongN;
        resultEl.className = 'sort-result wrong';
        let msg = '❌ <strong>Not quite!</strong> ';
        if (falsePositives.length > 0)
            msg += `${falsePositives.join(', ')} ${falsePositives.length > 1 ? 'are' : 'is'} <strong>not prime</strong>. `;
        if (missedPrimes.length > 0)
            msg += `You missed: <strong>${missedPrimes.join(', ')}</strong>.`;
        resultEl.innerHTML = msg;
        revealEl.innerHTML = `✅ The primes were: <strong>${correctPrimes.sort((a,b)=>a-b).join(', ')}</strong><br>` +
            `🔴 Not prime (these have other factors): ${sortPool.filter(n => !PRIME_SET.has(n)).sort((a,b)=>a-b).join(', ')}`;
        NG_Speech.sayInstruction('Not quite! Check which numbers are prime and try the next question.');
    }
}
</script>
</body>
</html>
