<?php
// Number Gear — Home Page
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Number Gear — Learn Numbers the Fun Way!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .home-hero {
            text-align: center;
            padding: 36px 24px 8px;
        }
        .home-hero-icon { font-size: 72px; line-height: 1; margin-bottom: 12px; }
        .home-hero h2   { font-size: 32px; font-weight: 900; color: var(--purple); margin-bottom: 8px; }
        .home-hero p    { font-size: 16px; color: var(--text-soft); font-weight: 600; }

        .reset-row {
            text-align: center;
            margin-top: 30px;
        }
        .reset-link {
            font-size: 12px;
            color: var(--text-soft);
            cursor: pointer;
            text-decoration: underline;
            background: none;
            border: none;
            font-family: inherit;
        }
    </style>
</head>
<body>
<div class="app-shell">

    <!-- Header -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">⚙️</div>
            <div>
                <h1>Number Gear</h1>
                <p>Pre-Primary Mathematics</p>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <div class="home-hero">
        <div class="home-hero-icon">🔢</div>
        <h2>Welcome to Number Gear!</h2>
        <p>Choose a level to start learning</p>
    </div>

    <main class="home-main" style="padding-top:20px;">

        <div class="level-grid">

            <!-- Level 1 -->
            <a href="modules/level1/index.php" class="level-card level-1">
                <div class="level-icon">🔢</div>
                <div class="level-badge">Level 1</div>
                <div class="level-name">Number Recognition</div>
                <div class="level-desc">Tap numbers 1–100 to hear them, then practise identifying them in fun lessons!</div>
                <div class="level-progress-wrap">
                    <div class="level-progress-bar" id="prog-1"></div>
                </div>
                <div class="level-play">Play →</div>
            </a>

            <!-- Level 2 -->
            <a href="modules/level2/index.php" class="level-card level-2">
                <div class="level-icon">🍎</div>
                <div class="level-badge">Level 2</div>
                <div class="level-name">Counting Objects</div>
                <div class="level-desc">Add and subtract using colourful fruits and toys with animated counting!</div>
                <div class="level-progress-wrap">
                    <div class="level-progress-bar" id="prog-2"></div>
                </div>
                <div class="level-play">Play →</div>
            </a>

            <!-- Level 3 -->
            <a href="modules/level3/index.php" class="level-card level-3">
                <div class="level-icon">⚙️</div>
                <div class="level-badge">Level 3</div>
                <div class="level-name">Number Gear</div>
                <div class="level-desc">Spin the circular number gear and discover amazing number patterns!</div>
                <div class="level-progress-wrap">
                    <div class="level-progress-bar" id="prog-3"></div>
                </div>
                <div class="level-play">Play →</div>
            </a>

            <!-- Level 4 -->
            <a href="modules/level4/index.php" class="level-card level-4">
                <div class="level-icon">✖️</div>
                <div class="level-badge">Level 4</div>
                <div class="level-name">Multiply &amp; Divide</div>
                <div class="level-desc">Use the number gear to explore multiplication tables and division patterns!</div>
                <div class="level-progress-wrap">
                    <div class="level-progress-bar" id="prog-4"></div>
                </div>
                <div class="level-play">Play →</div>
            </a>

            <!-- Level 5 -->
            <a href="modules/level5/index.php" class="level-card level-5">
                <div class="level-icon">🔵</div>
                <div class="level-badge">Level 5</div>
                <div class="level-name">Prime Numbers</div>
                <div class="level-desc">Explore the first 100 prime numbers on a spinning gear — discover what makes primes special!</div>
                <div class="level-progress-wrap">
                    <div class="level-progress-bar" id="prog-5"></div>
                </div>
                <div class="level-play">Play →</div>
            </a>

        </div><!-- /level-grid -->

        <div class="reset-row">
            <button class="reset-link" onclick="resetProgress()">Reset all progress</button>
        </div>

    </main>
</div>

<script src="assets/js/storage.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Level 1: how many of 100 numbers learned
    const learned = NG_Storage.getLearnedNums().length;
    document.getElementById('prog-1').style.width = Math.min(100, learned) + '%';

    // Level 2
    document.getElementById('prog-2').style.width = NG_Storage.getLvl2Score() + '%';

    // Level 3
    document.getElementById('prog-3').style.width = NG_Storage.getLvl3Score() + '%';

    // Level 4
    document.getElementById('prog-4').style.width = NG_Storage.getLvl4Score() + '%';

    // Level 5
    document.getElementById('prog-5').style.width = NG_Storage.getLvl5Score() + '%';
});

function resetProgress() {
    if (confirm('Reset all your progress? This cannot be undone.')) {
        NG_Storage.resetAll();
        location.reload();
    }
}
</script>
</body>
</html>
