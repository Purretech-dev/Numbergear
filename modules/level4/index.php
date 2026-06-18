<?php
// Number Gear — Level 4: Multiply & Divide
session_start();
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
    <style>

        /* ===== LAYOUT ===== */
        .l4-wrap {
            max-width: 860px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* ===== STORY CARD ===== */
        .story-card {
            background: linear-gradient(135deg, var(--purple-light) 0%, var(--peach-light) 100%);
            border: 2px solid var(--purple);
            border-radius: 20px;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .story-emoji { font-size: 48px; flex-shrink: 0; }
        .story-text  { flex: 1; }
        .story-text h3 {
            font-size: 18px; font-weight: 900;
            color: var(--purple-dark); margin-bottom: 4px;
        }
        .story-text p  { font-size: 14px; font-weight: 600; color: var(--text); line-height: 1.6; }

        /* ===== HOW TO PLAY CARDS ===== */
        .how-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .how-card {
            border-radius: 18px;
            padding: 16px 18px;
            border: 2.5px solid;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .how-card.mult {
            background: var(--mint-light);
            border-color: var(--mint);
        }
        .how-card.div {
            background: var(--purple-light);
            border-color: var(--purple);
        }
        .how-icon  { font-size: 32px; }
        .how-title { font-size: 15px; font-weight: 900; }
        .how-card.mult .how-title { color: var(--mint-dark); }
        .how-card.div  .how-title { color: var(--purple-dark); }
        .how-steps { list-style: none; display: flex; flex-direction: column; gap: 4px; }
        .how-steps li {
            font-size: 13px; font-weight: 700;
            color: var(--text); display: flex; align-items: flex-start; gap: 6px;
        }
        .how-steps li .step-num {
            width: 20px; height: 20px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 900; flex-shrink: 0; margin-top: 1px;
        }
        .how-card.mult .step-num { background: var(--mint); color: white; }
        .how-card.div  .step-num { background: var(--purple); color: white; }

        /* ===== OPERATION SWITCHER ===== */
        .op-switcher {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .op-btn {
            padding: 16px 12px;
            border: 3px solid var(--border);
            border-radius: 18px;
            background: var(--surface);
            font-size: 16px; font-weight: 900;
            cursor: pointer; font-family: inherit;
            display: flex; flex-direction: column;
            align-items: center; gap: 6px;
            transition: 0.2s ease;
            color: var(--text-soft);
        }
        .op-btn .op-icon { font-size: 34px; }
        .op-btn.active.mult {
            border-color: var(--mint);
            background: var(--mint-light);
            color: var(--mint-dark);
        }
        .op-btn.active.div {
            border-color: var(--purple);
            background: var(--purple-light);
            color: var(--purple-dark);
        }
        .op-btn:not(.active):hover { border-color: var(--peach); }

        /* ===== TABLE PICKER ===== */
        .table-label {
            font-size: 14px; font-weight: 800;
            color: var(--text); margin-bottom: 10px;
        }
        .table-picker {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 4px;
        }
        .tbl-btn {
            padding: 10px 4px;
            border: 2.5px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            font-size: 14px; font-weight: 900;
            cursor: pointer; font-family: inherit;
            color: var(--text-soft);
            transition: 0.18s ease;
            display: flex; flex-direction: column;
            align-items: center; gap: 2px;
            line-height: 1.2;
        }
        .tbl-btn .tbl-num  { font-size: 18px; font-weight: 900; }
        .tbl-btn .tbl-hint { font-size: 9px; font-weight: 700; opacity: 0.7; }
        .tbl-btn:hover           { border-color: var(--peach); color: var(--peach-dark); }
        .tbl-btn.active.mult-tbl { background: var(--mint); border-color: var(--mint); color: white; }
        .tbl-btn.active.div-tbl  { background: var(--purple); border-color: var(--purple); color: white; }

        /* ===== GEAR AREA ===== */
        .gear-area {
            display: flex;
            flex-direction: row;
            gap: 20px;
            align-items: flex-start;
        }
        .gear-canvas-col {
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .gear-controls-col {
            flex: 0 0 300px;
            width: 300px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        @media (max-width: 860px) {
            .gear-area { flex-direction: column; align-items: center; }
            .gear-controls-col { width: 100%; max-width: 560px; flex: none; }
        }

        #gearCanvas4 {
            display: block;
            width: 100%; max-width: 520px; height: auto;
            cursor: pointer; touch-action: manipulation;
            border-radius: 50%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.16);
        }

        /* ===== ANSWER DISPLAY ===== */
        .answer-display {
            border-radius: 18px;
            padding: 16px 18px;
            display: flex; align-items: center; gap: 14px;
            transition: background 0.3s, border-color 0.3s;
        }
        .answer-display.mult-mode {
            background: var(--mint-light);
            border: 2.5px solid var(--mint);
        }
        .answer-display.div-mode {
            background: var(--purple-light);
            border: 2.5px solid var(--purple);
        }
        .answer-big {
            font-size: 38px; font-weight: 900; line-height: 1;
            min-width: 60px; text-align: center;
            animation: popIn 0.3s ease;
        }
        .answer-display.mult-mode .answer-big { color: var(--mint-dark); }
        .answer-display.div-mode  .answer-big { color: var(--purple-dark); }
        .answer-meta { flex: 1; }
        .answer-eq   { font-size: 20px; font-weight: 900; color: var(--text); }
        .answer-sub  { font-size: 13px; font-weight: 700; color: var(--text-soft); margin-top: 3px; }
        @keyframes popIn { 0%{transform:scale(0.7);opacity:0} 60%{transform:scale(1.15)} 100%{transform:scale(1);opacity:1} }

        /* ===== SPIN CONTROLS ===== */
        .spin-card {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 18px;
            padding: 14px 16px;
        }
        .spin-card-title {
            font-size: 12px; font-weight: 800;
            color: var(--text-soft); text-transform: uppercase;
            letter-spacing: 0.6px; margin-bottom: 12px;
        }
        .big-spin-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        .big-spin-btn {
            padding: 18px 8px;
            border: 3px solid;
            border-radius: 18px;
            font-size: 13px; font-weight: 900;
            cursor: pointer; font-family: inherit;
            display: flex; flex-direction: column;
            align-items: center; gap: 6px;
            transition: 0.2s ease;
            background: var(--surface);
        }
        .big-spin-btn .spin-icon { font-size: 30px; }
        .big-spin-btn.ccw {
            border-color: var(--purple);
            color: var(--purple-dark);
            background: var(--purple-light);
        }
        .big-spin-btn.cw  {
            border-color: var(--mint);
            color: var(--mint-dark);
            background: var(--mint-light);
        }
        .big-spin-btn.ccw:hover { background: var(--purple); color: white; }
        .big-spin-btn.cw:hover  { background: var(--mint);   color: white; }
        .big-spin-btn:active { transform: scale(0.95); }
        .big-spin-btn .spin-label { font-size: 10px; font-weight: 700; opacity: 0.8; }

        .auto-spin-btn {
            width: 100%; padding: 11px;
            border: 2px solid var(--peach);
            border-radius: 13px;
            background: var(--peach-light); color: var(--peach-dark);
            font-size: 14px; font-weight: 800;
            cursor: pointer; font-family: inherit;
            transition: 0.18s ease;
        }
        .auto-spin-btn.spinning { background: var(--peach); color: white; }
        .auto-spin-btn:hover:not(.spinning) { background: var(--peach); color: white; }

        /* ===== WORKED EXAMPLE ===== */
        .worked-example {
            background: var(--surface);
            border: 2.5px solid var(--peach);
            border-radius: 20px;
            padding: 18px 20px;
            display: flex; flex-direction: column; gap: 12px;
        }
        .we-header {
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
        }
        .we-badge {
            font-size: 11px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.6px; color: var(--text-soft);
            background: var(--bg); border-radius: 20px; padding: 4px 12px;
        }
        .we-op {
            font-size: 13px; font-weight: 900; color: var(--peach-dark);
        }
        .we-equation {
            font-size: 36px; font-weight: 900; color: var(--text);
            text-align: center; padding: 8px 0;
        }
        .we-steps {
            display: flex; flex-direction: column; gap: 8px;
        }
        .we-step {
            display: flex; align-items: flex-start; gap: 10px;
            background: var(--bg); border-radius: 12px; padding: 10px 14px;
            font-size: 14px; font-weight: 700; color: var(--text); line-height: 1.5;
        }
        .we-step-num {
            width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 900; color: white; margin-top: 1px;
        }
        .we-step-num.mult-n { background: var(--mint); }
        .we-step-num.div-n  { background: var(--purple); }
        .we-answer {
            background: linear-gradient(135deg, var(--mint-light), var(--peach-light));
            border: 2px solid var(--mint); border-radius: 14px;
            padding: 14px 18px; text-align: center;
            animation: popIn 0.4s ease;
        }
        .we-answer-big {
            font-size: 42px; font-weight: 900; color: var(--mint-dark); line-height: 1;
        }
        .we-answer-eq {
            font-size: 16px; font-weight: 800; color: var(--text); margin-top: 4px;
        }
        .we-btn-row {
            display: flex; gap: 10px;
        }
        .we-show-btn {
            flex: 1; padding: 13px;
            background: var(--peach-light); border: 2px solid var(--peach);
            border-radius: 14px; font-size: 14px; font-weight: 800;
            cursor: pointer; font-family: inherit; color: var(--peach-dark);
            transition: 0.18s ease;
        }
        .we-show-btn:hover { background: var(--peach); color: white; }
        .we-show-btn:disabled { opacity: 0.4; cursor: default; }
        .we-next-btn {
            flex: 1; padding: 13px;
            background: var(--mint); border: none;
            border-radius: 14px; font-size: 14px; font-weight: 800;
            cursor: pointer; font-family: inherit; color: white;
            transition: 0.18s ease;
        }
        .we-next-btn:hover { background: var(--mint-dark); }

        @media (max-width: 480px) {
            .we-equation { font-size: 28px; }
            .we-btn-row  { flex-direction: column; }
        }

        /* ===== SINGLE EXPLAINER CARD ===== */
        .explainer-card {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 18px;
            padding: 14px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .ex-left { font-size: 32px; flex-shrink: 0; margin-top: 2px; }
        .ex-body  { flex: 1; display: flex; flex-direction: column; gap: 8px; }
        .ex-row   { font-size: 13px; font-weight: 600; color: var(--text); line-height: 1.6; display: flex; align-items: flex-start; gap: 8px; flex-wrap: wrap; }
        .ex-tag   { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 900; white-space: nowrap; flex-shrink: 0; }
        .mult-tag { background: var(--mint-light); color: var(--mint-dark); border: 1.5px solid var(--mint); }
        .div-tag  { background: var(--purple-light); color: var(--purple-dark); border: 1.5px solid var(--purple); }
        .ex-divider { height: 1px; background: var(--border); }

        /* ===== QUIZ OPEN BUTTON ===== */
        .quiz-open-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--peach) 0%, var(--peach-dark) 100%);
            border: none;
            border-radius: 18px;
            font-size: 20px;
            font-weight: 900;
            color: white;
            cursor: pointer;
            font-family: inherit;
            transition: 0.2s ease;
            box-shadow: 0 4px 14px rgba(244,165,113,0.35);
            letter-spacing: 0.3px;
        }
        .quiz-open-btn:hover  { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(244,165,113,0.45); }
        .quiz-open-btn:active { transform: translateY(0); }

        /* ===== QUIZ MODAL ===== */
        .quiz-modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9000;
            padding: 20px;
        }
        .quiz-modal-overlay.open { display: flex; animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from{opacity:0} to{opacity:1} }

        .quiz-modal {
            background: var(--surface);
            border-radius: 24px;
            padding: 24px;
            width: 100%;
            max-width: 480px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.25s ease;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        @keyframes slideUp { from{transform:translateY(30px);opacity:0} to{transform:translateY(0);opacity:1} }

        .qm-header {
            display: flex; align-items: center; justify-content: space-between;
        }
        .qm-title {
            font-size: 20px; font-weight: 900; color: var(--text);
        }
        .qm-close {
            width: 36px; height: 36px; border-radius: 50%;
            border: 2px solid var(--border); background: var(--bg);
            font-size: 16px; font-weight: 900; cursor: pointer;
            font-family: inherit; color: var(--text-soft);
            display: flex; align-items: center; justify-content: center;
            transition: 0.18s ease;
        }
        .qm-close:hover { background: var(--error-bg); border-color: var(--error); color: var(--error); }

        .qm-op-row {
            display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
        }
        .qm-op-btn {
            padding: 10px;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            font-size: 14px; font-weight: 800;
            cursor: pointer; font-family: inherit;
            color: var(--text-soft); transition: 0.18s ease;
        }
        .qm-op-btn.active {
            background: var(--peach); border-color: var(--peach); color: white;
        }
        .qm-op-btn:not(.active):hover { border-color: var(--peach); color: var(--peach-dark); }

        /* ===== QUIZ SECTION ===== */
        .quiz-card-l4 {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 18px;
            padding: 18px;
        }
        .quiz-title-l4 {
            font-size: 12px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.6px;
            color: var(--text-soft); margin-bottom: 12px;
        }
        .quiz-question-l4 {
            font-size: 32px; font-weight: 900;
            color: var(--text); text-align: center;
            margin-bottom: 8px;
        }
        .quiz-hint-l4 {
            font-size: 13px; font-weight: 700;
            color: var(--text-soft); text-align: center;
            margin-bottom: 14px; line-height: 1.5;
        }
        .quiz-hint-l4 strong { color: var(--peach-dark); }
        .quiz-choices-l4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }
        .quiz-choice-l4 {
            padding: 14px 6px;
            border: 2.5px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            font-size: 20px; font-weight: 900;
            cursor: pointer; font-family: inherit;
            color: var(--text); transition: 0.18s ease;
        }
        .quiz-choice-l4:hover:not(:disabled) {
            border-color: var(--peach);
            background: var(--peach-light);
            color: var(--peach-dark);
            transform: translateY(-2px);
        }
        .quiz-choice-l4.correct { background: var(--success-bg); border-color: var(--success); color: #276749; }
        .quiz-choice-l4.wrong   { background: var(--error-bg);   border-color: var(--error);   animation: shake 0.4s ease; }
        .quiz-choice-l4:disabled { cursor: default; }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)} }

        .quiz-score-row {
            display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;
        }
        .score-chip-l4 {
            background: var(--bg); border: 2px solid var(--border);
            border-radius: 12px; padding: 7px 14px;
            font-size: 13px; font-weight: 800; color: var(--text-soft);
        }
        .score-chip-l4 span { color: var(--peach-dark); font-size: 15px; font-weight: 900; }

        /* ===== CONGRATS BANNER ===== */
        .congrats-banner {
            background: linear-gradient(135deg, var(--mint-light), var(--peach-light));
            border: 2.5px solid var(--mint);
            border-radius: 18px; padding: 20px;
            text-align: center; display: none;
        }
        .congrats-banner.show { display: block; animation: popIn 0.4s ease; }
        .congrats-emoji { font-size: 48px; margin-bottom: 8px; }
        .congrats-title { font-size: 22px; font-weight: 900; color: var(--mint-dark); margin-bottom: 6px; }
        .congrats-sub   { font-size: 14px; font-weight: 700; color: var(--text-soft); margin-bottom: 14px; }

        @media (max-width: 560px) {
            .how-grid         { grid-template-columns: 1fr; }
            .op-switcher      { grid-template-columns: 1fr 1fr; }
            .table-picker     { grid-template-columns: repeat(5,1fr); gap: 5px; }
            .tbl-btn          { padding: 8px 2px; }
            .tbl-btn .tbl-num { font-size: 15px; }
            .big-spin-row     { grid-template-columns: 1fr 1fr; }
            .quiz-choices-l4  { grid-template-columns: repeat(4,1fr); }
        }
    </style>
</head>
<body>
<div class="app-shell">

    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">⚙️</div>
            <div>
                <h1>Level 4</h1>
                <p>Multiply &amp; Divide</p>
            </div>
        </div>
        <a href="../../index.php" class="back-btn">← Home</a>
    </header>

    <main class="level-page">
    <div class="l4-wrap">

        <!-- ===== SINGLE EXPLAINER CARD ===== -->
        <div class="explainer-card">
            <div class="ex-left">⚙️</div>
            <div class="ex-body">
                <div class="ex-row">
                    <span class="ex-tag mult-tag">✖️ Multiply</span>
                    Pick a <strong>plane</strong> (e.g. 3s) → Spin <strong>↻ forward</strong> → each step lands on the next multiple → the step count is your answer!
                </div>
                <div class="ex-divider"></div>
                <div class="ex-row">
                    <span class="ex-tag div-tag">➗ Divide</span>
                    Pick the <strong>plane</strong> matching your divisor → Spin <strong>↻ forward</strong> until your big number appears at the arrow ▼ → the step count is your answer!
                </div>
            </div>
        </div>

        <!-- ===== OPERATION SWITCHER ===== -->
        <div class="op-switcher">
            <button class="op-btn mult active" id="opBtnMult" onclick="setOp('mult')">
                <span class="op-icon">✖️</span>
                Multiply
                <small style="font-size:12px;font-weight:700;opacity:0.75;">Spin ↻ clockwise</small>
            </button>
            <button class="op-btn div" id="opBtnDiv" onclick="setOp('div')">
                <span class="op-icon">➗</span>
                Divide
                <small style="font-size:12px;font-weight:700;opacity:0.75;">Spin ↺ back</small>
            </button>
        </div>

        <!-- ===== TABLE PICKER ===== -->
        <div>
            <div class="table-label" id="tablePickerLabel">👇 Pick a <strong>plane</strong> — each plane counts in that number's steps:</div>
            <div class="table-picker" id="tablePicker"></div>
        </div>

        <!-- ===== GEAR + CONTROLS ===== -->
        <div class="gear-area">

            <!-- Canvas -->
            <div class="gear-canvas-col">
                <canvas id="gearCanvas4" width="520" height="520"></canvas>
            </div>

            <!-- Controls -->
            <div class="gear-controls-col">

                <!-- Answer display -->
                <div class="answer-display mult-mode" id="answerDisplay">
                    <div class="answer-big" id="answerBig">0</div>
                    <div class="answer-meta">
                        <div class="answer-eq"  id="answerEq">– × – = –</div>
                        <div class="answer-sub" id="answerSub">Pick a table and spin!</div>
                    </div>
                    <button style="padding:10px 12px;background:var(--peach);border:none;border-radius:11px;color:white;font-size:18px;cursor:pointer;flex-shrink:0;" onclick="hearCurrent()">🔊</button>
                </div>

                <!-- Spin controls -->
                <div class="spin-card">
                    <div class="spin-card-title" id="spinTitle">Spin to multiply ↻</div>
                    <div class="big-spin-row">
                        <button class="big-spin-btn ccw" onclick="spinStep(-1)" id="spinCCW">
                            <span class="spin-icon">↺</span>
                            <span>Back</span>
                            <span class="spin-label">divide / undo</span>
                        </button>
                        <button class="big-spin-btn cw" onclick="spinStep(+1)" id="spinCW">
                            <span class="spin-icon">↻</span>
                            <span>Forward</span>
                            <span class="spin-label">multiply</span>
                        </button>
                    </div>
                    <button class="auto-spin-btn" id="autoSpinBtn" onclick="toggleAutoSpin()">
                        ▶ Auto-Spin
                    </button>
                </div>

                <!-- Step counter -->
                <div style="background:var(--bg);border-radius:14px;padding:12px 16px;display:flex;align-items:center;gap:12px;">
                    <div style="font-size:36px;font-weight:900;color:var(--peach-dark);min-width:44px;text-align:center;" id="stepCounter">0</div>
                    <div>
                        <div style="font-size:13px;font-weight:800;color:var(--text);">Steps taken</div>
                        <div style="font-size:12px;font-weight:700;color:var(--text-soft);" id="stepHint">Spin ↻ forward to count up!</div>
                    </div>
                    <button onclick="resetGear()" style="margin-left:auto;padding:8px 12px;border:2px solid var(--border);border-radius:10px;background:var(--surface);font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;color:var(--text-soft);">↺ Reset</button>
                </div>

            </div>
        </div>

        <!-- ===== WORKED EXAMPLE ===== -->
        <div class="worked-example" id="workedExample">
            <div class="we-header">
                <div class="we-badge" id="weBadge">Example 1 of 6</div>
                <div class="we-op" id="weOp">✖️ Multiplication</div>
            </div>
            <div class="we-equation" id="weEq">3 × 4 = ?</div>
            <div class="we-steps" id="weSteps"></div>
            <div class="we-answer" id="weAnswer" style="display:none;"></div>
            <div class="we-btn-row">
                <button class="we-show-btn" id="weShowBtn" onclick="showWorkedAnswer()">👀 Show me the answer!</button>
                <button class="we-next-btn" onclick="nextExample()">Next Example →</button>
            </div>
        </div>

        <!-- ===== QUIZ BUTTON ===== -->
        <button class="quiz-open-btn" onclick="openQuiz()">
            🎯 Take a Quiz!
        </button>

    </div>
    </main>
</div>

<!-- ===== QUIZ MODAL ===== -->
<div class="quiz-modal-overlay" id="quizOverlay" onclick="closeQuizIfOutside(event)">
    <div class="quiz-modal" id="quizModal">
        <div class="qm-header">
            <div class="qm-title">🎯 Quiz Time!</div>
            <button class="qm-close" onclick="closeQuiz()">✕</button>
        </div>
        <div class="qm-op-row">
            <button class="qm-op-btn active" id="qmMult" onclick="setQuizOp('mult')">✖️ Multiply</button>
            <button class="qm-op-btn"        id="qmDiv"  onclick="setQuizOp('div')">➗ Divide</button>
        </div>
        <div class="quiz-question-l4" id="quizQ">– × – = ?</div>
        <div class="quiz-hint-l4"     id="quizHint">Pick the right plane, then spin to find the answer!</div>
        <div class="quiz-choices-l4"  id="quizChoices"></div>
        <div class="quiz-score-row">
            <div class="score-chip-l4">✅ Correct: <span id="scoreCorrect">0</span></div>
            <div class="score-chip-l4">🔥 Streak: <span id="scoreStreak">0</span></div>
        </div>
        <div class="congrats-banner" id="congratsBanner">
            <div class="congrats-emoji">🌟</div>
            <div class="congrats-title" id="congratsTitle">Brilliant!</div>
            <div class="congrats-sub"   id="congratsSub">You got it right!</div>
            <button class="btn btn-mint" onclick="nextQuestion()" style="margin-top:4px;">Next Question →</button>
        </div>
    </div>
</div>

<div class="feedback-toast" id="toast"></div>

<script src="../../assets/js/speech.js"></script>
<script src="../../assets/js/storage.js"></script>
<script>
/* ================================================================
   CONSTANTS & STATE
================================================================ */
const CS = 520, CX = 260, CY = 260;
const HUB_R = 28, RW = 22;
const SLOTS = 10;

// Colors matching Level 3
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

let currentTable  = 0;   // 0 = ×1
let currentOffset = 0;   // 0–9 (how many steps spun)
let currentOp     = 'mult';
let autoSpinTimer = null;

// Quiz state
let quizQ        = null;
let quizAnswered = false;
let scoreCorrect = 0;
let scoreStreak  = 0;

/* ================================================================
   CANVAS
================================================================ */
const canvas = document.getElementById('gearCanvas4');
const ctx    = canvas.getContext('2d');

function drawGear() {
    ctx.clearRect(0, 0, CS, CS);

    const OUTER = HUB_R + SLOTS * RW;

    // Background disc
    ctx.beginPath();
    ctx.arc(CX, CY, OUTER + 4, 0, Math.PI * 2);
    ctx.fillStyle = '#dde4ee';
    ctx.fill();

    // Draw all rings (outermost first)
    for (let i = SLOTS - 1; i >= 0; i--) {
        const rIn  = HUB_R + i * RW;
        const rOut = HUB_R + (i + 1) * RW;
        const isActive = (i === currentTable);

        // Ring fill
        ctx.beginPath();
        ctx.arc(CX, CY, rOut, 0, Math.PI * 2);
        ctx.arc(CX, CY, rIn,  0, Math.PI * 2, true);
        ctx.fillStyle = isActive
            ? (currentOp === 'mult' ? '#d4f5ec' : '#ede9ff')
            : RING_FILLS[i];
        ctx.fill();

        // Ring border
        ctx.beginPath();
        ctx.arc(CX, CY, rOut, 0, Math.PI * 2);
        ctx.strokeStyle = isActive
            ? (currentOp === 'mult' ? '#52c4a0' : '#7c6fcd')
            : '#c4cedd';
        ctx.lineWidth = isActive ? 2.5 : 0.8;
        ctx.stroke();

        // Top slot highlight (at arrow)
        const half = (Math.PI * 2 / SLOTS) / 2;
        const top  = -Math.PI / 2;
        ctx.beginPath();
        ctx.arc(CX, CY, rOut - 0.5, top - half, top + half);
        ctx.arc(CX, CY, rIn  + 0.5, top + half, top - half, true);
        ctx.closePath();
        ctx.fillStyle = isActive
            ? (currentOp === 'mult' ? 'rgba(82,196,160,0.35)' : 'rgba(124,111,205,0.35)')
            : 'rgba(0,0,0,0.03)';
        ctx.fill();
    }

    // Numbers on each ring
    for (let i = 0; i < SLOTS; i++) {
        const rIn  = HUB_R + i * RW;
        const rOut = HUB_R + (i + 1) * RW;
        const rMid = (rIn + rOut) / 2;
        const table     = i + 1;
        const isActive  = (i === currentTable);
        const offset    = (i === currentTable) ? currentOffset : 0;
        const arcPerSlot = rMid * (2 * Math.PI / SLOTS);
        const fontSize   = Math.min(12, Math.max(7, Math.floor(arcPerSlot / 4)));

        ctx.font = `${isActive ? '900' : '700'} ${fontSize}px Nunito, Segoe UI, sans-serif`;
        ctx.textAlign    = 'center';
        ctx.textBaseline = 'middle';

        for (let j = 0; j < SLOTS; j++) {
            const value    = table * j;
            const atArrow  = (j === offset % SLOTS);
            const angleDeg = 270 + (j - offset) * 36;
            const angleRad = angleDeg * Math.PI / 180;
            const x = CX + rMid * Math.cos(angleRad);
            const y = CY + rMid * Math.sin(angleRad);

            ctx.save();
            ctx.translate(x, y);

            // Highlight at-arrow number on active ring
            if (atArrow && isActive) {
                ctx.beginPath();
                ctx.arc(0, 0, fontSize + 5, 0, Math.PI * 2);
                ctx.fillStyle = currentOp === 'mult' ? '#52c4a0' : '#7c6fcd';
                ctx.fill();
                ctx.fillStyle = 'white';
            } else {
                ctx.globalAlpha = isActive ? 1 : 0.5;
                ctx.fillStyle   = isActive ? RING_ACCENTS[i] : '#718096';
            }

            ctx.fillText(String(value), 0, 0);
            ctx.globalAlpha = 1;
            ctx.restore();
        }
    }

    // Hub — shows × or ÷
    ctx.beginPath();
    ctx.arc(CX, CY, HUB_R, 0, Math.PI * 2);
    const hubGrad = ctx.createRadialGradient(CX, CY - 4, 2, CX, CY, HUB_R);
    hubGrad.addColorStop(0, currentOp === 'mult' ? '#6ee7c7' : '#a89ce8');
    hubGrad.addColorStop(1, currentOp === 'mult' ? '#52c4a0' : '#7c6fcd');
    ctx.fillStyle = hubGrad;
    ctx.fill();
    ctx.font = 'bold 16px Nunito, sans-serif';
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.fillStyle = 'white';
    ctx.fillText(currentOp === 'mult' ? '×' : '÷', CX, CY);

    // Reference arrow ▼ at top
    const arrowY = CY - (HUB_R + SLOTS * RW);
    ctx.beginPath();
    ctx.moveTo(CX, arrowY);
    ctx.lineTo(CX - 10, arrowY - 14);
    ctx.lineTo(CX + 10, arrowY - 14);
    ctx.closePath();
    ctx.fillStyle = '#f4a571';
    ctx.fill();
    ctx.strokeStyle = '#d4824a';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    // Step counter badge near arrow
    if (currentOffset > 0) {
        const badgeY = arrowY - 28;
        ctx.beginPath();
        ctx.arc(CX, badgeY, 14, 0, Math.PI * 2);
        ctx.fillStyle = currentOp === 'mult' ? '#52c4a0' : '#7c6fcd';
        ctx.fill();
        ctx.font = 'bold 13px Nunito, sans-serif';
        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
        ctx.fillStyle = 'white';
        ctx.fillText(String(currentOffset), CX, badgeY);
    }
}

/* ================================================================
   CANVAS CLICK — tap a ring to select it
================================================================ */
canvas.addEventListener('click', function(e) {
    const rect = canvas.getBoundingClientRect();
    const mx   = (e.clientX - rect.left) * (CS / rect.width);
    const my   = (e.clientY - rect.top)  * (CS / rect.height);
    const dx   = mx - CX, dy = my - CY;
    const dist = Math.sqrt(dx * dx + dy * dy);

    if (dist < HUB_R) return;
    for (let i = 0; i < SLOTS; i++) {
        const rIn  = HUB_R + i * RW;
        const rOut = HUB_R + (i + 1) * RW;
        if (dist >= rIn && dist < rOut) {
            selectTable(i);
            return;
        }
    }
});

/* ================================================================
   TABLE PICKER
================================================================ */
function buildTablePicker() {
    const picker = document.getElementById('tablePicker');
    picker.innerHTML = '';
    for (let i = 0; i < 10; i++) {
        const btn = document.createElement('button');
        const cls = currentOp === 'mult' ? 'mult-tbl' : 'div-tbl';
        btn.className = 'tbl-btn' + (i === currentTable ? ` active ${cls}` : '');
        btn.innerHTML = `<span class="tbl-num">${i + 1}s</span><span class="tbl-hint">${i+1},${(i+1)*2},${(i+1)*3}…</span>`;
        btn.onclick   = () => selectTable(i);
        picker.appendChild(btn);
    }
}

function selectTable(i) {
    currentTable  = i;
    currentOffset = 0;
    buildTablePicker();
    updateDisplay();
    drawGear();
    NG_Speech.sayInstruction(`The ${i + 1} times table. Spin to see the multiples!`);
}

/* ================================================================
   OPERATION SWITCH
================================================================ */
function setOp(op) {
    currentOp     = op;
    currentOffset = 0;

    document.getElementById('opBtnMult').className = 'op-btn mult' + (op === 'mult' ? ' active' : '');
    document.getElementById('opBtnDiv').className  = 'op-btn div'  + (op === 'div'  ? ' active' : '');

    // Update answer display class
    document.getElementById('answerDisplay').className = 'answer-display ' + (op === 'mult' ? 'mult-mode' : 'div-mode');

    // Update spin title
    document.getElementById('spinTitle').textContent = op === 'mult'
        ? 'Each step forward shows the next multiple!'
        : 'Spin forward — count the steps until your number appears at the arrow!';

    // Update table picker label
    document.getElementById('tablePickerLabel').innerHTML = op === 'mult'
        ? '👇 Pick a <strong>plane</strong> (the number you are multiplying by):'
        : '👇 Pick the <strong>plane</strong> that matches your dividing number:';

    weIdx = 0;
    buildTablePicker();
    updateDisplay();
    drawGear();
    generateQuiz();
    loadWorkedExample();
    NG_Speech.sayInstruction(op === 'mult'
        ? 'Multiplication! Pick a plane and spin to multiply!'
        : 'Division! Pick the right plane, spin forward and count the steps!'
    );
}

/* ================================================================
   SPIN
================================================================ */
function spinStep(dir) {
    currentOffset = ((currentOffset + dir) + SLOTS) % SLOTS;
    updateDisplay();
    drawGear();

    const table  = currentTable + 1;
    const result = table * currentOffset;
    if (currentOp === 'mult') {
        if (currentOffset === 0) {
            NG_Speech.sayInstruction(`Back to zero. ${table} times 0 is 0.`);
        } else {
            NG_Speech.sayMultiplication(table, currentOffset, result);
        }
    } else {
        NG_Speech.sayInstruction(`${result} is at the arrow. That is ${currentOffset} steps from zero.`);
    }
}

function toggleAutoSpin() {
    if (autoSpinTimer) {
        clearInterval(autoSpinTimer);
        autoSpinTimer = null;
        const btn = document.getElementById('autoSpinBtn');
        btn.textContent = '▶ Auto-Spin';
        btn.classList.remove('spinning');
    } else {
        const btn = document.getElementById('autoSpinBtn');
        btn.textContent = '⏸ Stop';
        btn.classList.add('spinning');
        autoSpinTimer = setInterval(() => spinStep(+1), 700);
    }
}

function resetGear() {
    if (autoSpinTimer) { clearInterval(autoSpinTimer); autoSpinTimer = null; }
    const btn = document.getElementById('autoSpinBtn');
    btn.textContent = '▶ Auto-Spin';
    btn.classList.remove('spinning');
    currentOffset = 0;
    updateDisplay();
    drawGear();
}

/* ================================================================
   DISPLAY UPDATE
================================================================ */
function updateDisplay() {
    const table  = currentTable + 1;
    const steps  = currentOffset;
    const result = table * steps;

    document.getElementById('stepCounter').textContent = steps;

    if (currentOp === 'mult') {
        document.getElementById('answerBig').textContent = result;
        document.getElementById('answerEq').textContent  = `${table} × ${steps} = ${result}`;
        document.getElementById('answerSub').textContent = steps === 0
            ? `${table} times 0 is always 0`
            : `${table} jumped ${steps} step${steps > 1 ? 's' : ''}`;
        document.getElementById('stepHint').textContent = `Each step adds ${table} more`;
    } else {
        document.getElementById('answerBig').textContent = result;
        document.getElementById('answerEq').textContent  = result > 0
            ? `${result} ÷ ${table} = ${steps}`
            : `– ÷ ${table} = –`;
        document.getElementById('answerSub').textContent = steps === 0
            ? `Spin ↻ forward to find a number to divide`
            : `${result} shared by ${table} = ${steps} each`;
        document.getElementById('stepHint').textContent = steps === 0
            ? `Spin ↻ forward to count up!`
            : `${steps} step${steps > 1 ? 's' : ''} to reach ${result}`;
    }

    updateStats();
}

/* ================================================================
   HEAR
================================================================ */
function hearCurrent() {
    const table  = currentTable + 1;
    const steps  = currentOffset;
    const result = table * steps;
    if (currentOp === 'mult') {
        NG_Speech.sayMultiplication(table, steps, result);
    } else {
        if (result > 0) NG_Speech.sayDivision(result, table, steps);
        else NG_Speech.sayInstruction(`Spin the gear to find a number to divide by ${table}.`);
    }
}

/* ================================================================
   WORKED EXAMPLES
================================================================ */
const MULT_EXAMPLES = [
    { a:2, b:3,  answer:6,  steps:['Pick the <strong>2s plane</strong> (it counts: 2, 4, 6, 8…)', 'The gear starts at <strong>0</strong> at the arrow ▼', 'Spin ↻ <strong>3 steps</strong> forward — the gear shows: 2 → 4 → 6', 'After 3 steps the arrow points at <strong>6</strong>'] },
    { a:3, b:4,  answer:12, steps:['Pick the <strong>3s plane</strong> (it counts: 3, 6, 9, 12…)', 'Start at <strong>0</strong> at the arrow ▼', 'Spin ↻ <strong>4 steps</strong> — the gear shows: 3 → 6 → 9 → 12', 'After 4 steps the arrow points at <strong>12</strong>'] },
    { a:5, b:4,  answer:20, steps:['Pick the <strong>5s plane</strong> (it counts: 5, 10, 15, 20…)', 'Start at <strong>0</strong> at the arrow ▼', 'Spin ↻ <strong>4 steps</strong> — the gear shows: 5 → 10 → 15 → 20', 'After 4 steps the arrow points at <strong>20</strong>'] },
    { a:4, b:5,  answer:20, steps:['Pick the <strong>4s plane</strong> (it counts: 4, 8, 12, 16, 20…)', 'Start at <strong>0</strong> at the arrow ▼', 'Spin ↻ <strong>5 steps</strong> — count along: 4 → 8 → 12 → 16 → 20', 'After 5 steps the arrow points at <strong>20</strong>'] },
    { a:6, b:3,  answer:18, steps:['Pick the <strong>6s plane</strong> (it counts: 6, 12, 18…)', 'Start at <strong>0</strong> at the arrow ▼', 'Spin ↻ <strong>3 steps</strong> — the gear shows: 6 → 12 → 18', 'After 3 steps the arrow points at <strong>18</strong>'] },
    { a:10, b:7, answer:70, steps:['Pick the <strong>10s plane</strong> (it counts: 10, 20, 30…)', 'Start at <strong>0</strong> at the arrow ▼', 'Spin ↻ <strong>7 steps</strong> — count: 10 → 20 → 30 → 40 → 50 → 60 → 70', 'After 7 steps the arrow points at <strong>70</strong>'] },
];

const DIV_EXAMPLES = [
    { dividend:12, divisor:3, answer:4, steps:['We want to share <strong>12</strong> into groups of <strong>3</strong>', 'Pick the <strong>3s plane</strong> — it counts in 3s: 3, 6, 9, 12…', 'Spin ↻ <strong>step by step</strong> until <strong>12</strong> appears at the arrow ▼', 'Count the steps: 3 → 6 → 9 → 12 = <strong>4 steps</strong>', 'So 12 ÷ 3 = <strong>4</strong> ✓'] },
    { dividend:20, divisor:4, answer:5, steps:['We want to share <strong>20</strong> into groups of <strong>4</strong>', 'Pick the <strong>4s plane</strong> — it counts in 4s: 4, 8, 12, 16, 20…', 'Spin ↻ <strong>step by step</strong> until <strong>20</strong> appears at the arrow ▼', 'Count the steps: 4 → 8 → 12 → 16 → 20 = <strong>5 steps</strong>', 'So 20 ÷ 4 = <strong>5</strong> ✓'] },
    { dividend:15, divisor:5, answer:3, steps:['We want to share <strong>15</strong> into groups of <strong>5</strong>', 'Pick the <strong>5s plane</strong> — it counts in 5s: 5, 10, 15…', 'Spin ↻ <strong>step by step</strong> until <strong>15</strong> appears at the arrow ▼', 'Count the steps: 5 → 10 → 15 = <strong>3 steps</strong>', 'So 15 ÷ 5 = <strong>3</strong> ✓'] },
    { dividend:18, divisor:6, answer:3, steps:['We want to share <strong>18</strong> into groups of <strong>6</strong>', 'Pick the <strong>6s plane</strong> — it counts in 6s: 6, 12, 18…', 'Spin ↻ <strong>step by step</strong> until <strong>18</strong> appears at the arrow ▼', 'Count the steps: 6 → 12 → 18 = <strong>3 steps</strong>', 'So 18 ÷ 6 = <strong>3</strong> ✓'] },
    { dividend:24, divisor:8, answer:3, steps:['We want to share <strong>24</strong> into groups of <strong>8</strong>', 'Pick the <strong>8s plane</strong> — it counts in 8s: 8, 16, 24…', 'Spin ↻ <strong>step by step</strong> until <strong>24</strong> appears at the arrow ▼', 'Count the steps: 8 → 16 → 24 = <strong>3 steps</strong>', 'So 24 ÷ 8 = <strong>3</strong> ✓'] },
    { dividend:30, divisor:10, answer:3, steps:['We want to share <strong>30</strong> into groups of <strong>10</strong>', 'Pick the <strong>10s plane</strong> — it counts in 10s: 10, 20, 30…', 'Spin ↻ <strong>step by step</strong> until <strong>30</strong> appears at the arrow ▼', 'Count the steps: 10 → 20 → 30 = <strong>3 steps</strong>', 'So 30 ÷ 10 = <strong>3</strong> ✓'] },
];

let weIdx = 0;

function loadWorkedExample() {
    const examples = currentOp === 'mult' ? MULT_EXAMPLES : DIV_EXAMPLES;
    const ex       = examples[weIdx % examples.length];
    const total    = examples.length;
    const numClass = currentOp === 'mult' ? 'mult-n' : 'div-n';

    document.getElementById('weBadge').textContent = `Example ${(weIdx % total) + 1} of ${total}`;
    document.getElementById('weOp').textContent    = currentOp === 'mult' ? '✖️ Multiplication' : '➗ Division';

    if (currentOp === 'mult') {
        document.getElementById('weEq').textContent = `${ex.a} × ${ex.b} = ?`;
    } else {
        document.getElementById('weEq').textContent = `${ex.dividend} ÷ ${ex.divisor} = ?`;
    }

    // Build steps
    const stepsEl = document.getElementById('weSteps');
    stepsEl.innerHTML = '';
    ex.steps.forEach((step, i) => {
        const div = document.createElement('div');
        div.className = 'we-step';
        div.innerHTML = `<span class="we-step-num ${numClass}">${i + 1}</span><span>${step}</span>`;
        stepsEl.appendChild(div);
    });

    // Hide answer
    const ansEl = document.getElementById('weAnswer');
    ansEl.style.display = 'none';
    ansEl.innerHTML = '';

    // Reset show button
    const showBtn = document.getElementById('weShowBtn');
    showBtn.disabled    = false;
    showBtn.textContent = '👀 Show me the answer!';

    // Speak it
    if (currentOp === 'mult') {
        NG_Speech.sayInstruction(`Example. What is ${ex.a} times ${ex.b}? Follow the steps on the gear!`);
    } else {
        NG_Speech.sayInstruction(`Example. What is ${ex.dividend} divided by ${ex.divisor}? Follow the steps on the gear!`);
    }
}

function showWorkedAnswer() {
    const examples = currentOp === 'mult' ? MULT_EXAMPLES : DIV_EXAMPLES;
    const ex       = examples[weIdx % examples.length];

    const ansEl = document.getElementById('weAnswer');
    ansEl.style.display = 'block';

    if (currentOp === 'mult') {
        ansEl.innerHTML = `<div class="we-answer-big">${ex.answer}</div><div class="we-answer-eq">${ex.a} × ${ex.b} = ${ex.answer} ✓</div>`;
        NG_Speech.sayMultiplication(ex.a, ex.b, ex.answer);
    } else {
        ansEl.innerHTML = `<div class="we-answer-big">${ex.answer}</div><div class="we-answer-eq">${ex.dividend} ÷ ${ex.divisor} = ${ex.answer} ✓</div>`;
        NG_Speech.sayDivision(ex.dividend, ex.divisor, ex.answer);
    }

    const showBtn = document.getElementById('weShowBtn');
    showBtn.disabled    = true;
    showBtn.textContent = '✓ Shown!';

    // Highlight the correct plane and spin the gear to the answer
    const planeIdx = currentOp === 'mult' ? ex.a - 1 : ex.divisor - 1;
    const steps    = currentOp === 'mult' ? ex.b : ex.answer;
    currentTable   = planeIdx;
    currentOffset  = steps % SLOTS;
    buildTablePicker();
    updateDisplay();
    drawGear();
}

function nextExample() {
    weIdx++;
    loadWorkedExample();
}

/* ================================================================
   QUIZ MODAL
================================================================ */
function openQuiz() {
    document.getElementById('quizOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
    generateQuiz();
}

function closeQuiz() {
    document.getElementById('quizOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

function closeQuizIfOutside(e) {
    if (e.target === document.getElementById('quizOverlay')) closeQuiz();
}

function setQuizOp(op) {
    currentOp = op;
    document.getElementById('qmMult').classList.toggle('active', op === 'mult');
    document.getElementById('qmDiv').classList.toggle('active',  op === 'div');
    generateQuiz();
}


function generateQuiz() {
    quizAnswered = false;
    document.getElementById('congratsBanner').classList.remove('show');

    const table  = currentTable + 1;
    let a, b, answer, qText, hintText;

    if (currentOp === 'mult') {
        b      = Math.floor(Math.random() * 9) + 1; // 1–9
        answer = table * b;
        qText  = `${table} × ${b} = ?`;
        hintText = `Use the <strong>${table}s</strong> table — spin ↻ <strong>${b} steps</strong> forward!`;
    } else {
        b      = Math.floor(Math.random() * 9) + 1;
        a      = table * b;
        answer = b;
        qText  = `${a} ÷ ${table} = ?`;
        hintText = `Use the <strong>${table}s</strong> table — spin ↻ until <strong>${a}</strong> is at the arrow ▼, then count the steps!`;
    }

    quizQ = { answer, table, b };
    document.getElementById('quizQ').textContent    = qText;
    document.getElementById('quizHint').innerHTML   = hintText;

    // Generate 4 choices including the correct answer
    const choices = new Set([answer]);
    while (choices.size < 4) {
        const wrong = answer + (Math.floor(Math.random() * 5) + 1) * table * (Math.random() < 0.5 ? 1 : -1);
        if (wrong > 0 && wrong !== answer) choices.add(wrong);
    }
    const shuffled = Array.from(choices).sort(() => Math.random() - 0.5);

    const container = document.getElementById('quizChoices');
    container.innerHTML = '';
    shuffled.forEach(val => {
        const btn = document.createElement('button');
        btn.className   = 'quiz-choice-l4';
        btn.textContent = val;
        btn.onclick     = () => checkAnswer(val, btn);
        container.appendChild(btn);
    });

    NG_Speech.sayInstruction(currentOp === 'mult'
        ? `What is ${table} times ${b}?`
        : `What is ${table * b} divided by ${table}?`
    );
}

function checkAnswer(selected, btn) {
    if (quizAnswered || !quizQ) return;
    quizAnswered = true;

    document.querySelectorAll('.quiz-choice-l4').forEach(b => b.disabled = true);

    if (selected === quizQ.answer) {
        btn.classList.add('correct');
        scoreCorrect++;
        scoreStreak++;
        document.getElementById('scoreCorrect').textContent = scoreCorrect;
        document.getElementById('scoreStreak').textContent  = scoreStreak;

        const congrats = ['Brilliant! 🌟', 'Amazing! 🎉', 'You got it! ⭐', 'Super! 🏆', 'Fantastic! 🎊'];
        document.getElementById('congratsTitle').textContent = congrats[Math.floor(Math.random() * congrats.length)];
        document.getElementById('congratsSub').textContent   = `${quizQ.table} × ${quizQ.b} = ${quizQ.answer} ✓`;
        document.getElementById('congratsBanner').classList.add('show');

        NG_Speech.sayCorrect(quizQ.answer);
        updateStats();
    } else {
        btn.classList.add('wrong');
        scoreStreak = 0;
        document.getElementById('scoreStreak').textContent = 0;
        // Reveal correct
        document.querySelectorAll('.quiz-choice-l4').forEach(b => {
            if (parseInt(b.textContent) === quizQ.answer) b.classList.add('correct');
        });
        NG_Speech.sayWrong(quizQ.answer);
        showToast(`The answer is ${quizQ.answer}`, 'error');
        setTimeout(nextQuestion, 2000);
    }
}

function nextQuestion() {
    generateQuiz();
}

/* ================================================================
   STATS & PROGRESS
================================================================ */
function updateStats() {
    const prog = Math.min(100, scoreCorrect * 10);
    NG_Storage.setLvl4Score(prog);
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
    buildTablePicker();
    updateDisplay();
    drawGear();
    generateQuiz();
    loadWorkedExample();
    setTimeout(() => {
        NG_Speech.sayInstruction('Welcome to Level 4! Pick a plane and spin the gear to multiply!');
    }, 600);
});
</script>
<footer class="ng-footer">
    <span>&copy; <?= date('Y') ?> Number Gear. Developed by <strong>Purretech Solutions</strong>.</span>
</footer>
</body>
</html>
