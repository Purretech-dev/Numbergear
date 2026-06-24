<?php
// Number Gear — Level 5: Even & Odd Numbers
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
    <title>Level 5 — Even & Odd Numbers | Number Gear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/accessibility.js"></script>
    <script src="../../assets/js/i18n-common.js"></script>
    <style>
        .level-main { max-width: 1100px; margin: 0 auto; padding: 26px 20px 50px; width: 100%; }
        .lesson-hero { background: linear-gradient(135deg, var(--sky-light), var(--purple-light)); border: 2px solid var(--border); border-radius: 24px; padding: 22px; box-shadow: var(--shadow); margin-bottom: 20px; }
        .lesson-hero h2 { font-size: 30px; font-weight: 900; color: var(--sky-dark); margin-bottom: 8px; }
        .lesson-hero p { font-size: 17px; line-height: 1.7; color: var(--text); font-weight: 700; }
        .lesson-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 20px; }
        @media (max-width: 820px) { .lesson-grid { grid-template-columns: 1fr; } }
        .concept-card, .quiz-card, .practice-card { background: var(--surface); border: 2px solid var(--border); border-radius: 22px; padding: 20px; box-shadow: var(--shadow); }
        .concept-card h3, .quiz-card h3, .practice-card h3 { font-size: 21px; font-weight: 900; margin-bottom: 10px; color: var(--text); }
        .even-card { border-top: 6px solid var(--mint); }
        .odd-card { border-top: 6px solid var(--peach); }
        .rule-box { border-radius: 18px; padding: 16px; margin-top: 12px; font-size: 18px; font-weight: 900; text-align: center; }
        .even-rule { background: var(--mint-light); color: var(--mint-dark); }
        .odd-rule { background: var(--peach-light); color: var(--peach-dark); }
        .objects { display: flex; flex-wrap: wrap; gap: 8px; margin: 14px 0; min-height: 52px; }
        .object-dot { width: 38px; height: 38px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 3px 8px rgba(0,0,0,0.12); background: var(--sky-light); }
        .pair-line { display: grid; grid-template-columns: repeat(5, minmax(44px, 1fr)); gap: 10px; margin-top: 16px; }
        .pair-box { min-height: 72px; border: 2px dashed var(--border); border-radius: 16px; display: flex; align-items: center; justify-content: center; gap: 4px; padding: 8px; background: var(--bg); font-size: 24px; }
        .number-strip { display: grid; grid-template-columns: repeat(10, 1fr); gap: 8px; margin-top: 14px; }
        @media (max-width: 600px) { .number-strip { grid-template-columns: repeat(5, 1fr); } }
        .num-pill { border: none; border-radius: 14px; padding: 12px 0; font-size: 18px; font-weight: 900; cursor: pointer; font-family: inherit; background: var(--bg); color: var(--text); border: 2px solid var(--border); transition: 0.18s ease; }
        .num-pill:hover { transform: translateY(-2px); }
        .num-pill.even { background: var(--mint-light); border-color: var(--mint); color: var(--mint-dark); }
        .num-pill.odd { background: var(--peach-light); border-color: var(--peach); color: var(--peach-dark); }
        .feedback { margin-top: 14px; padding: 14px 16px; border-radius: 16px; font-size: 17px; font-weight: 900; text-align: center; background: var(--sky-light); color: var(--sky-dark); min-height: 54px; display: flex; align-items: center; justify-content: center; }
        .quiz-area { text-align: center; }
        .quiz-number { width: 120px; height: 120px; margin: 12px auto 16px; border-radius: 50%; background: var(--sky); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 50px; font-weight: 900; box-shadow: var(--shadow-lg); }
        .answer-row { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .answer-btn, .next-btn, .speak-btn { border: none; border-radius: 16px; padding: 14px 22px; font-size: 17px; font-weight: 900; cursor: pointer; font-family: inherit; transition: 0.18s ease; }
        .answer-btn:hover, .next-btn:hover, .speak-btn:hover { transform: translateY(-2px); }
        .answer-btn.even-answer { background: var(--mint); color: white; }
        .answer-btn.odd-answer { background: var(--peach); color: white; }
        .next-btn { background: var(--purple); color: white; margin-top: 12px; }
        .speak-btn { background: var(--sky-light); color: var(--sky-dark); border: 2px solid var(--sky); margin-top: 12px; }
        .score-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 14px; }
        .score-box { background: var(--bg); border: 2px solid var(--border); border-radius: 16px; padding: 12px; text-align: center; }
        .score-box strong { display: block; font-size: 24px; color: var(--purple); }
        .score-box span { font-size: 12px; font-weight: 800; color: var(--text-soft); text-transform: uppercase; }
        .mini-note { margin-top: 10px; font-size: 14px; font-weight: 700; color: var(--text-soft); line-height: 1.6; }
    </style>
    <script>document.addEventListener('DOMContentLoaded', function () { if (window.NG_I18nCommon) NG_I18nCommon.apply(5); });</script>
</head>
<body>
<div class="app-shell">
    <header class="app-header">
        <a href="../../index.php" class="brand">
            <div class="brand-icon">⚙️</div>
            <div><h1>Number Gear</h1><p id="lvlHeading">Level 5: Even & Odd Numbers</p></div>
        </a>
        <a href="../../index.php" class="back-btn" id="lvlBackLink">← Back Home</a>
    </header>

    <main class="level-main">
        <section class="lesson-hero">
            <h2>🌈 Learn Even and Odd Numbers</h2>
            <p>Even numbers can be shared into two equal groups. Odd numbers always have one left over. Tap numbers, see the objects, then answer the quizzes.</p>
        </section>

        <section class="lesson-grid">
            <div class="concept-card even-card">
                <h3>✅ Even Numbers</h3>
                <p>When every object gets a partner, the number is even.</p>
                <div class="objects" aria-label="six objects"><span class="object-dot">🍎</span><span class="object-dot">🍎</span><span class="object-dot">🍎</span><span class="object-dot">🍎</span><span class="object-dot">🍎</span><span class="object-dot">🍎</span></div>
                <div class="pair-line"><div class="pair-box">🍎🍎</div><div class="pair-box">🍎🍎</div><div class="pair-box">🍎🍎</div></div>
                <div class="rule-box even-rule">2, 4, 6, 8, 10 are even.</div>
            </div>
            <div class="concept-card odd-card">
                <h3>⭐ Odd Numbers</h3>
                <p>When one object has no partner, the number is odd.</p>
                <div class="objects" aria-label="five objects"><span class="object-dot">⭐</span><span class="object-dot">⭐</span><span class="object-dot">⭐</span><span class="object-dot">⭐</span><span class="object-dot">⭐</span></div>
                <div class="pair-line"><div class="pair-box">⭐⭐</div><div class="pair-box">⭐⭐</div><div class="pair-box">⭐</div></div>
                <div class="rule-box odd-rule">1, 3, 5, 7, 9 are odd.</div>
            </div>
        </section>

        <section class="practice-card">
            <h3>👆 Tap a Number to Check</h3>
            <p class="mini-note">Tip: Look at the last digit. Numbers ending in 0, 2, 4, 6, or 8 are even. Numbers ending in 1, 3, 5, 7, or 9 are odd.</p>
            <div class="number-strip" id="numberStrip"></div>
            <div class="feedback" id="practiceFeedback">Tap any number from 1 to 20.</div>
        </section>

        <section class="quiz-card" style="margin-top:20px;">
            <h3>🎯 Quick Quiz</h3>
            <div class="score-row">
                <div class="score-box"><strong id="scoreCorrect">0</strong><span>Correct</span></div>
                <div class="score-box"><strong id="scoreTotal">0</strong><span>Answered</span></div>
                <div class="score-box"><strong id="scorePercent">0%</strong><span>Progress</span></div>
            </div>
            <div class="quiz-area">
                <p><strong>Is this number even or odd?</strong></p>
                <div class="quiz-number" id="quizNumber">4</div>
                <div class="answer-row">
                    <button class="answer-btn even-answer" onclick="checkAnswer('even')">Even</button>
                    <button class="answer-btn odd-answer" onclick="checkAnswer('odd')">Odd</button>
                </div>
                <button class="speak-btn" onclick="speakCurrent()">🔊 Hear the question</button><br>
                <button class="next-btn" onclick="newQuestion()">Next Number →</button>
                <div class="feedback" id="quizFeedback">Choose Even or Odd.</div>
            </div>
        </section>
    </main>
</div>

<script>
    window.NG_USER_ID  = <?= json_encode($ng_current_user['id']) ?>;
    window.NG_API_BASE = '../../api/';
</script>
<script src="../../assets/js/storage.js"></script>
<script src="../../assets/js/speech.js"></script>
<script>
const numberStrip = document.getElementById('numberStrip');
const practiceFeedback = document.getElementById('practiceFeedback');
const quizNumberEl = document.getElementById('quizNumber');
const quizFeedback = document.getElementById('quizFeedback');
const scoreCorrectEl = document.getElementById('scoreCorrect');
const scoreTotalEl = document.getElementById('scoreTotal');
const scorePercentEl = document.getElementById('scorePercent');

let currentNumber = 4;
let correct = 0;
let total = 0;
let askedNumbers = [];

function isEven(n) { return n % 2 === 0; }
function kindOf(n) { return isEven(n) ? 'even' : 'odd'; }

function speak(text) {
    // Use the shared Number Gear narrator so Level 5 has the same slow, child-friendly pace as the other levels.
    if (window.NG_Speech && typeof NG_Speech.speak === 'function') {
        NG_Speech.speak(text);
        return;
    }

    if (!('speechSynthesis' in window)) return;
    window.speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.rate = 0.62;
    utterance.pitch = 1.15;
    utterance.volume = 1;
    window.speechSynthesis.speak(utterance);
}

function buildNumberStrip() {
    for (let n = 1; n <= 20; n++) {
        const btn = document.createElement('button');
        btn.className = 'num-pill';
        btn.textContent = n;
        btn.onclick = () => showPractice(n, btn);
        numberStrip.appendChild(btn);
    }
}

function showPractice(n, btn) {
    document.querySelectorAll('.num-pill').forEach(b => b.classList.remove('even', 'odd'));
    btn.classList.add(kindOf(n));
    const lastDigit = n % 10;
    const message = `${n} is ${kindOf(n)}. Its last digit is ${lastDigit}. ${isEven(n) ? 'It can make equal pairs.' : 'One is left without a partner.'}`;
    practiceFeedback.textContent = message;
    speak(message);
}

function newQuestion() {
    if (askedNumbers.length >= 20) askedNumbers = [];
    do { currentNumber = Math.floor(Math.random() * 20) + 1; }
    while (askedNumbers.includes(currentNumber) && askedNumbers.length < 20);
    askedNumbers.push(currentNumber);
    quizNumberEl.textContent = currentNumber;
    quizFeedback.textContent = 'Choose Even or Odd.';
}

function checkAnswer(answer) {
    total++;
    const expected = kindOf(currentNumber);
    if (answer === expected) {
        correct++;
        quizFeedback.textContent = `Great job! ${currentNumber} is ${expected}.`;
        speak(`Great job! ${currentNumber} is ${expected}.`);
    } else {
        quizFeedback.textContent = `Good try! ${currentNumber} is ${expected}. Remember to check the last digit.`;
        speak(`Good try. ${currentNumber} is ${expected}. Remember to check the last digit.`);
    }
    updateScore();
    setTimeout(newQuestion, 1200);
}

function updateScore() {
    const percent = total === 0 ? 0 : Math.round((correct / total) * 100);
    scoreCorrectEl.textContent = correct;
    scoreTotalEl.textContent = total;
    scorePercentEl.textContent = percent + '%';
    NG_Storage.setLvl5Score(percent);
}

function speakCurrent() {
    speak(`Is ${currentNumber} even or odd?`);
}

buildNumberStrip();
newQuestion();
</script>
</body>
</html>
