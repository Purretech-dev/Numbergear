<?php
// Number Gear — Level 1: Number Recognition
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level 1 — Number Recognition | Number Gear</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<canvas id="confettiCanvas" style="position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999;display:none;"></canvas>
<div class="app-shell">

    <!-- Header -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">🔢</div>
            <div>
                <h1>Level 1</h1>
                <p>Number Recognition</p>
            </div>
        </div>
        <a href="../../index.php" class="back-btn">← Home</a>
    </header>

    <main class="level-page">

        <!-- Mode tabs -->
        <div class="tab-bar" id="modeTabs">
            <button class="tab-btn active" onclick="showMode('map')">📖 Number Map</button>
            <button class="tab-btn" onclick="showMode('quiz')">🎯 Identification Quiz</button>
        </div>

        <!-- ============================================================
             MODE A: NUMBER MAP
        ============================================================ -->
        <div id="modeMap">
            <div class="section-card">

                <div class="section-title">Tap a number to hear it!</div>

                <!-- Batch tabs -->
                <div class="tab-bar" id="batchTabs" style="margin-bottom:16px;">
                    <button class="tab-btn active" onclick="showBatch(1)">1–20</button>
                    <button class="tab-btn" onclick="showBatch(2)">21–40</button>
                    <button class="tab-btn" onclick="showBatch(3)">41–60</button>
                    <button class="tab-btn" onclick="showBatch(4)">61–80</button>
                    <button class="tab-btn" onclick="showBatch(5)">81–100</button>
                </div>

                <!-- Number grid -->
                <div class="number-grid" id="numGrid"></div>

                <!-- Learned count -->
                <div class="learned-banner">
                    <strong id="learnedCount">0</strong> of 100 numbers learned ✓
                </div>

            </div>

            <!-- Nav buttons -->
            <div style="display:flex;gap:10px;justify-content:center;">
                <button class="btn btn-outline btn-sm" id="prevBatchBtn" onclick="prevBatch()" disabled>← Previous</button>
                <button class="btn btn-purple btn-sm" id="nextBatchBtn" onclick="nextBatch()">Next batch →</button>
            </div>
        </div>

        <!-- ============================================================
             MODE B: IDENTIFICATION QUIZ
        ============================================================ -->
        <div id="modeQuiz" style="display:none;">

            <!-- Batch indicator -->
            <div id="batchIndicator" style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;justify-content:center;"></div>

            <!-- Progress bar -->
            <div class="quiz-progress">
                <div class="quiz-progress-track">
                    <div class="quiz-progress-fill" id="quizFill"></div>
                </div>
                <span class="quiz-progress-label" id="quizLabel">0 / 10</span>
            </div>

            <!-- Question card -->
            <div class="quiz-card" id="quizCard">
                <div class="quiz-prompt">What number is this?</div>
                <div class="quiz-big-number" id="quizNum">–</div>
                <div class="quiz-options" id="quizOpts"></div>
            </div>

            <!-- Batch completion screen -->
            <div class="completion-screen" id="completionScreen">
                <div class="completion-inner">
                    <div class="completion-emoji" id="completionEmoji">🎉</div>
                    <div class="completion-title" id="completionTitle">Well Done!</div>
                    <div class="completion-sub" id="completionSub">You identified all 10 numbers!</div>
                    <div id="narratorQuestion" style="display:none;font-size:15px;font-weight:700;color:var(--purple-dark);background:var(--purple-light);border-radius:12px;padding:12px 14px;margin-bottom:14px;line-height:1.6;"></div>
                    <div class="btn-row" id="completionBtns" style="flex-direction:column;gap:10px;">
                        <button class="btn btn-purple" id="nextBatchBtn2" onclick="proceedNextBatch()" style="width:100%;padding:14px;font-size:16px;">Next batch →</button>
                        <button class="btn btn-outline" id="practiseAgainBtn" onclick="practiseSameBatch()" style="width:100%;padding:12px;">Practise again 🔁</button>
                        <button class="btn btn-outline btn-sm" onclick="showMode('map')" style="width:100%;">Back to Map</button>
                    </div>
                </div>
            </div>

        </div>

    </main>
</div>

<!-- Toast -->
<div class="feedback-toast" id="toast"></div>

<script src="../../assets/js/speech.js"></script>
<script src="../../assets/js/storage.js"></script>
<script>
/* ================================================================
   NUMBER MAP
================================================================ */

let currentBatch = 1;

function showBatch(batch) {
    currentBatch = batch;

    // Update batch tab highlights
    document.querySelectorAll('#batchTabs .tab-btn').forEach((btn, i) => {
        btn.classList.toggle('active', i + 1 === batch);
    });

    // Update prev/next buttons
    document.getElementById('prevBatchBtn').disabled = (batch === 1);
    document.getElementById('nextBatchBtn').disabled = (batch === 5);

    renderGrid();
}

function prevBatch() { if (currentBatch > 1) showBatch(currentBatch - 1); }
function nextBatch() { if (currentBatch < 5) showBatch(currentBatch + 1); }

function renderGrid() {
    const grid  = document.getElementById('numGrid');
    const start = (currentBatch - 1) * 20 + 1;
    const end   = start + 19;
    grid.innerHTML = '';

    for (let n = start; n <= end; n++) {
        const btn = document.createElement('button');
        btn.className = 'number-btn' + (NG_Storage.isLearned(n) ? ' learned' : '');
        btn.textContent = n;
        btn.setAttribute('data-n', n);
        btn.onclick = () => tapNumber(n, btn);
        grid.appendChild(btn);
    }

    updateLearnedCount();
}

let speakingBtn = null;

function tapNumber(n, btn) {
    // Mark learned
    NG_Storage.markLearned(n);
    btn.classList.add('learned');

    // Visual speaking feedback
    if (speakingBtn) speakingBtn.classList.remove('speaking');
    speakingBtn = btn;
    btn.classList.add('speaking');

    NG_Speech.sayNumber(n);

    setTimeout(() => {
        if (btn) btn.classList.remove('speaking');
    }, 1200);

    updateLearnedCount();
}

function updateLearnedCount() {
    document.getElementById('learnedCount').textContent = NG_Storage.getLearnedNums().length;
}

/* ================================================================
   IDENTIFICATION QUIZ — batch mode (10 numbers per batch)
================================================================ */

const BATCH_SIZE   = 10;       // numbers per batch
const TOTAL_NUMS   = 100;
const TOTAL_BATCHES = TOTAL_NUMS / BATCH_SIZE;  // 10 batches

let quizBatch   = 1;           // current batch (1–10)
let lessonPool  = [];          // numbers left in this pass
let currentNum  = null;
let answering   = false;
let batchErrors = 0;           // mistakes made in this batch pass

function batchRange(batch) {
    // returns { start, end } both inclusive, 1-based
    return { start: (batch - 1) * BATCH_SIZE + 1, end: batch * BATCH_SIZE };
}

function renderBatchIndicator() {
    const wrap = document.getElementById('batchIndicator');
    if (!wrap) return;
    wrap.innerHTML = '';
    for (let b = 1; b <= TOTAL_BATCHES; b++) {
        const dot = document.createElement('div');
        const { start, end } = batchRange(b);
        dot.title = start + '–' + end;
        dot.style.cssText = [
            'width:22px', 'height:22px', 'border-radius:50%',
            'font-size:9px', 'font-weight:800',
            'display:flex', 'align-items:center', 'justify-content:center',
            'cursor:pointer', 'transition:0.2s ease',
            b === quizBatch
                ? 'background:var(--purple);color:white;box-shadow:0 2px 6px rgba(124,111,205,0.4);'
                : b < quizBatch
                    ? 'background:var(--mint-light);color:var(--mint-dark);border:1.5px solid var(--mint);'
                    : 'background:var(--surface);color:var(--text-soft);border:1.5px solid var(--border);'
        ].join(';');
        dot.textContent = b;
        dot.onclick = () => {
            if (b !== quizBatch) startBatch(b);
        };
        wrap.appendChild(dot);
    }
}

function startBatch(batch) {
    quizBatch   = batch;
    batchErrors = 0;

    const { start, end } = batchRange(batch);
    const nums = Array.from({ length: BATCH_SIZE }, (_, i) => start + i);

    // Shuffle
    lessonPool = nums.sort(() => Math.random() - 0.5);

    document.getElementById('completionScreen').classList.remove('show');
    document.getElementById('quizCard').style.display = '';

    renderBatchIndicator();
    updateQuizProgress();
    nextQuestion();

    const rangeLabel = start + ' to ' + end;
    NG_Speech.sayInstruction('Batch ' + batch + '. Numbers ' + rangeLabel + '. What number is this?');
}

// Alias so existing callers still work
function startLesson() { startBatch(quizBatch); }

function nextQuestion() {
    if (lessonPool.length === 0) {
        showBatchCompletion();
        return;
    }

    answering  = false;
    currentNum = lessonPool.shift();

    const numEl = document.getElementById('quizNum');
    numEl.style.opacity   = '0';
    numEl.style.transform = 'scale(0.8)';

    setTimeout(() => {
        numEl.textContent    = currentNum;
        numEl.style.opacity  = '1';
        numEl.style.transform = 'scale(1)';
        numEl.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
    }, 150);

    updateQuizProgress();
    renderOptions(currentNum);
    setTimeout(() => NG_Speech.sayInstruction('What number is this?'), 400);
}

function renderOptions(correct) {
    const { start, end } = batchRange(quizBatch);
    const opts   = generateOptions(correct, start, end);
    const optsEl = document.getElementById('quizOpts');
    optsEl.innerHTML = '';

    opts.forEach(n => {
        const btn = document.createElement('button');
        btn.className   = 'option-btn';
        btn.textContent = n;
        btn.onclick     = () => checkAnswer(n, btn, correct);
        optsEl.appendChild(btn);
    });
}

function generateOptions(correct, rangeStart, rangeEnd) {
    // All distractors come from the SAME batch so learners
    // are always choosing within familiar numbers
    const set = new Set([correct]);
    const batchNums = Array.from({ length: BATCH_SIZE }, (_, i) => rangeStart + i)
                          .filter(n => n !== correct);

    // Shuffle batch and take 3 distractors
    const shuffled = batchNums.sort(() => Math.random() - 0.5);
    for (let i = 0; i < 3 && i < shuffled.length; i++) set.add(shuffled[i]);

    // If batch is too small (shouldn't happen with size 10), fill from nearby
    while (set.size < 4) {
        let r = Math.max(1, Math.min(TOTAL_NUMS, correct + Math.floor(Math.random() * 10) - 5));
        set.add(r);
    }

    return Array.from(set).sort(() => Math.random() - 0.5);
}

function checkAnswer(selected, btn, correct) {
    if (answering) return;
    answering = true;

    document.querySelectorAll('.option-btn').forEach(b => b.disabled = true);

    if (selected === correct) {
        btn.classList.add('correct');
        NG_Speech.sayInstruction(`Very good! That is number ${correct}.`);
        NG_Storage.markIdentified(correct);
        showToast('✓ Correct! That is ' + correct, 'success');
        setTimeout(nextQuestion, 1400);
    } else {
        batchErrors++;
        btn.classList.add('wrong');
        document.querySelectorAll('.option-btn').forEach(b => {
            if (parseInt(b.textContent) === correct) b.classList.add('correct');
        });
        NG_Speech.sayInstruction(`Sorry, try again. That is number ${correct}.`);
        showToast('The answer is ' + correct, 'error');

        setTimeout(() => {
            document.querySelectorAll('.option-btn').forEach(b => {
                b.classList.remove('correct', 'wrong');
                b.disabled = false;
            });
            answering = false;
        }, 1800);
    }
}

function updateQuizProgress() {
    const done = BATCH_SIZE - lessonPool.length;
    const pct  = (done / BATCH_SIZE) * 100;
    const { start, end } = batchRange(quizBatch);
    document.getElementById('quizFill').style.width  = pct + '%';
    document.getElementById('quizLabel').textContent =
        done + ' / ' + BATCH_SIZE + '  (Batch ' + quizBatch + ': ' + start + '–' + end + ')';
}

function showBatchCompletion() {
    document.getElementById('quizCard').style.display = 'none';
    document.getElementById('completionScreen').classList.add('show');

    const { start, end } = batchRange(quizBatch);
    const isLastBatch    = quizBatch >= TOTAL_BATCHES;
    const didWell        = batchErrors === 0;

    // Title & emoji
    document.getElementById('completionEmoji').textContent = didWell ? '🌟' : '🎉';
    document.getElementById('completionTitle').textContent = didWell ? 'Perfect!' : 'Well Done!';
    document.getElementById('completionSub').textContent   =
        'You identified all numbers ' + start + ' to ' + end + '!';

    // Next batch button visibility
    const nextBtn = document.getElementById('nextBatchBtn2');
    if (isLastBatch) {
        nextBtn.textContent = '🏆 All done!';
        nextBtn.onclick     = () => showMode('map');
    } else {
        nextBtn.textContent = 'Next batch (' + (end + 1) + '–' + (end + BATCH_SIZE) + ') →';
        nextBtn.onclick     = proceedNextBatch;
    }

    // Narrator question
    const qEl = document.getElementById('narratorQuestion');
    qEl.style.display = 'block';

    let msg, speech;
    if (isLastBatch) {
        msg    = '🏆 Amazing! You have identified all 100 numbers! You are a number star!';
        speech = 'Amazing! You have identified all one hundred numbers! You are a number star!';
        document.getElementById('practiseAgainBtn').textContent = 'Practise again 🔁';
    } else if (didWell) {
        msg    = '⭐ Excellent! You got every number right! Would you like to move on to numbers ' +
                 (end + 1) + ' to ' + (end + BATCH_SIZE) + ', or practise ' + start + ' to ' + end + ' again to make sure you know them really well?';
        speech = 'Excellent! You got every number right! Would you like to move on to the next batch, or practise this batch again?';
    } else {
        msg    = '👍 Good effort! You made ' + batchErrors + ' mistake' + (batchErrors > 1 ? 's' : '') +
                 '. Would you like to try numbers ' + start + ' to ' + end + ' again to get even better, or move on to ' +
                 (end + 1) + ' to ' + (end + BATCH_SIZE) + '?';
        speech = 'Good effort! Would you like to practise this batch again to get even better, or move on to the next batch?';
    }

    qEl.textContent = msg;
    setTimeout(() => NG_Speech.sayInstruction(speech), 600);

    // 🌸 Launch confetti flowers and stars!
    launchConfetti(didWell || isLastBatch);
}

function proceedNextBatch() {
    stopConfetti();
    if (quizBatch < TOTAL_BATCHES) startBatch(quizBatch + 1);
    else showMode('map');
}

function practiseSameBatch() {
    stopConfetti();
    startBatch(quizBatch);
}

/* ================================================================
   MODE SWITCH
================================================================ */

function showMode(mode) {
    // Update mode tabs
    document.querySelectorAll('#modeTabs .tab-btn').forEach((btn, i) => {
        btn.classList.toggle('active', (mode === 'map' && i === 0) || (mode === 'quiz' && i === 1));
    });

    document.getElementById('modeMap').style.display  = mode === 'map'  ? '' : 'none';
    document.getElementById('modeQuiz').style.display = mode === 'quiz' ? '' : 'none';

    NG_Speech.stop();

    if (mode === 'quiz') {
        // Auto-start batch 1 if nothing in progress
        if (lessonPool.length === 0 && currentNum === null) {
            startBatch(1);
        }
    } else {
        NG_Speech.sayInstruction('Tap any number to hear it!');
    }
}

/* ================================================================
   TOAST
================================================================ */

let _toastTimer = null;

function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = 'feedback-toast show' + (type ? ' ' + type : '');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => t.classList.remove('show'), 2200);
}

/* ================================================================
   INIT
================================================================ */

/* ================================================================
   CONFETTI — flowers, stars and sparkles rain down on completion
================================================================ */
(function() {
    const EMOJIS_GOOD   = ['🌸','🌺','🌼','⭐','✨','🌟','💛','🌷','🎉','🏆','🎀','🌻','💮','🎊'];
    const EMOJIS_NORMAL = ['🎉','⭐','✨','🌸','🌼','💐','🎊','🌟','👏','🥳','🎈','🌺','💫','🎁'];
    let confettiTimer = null;
    let particles     = [];
    let frameCount    = 0;

    function makeParticle(emojis, canvasW, canvasH, delay) {
        return {
            x:     Math.random() * canvasW,
            y:     -30 - delay,              // stagger start: some begin higher
            size:  22 + Math.random() * 22,
            emoji: emojis[Math.floor(Math.random() * emojis.length)],
            speed: 2.5 + Math.random() * 3.5,  // faster fall
            drift: (Math.random() - 0.5) * 2.5, // slight sideways sway
            swing: Math.random() * 0.04,         // gentle oscillation
            swingOffset: Math.random() * Math.PI * 2,
            spin:  (Math.random() - 0.5) * 0.1,
            angle: Math.random() * Math.PI * 2,
            alpha: 1,
        };
    }

    function launchConfetti(great) {
        const cvs = document.getElementById('confettiCanvas');
        cvs.style.display = 'block';
        cvs.width  = window.innerWidth;
        cvs.height = window.innerHeight;
        const ctx    = cvs.getContext('2d');
        const emojis = great ? EMOJIS_GOOD : EMOJIS_NORMAL;
        particles    = [];
        frameCount   = 0;

        // 80 particles, staggered so they rain down in waves
        for (let i = 0; i < 80; i++) {
            particles.push(makeParticle(emojis, cvs.width, cvs.height, i * 8));
        }

        clearInterval(confettiTimer);
        confettiTimer = setInterval(() => {
            ctx.clearRect(0, 0, cvs.width, cvs.height);
            frameCount++;
            let alive = 0;

            for (const p of particles) {
                p.y     += p.speed;
                p.x     += p.drift + Math.sin(frameCount * p.swing + p.swingOffset) * 0.8;
                p.angle += p.spin;

                // Fade out gently in the lower quarter
                if (p.y > cvs.height * 0.75) {
                    p.alpha = Math.max(0, 1 - (p.y - cvs.height * 0.75) / (cvs.height * 0.28));
                }

                if (p.y < cvs.height + 50 && p.alpha > 0.02) {
                    alive++;
                    ctx.save();
                    ctx.globalAlpha = p.alpha;
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.angle);
                    ctx.font = p.size + 'px serif';
                    ctx.textAlign    = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(p.emoji, 0, 0);
                    ctx.restore();
                }
            }

            // Recycle particles that have left the screen to keep the rain going
            // for the first 5 seconds (300 frames), then let them drain out
            if (frameCount < 300) {
                for (const p of particles) {
                    if (p.y > cvs.height + 40) {
                        // Recycle: send back to top with fresh properties
                        Object.assign(p, makeParticle(emojis, cvs.width, cvs.height, 0));
                    }
                }
            }

            // Stop once all particles have drained off screen (after frame 300)
            if (frameCount > 300 && alive === 0) {
                clearInterval(confettiTimer);
                ctx.clearRect(0, 0, cvs.width, cvs.height);
                cvs.style.display = 'none';
            }
        }, 16);
    }

    function stopConfetti() {
        clearInterval(confettiTimer);
        const cvs = document.getElementById('confettiCanvas');
        if (cvs) {
            cvs.getContext('2d').clearRect(0, 0, cvs.width, cvs.height);
            cvs.style.display = 'none';
        }
    }

    window.launchConfetti = launchConfetti;
    window.stopConfetti   = stopConfetti;
})();

document.addEventListener('DOMContentLoaded', function () {
    renderGrid();
    setTimeout(() => NG_Speech.sayInstruction('Welcome to Level 1! Tap any number to hear it!'), 500);
});
</script>
</body>
</html>
