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
    <script src="../../assets/js/i18n-level.js"></script>
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
    <script>document.addEventListener('DOMContentLoaded', function () {
        if (window.NG_I18nCommon) NG_I18nCommon.apply(5);
        if (window.NG_LevelI18n) NG_LevelI18n.applyStatic(L5);
    });</script>
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
            <h2 data-i18n="heroTitle">🌈 Learn Even and Odd Numbers</h2>
            <p data-i18n="heroDesc">Even numbers can be shared into two equal groups. Odd numbers always have one left over. Tap numbers, see the objects, then answer the quizzes.</p>
        </section>

        <section class="lesson-grid">
            <div class="concept-card even-card">
                <h3 data-i18n="evenTitle">✅ Even Numbers</h3>
                <p data-i18n="evenDesc">When every object gets a partner, the number is even.</p>
                <div class="objects" aria-label="six objects"><span class="object-dot">🍎</span><span class="object-dot">🍎</span><span class="object-dot">🍎</span><span class="object-dot">🍎</span><span class="object-dot">🍎</span><span class="object-dot">🍎</span></div>
                <div class="pair-line"><div class="pair-box">🍎🍎</div><div class="pair-box">🍎🍎</div><div class="pair-box">🍎🍎</div></div>
                <div class="rule-box even-rule" data-i18n="evenRule">2, 4, 6, 8, 10 are even.</div>
            </div>
            <div class="concept-card odd-card">
                <h3 data-i18n="oddTitle">⭐ Odd Numbers</h3>
                <p data-i18n="oddDesc">When one object has no partner, the number is odd.</p>
                <div class="objects" aria-label="five objects"><span class="object-dot">⭐</span><span class="object-dot">⭐</span><span class="object-dot">⭐</span><span class="object-dot">⭐</span><span class="object-dot">⭐</span></div>
                <div class="pair-line"><div class="pair-box">⭐⭐</div><div class="pair-box">⭐⭐</div><div class="pair-box">⭐</div></div>
                <div class="rule-box odd-rule" data-i18n="oddRule">1, 3, 5, 7, 9 are odd.</div>
            </div>
        </section>

        <section class="practice-card">
            <h3 data-i18n="practiceTitle">👆 Tap a Number to Check</h3>
            <p class="mini-note" data-i18n="practiceTip">Tip: Look at the last digit. Numbers ending in 0, 2, 4, 6, or 8 are even. Numbers ending in 1, 3, 5, 7, or 9 are odd.</p>
            <div class="number-strip" id="numberStrip"></div>
            <div class="feedback" id="practiceFeedback" data-i18n="practiceDefault">Tap any number from 1 to 20.</div>
        </section>

        <section class="quiz-card" style="margin-top:20px;">
            <h3 data-i18n="quizTitle">🎯 Quick Quiz</h3>
            <div class="score-row">
                <div class="score-box"><strong id="scoreCorrect">0</strong><span data-i18n="correctLabel">Correct</span></div>
                <div class="score-box"><strong id="scoreTotal">0</strong><span data-i18n="answeredLabel">Answered</span></div>
                <div class="score-box"><strong id="scorePercent">0%</strong><span data-i18n="progressLabel">Progress</span></div>
            </div>
            <div class="quiz-area">
                <p><strong data-i18n="quizQuestion">Is this number even or odd?</strong></p>
                <div class="quiz-number" id="quizNumber">4</div>
                <div class="answer-row">
                    <button class="answer-btn even-answer" onclick="checkAnswer('even')" data-i18n="evenBtn">Even</button>
                    <button class="answer-btn odd-answer" onclick="checkAnswer('odd')" data-i18n="oddBtn">Odd</button>
                </div>
                <button class="speak-btn" onclick="speakCurrent()" data-i18n="hearBtn">🔊 Hear the question</button><br>
                <button class="next-btn" onclick="newQuestion()" data-i18n="nextBtn">Next Number →</button>
                <div class="feedback" id="quizFeedback" data-i18n="quizDefault">Choose Even or Odd.</div>
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
const L5 = {
    en: {
        heroTitle: '🌈 Learn Even and Odd Numbers',
        heroDesc: 'Even numbers can be shared into two equal groups. Odd numbers always have one left over. Tap numbers, see the objects, then answer the quizzes.',
        evenTitle: '✅ Even Numbers', evenDesc: 'When every object gets a partner, the number is even.', evenRule: '2, 4, 6, 8, 10 are even.',
        oddTitle: '⭐ Odd Numbers', oddDesc: 'When one object has no partner, the number is odd.', oddRule: '1, 3, 5, 7, 9 are odd.',
        practiceTitle: '👆 Tap a Number to Check',
        practiceTip: 'Tip: Look at the last digit. Numbers ending in 0, 2, 4, 6, or 8 are even. Numbers ending in 1, 3, 5, 7, or 9 are odd.',
        practiceDefault: 'Tap any number from 1 to 20.',
        quizTitle: '🎯 Quick Quiz', correctLabel: 'Correct', answeredLabel: 'Answered', progressLabel: 'Progress',
        quizQuestion: 'Is this number even or odd?', evenBtn: 'Even', oddBtn: 'Odd',
        hearBtn: '🔊 Hear the question', nextBtn: 'Next Number →', quizDefault: 'Choose Even or Odd.',
        evenWord: 'even', oddWord: 'odd',
        practiceMsg: '{n} is {kind}. Its last digit is {digit}. {note}',
        noteEven: 'It can make equal pairs.', noteOdd: 'One is left without a partner.',
        correctMsg: 'Great job! {n} is {kind}.',
        wrongMsg: 'Good try! {n} is {kind}. Remember to check the last digit.',
        speakQuestion: 'Is {n} even or odd?'
    },
    de: {
        heroTitle: '🌈 Lerne gerade und ungerade Zahlen',
        heroDesc: 'Gerade Zahlen lassen sich in zwei gleiche Gruppen teilen. Bei ungeraden Zahlen bleibt immer eine übrig. Tippe auf Zahlen, schau dir die Objekte an und beantworte dann die Quizfragen.',
        evenTitle: '✅ Gerade Zahlen', evenDesc: 'Wenn jedes Objekt einen Partner hat, ist die Zahl gerade.', evenRule: '2, 4, 6, 8, 10 sind gerade.',
        oddTitle: '⭐ Ungerade Zahlen', oddDesc: 'Wenn ein Objekt keinen Partner hat, ist die Zahl ungerade.', oddRule: '1, 3, 5, 7, 9 sind ungerade.',
        practiceTitle: '👆 Tippe auf eine Zahl zum Prüfen',
        practiceTip: 'Tipp: Schau auf die letzte Ziffer. Zahlen, die auf 0, 2, 4, 6 oder 8 enden, sind gerade. Zahlen, die auf 1, 3, 5, 7 oder 9 enden, sind ungerade.',
        practiceDefault: 'Tippe auf eine Zahl von 1 bis 20.',
        quizTitle: '🎯 Schnelles Quiz', correctLabel: 'Richtig', answeredLabel: 'Beantwortet', progressLabel: 'Fortschritt',
        quizQuestion: 'Ist diese Zahl gerade oder ungerade?', evenBtn: 'Gerade', oddBtn: 'Ungerade',
        hearBtn: '🔊 Frage anhören', nextBtn: 'Nächste Zahl →', quizDefault: 'Wähle Gerade oder Ungerade.',
        evenWord: 'gerade', oddWord: 'ungerade',
        practiceMsg: '{n} ist {kind}. Die letzte Ziffer ist {digit}. {note}',
        noteEven: 'Sie kann in gleiche Paare aufgeteilt werden.', noteOdd: 'Eine bleibt ohne Partner übrig.',
        correctMsg: 'Gut gemacht! {n} ist {kind}.',
        wrongMsg: 'Guter Versuch! {n} ist {kind}. Denk daran, die letzte Ziffer zu prüfen.',
        speakQuestion: 'Ist {n} gerade oder ungerade?'
    },
    fr: {
        heroTitle: '🌈 Apprends les nombres pairs et impairs',
        heroDesc: "Les nombres pairs peuvent être partagés en deux groupes égaux. Les nombres impairs ont toujours un élément en trop. Touche les nombres, observe les objets, puis réponds aux quiz.",
        evenTitle: '✅ Nombres pairs', evenDesc: "Quand chaque objet a un partenaire, le nombre est pair.", evenRule: '2, 4, 6, 8, 10 sont pairs.',
        oddTitle: '⭐ Nombres impairs', oddDesc: "Quand un objet n'a pas de partenaire, le nombre est impair.", oddRule: '1, 3, 5, 7, 9 sont impairs.',
        practiceTitle: '👆 Touche un nombre pour vérifier',
        practiceTip: 'Astuce : regarde le dernier chiffre. Les nombres se terminant par 0, 2, 4, 6 ou 8 sont pairs. Ceux se terminant par 1, 3, 5, 7 ou 9 sont impairs.',
        practiceDefault: 'Touche un nombre de 1 à 20.',
        quizTitle: '🎯 Quiz rapide', correctLabel: 'Correct', answeredLabel: 'Répondu', progressLabel: 'Progression',
        quizQuestion: 'Ce nombre est-il pair ou impair ?', evenBtn: 'Pair', oddBtn: 'Impair',
        hearBtn: '🔊 Écouter la question', nextBtn: 'Nombre suivant →', quizDefault: 'Choisis Pair ou Impair.',
        evenWord: 'pair', oddWord: 'impair',
        practiceMsg: '{n} est {kind}. Son dernier chiffre est {digit}. {note}',
        noteEven: 'Il peut former des paires égales.', noteOdd: 'Un élément reste sans partenaire.',
        correctMsg: 'Bravo ! {n} est {kind}.',
        wrongMsg: "Bel essai ! {n} est {kind}. N'oublie pas de vérifier le dernier chiffre.",
        speakQuestion: '{n} est-il pair ou impair ?'
    },
    ar: {
        heroTitle: '🌈 تعلّم الأعداد الزوجية والفردية',
        heroDesc: 'يمكن تقسيم الأعداد الزوجية إلى مجموعتين متساويتين. أما الأعداد الفردية فيتبقى منها عنصر واحد دائمًا. اضغط على الأرقام، شاهد الأشياء، ثم أجب عن الأسئلة.',
        evenTitle: '✅ الأعداد الزوجية', evenDesc: 'عندما يحصل كل شيء على شريك، يكون العدد زوجيًا.', evenRule: '٢، ٤، ٦، ٨، ١٠ أعداد زوجية.',
        oddTitle: '⭐ الأعداد الفردية', oddDesc: 'عندما لا يحصل أحد الأشياء على شريك، يكون العدد فرديًا.', oddRule: '١، ٣، ٥، ٧، ٩ أعداد فردية.',
        practiceTitle: '👆 اضغط على عدد للتحقق',
        practiceTip: 'نصيحة: انظر إلى آخر رقم. الأعداد المنتهية بـ ٠ أو ٢ أو ٤ أو ٦ أو ٨ زوجية. الأعداد المنتهية بـ ١ أو ٣ أو ٥ أو ٧ أو ٩ فردية.',
        practiceDefault: 'اضغط على أي عدد من ١ إلى ٢٠.',
        quizTitle: '🎯 اختبار سريع', correctLabel: 'صحيح', answeredLabel: 'مجاب عنها', progressLabel: 'التقدم',
        quizQuestion: 'هل هذا العدد زوجي أم فردي؟', evenBtn: 'زوجي', oddBtn: 'فردي',
        hearBtn: '🔊 استمع إلى السؤال', nextBtn: '← العدد التالي', quizDefault: 'اختر زوجي أو فردي.',
        evenWord: 'زوجي', oddWord: 'فردي',
        practiceMsg: '{n} عدد {kind}. آخر رقم فيه هو {digit}. {note}',
        noteEven: 'يمكن تكوين أزواج متساوية منه.', noteOdd: 'يتبقى عنصر واحد بدون شريك.',
        correctMsg: 'أحسنت! {n} عدد {kind}.',
        wrongMsg: 'محاولة جيدة! {n} عدد {kind}. تذكّر التحقق من آخر رقم.',
        speakQuestion: 'هل {n} زوجي أم فردي؟'
    },
    zh: {
        heroTitle: '🌈 学习奇数和偶数',
        heroDesc: '偶数可以平均分成两组。奇数总会多出一个。点击数字，观察物体，然后回答测验。',
        evenTitle: '✅ 偶数', evenDesc: '当每个物体都能配对时，这个数就是偶数。', evenRule: '2、4、6、8、10 都是偶数。',
        oddTitle: '⭐ 奇数', oddDesc: '当有一个物体没有配对时，这个数就是奇数。', oddRule: '1、3、5、7、9 都是奇数。',
        practiceTitle: '👆 点击数字检查',
        practiceTip: '小提示：看最后一位数字。以 0、2、4、6 或 8 结尾的数是偶数。以 1、3、5、7 或 9 结尾的数是奇数。',
        practiceDefault: '点击 1 到 20 之间的任意数字。',
        quizTitle: '🎯 快速测验', correctLabel: '正确', answeredLabel: '已答', progressLabel: '进度',
        quizQuestion: '这个数是奇数还是偶数？', evenBtn: '偶数', oddBtn: '奇数',
        hearBtn: '🔊 听题目', nextBtn: '下一个数字 →', quizDefault: '选择偶数或奇数。',
        evenWord: '偶数', oddWord: '奇数',
        practiceMsg: '{n} 是{kind}。它的最后一位是 {digit}。{note}',
        noteEven: '它可以组成相等的对。', noteOdd: '有一个没有配对。',
        correctMsg: '太棒了！{n} 是{kind}。',
        wrongMsg: '再接再厉！{n} 是{kind}。记得检查最后一位数字。',
        speakQuestion: '{n} 是奇数还是偶数？'
    }
};

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
function kindWord(n) { return NG_LevelI18n.t(L5, isEven(n) ? 'evenWord' : 'oddWord'); }

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
    const message = NG_LevelI18n.t(L5, 'practiceMsg', {
        n: n,
        kind: kindWord(n),
        digit: lastDigit,
        note: NG_LevelI18n.t(L5, isEven(n) ? 'noteEven' : 'noteOdd')
    });
    practiceFeedback.textContent = message;
    speak(message);
}

function newQuestion() {
    if (askedNumbers.length >= 20) askedNumbers = [];
    do { currentNumber = Math.floor(Math.random() * 20) + 1; }
    while (askedNumbers.includes(currentNumber) && askedNumbers.length < 20);
    askedNumbers.push(currentNumber);
    quizNumberEl.textContent = currentNumber;
    quizFeedback.textContent = NG_LevelI18n.t(L5, 'quizDefault');
}

function checkAnswer(answer) {
    total++;
    const expected = kindOf(currentNumber);
    if (answer === expected) {
        correct++;
        const msg = NG_LevelI18n.t(L5, 'correctMsg', { n: currentNumber, kind: kindWord(currentNumber) });
        quizFeedback.textContent = msg;
        speak(msg);
    } else {
        const msg = NG_LevelI18n.t(L5, 'wrongMsg', { n: currentNumber, kind: kindWord(currentNumber) });
        quizFeedback.textContent = msg;
        speak(msg);
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
    speak(NG_LevelI18n.t(L5, 'speakQuestion', { n: currentNumber }));
}

buildNumberStrip();
newQuestion();
</script>
</body>
</html>
