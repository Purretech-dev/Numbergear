<?php
// Number Gear — Level 2: Counters & Objects
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
    <title>Level 2 — Counting Objects | Number Gear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/accessibility.js"></script>
    <script src="../../assets/js/i18n-common.js"></script>
    <script src="../../assets/js/i18n-level.js"></script>
    <style>
        .answer-reveal {
            font-size: 68px;
            font-weight: 900;
            color: var(--mint);
            text-align: center;
            animation: popIn 0.4s ease;
            margin: 8px 0;
        }
        .eq-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            font-size: 32px;
            font-weight: 900;
            color: var(--text);
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .eq-row .num-a { color: var(--purple); }
        .eq-row .num-b { color: var(--peach-dark); }
        .eq-row .op    { color: var(--text-soft); font-size: 38px; }
        .eq-row .eq    { color: var(--text-soft); font-size: 38px; }
        .eq-row .blank {
            display: inline-block;
            width: 60px; height: 52px;
            border: 3px dashed var(--border);
            border-radius: 12px;
            background: var(--bg);
        }

        .activity-score {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .score-chip {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 800;
            color: var(--text-soft);
        }
        .score-chip span { color: var(--mint-dark); font-size: 18px; }

        .next-btn-wrap { text-align: center; margin-top: 6px; }

        .activity-instruction {
            text-align: center;
            font-size: 18px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 16px;
        }

        .difficulty-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            align-items: center;
        }
        .difficulty-row span { font-size: 13px; font-weight: 700; color: var(--text-soft); }
        .diff-btn {
            padding: 7px 14px;
            border: 2px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            color: var(--text-soft);
            font-family: inherit;
            transition: 0.18s ease;
        }
        .diff-btn.active { background: var(--sky); border-color: var(--sky); color: white; }

        /* ---- Wrong answer banner ---- */
        .wrong-banner {
            display: none;
            background: var(--error-bg);
            border: 2px solid var(--error);
            border-radius: 14px;
            padding: 14px 18px;
            margin-bottom: 14px;
            text-align: center;
            animation: shake 0.4s ease;
        }
        .wrong-banner.show { display: block; }
        .wrong-banner-icon { font-size: 32px; margin-bottom: 4px; }
        .wrong-banner-text {
            font-size: 16px;
            font-weight: 800;
            color: #9b2335;
        }
        .wrong-banner-answer {
            font-size: 13px;
            color: var(--text-soft);
            font-weight: 700;
            margin-top: 4px;
        }

        /* ---- Addition merge animation ---- */
        @keyframes mergeToLeft {
            0%   { transform: translateX(0)    scale(1);   opacity: 1; }
            60%  { transform: translateX(-50px) scale(1.1); opacity: 0.6; }
            100% { transform: translateX(-110px) scale(0);  opacity: 0; }
        }
        @keyframes popGroup {
            0%   { transform: scale(0.5); opacity: 0; }
            60%  { transform: scale(1.15); }
            100% { transform: scale(1);   opacity: 1; }
        }
        .obj-emoji.merging { animation: mergeToLeft 0.55s ease forwards; }

        .merged-group {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: center;
            animation: popGroup 0.45s ease;
            max-width: 260px;
        }
    </style>
    <script>document.addEventListener('DOMContentLoaded', function () { if (window.NG_I18nCommon) NG_I18nCommon.apply(2); });</script>
</head>
<body>
<canvas id="confettiCanvas2" style="position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999;display:none;"></canvas>
<div class="app-shell">

    <!-- Header -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">🍎</div>
            <div>
                <h1 id="lvlHeading">Level 2</h1>
                <p data-i18n="subHeading">Counting Objects</p>
            </div>
        </div>
        <a href="../../index.php" class="back-btn" id="lvlBackLink">← Home</a>
    </header>

    <main class="level-page">

        <!-- Activity tabs -->
        <div class="tab-bar activity-tabs">
            <button class="tab-btn mint"         id="tabCount" onclick="switchActivity('counters')" data-i18n="tabCount">🔢 Counters</button>
            <button class="tab-btn mint active"  id="tabAdd"   onclick="switchActivity('addition')" data-i18n="tabAdd">➕ Addition</button>
            <button class="tab-btn mint"         id="tabSub"   onclick="switchActivity('subtraction')" data-i18n="tabSub">➖ Subtraction</button>
        </div>

        <!-- Score row -->
        <div class="activity-score">
            <div class="score-chip"><span data-i18n="correctLabel">Correct:</span> <span id="scoreCorrect">0</span></div>
            <div class="score-chip"><span data-i18n="streakLabel">Streak:</span> <span id="scoreStreak">0</span></div>
            <div class="difficulty-row" style="margin:0;">
                <span data-i18n="levelLabel">Level:</span>
                <button class="diff-btn active" id="diffEasy"   onclick="setDifficulty('easy')" data-i18n="easy">Easy</button>
                <button class="diff-btn"         id="diffMedium" onclick="setDifficulty('medium')" data-i18n="medium">Medium</button>
                <button class="diff-btn"         id="diffHard"   onclick="setDifficulty('hard')" data-i18n="hard">Hard</button>
            </div>
        </div>

        <!-- Activity card -->
        <div class="section-card">

            <div class="activity-instruction" id="actInstruction">How many objects altogether?</div>

            <!-- Equation display -->
            <div class="eq-row" id="eqRow">
                <span class="num-a" id="numA">–</span>
                <span class="op"   id="opSign">+</span>
                <span class="num-b" id="numB">–</span>
                <span class="eq" id="eqSign">= ?</span>
            </div>

            <!-- Object stage -->
            <div class="counter-stage" id="counterStage"></div>

            <!-- Answer label (shown after correct) -->
            <div id="answerReveal" style="display:none;"></div>

            <!-- Wrong answer banner -->
            <div class="wrong-banner" id="wrongBanner">
                <div class="wrong-banner-icon">❌</div>
                <div class="wrong-banner-text" data-i18n="wrongBannerText">That is not right — try again!</div>
                <div class="wrong-banner-answer" id="wrongHint"></div>
            </div>

            <!-- Feedback message -->
            <div class="level2-feedback" id="l2Feedback"></div>

            <!-- Answer choices -->
            <div class="choice-row" id="choiceRow"></div>

            <!-- Next question -->
            <div class="next-btn-wrap">
                <button class="btn btn-mint btn-sm" id="nextBtn" onclick="newQuestion()" style="display:none;" data-i18n="nextQuestion">Next Question →</button>
                <button class="btn btn-outline btn-sm" onclick="newQuestion()" data-i18n="newExample">New Example 🔄</button>
            </div>

        </div>

        <!-- Section completion overlay -->
        <div id="lvl2CompletionOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9998;align-items:center;justify-content:center;">
            <div style="background:white;border-radius:24px;padding:30px 26px 22px;max-width:400px;width:92%;text-align:center;box-shadow:0 16px 50px rgba(0,0,0,0.3);position:relative;z-index:9999;">
                <div id="lvl2CompEmoji"  style="font-size:56px;margin-bottom:10px;">🎉</div>
                <div id="lvl2CompTitle"  style="font-size:26px;font-weight:900;color:var(--purple-dark);margin-bottom:6px;">Well Done!</div>
                <div id="lvl2CompSub"    style="font-size:14px;font-weight:700;color:var(--text-soft);margin-bottom:12px;line-height:1.6;"></div>
                <div id="lvl2CompPrompt" style="font-size:13px;font-weight:700;color:var(--text);background:var(--purple-light);border-radius:12px;padding:10px 14px;margin-bottom:16px;line-height:1.7;"></div>
                <div style="display:flex;flex-direction:column;gap:9px;">
                    <button id="lvl2NextLevelBtn"   style="padding:14px;background:var(--purple);color:white;border:none;border-radius:13px;font-size:16px;font-weight:900;font-family:inherit;cursor:pointer;">⭐ Go to Next Level</button>
                    <button id="lvl2NextSectionBtn" style="padding:13px;background:var(--mint);color:white;border:none;border-radius:13px;font-size:15px;font-weight:900;font-family:inherit;cursor:pointer;">Continue →</button>
                    <button id="lvl2TryAgainBtn"    style="padding:11px;background:transparent;color:var(--purple-dark);border:2px solid var(--purple);border-radius:13px;font-size:14px;font-weight:800;font-family:inherit;cursor:pointer;">Try again 🔁</button>
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
   LEVEL 2 — translations (visible text only; narration stays
   English — see speech.js)
================================================================ */
const L2 = {
    en: {
        subHeading:'Counting Objects', tabCount:'🔢 Counters', tabAdd:'➕ Addition', tabSub:'➖ Subtraction',
        correctLabel:'Correct:', streakLabel:'Streak:', levelLabel:'Level:',
        easy:'Easy', medium:'Medium', hard:'Hard',
        wrongBannerText:'That is not right — try again!',
        nextQuestion:'Next Question →', newExample:'New Example 🔄',
        howManyTogether:'How many all together?', howManyLeft:'How many are left?',
        countMatch:'Count the {item} and match to the right number!',
        removeN:'Remove {n}',
        wrongHintCounters:'Count each {emoji} one by one — there are {n} {item}.',
        wrongHintMath:'Hint: count all the objects carefully. The answer is {n}.',
        toastCountersCorrect:'✓ There are {n} {item} all together!',
        toastCorrect:'✓ Correct! The answer is {n}',
        toastCountersWrong:'❌ Sorry — there are {n} {item}.',
        toastWrong:'❌ The answer is {n}',
        counterResultLabel:'{n} {item} — well done!',
        mergedLabel:'{a} and {b} all together = {sum}!',
        brilliant:'Brilliant!',
        sectionCompleteSub:'You got <strong>{target} correct</strong> in <strong>{activity}</strong>! Amazing work! 🎉',
        sectionCompletePrompt:'Would you like to move on to <strong>{nextLabel}</strong>, or <strong>proceed to Level 3</strong> (Number Gear), or practise <strong>{activity}</strong> again?',
        goToLevel3:'⭐ Go to Level 3 — Number Gear', tryNext:'Try {nextLabel} →', tryAgain:'Try again 🔁',
        actNameCounters:'Counting Objects', actNameAddition:'Addition', actNameSubtraction:'Subtraction',
        actLabelAddition:'Addition ➕', actLabelSubtraction:'Subtraction ➖', actLabelCounters:'Counters 🔢'
    },
    de: {
        subHeading:'Objekte zählen', tabCount:'🔢 Zähler', tabAdd:'➕ Addition', tabSub:'➖ Subtraktion',
        correctLabel:'Richtig:', streakLabel:'Serie:', levelLabel:'Stufe:',
        easy:'Leicht', medium:'Mittel', hard:'Schwer',
        wrongBannerText:'Das ist nicht richtig — versuch es noch einmal!',
        nextQuestion:'Nächste Frage →', newExample:'Neues Beispiel 🔄',
        howManyTogether:'Wie viele sind es zusammen?', howManyLeft:'Wie viele bleiben übrig?',
        countMatch:'Zähle die {item} und finde die richtige Zahl!',
        removeN:'Entferne {n}',
        wrongHintCounters:'Zähle jedes {emoji} einzeln — es sind {n} {item}.',
        wrongHintMath:'Tipp: Zähle alle Objekte sorgfältig. Die Antwort ist {n}.',
        toastCountersCorrect:'✓ Es sind {n} {item} zusammen!',
        toastCorrect:'✓ Richtig! Die Antwort ist {n}',
        toastCountersWrong:'❌ Es sind {n} {item}.',
        toastWrong:'❌ Die Antwort ist {n}',
        counterResultLabel:'{n} {item} — gut gemacht!',
        mergedLabel:'{a} und {b} zusammen = {sum}!',
        brilliant:'Großartig!',
        sectionCompleteSub:'Du hast <strong>{target} richtig</strong> bei <strong>{activity}</strong>! Tolle Arbeit! 🎉',
        sectionCompletePrompt:'Möchtest du zu <strong>{nextLabel}</strong> weitergehen, <strong>zu Stufe 3</strong> (Zahlenrad) übergehen, oder <strong>{activity}</strong> noch einmal üben?',
        goToLevel3:'⭐ Zu Stufe 3 — Zahlenrad', tryNext:'{nextLabel} probieren →', tryAgain:'Noch einmal versuchen 🔁',
        actNameCounters:'Objekte zählen', actNameAddition:'Addition', actNameSubtraction:'Subtraktion',
        actLabelAddition:'Addition ➕', actLabelSubtraction:'Subtraktion ➖', actLabelCounters:'Zähler 🔢'
    },
    fr: {
        subHeading:'Compter les objets', tabCount:'🔢 Compteurs', tabAdd:'➕ Addition', tabSub:'➖ Soustraction',
        correctLabel:'Correct :', streakLabel:'Série :', levelLabel:'Niveau :',
        easy:'Facile', medium:'Moyen', hard:'Difficile',
        wrongBannerText:"Ce n'est pas correct — réessaie !",
        nextQuestion:'Question suivante →', newExample:'Nouvel exemple 🔄',
        howManyTogether:'Combien y en a-t-il en tout ?', howManyLeft:'Combien en reste-t-il ?',
        countMatch:'Compte les {item} et trouve le bon nombre !',
        removeN:'Retire {n}',
        wrongHintCounters:'Compte chaque {emoji} un par un — il y en a {n}.',
        wrongHintMath:'Astuce : compte bien tous les objets. La réponse est {n}.',
        toastCountersCorrect:'✓ Il y a {n} {item} en tout !',
        toastCorrect:'✓ Correct ! La réponse est {n}',
        toastCountersWrong:'❌ Il y a {n} {item}.',
        toastWrong:'❌ La réponse est {n}',
        counterResultLabel:'{n} {item} — bien joué !',
        mergedLabel:'{a} et {b} en tout = {sum} !',
        brilliant:'Génial !',
        sectionCompleteSub:'Tu as eu <strong>{target} bonnes réponses</strong> en <strong>{activity}</strong> ! Bravo ! 🎉',
        sectionCompletePrompt:'Veux-tu continuer avec <strong>{nextLabel}</strong>, passer au <strong>niveau 3</strong> (engrenage), ou refaire <strong>{activity}</strong> ?',
        goToLevel3:'⭐ Aller au niveau 3 — Engrenage', tryNext:'Essayer {nextLabel} →', tryAgain:'Réessayer 🔁',
        actNameCounters:'Compter les objets', actNameAddition:'Addition', actNameSubtraction:'Soustraction',
        actLabelAddition:'Addition ➕', actLabelSubtraction:'Soustraction ➖', actLabelCounters:'Compteurs 🔢'
    },
    ar: {
        subHeading:'عدّ الأشياء', tabCount:'🔢 العدّ', tabAdd:'➕ الجمع', tabSub:'➖ الطرح',
        correctLabel:'صحيح:', streakLabel:'متتالية:', levelLabel:'المستوى:',
        easy:'سهل', medium:'متوسط', hard:'صعب',
        wrongBannerText:'هذا غير صحيح — حاول مرة أخرى!',
        nextQuestion:'السؤال التالي ←', newExample:'مثال جديد 🔄',
        howManyTogether:'كم عددها معاً؟', howManyLeft:'كم تبقى؟',
        countMatch:'عدّ {item} وطابقه مع الرقم الصحيح!',
        removeN:'أزل {n}',
        wrongHintCounters:'عدّ كل {emoji} واحدة تلو الأخرى — العدد هو {n}.',
        wrongHintMath:'تلميح: عدّ كل الأشياء بعناية. الإجابة هي {n}.',
        toastCountersCorrect:'✓ يوجد {n} {item} معاً!',
        toastCorrect:'✓ صحيح! الإجابة هي {n}',
        toastCountersWrong:'❌ العدد الصحيح هو {n} {item}.',
        toastWrong:'❌ الإجابة هي {n}',
        counterResultLabel:'{n} {item} — أحسنت!',
        mergedLabel:'{a} و {b} معاً = {sum}!',
        brilliant:'رائع!',
        sectionCompleteSub:'حصلت على <strong>{target} إجابة صحيحة</strong> في <strong>{activity}</strong>! عمل مذهل! 🎉',
        sectionCompletePrompt:'هل تريد الانتقال إلى <strong>{nextLabel}</strong>، أو الانتقال إلى <strong>المستوى 3</strong> (تروس الأرقام)، أو التدرب على <strong>{activity}</strong> مرة أخرى؟',
        goToLevel3:'⭐ الانتقال إلى المستوى 3 — تروس الأرقام', tryNext:'جرّب {nextLabel} ←', tryAgain:'حاول مرة أخرى 🔁',
        actNameCounters:'عدّ الأشياء', actNameAddition:'الجمع', actNameSubtraction:'الطرح',
        actLabelAddition:'الجمع ➕', actLabelSubtraction:'الطرح ➖', actLabelCounters:'العدّ 🔢'
    },
    zh: {
        subHeading:'数数物体', tabCount:'🔢 数数', tabAdd:'➕ 加法', tabSub:'➖ 减法',
        correctLabel:'正确：', streakLabel:'连续：', levelLabel:'难度：',
        easy:'简单', medium:'中等', hard:'困难',
        wrongBannerText:'不对哦，再试一次！',
        nextQuestion:'下一题 →', newExample:'换一个例子 🔄',
        howManyTogether:'一共有多少？', howManyLeft:'还剩多少？',
        countMatch:'数一数{item}，找到正确的数字！',
        removeN:'拿走 {n} 个',
        wrongHintCounters:'一个一个数{emoji}——一共有 {n} 个{item}。',
        wrongHintMath:'提示：仔细数一数所有物体。答案是 {n}。',
        toastCountersCorrect:'✓ 一共有 {n} 个{item}！',
        toastCorrect:'✓ 正确！答案是 {n}',
        toastCountersWrong:'❌ 正确数量是 {n} 个{item}。',
        toastWrong:'❌ 答案是 {n}',
        counterResultLabel:'{n} 个{item} —— 做得好！',
        mergedLabel:'{a} 和 {b} 一共 = {sum}！',
        brilliant:'太棒了！',
        sectionCompleteSub:'你在<strong>{activity}</strong>中答对了 <strong>{target} 题</strong>！太厉害了！🎉',
        sectionCompletePrompt:'你想继续学习 <strong>{nextLabel}</strong>，还是前往 <strong>第3关</strong>（数字齿轮），或者再练习一次 <strong>{activity}</strong>？',
        goToLevel3:'⭐ 前往第3关 — 数字齿轮', tryNext:'试试{nextLabel} →', tryAgain:'再试一次 🔁',
        actNameCounters:'数数物体', actNameAddition:'加法', actNameSubtraction:'减法',
        actLabelAddition:'加法 ➕', actLabelSubtraction:'减法 ➖', actLabelCounters:'数数 🔢'
    }
};

// Number words 0–20, used in the visible (not spoken) Counters feedback text.
const NUM_WORDS_BY_LANG = {
    en: ['zero','one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen','twenty'],
    de: ['null','eins','zwei','drei','vier','fünf','sechs','sieben','acht','neun','zehn','elf','zwölf','dreizehn','vierzehn','fünfzehn','sechzehn','siebzehn','achtzehn','neunzehn','zwanzig'],
    fr: ['zéro','un','deux','trois','quatre','cinq','six','sept','huit','neuf','dix','onze','douze','treize','quatorze','quinze','seize','dix-sept','dix-huit','dix-neuf','vingt'],
    ar: ['صفر','واحد','اثنان','ثلاثة','أربعة','خمسة','ستة','سبعة','ثمانية','تسعة','عشرة','أحد عشر','اثنا عشر','ثلاثة عشر','أربعة عشر','خمسة عشر','ستة عشر','سبعة عشر','ثمانية عشر','تسعة عشر','عشرون'],
    zh: ['零','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十']
};
function numWordLocalized(n) {
    const words = NUM_WORDS_BY_LANG[NG_LevelI18n.lang()] || NUM_WORDS_BY_LANG.en;
    return words[n] || String(n);
}

// 12 countable items: {en:{s,p}, de:{s,p}, fr:{s,p}, ar:'single form', zh:'single form'}
const ITEM_NAMES = [
    { emoji:'🍌', en:{s:'banana',p:'bananas'}, de:{s:'Banane',p:'Bananen'}, fr:{s:'banane',p:'bananes'}, ar:'موز', zh:'香蕉' },
    { emoji:'🍎', en:{s:'apple',p:'apples'}, de:{s:'Apfel',p:'Äpfel'}, fr:{s:'pomme',p:'pommes'}, ar:'تفاح', zh:'苹果' },
    { emoji:'🍊', en:{s:'orange',p:'oranges'}, de:{s:'Orange',p:'Orangen'}, fr:{s:'orange',p:'oranges'}, ar:'برتقال', zh:'橙子' },
    { emoji:'🍇', en:{s:'grape',p:'grapes'}, de:{s:'Traube',p:'Trauben'}, fr:{s:'raisin',p:'raisins'}, ar:'عنب', zh:'葡萄' },
    { emoji:'🍓', en:{s:'strawberry',p:'strawberries'}, de:{s:'Erdbeere',p:'Erdbeeren'}, fr:{s:'fraise',p:'fraises'}, ar:'فراولة', zh:'草莓' },
    { emoji:'🧸', en:{s:'teddy bear',p:'teddy bears'}, de:{s:'Teddybär',p:'Teddybären'}, fr:{s:'ours en peluche',p:'ours en peluche'}, ar:'دبدوب', zh:'玩具熊' },
    { emoji:'🎈', en:{s:'balloon',p:'balloons'}, de:{s:'Luftballon',p:'Luftballons'}, fr:{s:'ballon',p:'ballons'}, ar:'بالون', zh:'气球' },
    { emoji:'⭐', en:{s:'star',p:'stars'}, de:{s:'Stern',p:'Sterne'}, fr:{s:'étoile',p:'étoiles'}, ar:'نجمة', zh:'星星' },
    { emoji:'🌸', en:{s:'flower',p:'flowers'}, de:{s:'Blume',p:'Blumen'}, fr:{s:'fleur',p:'fleurs'}, ar:'زهرة', zh:'花' },
    { emoji:'🐱', en:{s:'cat',p:'cats'}, de:{s:'Katze',p:'Katzen'}, fr:{s:'chat',p:'chats'}, ar:'قطة', zh:'猫' },
    { emoji:'🐶', en:{s:'dog',p:'dogs'}, de:{s:'Hund',p:'Hunde'}, fr:{s:'chien',p:'chiens'}, ar:'كلب', zh:'狗' },
    { emoji:'🦋', en:{s:'butterfly',p:'butterflies'}, de:{s:'Schmetterling',p:'Schmetterlinge'}, fr:{s:'papillon',p:'papillons'}, ar:'فراشة', zh:'蝴蝶' },
];
function itemNameLocalized(item, count) {
    const lang = NG_LevelI18n.lang();
    const entry = item[lang] || item.en;
    if (typeof entry === 'string') return entry;        // ar / zh — single form
    return count === 1 ? entry.s : entry.p;              // en / de / fr — singular/plural
}
function itemNamePluralEn(item) { return item.en.p; }    // kept for the few spots that still want the English-only plural (storage/back-compat)

/* ================================================================
   NUMBER TO WORD HELPER
================================================================ */
const NUM_WORDS = ['zero','one','two','three','four','five','six','seven',
                   'eight','nine','ten','eleven','twelve','thirteen',
                   'fourteen','fifteen','sixteen','seventeen','eighteen',
                   'nineteen','twenty'];
function numWord(n) { return NUM_WORDS[n] || String(n); }

/* ================================================================
   OBJECT SETS
================================================================ */
const FRUITS = ['🍎','🍊','🍋','🍇','🍓','🍑','🍍','🥝','🍒','🍌'];
const TOYS   = ['🧸','🎯','🎈','🪀','🎠','🎲','🎮','🪁','🏈','⚽'];

let currentActivity  = 'addition';
let difficulty       = 'easy';
let correctScore     = 0;
let streak           = 0;
let sectionCorrect   = 0;   // correct answers in current section (resets per section)
const SECTION_TARGET = 10;  // complete 10 correct to trigger celebration
let questionActive  = false;
let currentAnswer   = 0;
let currentA        = 0;
let currentB        = 0;
let currentEmoji    = '';

/* ================================================================
   DIFFICULTY RANGES
================================================================ */
function getRange() {
    switch(difficulty) {
        case 'easy':   return { min: 1, max: 5 };
        case 'medium': return { min: 1, max: 10 };
        case 'hard':   return { min: 5, max: 15 };
    }
}

/* ================================================================
   NEW QUESTION
================================================================ */
function newQuestion() {
    questionActive = false;
    document.getElementById('nextBtn').style.display = 'none';
    document.getElementById('answerReveal').style.display = 'none';
    document.getElementById('l2Feedback').textContent = '';
    document.getElementById('eqSign').textContent = '= ?';
    document.getElementById('eqSign').style.color = '';
    document.getElementById('eqSign').style.fontSize = '';
    hideWrongBanner();

    if (currentActivity === 'counters') {
        newCounterQuestion();
        return;
    }

    document.getElementById('eqRow').style.display = 'flex';

    const range = getRange();
    let a = rand(range.min, range.max);
    let b = rand(range.min, range.max);

    if (currentActivity === 'subtraction') {
        // Guarantee a >= b so answer is never negative
        if (a <= b) { const tmp = a; a = Math.max(b, tmp) + rand(0,2); b = Math.min(b, tmp); }
        if (a === b) a = b + rand(1, 3);   // avoid zero answer
        a = Math.min(a, range.max);
        b = Math.min(b, a - 1);
        currentAnswer = a - b;             // always positive
        document.getElementById('actInstruction').textContent = NG_LevelI18n.t(L2, 'howManyLeft');
        document.getElementById('opSign').textContent = '−';
    } else {
        currentAnswer = a + b;
        document.getElementById('actInstruction').textContent = NG_LevelI18n.t(L2, 'howManyTogether');
        document.getElementById('opSign').textContent = '+';
    }

    currentA = a;
    currentB = b;
    document.getElementById('numA').textContent = a;
    document.getElementById('numB').textContent = b;

    const objSet   = Math.random() < 0.5 ? FRUITS : TOYS;
    currentEmoji   = objSet[Math.floor(Math.random() * objSet.length)];

    buildStage(a, b, currentEmoji);
    buildChoices(currentAnswer);

    if (currentActivity === 'addition') {
        NG_Speech.sayInstruction(`${a} and ${b}. How many all together?`);
    } else {
        NG_Speech.sayInstruction(`${a} take away ${b}. How many are left?`);
    }

    questionActive = true;
}

/* ================================================================
   COUNTERS ACTIVITY — show N objects, match to the right number
================================================================ */
let counterItem = null;
let counterCount = 0;

function newCounterQuestion() {
    document.getElementById('eqRow').style.display = 'none';

    const range = getRange();
    counterCount = rand(range.min, range.max);
    counterItem  = ITEM_NAMES[Math.floor(Math.random() * ITEM_NAMES.length)];
    currentAnswer = counterCount;

    // Instruction
    document.getElementById('actInstruction').textContent =
        NG_LevelI18n.t(L2, 'countMatch', { item: itemNameLocalized(counterItem, counterCount) });

    // Build stage — just one group of N emojis
    const stage = document.getElementById('counterStage');
    stage.innerHTML = '';

    const grp = document.createElement('div');
    grp.style.cssText = 'display:flex;flex-wrap:wrap;gap:10px;justify-content:center;max-width:320px;';
    for (let i = 0; i < counterCount; i++) {
        const span = document.createElement('span');
        span.className  = 'obj-emoji';
        span.style.fontSize = '40px';
        span.textContent    = counterItem.emoji;
        grp.appendChild(span);
    }
    stage.appendChild(grp);

    buildChoices(counterCount);
    const pluralEn = itemNamePluralEn(counterItem);
    NG_Speech.sayInstruction(`How many ${pluralEn} do you see? Find the matching number!`);
    questionActive = true;
}

/* ================================================================
   BUILD STAGE (objects)
================================================================ */
function buildStage(a, b, emoji) {
    const stage = document.getElementById('counterStage');
    stage.innerHTML = '';

    if (currentActivity === 'addition') {
        const grpA = makeGroup(a, emoji, false, 'grpA');
        const op   = document.createElement('div');
        op.className  = 'op-sign';
        op.textContent = '+';
        const grpB = makeGroup(b, emoji, false, 'grpB');
        const eq   = document.createElement('div');
        eq.textContent = '=';
        eq.className = 'eq-sign';
        const qmark = document.createElement('div');
        qmark.style.cssText = 'font-size:44px;font-weight:900;color:var(--text-soft);';
        qmark.id = 'stageQmark';
        qmark.textContent = '?';

        stage.append(grpA, op, grpB, eq, qmark);

    } else {
        const total = document.createElement('div');
        total.style.cssText = 'display:flex;flex-direction:column;align-items:center;gap:6px;';

        const grp = makeGroup(a, emoji, false, 'subGroup');
        total.appendChild(grp);

        const removeNote = document.createElement('div');
        removeNote.style.cssText = 'font-size:13px;font-weight:700;color:var(--peach-dark);margin-top:4px;';
        removeNote.textContent = NG_LevelI18n.t(L2, 'removeN', { n: b });
        total.appendChild(removeNote);

        stage.appendChild(total);
    }
}

function makeGroup(count, emoji, small, groupId) {
    const grp = document.createElement('div');
    grp.className = 'obj-group';
    if (groupId) grp.id = groupId;

    for (let i = 0; i < count; i++) {
        const span = document.createElement('span');
        span.className = 'obj-emoji';
        span.style.fontSize = small ? '24px' : '32px';
        span.textContent = emoji;
        grp.appendChild(span);
    }
    return grp;
}

/* ================================================================
   COUNTER CORRECT — glow all emoji items to confirm the count
================================================================ */
function animateCounterCorrect() {
    const stage = document.getElementById('counterStage');
    const items = stage.querySelectorAll('.obj-emoji');
    items.forEach((el, i) => {
        setTimeout(() => {
            el.style.transition = 'transform 0.2s ease, filter 0.2s ease';
            el.style.transform  = 'scale(1.25)';
            el.style.filter     = 'drop-shadow(0 0 6px #52c4a0)';
            setTimeout(() => {
                el.style.transform = 'scale(1)';
                el.style.filter    = '';
            }, 350);
        }, i * 80);
    });

    // After glow, show a clear count label below the items
    const totalDelay = items.length * 80 + 500;
    setTimeout(() => {
        const existing = stage.querySelector('.counter-result-label');
        if (existing) existing.remove();
        const label = document.createElement('div');
        label.className = 'counter-result-label';
        label.style.cssText = 'font-size:18px;font-weight:900;color:var(--mint-dark);margin-top:10px;text-align:center;';
        label.textContent = NG_LevelI18n.t(L2, 'counterResultLabel', { n: counterCount, item: itemNameLocalized(counterItem, counterCount) });
        stage.appendChild(label);
    }, totalDelay);
}

/* ================================================================
   ADDITION DEMO — merge group B into group A
================================================================ */
function animateAddition(a, b, emoji) {
    const grpB = document.getElementById('grpB');
    if (!grpB) return;

    // Slide grpB items toward grpA one by one
    const items = Array.from(grpB.children);
    items.forEach((el, i) => {
        setTimeout(() => el.classList.add('merging'), i * 120);
    });

    // After all items gone, replace stage with merged group
    const totalDelay = items.length * 120 + 600;
    setTimeout(() => {
        const stage = document.getElementById('counterStage');
        stage.innerHTML = '';

        const mergedWrap = document.createElement('div');
        mergedWrap.style.cssText = 'display:flex;flex-direction:column;align-items:center;gap:10px;';

        const merged = document.createElement('div');
        merged.className = 'merged-group';
        for (let i = 0; i < a + b; i++) {
            const span = document.createElement('span');
            span.className = 'obj-emoji';
            span.style.fontSize = '32px';
            span.textContent = emoji;
            merged.appendChild(span);
        }

        const label = document.createElement('div');
        label.style.cssText = 'font-size:14px;font-weight:700;color:var(--mint-dark);';
        label.textContent = NG_LevelI18n.t(L2, 'mergedLabel', { a, b, sum: a + b });

        mergedWrap.appendChild(merged);
        mergedWrap.appendChild(label);
        stage.appendChild(mergedWrap);
    }, totalDelay);
}

/* ================================================================
   BUILD CHOICES
================================================================ */
function buildChoices(correct) {
    const row = document.getElementById('choiceRow');
    row.innerHTML = '';

    const opts = genChoices(correct);
    opts.forEach(n => {
        const btn = document.createElement('button');
        btn.className   = 'choice-btn';
        btn.textContent = n;
        btn.onclick     = () => checkChoice(n, btn, correct);
        row.appendChild(btn);
    });
}

function genChoices(correct) {
    const set = new Set([correct]);
    const maxVal = (difficulty === 'hard') ? 20 : (difficulty === 'medium') ? 15 : 10;
    // For subtraction the answer is always small — keep distractors nearby and non-negative
    const isSubtraction = (currentActivity === 'subtraction');
    let attempts = 0;

    while (set.size < 4 && attempts < 50) {
        attempts++;
        let r = correct + rand(-3, 3);
        if (r < 0) r = rand(0, 2);
        if (isSubtraction && r > currentA) continue;  // distractor can't exceed the starting number
        if (r < 0 || r > maxVal) continue;
        set.add(r);
    }
    // Fallback: fill any remaining with safe values
    let fill = 0;
    while (set.size < 4) { if (!set.has(fill) && fill <= maxVal) set.add(fill); fill++; }
    return Array.from(set).sort(() => Math.random() - 0.5);
}

/* ================================================================
   CHECK ANSWER
================================================================ */
function checkChoice(selected, btn, correct) {
    if (!questionActive) return;
    questionActive = false;
    hideWrongBanner();

    document.querySelectorAll('.choice-btn').forEach(b => b.disabled = true);

    if (selected === correct) {
        btn.classList.add('correct');
        correctScore++;
        streak++;
        sectionCorrect++;
        updateScores();

        if (currentActivity === 'subtraction') {
            animateRemoval(parseInt(document.getElementById('numB').textContent));
        } else if (currentActivity === 'addition') {
            animateAddition(currentA, currentB, currentEmoji);
        } else {
            // counters — just highlight all items with a glow, no merge animation
            animateCounterCorrect();
        }

        showAnswerReveal(correct);

        if (currentActivity === 'counters') {
            const plural = correct === 1 ? counterItem.en.s : counterItem.en.p;
            NG_Speech.sayInstruction(`Very good! There are ${numWord(correct)} ${plural} all together.`);
        } else if (currentActivity === 'addition') {
            NG_Speech.sayAddition(
                parseInt(document.getElementById('numA').textContent),
                parseInt(document.getElementById('numB').textContent),
                correct
            );
        } else {
            NG_Speech.saySubtraction(
                parseInt(document.getElementById('numA').textContent),
                parseInt(document.getElementById('numB').textContent),
                correct
            );
        }

        if (currentActivity === 'counters') {
            showToast(NG_LevelI18n.t(L2, 'toastCountersCorrect', { n: numWordLocalized(correct), item: itemNameLocalized(counterItem, correct) }), 'success');
        } else {
            showToast(NG_LevelI18n.t(L2, 'toastCorrect', { n: correct }), 'success');
        }
        const newScore = Math.min(100, NG_Storage.getLvl2Score() + 5);
        NG_Storage.setLvl2Score(newScore);

        if (sectionCorrect >= SECTION_TARGET) {
            setTimeout(() => showSectionCompletion(), 900);
        } else {
            document.getElementById('nextBtn').style.display = 'inline-flex';
        }

    } else {
        btn.classList.add('wrong');
        streak = 0;
        updateScores();

        // Highlight correct button
        document.querySelectorAll('.choice-btn').forEach(b => {
            if (parseInt(b.textContent) === correct) b.classList.add('correct');
        });

        // Show prominent wrong banner
        showWrongBanner(correct);

        if (currentActivity === 'counters') {
            const plural = correct === 1 ? counterItem.en.s : counterItem.en.p;
            NG_Speech.sayInstruction(`Sorry, try again. There are ${numWord(correct)} ${plural}.`);
            showToast(NG_LevelI18n.t(L2, 'toastCountersWrong', { n: numWordLocalized(correct), item: itemNameLocalized(counterItem, correct) }), 'error');
        } else {
            NG_Speech.sayWrong(correct);
            showToast(NG_LevelI18n.t(L2, 'toastWrong', { n: correct }), 'error');
        }

        setTimeout(() => {
            document.querySelectorAll('.choice-btn').forEach(b => {
                b.classList.remove('correct', 'wrong');
                b.disabled = false;
            });
            hideWrongBanner();
            questionActive = true;
        }, 2800);
    }
}

/* ================================================================
   WRONG BANNER
================================================================ */
function showWrongBanner(correct) {
    const banner = document.getElementById('wrongBanner');
    banner.classList.add('show');
    if (currentActivity === 'counters') {
        document.getElementById('wrongHint').textContent =
            NG_LevelI18n.t(L2, 'wrongHintCounters', { emoji: counterItem.emoji, n: numWordLocalized(correct), item: itemNameLocalized(counterItem, correct) });
    } else {
        document.getElementById('wrongHint').textContent =
            NG_LevelI18n.t(L2, 'wrongHintMath', { n: correct });
    }
}

function hideWrongBanner() {
    document.getElementById('wrongBanner').classList.remove('show');
    document.getElementById('wrongHint').textContent = '';
}

/* ================================================================
   SUBTRACTION ANIMATION
================================================================ */
function animateRemoval(removeCount) {
    const grp = document.getElementById('subGroup');
    if (!grp) return;
    const children = Array.from(grp.children);
    const toRemove = children.slice(0, removeCount);
    toRemove.forEach((el, i) => {
        setTimeout(() => el.classList.add('removing'), i * 150);
    });
}

function showAnswerReveal(n) {
    const el = document.getElementById('answerReveal');
    el.innerHTML = '<div class="answer-reveal">' + n + '</div>';
    el.style.display = 'block';
    if (currentActivity !== 'counters') {
        // Update the dedicated span — never mutate innerHTML
        const eqSign = document.getElementById('eqSign');
        eqSign.textContent = '= ' + n;
        eqSign.style.color    = 'var(--mint)';
        eqSign.style.fontSize = '38px';
    }
}

/* ================================================================
   SCORES & DIFFICULTY
================================================================ */
function updateScores() {
    document.getElementById('scoreCorrect').textContent = correctScore;
    document.getElementById('scoreStreak').textContent  = streak;
}

function setDifficulty(d) {
    difficulty = d;
    document.querySelectorAll('.diff-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('diff' + d.charAt(0).toUpperCase() + d.slice(1)).classList.add('active');
    newQuestion();
}

function switchActivity(act) {
    currentActivity = act;
    sectionCorrect  = 0;
    NG_Storage.setLvl2Activity(act);
    document.getElementById('tabCount').classList.toggle('active', act === 'counters');
    document.getElementById('tabAdd').classList.toggle('active', act === 'addition');
    document.getElementById('tabSub').classList.toggle('active', act === 'subtraction');
    NG_Speech.stop();
    newQuestion();
}

/* ================================================================
   TOAST & UTILS
================================================================ */
function rand(min, max) { return Math.floor(Math.random() * (max - min + 1)) + min; }

let _toastTimer = null;
function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = 'feedback-toast show' + (type ? ' ' + type : '');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => t.classList.remove('show'), 2500);
}

/* ================================================================
   SECTION COMPLETION + CONFETTI
================================================================ */
(function() {
    const EMOJIS = ['🌸','🌺','🌼','⭐','✨','🌟','💛','🌷','🎉','🏆','🎀','🎊','💫','🥳'];
    let timer = null, frameCount = 0;

    function makeP(w, h) {
        return {
            x: Math.random() * w, y: -30 - Math.random() * 150,
            size: 22 + Math.random() * 20,
            emoji: EMOJIS[Math.floor(Math.random() * EMOJIS.length)],
            speed: 2.5 + Math.random() * 3.5,
            drift: (Math.random() - 0.5) * 2.5,
            swing: Math.random() * 0.04, swingOff: Math.random() * Math.PI * 2,
            spin: (Math.random() - 0.5) * 0.1, angle: Math.random() * Math.PI * 2,
            alpha: 1,
        };
    }

    window.launchConfetti2 = function() {
        const cvs = document.getElementById('confettiCanvas2');
        cvs.style.display = 'block';
        cvs.width = window.innerWidth; cvs.height = window.innerHeight;
        const ctx = cvs.getContext('2d');
        const ps  = Array.from({length: 80}, (_, i) => {
            const p = makeP(cvs.width, cvs.height);
            p.y = -30 - i * 8;
            return p;
        });
        frameCount = 0;
        clearInterval(timer);
        timer = setInterval(() => {
            ctx.clearRect(0, 0, cvs.width, cvs.height);
            frameCount++;
            let alive = 0;
            for (const p of ps) {
                p.y += p.speed;
                p.x += p.drift + Math.sin(frameCount * p.swing + p.swingOff) * 0.8;
                p.angle += p.spin;
                if (p.y > cvs.height * 0.75) p.alpha = Math.max(0, 1 - (p.y - cvs.height * 0.75) / (cvs.height * 0.28));
                if (p.y < cvs.height + 50 && p.alpha > 0.02) {
                    alive++;
                    ctx.save();
                    ctx.globalAlpha = p.alpha;
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.angle);
                    ctx.font = p.size + 'px serif';
                    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
                    ctx.fillText(p.emoji, 0, 0);
                    ctx.restore();
                }
            }
            if (frameCount < 300) {
                for (const p of ps) {
                    if (p.y > cvs.height + 40) Object.assign(p, makeP(cvs.width, cvs.height));
                }
            }
            if (frameCount > 300 && alive === 0) {
                clearInterval(timer);
                ctx.clearRect(0, 0, cvs.width, cvs.height);
                cvs.style.display = 'none';
            }
        }, 16);
    };

    window.stopConfetti2 = function() {
        clearInterval(timer);
        const cvs = document.getElementById('confettiCanvas2');
        if (cvs) { cvs.getContext('2d').clearRect(0, 0, cvs.width, cvs.height); cvs.style.display = 'none'; }
    };
})();

function showSectionCompletion() {
    const actNames = { counters: NG_LevelI18n.t(L2, 'actNameCounters'), addition: NG_LevelI18n.t(L2, 'actNameAddition'), subtraction: NG_LevelI18n.t(L2, 'actNameSubtraction') };
    const actNext  = { counters: 'addition', addition: 'subtraction', subtraction: 'counters' };
    const actLabel = { counters: NG_LevelI18n.t(L2, 'actLabelAddition'), addition: NG_LevelI18n.t(L2, 'actLabelSubtraction'), subtraction: NG_LevelI18n.t(L2, 'actLabelCounters') };

    const overlay = document.getElementById('lvl2CompletionOverlay');
    overlay.style.display = 'flex';

    document.getElementById('lvl2CompEmoji').textContent = '🌟';
    document.getElementById('lvl2CompTitle').textContent = NG_LevelI18n.t(L2, 'brilliant');
    document.getElementById('lvl2CompSub').innerHTML =
        NG_LevelI18n.t(L2, 'sectionCompleteSub', { target: SECTION_TARGET, activity: actNames[currentActivity] });

    const nextAct   = actNext[currentActivity];
    const nextLabel = actLabel[nextAct];

    document.getElementById('lvl2CompPrompt').innerHTML =
        NG_LevelI18n.t(L2, 'sectionCompletePrompt', { nextLabel, activity: actNames[currentActivity] });

    // Next level button
    document.getElementById('lvl2NextLevelBtn').textContent = NG_LevelI18n.t(L2, 'goToLevel3');
    document.getElementById('lvl2NextLevelBtn').onclick = () => {
        closeSectionCompletion();
        window.location.href = '../level3/index.php';
    };

    // Next section button
    document.getElementById('lvl2NextSectionBtn').textContent = NG_LevelI18n.t(L2, 'tryNext', { nextLabel });
    document.getElementById('lvl2NextSectionBtn').onclick = () => {
        closeSectionCompletion();
        switchActivity(nextAct);
    };

    // Try again button
    document.getElementById('lvl2TryAgainBtn').textContent = NG_LevelI18n.t(L2, 'tryAgain');
    document.getElementById('lvl2TryAgainBtn').onclick = () => {
        closeSectionCompletion();
        sectionCorrect = 0;
        newQuestion();
    };

    launchConfetti2();
    NG_Speech.sayInstruction(
        `Brilliant! You got ${SECTION_TARGET} correct in ${actNames[currentActivity]}! ` +
        `You can move to Level 3, try ${actNames[nextAct]}, or practise again!`
    );
}

function closeSectionCompletion() {
    document.getElementById('lvl2CompletionOverlay').style.display = 'none';
    stopConfetti2();
    sectionCorrect = 0;
}

/* ================================================================
   INIT
================================================================ */
document.addEventListener('DOMContentLoaded', function () {
    NG_LevelI18n.applyStatic(L2);
    const saved = NG_Storage.getLvl2Activity();
    if (saved === 'subtraction') switchActivity('subtraction');
    else newQuestion();

    setTimeout(() => NG_Speech.sayInstruction('Welcome to Level 2! Count the objects and choose the right answer!'), 600);
});
</script>
</body>
</html>
