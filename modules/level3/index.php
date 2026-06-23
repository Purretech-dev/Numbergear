<?php
// Number Gear — Level 3: Number Gear (Multiplication Rings)
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
    <title>Level 3 — Number Gear | Number Gear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
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
        }
        .gear-right {
            flex: 0 0 360px;
            width: 360px;
            display: flex;
            flex-direction: column;
            gap: 13px;
        }
        @media (max-width: 900px) {
            .gear-layout { flex-direction: column; align-items: center; }
            .gear-right  { width: 100%; max-width: 560px; flex: none; }
        }

        #gearCanvas {
            display: block;
            width: 100%;
            max-width: 560px;
            height: auto;
            cursor: pointer;
            touch-action: manipulation;
            border-radius: 50%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }

        .stats-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .stat-chip-sm {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 800;
            color: var(--text-soft);
        }
        .stat-chip-sm span { color: var(--peach-dark); font-size: 15px; }

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
            font-size: 50px;
            font-weight: 900;
            color: var(--peach-dark);
            line-height: 1;
            min-width: 62px;
            text-align: center;
            animation: popIn 0.3s ease;
        }
        .ring-meta { flex: 1; }
        .ring-meta .rm-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-soft);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .ring-meta .rm-detail {
            font-size: 13px;
            font-weight: 800;
            color: var(--peach-dark);
            margin-top: 2px;
        }
        .ring-meta .rm-table {
            font-size: 11px;
            color: var(--text-soft);
            font-weight: 600;
            margin-top: 3px;
            line-height: 1.4;
        }
        .hear-btn {
            padding: 10px 12px;
            background: var(--peach);
            border: none;
            border-radius: 11px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            font-family: inherit;
            transition: 0.18s ease;
            flex-shrink: 0;
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
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            color: var(--text-soft);
            font-family: inherit;
            text-align: center;
            transition: 0.18s ease;
            line-height: 1.3;
            position: relative;
        }
        .ring-btn:hover             { border-color: var(--peach); color: var(--peach-dark); }
        .ring-btn.active            { background: var(--peach); border-color: var(--peach); color: white; }
        .ring-btn.aligned           { border-color: var(--mint); background: var(--mint-light); color: var(--mint-dark); }
        .ring-btn.active.aligned    { background: var(--mint); border-color: var(--mint); color: white; }
        .ring-check {
            position: absolute;
            top: -5px; right: -5px;
            width: 14px; height: 14px;
            background: var(--mint);
            border-radius: 50%;
            font-size: 8px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-weight: 900;
            pointer-events: none;
        }

        .rot-row {
            display: flex;
            gap: 8px;
        }
        .rot-btn {
            flex: 1;
            padding: 13px 6px;
            border: 2.5px solid;
            border-radius: 13px;
            background: var(--surface);
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: 0.18s ease;
            line-height: 1.2;
        }
        .rot-btn .rot-icon { font-size: 24px; line-height: 1; }
        .rot-btn.ccw {
            border-color: var(--purple);
            color: var(--purple-dark);
            background: var(--purple-light);
        }
        .rot-btn.cw  {
            border-color: var(--mint);
            color: var(--mint-dark);
            background: var(--mint-light);
        }
        .rot-btn.ccw:hover  { background: var(--purple); color: white; }
        .rot-btn.cw:hover   { background: var(--mint);   color: white; }
        .rot-btn:active     { transform: scale(0.96); }

        .sub-row {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }
        .spin-btn {
            flex: 1;
            padding: 9px 8px;
            border: 2px solid var(--sky);
            border-radius: 10px;
            background: var(--sky-light);
            color: var(--sky-dark);
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            transition: 0.18s ease;
            text-align: center;
        }
        .spin-btn.spinning { background: var(--sky); color: white; border-color: var(--sky); }
        .reset-ring-btn {
            padding: 9px 13px;
            border: 2px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            font-size: 13px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 800;
            color: var(--text-soft);
            transition: 0.18s ease;
            white-space: nowrap;
        }
        .reset-ring-btn:hover { background: var(--peach-light); border-color: var(--peach); color: var(--peach-dark); }

        .action-row {
            display: flex;
            gap: 8px;
        }
        .action-btn {
            flex: 1;
            padding: 11px 8px;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            color: var(--text-soft);
            transition: 0.18s ease;
            text-align: center;
        }
        .action-btn:hover { background: var(--bg); }
        .action-btn.reset { border-color: var(--mint);   color: var(--mint-dark); }
        .action-btn.reset:hover { background: var(--mint-light); }
        .action-btn.mix   { border-color: var(--purple); color: var(--purple); }
        .action-btn.mix:hover   { background: var(--purple-light); }
        .action-btn.mix.active  { background: var(--purple); color: white; }

        .mix-card {
            background: linear-gradient(135deg, var(--purple-light) 0%, #fde8d8 100%);
            border: 2px solid var(--purple);
            border-radius: 16px;
            padding: 14px 16px;
            display: none;
        }
        .mix-card.show { display: block; animation: popIn 0.35s ease; }
        .mix-card-title {
            font-size: 14px;
            font-weight: 900;
            color: var(--purple-dark);
            margin-bottom: 7px;
        }
        .mix-card-desc {
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.55;
            margin-bottom: 10px;
        }
        .mix-prog-label {
            font-size: 12px;
            font-weight: 800;
            color: var(--purple-dark);
            margin-bottom: 5px;
        }
        .mix-prog-track {
            height: 8px;
            background: rgba(0,0,0,0.1);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .mix-prog-fill {
            height: 100%;
            background: var(--mint);
            border-radius: 6px;
            width: 0%;
            transition: width 0.4s ease;
        }
        .mix-hint {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-soft);
            background: rgba(255,255,255,0.65);
            border-radius: 8px;
            padding: 6px 10px;
            min-height: 28px;
            line-height: 1.5;
        }
        .mix-hint.aligned { color: var(--mint-dark); }

        /* ── Mode toggle switch ── */
        .mode-toggle-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 10px 14px;
        }
        .mode-label {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-soft);
            flex: 1;
            transition: color 0.2s;
        }
        .mode-label.active { color: var(--peach-dark); }
        .toggle-track {
            position: relative;
            width: 48px; height: 26px;
            background: #c8d0dc;
            border-radius: 13px;
            cursor: pointer;
            transition: background 0.25s;
            flex-shrink: 0;
            border: none;
            padding: 0;
        }
        .toggle-track.on { background: var(--peach); }
        .toggle-thumb {
            position: absolute;
            top: 3px; left: 3px;
            width: 20px; height: 20px;
            background: white;
            border-radius: 50%;
            transition: left 0.25s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.18);
            pointer-events: none;
        }
        .toggle-track.on .toggle-thumb { left: 25px; }

        /* ── Calc panel ── */
        .calc-panel {
            display: none;
            flex-direction: column;
            gap: 12px;
        }
        .calc-panel.show { display: flex; animation: popIn 0.3s ease; }

        .calc-card {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 14px 16px;
        }
        .calc-row {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .calc-row label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-soft);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 5px;
        }
        .calc-field {
            flex: 1;
            min-width: 60px;
        }
        .calc-input {
            width: 100%;
            padding: 9px 10px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 16px;
            font-weight: 800;
            font-family: inherit;
            color: var(--text);
            background: var(--bg);
            box-sizing: border-box;
            text-align: center;
            transition: border-color 0.2s;
        }
        .calc-input:focus { outline: none; border-color: var(--peach); }
        .calc-op-row {
            display: flex;
            gap: 8px;
            margin: 4px 0;
        }
        .op-btn {
            flex: 1;
            padding: 11px 6px;
            font-size: 22px;
            font-weight: 900;
            border: 2.5px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            cursor: pointer;
            font-family: inherit;
            transition: 0.18s ease;
            color: var(--text-soft);
        }
        .op-btn.selected-add { background: var(--mint-light);   border-color: var(--mint);   color: var(--mint-dark); }
        .op-btn.selected-sub { background: var(--purple-light); border-color: var(--purple); color: var(--purple-dark); }
        .op-btn:hover { background: var(--bg); }

        .calc-go-btn {
            width: 100%;
            margin-top: 4px;
            padding: 13px;
            background: var(--peach);
            color: white;
            border: none;
            border-radius: 13px;
            font-size: 15px;
            font-weight: 900;
            font-family: inherit;
            cursor: pointer;
            transition: 0.18s ease;
        }
        .calc-go-btn:hover  { background: var(--peach-dark); }
        .calc-go-btn:active { transform: scale(0.97); }
        .calc-go-btn:disabled { background: #c8d0dc; cursor: not-allowed; }

        .calc-result {
            display: none;
            background: linear-gradient(135deg, var(--mint-light) 0%, #fff8f0 100%);
            border: 2.5px solid var(--mint);
            border-radius: 16px;
            padding: 14px 16px;
            animation: popIn 0.35s ease;
        }
        .calc-result.show { display: block; }
        .cr-equation {
            font-size: 22px;
            font-weight: 900;
            color: var(--mint-dark);
            text-align: center;
            margin-bottom: 6px;
        }
        .cr-steps {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-soft);
            text-align: center;
            line-height: 1.5;
        }
        .cr-hint {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-soft);
            background: rgba(255,255,255,0.7);
            border-radius: 8px;
            padding: 6px 10px;
            margin-top: 8px;
            line-height: 1.5;
        }
        .calc-error {
            font-size: 12px;
            font-weight: 700;
            color: #c0392b;
            background: #fdf0ef;
            border: 1.5px solid #f0b9b5;
            border-radius: 10px;
            padding: 8px 12px;
            display: none;
            margin-top: 4px;
        }
        .calc-error.show { display: block; }

        /* calibration tick overlay on canvas */
        /* (drawn via JS) */

        /* ── Example card ── */
        .example-card {
            background: linear-gradient(135deg, #fff8f0 0%, #edf4fb 100%);
            border: 2.5px solid var(--peach);
            border-radius: 18px;
            padding: 18px 16px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .ex-badge {
            font-size: 10px;
            font-weight: 800;
            color: var(--text-soft);
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .ex-sum {
            font-size: 38px;
            font-weight: 900;
            color: var(--peach-dark);
            line-height: 1;
            letter-spacing: -1px;
        }
        .ex-ring-hint {
            display: inline-block;
            font-size: 12px;
            font-weight: 800;
            color: var(--purple-dark);
            background: var(--purple-light);
            border: 1.5px solid var(--purple);
            border-radius: 20px;
            padding: 3px 10px;
            width: fit-content;
        }
        .ex-desc {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.6;
        }
        .ex-go-btn {
            margin-top: 4px;
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
        .ex-go-btn:hover  { background: var(--peach-dark); }
        .ex-go-btn:active { transform: scale(0.97); }
        .ex-go-btn .ex-go-icon { font-size: 20px; }

        .ex-next-btn {
            width: 100%;
            padding: 12px;
            background: var(--mint);
            color: white;
            border: none;
            border-radius: 13px;
            font-size: 14px;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            transition: 0.18s ease;
            margin-top: 4px;
        }
        .ex-next-btn:hover  { background: var(--mint-dark); }
        .ex-next-btn:active { transform: scale(0.97); }
        .ex-next-btn.outline {
            background: transparent;
            color: var(--mint-dark);
            border: 2px solid var(--mint);
        }
        .ex-next-btn.outline:hover { background: var(--mint-light); }
    </style>
</head>
<body>
<div class="app-shell">

    <!-- Header -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">⚙️</div>
            <div>
                <h1>Level 3</h1>
                <p>Number Gear</p>
            </div>
        </div>
        <a href="../../index.php" class="back-btn">← Home</a>
    </header>

    <main class="level-page">

        <p class="gear-intro">The Number Gear has <strong>10 spinning planes</strong>. Each plane counts in multiples — Plane 1 starts at <strong>1</strong> and counts 1, 2, 3 … up to 10. Plane 2 starts at <strong>2</strong> and counts 2, 4, 6 … up to 20. Plane 3 starts at <strong>3</strong> and counts 3, 6, 9 … up to 30, and so on. Plane 10 starts at <strong>10</strong> and counts 10, 20, 30 … all the way to 100. The <strong>orange arrow ▼</strong> marks the reference point — every plane's first number always lines up there!</p>

        <div class="gear-layout">

            <!-- LEFT — Canvas -->
            <div class="gear-left">
                <div class="stats-row">
                    <div class="stat-chip-sm">Planes explored: <span id="exploredCount">0</span>/10</div>
                    <div class="stat-chip-sm">Rotations: <span id="rotCount">0</span></div>
                </div>
                <canvas id="gearCanvas" width="560" height="560"></canvas>
            </div>

            <!-- RIGHT — Controls -->
            <div class="gear-right">

                <!-- Mode toggle -->
                <div class="mode-toggle-wrap">
                    <span class="mode-label active" id="normalLabel">Normal</span>
                    <button class="toggle-track" id="modeToggle" onclick="toggleCalcMode()" aria-label="Switch mode">
                        <div class="toggle-thumb"></div>
                    </button>
                    <span class="mode-label" id="calcLabel">Calc Mode</span>
                </div>

                <!-- Calc panel (hidden in normal mode) -->
                <div class="calc-panel" id="calcPanel">

                    <!-- Example card -->
                    <div class="example-card" id="exampleCard">
                        <div class="ex-badge" id="exBadge">Example 1 of 12</div>
                        <div class="ex-sum" id="exSum">15 + 9</div>
                        <div class="ex-ring-hint" id="exRingHint">Use Plane 3</div>
                        <div class="ex-desc" id="exDesc">Find 15 on Plane 3, then count 9 steps — move clockwise.</div>
                        <button class="ex-go-btn" id="exGoBtn" onclick="runExample()">
                            <span class="ex-go-icon">⚙️</span> Show me on the gear!
                        </button>
                    </div>

                    <!-- Result -->
                    <div class="calc-result" id="calcResult">
                        <div class="cr-equation" id="crEquation"></div>
                        <div class="cr-steps"   id="crSteps"></div>
                        <div class="cr-hint"    id="crHint"></div>
                        <button class="ex-next-btn" onclick="nextExample()">Next Example →</button>
                    </div>

                    <!-- Next button (before result too) -->
                    <button class="ex-next-btn outline" id="nextBtnTop" onclick="nextExample()">Next Example →</button>

                </div>

                <!-- Normal-mode controls (hidden in calc mode) -->
                <div id="normalPanel">

                <!-- Number at pointer -->
                <div class="ring-display">
                    <div class="ring-meta">
                        <div class="rm-label">Number at pointer</div>
                        <div class="ring-big-num" id="windowNum">1</div>
                        <div class="rm-detail" id="windowDetail">Plane 1 · step 1 = 1</div>
                        <div class="rm-table"  id="windowTable">Table: 1, 2, 3, 4, 5, 6, 7, 8, 9, 10</div>
                    </div>
                    <button class="hear-btn" onclick="hearCurrent()" title="Hear this number">🔊</button>
                </div>

                <!-- Plane selector -->
                <div class="ctrl-card">
                    <div class="card-title">Select a plane</div>
                    <div class="ring-grid" id="ringGrid"></div>
                </div>

                <!-- Rotate controls -->
                <div class="ctrl-card">
                    <div class="card-title">Rotate selected plane</div>
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
                        <button class="reset-ring-btn" onclick="resetActiveRing()" title="Reset this ring only">↺ This ring</button>
                    </div>
                </div>

                <!-- Gear-level actions -->
                <div class="ctrl-card">
                    <div class="card-title">Gear actions</div>
                    <div class="action-row">
                        <button class="action-btn reset" onclick="resetAllRings()">↺ Reset All</button>
                        <button class="action-btn mix" id="mixBtn" onclick="toggleMix()">🎲 Mix Challenge</button>
                    </div>
                </div>

                <!-- Mix challenge status -->
                <div class="mix-card" id="mixCard">
                    <div class="mix-card-title">🎯 Mix Challenge</div>
                    <div class="mix-card-desc">All planes are shuffled! Click a plane, then use <strong>↺ Anti-clockwise</strong> or <strong>↻ Clockwise</strong> to rotate it until its reference number lines up with the <strong>orange arrow</strong>.</div>
                    <div class="mix-prog-label" id="mixProgLabel">0 / 10 planes aligned</div>
                    <div class="mix-prog-track">
                        <div class="mix-prog-fill" id="mixProgFill"></div>
                    </div>
                    <div class="mix-hint" id="mixHint">Select a plane to begin</div>
                </div>

                </div><!-- /normalPanel -->
            </div><!-- /gear-right -->
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
   GEOMETRY
   CANVAS = 560 × 560   CX = CY = 280
   HUB_R    = 30        — centre "0" circle
   REF_R_IN = 30        — reference ring inner (= HUB_R)
   REF_R_OUT= 52        — reference ring outer  (width = 22 px)
   RING_W   = 25        — each rotating ring width  (wider → bigger numbers)
   Ring i (0-based): inner = 52 + i*25,  outer = 52 + (i+1)*25
   Ring 9 outer = 52 + 250 = 302  → fits within 280 (radius) ✓
   (Actually 302 > 280 — so we need to recalculate)
   Correct:  outer ring 9 = 52 + 10*25 = 302 → too big
   Reduce RING_W to 22: 52 + 10*22 = 272 < 280 ✓  (8px margin)
================================================================ */
const CS = 560, CX = 280, CY = 280;
const HUB_R     = 30;
const REF_R_OUT = 54;   // reference ring outer radius
const RING_W    = 22;   // each plane width
const NUM_RINGS = 10;
const SLOTS     = 10;
const SLOT_DEG  = 360 / SLOTS;  // 36°

// Pastel fills (inner→outer)
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

/* ================================================================
   STATE
================================================================ */
let ringOffsets    = new Array(NUM_RINGS).fill(0);
let activePlane    = 0;
let exploredPlanes = new Set([0]);
let totalRotations = 0;
let spinInterval   = null;
let mixMode        = false;

/* Calc mode */
let calcMode       = false;
let calcRing       = 0;          // active ring in calc mode (0-based)
let calcHighlight  = null;       // { startAngle, steps, op, stepsDone } for canvas overlay
let calcAnimTimer  = null;

/* ================================================================
   CANVAS
================================================================ */
const canvas = document.getElementById('gearCanvas');
const ctx    = canvas.getContext('2d');

// Outer radius of the last ring
const GEAR_OUTER_R = REF_R_OUT + NUM_RINGS * RING_W;  // 54 + 220 = 274

function drawGear() {
    ctx.clearRect(0, 0, CS, CS);

    // — Background disc —
    ctx.beginPath();
    ctx.arc(CX, CY, GEAR_OUTER_R + 6, 0, Math.PI * 2);
    ctx.fillStyle = '#dde4ee';
    ctx.fill();
    ctx.strokeStyle = '#bcc8d8';
    ctx.lineWidth = 2;
    ctx.stroke();

    // ---- Rings (outermost first) ----
    for (let i = NUM_RINGS - 1; i >= 0; i--) {
        const rIn  = REF_R_OUT + i * RING_W;
        const rOut = REF_R_OUT + (i + 1) * RING_W;
        const active  = (i === activePlane);
        const aligned = mixMode && ringOffsets[i] === 0;

        // Annulus fill
        ctx.beginPath();
        ctx.arc(CX, CY, rOut, 0, Math.PI * 2);
        ctx.arc(CX, CY, rIn,  0, Math.PI * 2, true);
        if (aligned)      ctx.fillStyle = active ? '#cdf5ea' : '#e4faf3';
        else if (active)  ctx.fillStyle = lightenHex(RING_FILLS[i], -14);
        else              ctx.fillStyle = RING_FILLS[i];
        ctx.fill();

        // Ring border
        ctx.beginPath();
        ctx.arc(CX, CY, rOut, 0, Math.PI * 2);
        if (aligned && active)   { ctx.strokeStyle = '#2eaa86'; ctx.lineWidth = 2.5; }
        else if (aligned)        { ctx.strokeStyle = '#52c4a0'; ctx.lineWidth = 1.5; }
        else if (active)         { ctx.strokeStyle = '#f4a571'; ctx.lineWidth = 2; }
        else                     { ctx.strokeStyle = '#c4cedd'; ctx.lineWidth = 1; }
        ctx.stroke();
    }

    // ---- Pointer-slot highlight (top arc, each plane) ----
    for (let i = 0; i < NUM_RINGS; i++) {
        const rIn  = REF_R_OUT + i * RING_W;
        const rOut = REF_R_OUT + (i + 1) * RING_W;
        const half = (Math.PI * 2 / SLOTS) / 2 * 0.82;
        const top  = -Math.PI / 2;
        const active  = (i === activePlane);
        const aligned = mixMode && ringOffsets[i] === 0;

        ctx.beginPath();
        ctx.arc(CX, CY, rOut - 0.5, top - half, top + half);
        ctx.arc(CX, CY, rIn  + 0.5, top + half, top - half, true);
        ctx.closePath();
        if      (active && aligned && mixMode) ctx.fillStyle = 'rgba(82,196,160,0.5)';
        else if (active)                       ctx.fillStyle = 'rgba(244,165,113,0.52)';
        else if (aligned && mixMode)           ctx.fillStyle = 'rgba(82,196,160,0.22)';
        else                                   ctx.fillStyle = 'rgba(0,0,0,0.04)';
        ctx.fill();
    }

    // ---- Scale calibration ticks on active ring (calc mode) ----
    if (calcMode) {
        const i    = calcRing;
        const rIn  = REF_R_OUT + i * RING_W;
        const rOut = REF_R_OUT + (i + 1) * RING_W;
        const mult = i + 1;

        // Total individual steps across the full ring = SLOTS * mult
        // Each slot (between two multiples) is divided into `mult` sub-steps
        // Tick heights (from inner edge outward):
        //   every 1 step  → short tick  (2px)
        //   every 5 steps → medium tick (5px)
        //   every mult steps (= at a multiple) → drawn as the number itself, skip tick

        const totalSteps = SLOTS * mult;   // e.g. ring×3 → 30 steps

        for (let j = 0; j < SLOTS; j++) {           // each slot (multiple gap)
            for (let t = 1; t < mult; t++) {         // sub-ticks within slot (skip t=0, that's the number)
                const globalStep = j * mult + t;     // global step index
                const tickDeg = 270 + (j - ringOffsets[i]) * SLOT_DEG + (t / mult) * SLOT_DEG;
                const tickRad = tickDeg * Math.PI / 180;

                // Tick length: medium every 5 global steps, short otherwise
                const isMedium = (globalStep % 5 === 0);
                const tickLen  = isMedium ? 6 : 3;
                const tickW    = isMedium ? 1.2 : 0.8;
                const color    = isMedium ? 'rgba(80,100,140,0.7)' : 'rgba(100,120,160,0.45)';

                // Ticks drawn from inner edge of ring outward
                const r1 = rIn + 2;
                const r2 = rIn + 2 + tickLen;
                ctx.beginPath();
                ctx.moveTo(CX + r1 * Math.cos(tickRad), CY + r1 * Math.sin(tickRad));
                ctx.lineTo(CX + r2 * Math.cos(tickRad), CY + r2 * Math.sin(tickRad));
                ctx.strokeStyle = color;
                ctx.lineWidth   = tickW;
                ctx.lineCap     = 'round';
                ctx.stroke();
            }

            // Long tick at each multiple boundary (between slots)
            const boundaryDeg = 270 + (j - ringOffsets[i]) * SLOT_DEG;
            const boundaryRad = boundaryDeg * Math.PI / 180;
            ctx.beginPath();
            ctx.moveTo(CX + rIn  * Math.cos(boundaryRad), CY + rIn  * Math.sin(boundaryRad));
            ctx.lineTo(CX + rOut * Math.cos(boundaryRad), CY + rOut * Math.sin(boundaryRad));
            ctx.strokeStyle = 'rgba(60,80,130,0.25)';
            ctx.lineWidth   = 0.8;
            ctx.stroke();
        }

        // ---- Calc highlight: needle + counted tick marks ----
        if (calcHighlight) {
            const { startSlot, steps, op, stepsDone } = calcHighlight;
            const mult      = calcRing + 1;
            const dir       = op === 'add' ? 1 : -1;
            const accentCol = op === 'add' ? '#2eaa86' : '#8250c8';
            const rMid      = (rIn + rOut) / 2;

            // Ticks are drawn relative to the CURRENT ring offset (ring is physically rotating)
            // so they stay locked to the ring face as it spins.
            // Start marker sits at the reference (top) since we rotated the ring to put startVal there.
            // Counted ticks advance clockwise (add) or anticlockwise (sub) from reference.

            // Highlight each counted unit step with a tick at its position on the ring face
            for (let s = 1; s <= stepsDone; s++) {
                // Position relative to current ring offset — ticks move WITH the ring
                const tickOffsetDeg = dir * (s / mult) * SLOT_DEG;
                const tickDeg = 270 + tickOffsetDeg;   // reference is always 270°
                const tickRad = tickDeg * Math.PI / 180;
                const isLong  = (s % mult === 0);
                const isMid5  = (s % 5 === 0);
                const len     = isLong ? RING_W - 2 : isMid5 ? 7 : 4;
                const r1h     = rIn + 2;
                const r2h     = rIn + 2 + len;
                ctx.beginPath();
                ctx.moveTo(CX + r1h * Math.cos(tickRad), CY + r1h * Math.sin(tickRad));
                ctx.lineTo(CX + r2h * Math.cos(tickRad), CY + r2h * Math.sin(tickRad));
                ctx.strokeStyle = accentCol;
                ctx.lineWidth   = isLong ? 2 : 1.5;
                ctx.lineCap     = 'round';
                ctx.stroke();
            }

            // Start marker — orange needle at the reference (top, 270°)
            const startRad = -Math.PI / 2;
            ctx.beginPath();
            ctx.moveTo(CX + rIn  * Math.cos(startRad), CY + rIn  * Math.sin(startRad));
            ctx.lineTo(CX + rOut * Math.cos(startRad), CY + rOut * Math.sin(startRad));
            ctx.strokeStyle = '#f4a571';
            ctx.lineWidth   = 2.5;
            ctx.lineCap     = 'round';
            ctx.stroke();

            // Current position needle — advances clockwise (add) or anticlockwise (sub) from top
            if (stepsDone > 0) {
                const curOffsetDeg = dir * (stepsDone / mult) * SLOT_DEG;
                const curDeg = 270 + curOffsetDeg;
                const curRad = curDeg * Math.PI / 180;
                ctx.beginPath();
                ctx.moveTo(CX + rIn  * Math.cos(curRad), CY + rIn  * Math.sin(curRad));
                ctx.lineTo(CX + rOut * Math.cos(curRad), CY + rOut * Math.sin(curRad));
                ctx.strokeStyle = accentCol;
                ctx.lineWidth   = 2.5;
                ctx.lineCap     = 'round';
                ctx.stroke();

                // Arrowhead at outer edge pointing in direction of travel
                const arrowSize = 4;
                const tipX = CX + rOut * Math.cos(curRad);
                const tipY = CY + rOut * Math.sin(curRad);
                ctx.beginPath();
                ctx.moveTo(tipX, tipY);
                ctx.lineTo(tipX - arrowSize * Math.cos(curRad - 0.4),
                           tipY - arrowSize * Math.sin(curRad - 0.4));
                ctx.lineTo(tipX - arrowSize * Math.cos(curRad + 0.4),
                           tipY - arrowSize * Math.sin(curRad + 0.4));
                ctx.closePath();
                ctx.fillStyle = accentCol;
                ctx.fill();
            }
        }
    }


    // ---- Numbers — always drawn LAST so nothing covers them ----
    // Numbers are drawn after ticks/highlights to guarantee visibility
    for (let i = 0; i < NUM_RINGS; i++) {
        const rIn  = REF_R_OUT + i * RING_W;
        const rOut = REF_R_OUT + (i + 1) * RING_W;
        const rMid = (rIn + rOut) / 2;
        const active  = (i === activePlane);
        const aligned = mixMode && ringOffsets[i] === 0;

        // Font: always large enough to read — minimum 11px, scale with ring width
        const fs = Math.max(11, Math.floor(RING_W / 1.9));
        const fw = active ? 900 : 800;
        ctx.font = `${fw} ${fs}px Nunito, Segoe UI, system-ui, sans-serif`;
        ctx.textAlign    = 'center';
        ctx.textBaseline = 'middle';

        for (let j = 0; j < SLOTS; j++) {
            const value     = (i + 1) * (j + 1);
            const atPointer = (j === ringOffsets[i]);
            const angleDeg  = 270 + (j - ringOffsets[i]) * SLOT_DEG;
            const angleRad  = angleDeg * Math.PI / 180;
            const x = CX + rMid * Math.cos(angleRad);
            const y = CY + rMid * Math.sin(angleRad);

            ctx.save();
            ctx.translate(x, y);

            // Always draw a white pill/circle behind the number first
            // so ticks, highlights, and ring fills never obscure the text
            const label    = String(value);
            const metrics  = ctx.measureText(label);
            const padX     = 3.5;
            const padY     = 2.5;
            const pillW    = metrics.width + padX * 2;
            const pillH    = fs + padY * 2;
            const pillR    = pillH / 2;

            ctx.beginPath();
            ctx.roundRect(-pillW / 2, -pillH / 2, pillW, pillH, pillR);
            if (atPointer && active) {
                ctx.fillStyle = (aligned && mixMode) ? '#2eaa86' : '#f4a571';
            } else if (atPointer) {
                ctx.fillStyle = (aligned && mixMode) ? '#52c4a0' : '#ffe0c8';
            } else {
                ctx.fillStyle = 'rgba(255,255,255,0.92)';
            }
            ctx.fill();

            // Number text — always dark enough to read against its pill
            if (atPointer && active) {
                ctx.fillStyle = 'white';
            } else if (atPointer) {
                ctx.fillStyle = (aligned && mixMode) ? 'white' : '#b85000';
            } else {
                ctx.fillStyle = active ? RING_ACCENTS[i] : '#4a5568';
            }

            ctx.fillText(label, 0, 0);
            ctx.restore();
        }
    }

    // ---- Fixed reference ring (position labels 1–10) ----
    ctx.beginPath();
    ctx.arc(CX, CY, REF_R_OUT, 0, Math.PI * 2);
    ctx.arc(CX, CY, HUB_R,    0, Math.PI * 2, true);
    ctx.fillStyle = '#ede8ff';
    ctx.fill();
    ctx.beginPath();
    ctx.arc(CX, CY, REF_R_OUT, 0, Math.PI * 2);
    ctx.strokeStyle = '#b4a8e0';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    // Reference ring has no labels — arrow outside ring 10 marks the reference point

    // ---- Hub — "0" at centre ----
    const g = ctx.createRadialGradient(CX, CY - 5, 2, CX, CY, HUB_R);
    g.addColorStop(0, '#9d8fe0');
    g.addColorStop(1, '#5a4fb5');
    ctx.beginPath();
    ctx.arc(CX, CY, HUB_R, 0, Math.PI * 2);
    ctx.fillStyle = g;
    ctx.fill();
    ctx.strokeStyle = '#3d2fa0';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    ctx.font = 'bold 17px Segoe UI, system-ui';
    ctx.textAlign    = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillStyle    = 'white';
    ctx.fillText('0', CX, CY);

    // ---- Small arrow just outside ring 10 at 12 o'clock ----
    // Sits snugly between the outer edge of ring 10 and the background disc
    const arrowTip  = CY - GEAR_OUTER_R - 1;   // tip touching outer edge of ring 10
    const arrowH    = 12;                        // total arrow height
    const arrowW    = 9;                         // half-width of arrowhead

    ctx.beginPath();
    ctx.moveTo(CX,           arrowTip);           // tip (pointing down into ring 10)
    ctx.lineTo(CX - arrowW,  arrowTip - arrowH);  // left wing
    ctx.lineTo(CX + arrowW,  arrowTip - arrowH);  // right wing
    ctx.closePath();
    ctx.fillStyle   = '#f4a571';
    ctx.fill();
    ctx.strokeStyle = '#d4824a';
    ctx.lineWidth   = 1.5;
    ctx.stroke();
}

function lightenHex(hex, amt) {
    const n = parseInt(hex.replace('#',''), 16);
    const r = Math.min(255, Math.max(0, (n >> 16) + amt));
    const gg = Math.min(255, Math.max(0, ((n >> 8) & 0xff) + amt));
    const b = Math.min(255, Math.max(0, (n & 0xff) + amt));
    return '#' + ((r << 16) | (gg << 8) | b).toString(16).padStart(6,'0');
}

/* ================================================================
   CANVAS CLICK — select ring by clicking on it
================================================================ */
canvas.addEventListener('click', function(e) {
    const rect   = canvas.getBoundingClientRect();
    const sx = CS / rect.width, sy = CS / rect.height;
    const mx = (e.clientX - rect.left) * sx;
    const my = (e.clientY - rect.top)  * sy;
    const dist = Math.hypot(mx - CX, my - CY);

    if (dist < HUB_R) {
        NG_Speech.sayInstruction('Zero. The centre of the gear.');
        return;
    }
    for (let i = 0; i < NUM_RINGS; i++) {
        const rIn  = REF_R_OUT + i * RING_W;
        const rOut = REF_R_OUT + (i + 1) * RING_W;
        if (dist >= rIn && dist < rOut) { setActivePlane(i); return; }
    }
});

/* ================================================================
   RING SELECTOR GRID
================================================================ */
function buildRingGrid() {
    const grid = document.getElementById('ringGrid');
    grid.innerHTML = '';
    for (let i = 0; i < NUM_RINGS; i++) {
        const aligned = mixMode && ringOffsets[i] === 0;
        const btn = document.createElement('button');
        btn.className = 'ring-btn'
            + (i === activePlane ? ' active' : '')
            + (aligned ? ' aligned' : '');
        btn.id = 'ring-btn-' + i;
        btn.innerHTML = `<strong>Plane ${i+1}</strong><br><small>${i+1}–${(i+1)*10}</small>`;
        if (aligned) {
            const chk = document.createElement('div');
            chk.className = 'ring-check';
            chk.textContent = '✓';
            btn.appendChild(chk);
        }
        btn.onclick = () => setActivePlane(i);
        grid.appendChild(btn);
    }
}

function setActivePlane(i) {
    if (spinInterval) stopSpin();
    activePlane = i;
    exploredPlanes.add(i);
    buildRingGrid();
    updateWindowDisplay();
    updateStats();
    drawGear();

    if (mixMode) {
        updateMixHint();
        NG_Speech.sayInstruction(`Plane ${i+1}. Number at pointer: ${getPointerNumber(i)}.`);
    } else {
        NG_Speech.sayInstruction(`Plane ${i+1}. The ${i+1} times table. Number at pointer: ${getPointerNumber(i)}.`);
    }
}

/* ================================================================
   ROTATE
================================================================ */
function rotateCW() {
    ringOffsets[activePlane] = (ringOffsets[activePlane] + 1) % SLOTS;
    afterRotate();
}

function rotateCCW() {
    ringOffsets[activePlane] = (ringOffsets[activePlane] + SLOTS - 1) % SLOTS;
    afterRotate();
}

function afterRotate() {
    totalRotations++;
    updateWindowDisplay();
    updateStats();
    buildRingGrid();
    drawGear();
    NG_Speech.sayNumber(getPointerNumber(activePlane));
    if (mixMode) checkMixProgress();
}

function resetActiveRing() {
    if (spinInterval) stopSpin();
    ringOffsets[activePlane] = 0;
    buildRingGrid();
    updateWindowDisplay();
    drawGear();
    if (mixMode) checkMixProgress();
    showToast('Plane ' + (activePlane + 1) + ' reset ↺', '');
}

function resetAllRings() {
    if (spinInterval) stopSpin();
    exitMixMode();
    ringOffsets.fill(0);
    activePlane = 0;
    buildRingGrid();
    updateWindowDisplay();
    updateStats();
    drawGear();
    showToast('All planes reset to reference ↺', '');
    NG_Speech.sayInstruction('All planes reset. The reference numbers are lined up.');
}

function getPointerNumber(i) {
    return (i + 1) * (ringOffsets[i] + 1);
}

/* ================================================================
   AUTO-SPIN
================================================================ */
function toggleSpin() {
    if (spinInterval) stopSpin(); else startSpin();
}
function startSpin() {
    document.getElementById('spinBtn').textContent = '⏸ Stop';
    document.getElementById('spinBtn').classList.add('spinning');
    spinInterval = setInterval(rotateCW, 650);
}
function stopSpin() {
    clearInterval(spinInterval);
    spinInterval = null;
    document.getElementById('spinBtn').textContent = '▶ Auto-Spin';
    document.getElementById('spinBtn').classList.remove('spinning');
}

/* ================================================================
   MIX CHALLENGE
================================================================ */
function toggleMix() {
    if (mixMode) resetAllRings(); else startMix();
}

function startMix() {
    if (spinInterval) stopSpin();
    mixMode = true;
    document.getElementById('mixBtn').textContent  = '✕ Exit Challenge';
    document.getElementById('mixBtn').classList.add('active');
    document.getElementById('mixCard').classList.add('show');

    for (let i = 0; i < NUM_RINGS; i++) {
        let off;
        do { off = Math.floor(Math.random() * SLOTS); } while (off === 0);
        ringOffsets[i] = off;
    }

    activePlane = 0;
    buildRingGrid();
    updateWindowDisplay();
    updateStats();
    drawGear();
    checkMixProgress();
    showToast('Rings mixed! 🎲 Line them all up!', '');
    NG_Speech.sayInstruction('Mix challenge! Rotate each plane until its number lines up with the reference arrow.');
}

function exitMixMode() {
    mixMode = false;
    document.getElementById('mixBtn').textContent = '🎲 Mix Challenge';
    document.getElementById('mixBtn').classList.remove('active');
    document.getElementById('mixCard').classList.remove('show');
}

function checkMixProgress() {
    const aligned = ringOffsets.filter(o => o === 0).length;
    document.getElementById('mixProgLabel').textContent = `${aligned} / 10 planes aligned`;
    document.getElementById('mixProgFill').style.width  = (aligned * 10) + '%';
    updateMixHint();
    if (aligned === NUM_RINGS) onMixComplete();
}

function updateMixHint() {
    const hintEl  = document.getElementById('mixHint');
    const offset  = ringOffsets[activePlane];
    if (offset === 0) {
        hintEl.className = 'mix-hint aligned';
        hintEl.textContent = `✓ Plane ${activePlane + 1} is aligned! Select another plane.`;
    } else {
        hintEl.className = 'mix-hint';
        const cwSteps  = SLOTS - offset;
        const ccwSteps = offset;
        if (cwSteps <= ccwSteps) {
            hintEl.textContent = `Plane ${activePlane + 1}: rotate ↻ clockwise ${cwSteps} step${cwSteps > 1 ? 's' : ''}.`;
        } else {
            hintEl.textContent = `Plane ${activePlane + 1}: rotate ↺ anti-clockwise ${ccwSteps} step${ccwSteps > 1 ? 's' : ''}.`;
        }
    }
}

function onMixComplete() {
    if (spinInterval) stopSpin();
    exitMixMode();
    buildRingGrid();
    drawGear();
    showToast('🎉 All rings aligned! Well done!', 'success');
    NG_Speech.sayInstruction('Very good! All rings are lined up. You solved the mix challenge!');
    NG_Storage.setLvl3Score(Math.min(100, NG_Storage.getLvl3Score() + 20));
}

/* ================================================================
   WINDOW DISPLAY
================================================================ */
function updateWindowDisplay() {
    const num   = getPointerNumber(activePlane);
    const step  = ringOffsets[activePlane] + 1;
    const table = Array.from({length: SLOTS}, (_, k) => (activePlane + 1) * (k + 1)).join(', ');

    const el = document.getElementById('windowNum');
    el.style.animation = 'none'; void el.offsetWidth; el.style.animation = '';
    el.textContent = num;

    document.getElementById('windowDetail').textContent =
        `Plane ${activePlane + 1}  ·  step ${step}  =  ${num}`;
    document.getElementById('windowTable').textContent =
        `Table: ${table}`;
}

/* ================================================================
   STATS
================================================================ */
function updateStats() {
    document.getElementById('exploredCount').textContent = exploredPlanes.size;
    document.getElementById('rotCount').textContent      = totalRotations;
    NG_Storage.setLvl3Score(Math.min(100, exploredPlanes.size * 10));
}

/* ================================================================
   HEAR
================================================================ */
function hearCurrent() {
    NG_Speech.sayNumber(getPointerNumber(activePlane));
}

/* ================================================================
   TOAST
================================================================ */
let _toastTimer = null;
function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'feedback-toast show' + (type ? ' ' + type : '');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => t.classList.remove('show'), 2500);
}

/* ================================================================
   CALC MODE — EXAMPLES
================================================================ */

// All examples: { ring (0-based), start, op, steps, desc }
const EXAMPLES = [
    { ring:2, start:15, op:'add', steps:9,  desc:'Find 15 on Plane 3, then count 9 steps — move clockwise.' },
    { ring:2, start:24, op:'sub', steps:6,  desc:'Find 24 on Plane 3, then count 6 steps — move anticlockwise.' },
    { ring:1, start:8,  op:'add', steps:6,  desc:'Find 8 on Plane 2, then count 6 steps — move clockwise.' },
    { ring:1, start:14, op:'sub', steps:4,  desc:'Find 14 on Plane 2, then count 4 steps — move anticlockwise.' },
    { ring:4, start:25, op:'add', steps:10, desc:'Find 25 on Plane 5, then count 10 steps — move clockwise.' },
    { ring:4, start:40, op:'sub', steps:15, desc:'Find 40 on Plane 5, then count 15 steps — move anticlockwise.' },
    { ring:3, start:16, op:'add', steps:8,  desc:'Find 16 on Plane 4, then count 8 steps — move clockwise.' },
    { ring:3, start:32, op:'sub', steps:12, desc:'Find 32 on Plane 4, then count 12 steps — move anticlockwise.' },
    { ring:5, start:18, op:'add', steps:12, desc:'Find 18 on Plane 6, then count 12 steps — move clockwise.' },
    { ring:6, start:21, op:'add', steps:14, desc:'Find 21 on Plane 7, then count 14 steps — move clockwise.' },
    { ring:7, start:32, op:'add', steps:16, desc:'Find 32 on Plane 8, then count 16 steps — move clockwise.' },
    { ring:8, start:27, op:'add', steps:18, desc:'Find 27 on Plane 9, then count 18 steps — move clockwise.' },
];

let currentExampleIdx = 0;

function loadExample(idx) {
    const ex   = EXAMPLES[idx];
    const sym  = ex.op === 'add' ? '+' : '−';
    const ans  = ex.op === 'add' ? ex.start + ex.steps : ex.start - ex.steps;
    const mult = ex.ring + 1;

    document.getElementById('exBadge').textContent   = `Example ${idx + 1} of ${EXAMPLES.length}`;
    document.getElementById('exSum').textContent      = `${ex.start} ${sym} ${ex.steps}`;
    document.getElementById('exRingHint').textContent = `Use Plane ${mult}  (counts ${mult}, ${mult*2}, ${mult*3}…${mult*10})`;
    document.getElementById('exDesc').textContent     = ex.desc;

    // Reset result
    document.getElementById('calcResult').classList.remove('show');
    calcHighlight = null;

    // Switch to the right ring
    calcRing    = ex.ring;
    activePlane = ex.ring;
    ringOffsets[calcRing] = 0;
    buildCalcRingGrid && buildCalcRingGrid();
    drawGear();

    document.getElementById('exGoBtn').disabled = false;
}

function nextExample() {
    currentExampleIdx = (currentExampleIdx + 1) % EXAMPLES.length;
    clearInterval(calcAnimTimer);
    loadExample(currentExampleIdx);
}

function runExample() {
    const ex      = EXAMPLES[currentExampleIdx];
    const mult    = ex.ring + 1;
    const answer  = ex.op === 'add' ? ex.start + ex.steps : ex.start - ex.steps;

    document.getElementById('exGoBtn').disabled = true;
    document.getElementById('calcResult').classList.remove('show');

    // Snap ring so start value is at the pointer (top / reference arrow)
    const startSlotIdx = Math.max(0, Math.min(SLOTS - 1, Math.round((ex.start / mult) - 1)));
    ringOffsets[calcRing] = startSlotIdx;

    // Ring stays completely still — only ticks and needles animate
    calcHighlight = { startSlot: startSlotIdx, steps: ex.steps, op: ex.op, stepsDone: 0 };
    drawGear();

    clearInterval(calcAnimTimer);
    let done = 0;
    const interval = Math.max(60, Math.min(280, 900 / ex.steps));
    calcAnimTimer = setInterval(() => {
        done++;
        calcHighlight.stepsDone = done;
        drawGear();
        if (done >= ex.steps) {
            clearInterval(calcAnimTimer);
            showExampleResult(ex, answer, mult);
        }
    }, interval);
}

function showExampleResult(ex, answer, mult) {
    const sym    = ex.op === 'add' ? '+' : '−';
    const opWord = ex.op === 'add' ? 'moving clockwise' : 'moving anticlockwise';

    document.getElementById('crEquation').textContent = `${ex.start} ${sym} ${ex.steps} = ${answer}`;
    document.getElementById('crSteps').textContent =
        `Started at ${ex.start} on Plane ${mult}, ${opWord} ${ex.steps} step${ex.steps > 1 ? 's' : ''}.`;

    const nearestMultiple = Math.round(answer / mult) * mult;
    const hint = nearestMultiple === answer
        ? `${answer} = ${answer/mult} × ${mult} — lands right on a ring number! ✓`
        : `${answer} sits between ${Math.floor(answer/mult)*mult} and ${Math.ceil(answer/mult)*mult} on Plane ${mult}.`;
    document.getElementById('crHint').textContent = hint;

    document.getElementById('calcResult').classList.add('show');
    NG_Speech.sayInstruction(`${ex.start} ${ex.op === 'add' ? 'plus' : 'minus'} ${ex.steps} equals ${answer}.`);
}

function toggleCalcMode() {
    calcMode = !calcMode;
    const toggle = document.getElementById('modeToggle');
    toggle.classList.toggle('on', calcMode);
    document.getElementById('normalLabel').classList.toggle('active', !calcMode);
    document.getElementById('calcLabel').classList.toggle('active', calcMode);
    document.getElementById('calcPanel').classList.toggle('show', calcMode);
    document.getElementById('normalPanel').style.display = calcMode ? 'none' : '';

    if (calcMode) {
        if (spinInterval) stopSpin();
        exitMixMode();
        currentExampleIdx = 0;
        calcHighlight = null;
        loadExample(0);
    } else {
        calcHighlight = null;
        clearInterval(calcAnimTimer);
        drawGear();
    }
}

function buildCalcRingGrid() {
    // no-op in example mode — ring is set automatically by example
}


/* ================================================================
   INIT
================================================================ */
document.addEventListener('DOMContentLoaded', function () {
    buildRingGrid();
    updateWindowDisplay();
    updateStats();
    drawGear();
    setTimeout(() => {
        NG_Speech.sayInstruction(
            'Welcome to Number Gear! Each plane shows a times table. ' +
            'Click a ring to select it, then use the clockwise and anti-clockwise buttons to rotate it!'
        );
    }, 600);
});
</script>
</body>
</html>
