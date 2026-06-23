<?php
require_once __DIR__ . '/auth/auth.php';
ng_session_start();
$user = ng_current_user();
if (!$user) { header('Location: auth/login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Number Gear — Learn Numbers the Fun Way!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/accessibility.js"></script>
    <style>
        /* ── Full-viewport layout ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            overflow: hidden;   /* no body scroll — inner area scrolls */
            background: var(--bg);
            font-family: var(--ng-user-font-family, 'Nunito', 'Segoe UI', system-ui, sans-serif);
        }

        /* Gear background canvas */
        #gearBg {
            position: fixed; inset: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Outer shell: flex column, full height ── */
        .page-shell {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* ── FIXED HEADER (lang bar + app-header) ── */
        .fixed-top {
            flex-shrink: 0;
            background: var(--surface);
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
            z-index: 100;
        }

        /* Language bar */
        .lang-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            padding: 8px 20px;
            background: var(--bg);
            border-bottom: 1px solid var(--border);
        }
        .lang-bar-label { font-size: 13px; font-weight: 700; color: var(--text-soft); }
        .lang-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border: 2px solid var(--border);
            border-radius: 20px; background: var(--surface);
            font-size: 13px; font-weight: 700; cursor: pointer;
            font-family: inherit; color: var(--text-soft); transition: 0.18s ease;
            white-space: nowrap;
        }
        .lang-btn:hover  { border-color: var(--purple); color: var(--purple); }
        .lang-btn.active { background: var(--purple); border-color: var(--purple); color: white; }
        .lang-flag { font-size: 16px; line-height: 1; }

        .user-pill {
            display: flex; align-items: center; gap: 8px;
            margin-left: auto; flex-wrap: wrap;
        }
        .user-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            background: var(--purple); color: white;
            font-size: 12px; font-weight: 900;
            display: flex; align-items: center; justify-content: center;
        }
        .user-name         { font-size: 13px; font-weight: 800; color: var(--text); }
        .user-role-badge   { font-size: 11px; font-weight: 800; color: white; padding: 2px 8px; border-radius: 20px; }
        .user-logout       { font-size: 12px; font-weight: 700; color: var(--text-soft); text-decoration: none; padding: 3px 8px; border: 1.5px solid var(--border); border-radius: 8px; transition: 0.15s ease; }
        .user-logout:hover { border-color: #e74c3c; color: #e74c3c; }

        /* App header row */
        .app-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 24px;
            background: var(--surface);
        }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-icon { font-size: 34px; line-height: 1; }
        .brand h1   { font-size: 22px; font-weight: 900; color: var(--purple); line-height: 1.1; }
        .brand p    { font-size: 13px; font-weight: 700; color: var(--text-soft); }

        .hero-inline {
            text-align: center;
            flex: 1;
        }
        .hero-inline h2 { font-size: 18px; font-weight: 900; color: var(--purple); }
        .hero-inline p  { font-size: 13px; font-weight: 600; color: var(--text-soft); }

        /* ── SCROLLABLE MIDDLE ── */
        .scroll-area {
            flex: 1 1 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px 20px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ── 2 × 3 CARD GRID ── */
        .level-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            width: 100%;
            max-width: 960px;
        }
        @media (max-width: 700px) {
            .level-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        }
        @media (max-width: 440px) {
            .level-grid { grid-template-columns: 1fr; gap: 10px; }
        }

        /* Level card */
        .level-card {
            display: flex;
            flex-direction: column;
            background: var(--surface);
            border-radius: 18px;
            border-top: 4px solid var(--border);
            padding: 16px 16px 14px;
            text-decoration: none;
            color: var(--text);
            box-shadow: 0 4px 16px rgba(0,0,0,0.07);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            gap: 6px;
        }
        .level-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(0,0,0,0.14);
        }
        .level-card:active { transform: scale(0.98); }

        .level-icon  { font-size: 32px; line-height: 1; margin-bottom: 2px; }
        .level-badge { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-soft); }
        .level-name  { font-size: 16px; font-weight: 900; color: var(--text); line-height: 1.2; }
        .level-desc  { font-size: 12px; font-weight: 600; color: var(--text-soft); line-height: 1.5; flex: 1; }

        .level-progress-wrap {
            height: 6px; background: var(--border); border-radius: 4px;
            overflow: hidden; margin-top: 4px;
        }
        .level-progress-bar { height: 100%; border-radius: 4px; width: 0%; transition: width 0.5s ease; background: var(--mint); }
        .progress-label { font-size: 11px; font-weight: 700; color: var(--text-soft); }
        .level-play { font-size: 13px; font-weight: 900; color: var(--purple); margin-top: 4px; }

        /* Level accent colours */
        .level-1 { border-top-color: var(--purple); }
        .level-1 .level-badge { color: var(--purple-dark); }
        .level-1 .level-progress-bar { background: var(--purple); }

        .level-2 { border-top-color: var(--mint); }
        .level-2 .level-badge { color: var(--mint-dark); }
        .level-2 .level-progress-bar { background: var(--mint); }

        .level-3 { border-top-color: var(--peach); }
        .level-3 .level-badge { color: var(--peach-dark); }
        .level-3 .level-progress-bar { background: var(--peach); }

        .level-4 { border-top-color: var(--sky); }
        .level-4 .level-badge { color: var(--sky-dark); }
        .level-4 .level-progress-bar { background: var(--sky); }

        .level-5 { border-top-color: #9b59e8; }
        .level-5 .level-badge { color: #5b10a0; }
        .level-5 .level-progress-bar { background: #9b59e8; }

        .level-6 { border-top-color: var(--mint); }
        .level-6 .level-badge { color: var(--mint-dark); }
        .level-6 .level-progress-bar { background: var(--mint); }

        .level-7 { border-top-color: var(--peach); }
        .level-7 .level-badge { color: var(--peach-dark); }
        .level-7 .level-progress-bar { background: var(--peach); }



        .accessibility-panel {
            width: 100%;
            max-width: 960px;
            margin-bottom: 14px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }
        .accessibility-title {
            font-size: 14px;
            font-weight: 900;
            color: var(--purple-dark);
        }
        .accessibility-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .accessibility-field {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
            color: var(--text-soft);
        }
        .accessibility-select {
            min-width: 130px;
            padding: 7px 10px;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--bg);
            color: var(--text);
            font-weight: 800;
            outline: none;
            font-family: var(--ng-user-font-family, 'Nunito', 'Segoe UI', system-ui, sans-serif);
        }
        .accessibility-select:focus { border-color: var(--purple); }
        .accessibility-reset {
            padding: 7px 10px;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            color: var(--text-soft);
            font-weight: 800;
            cursor: pointer;
            font-family: var(--ng-user-font-family, 'Nunito', 'Segoe UI', system-ui, sans-serif);
        }
        .accessibility-reset:hover { border-color: var(--purple); color: var(--purple); }

        .reset-row {
            text-align: center;
            margin-top: 16px;
            padding-bottom: 4px;
        }
        .reset-link {
            font-size: 12px; color: var(--text-soft);
            cursor: pointer; text-decoration: underline;
            background: none; border: none; font-family: inherit;
        }

        /* ── FIXED FOOTER ── */
        .ng-footer {
            flex-shrink: 0;
            text-align: center;
            padding: 10px 20px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-soft);
            background: var(--surface);
            border-top: 1px solid var(--border);
        }

        /* RTL */
        [dir="rtl"] .brand, [dir="rtl"] .lang-bar,
        [dir="rtl"] .level-card, [dir="rtl"] .user-pill { direction: rtl; text-align: right; }
        [dir="rtl"] .level-play { text-align: left; }
    </style>
</head>
<body>

<canvas id="gearBg"></canvas>

<div class="page-shell">

    <!-- ── FIXED TOP ── -->
    <div class="fixed-top">

        <!-- Language bar -->
        <div class="lang-bar">
            <span class="lang-bar-label" id="langLabel">🌐 Language:</span>
            <button class="lang-btn active" onclick="setLang('en')" id="lang-en"><span class="lang-flag">🇬🇧</span> English</button>
            <button class="lang-btn" onclick="setLang('de')" id="lang-de"><span class="lang-flag">🇩🇪</span> Deutsch</button>
            <button class="lang-btn" onclick="setLang('fr')" id="lang-fr"><span class="lang-flag">🇫🇷</span> Français</button>
            <button class="lang-btn" onclick="setLang('ar')" id="lang-ar"><span class="lang-flag">🇸🇦</span> العربية</button>
            <button class="lang-btn" onclick="setLang('zh')" id="lang-zh"><span class="lang-flag">🇨🇳</span> 中文</button>
            <?php if ($user): ?>
            <div class="user-pill">
                <span class="user-avatar"><?= strtoupper(mb_substr($user['name'], 0, 1)) ?></span>
                <span class="user-name"><?= htmlspecialchars($user['name']) ?></span>
                <span class="user-role-badge" style="background:<?= ng_role_color($user['role']) ?>;"><?= ng_role_label($user['role']) ?></span>
                <?php if (in_array($user['role'], ['instructor', 'admin'], true)): ?>
                <a href="instructor/dashboard.php" class="user-logout" style="border-color:var(--sky); color:var(--sky-dark);">📊 Dashboard</a>
                <?php endif; ?>
                <a href="auth/logout.php" class="user-logout">Log out</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- App header -->
        <header class="app-header">
            <div class="brand">
                <div class="brand-icon">⚙️</div>
                <div>
                    <h1>Number Gear</h1>
                    <p id="headerSub">Pre-Primary Mathematics</p>
                </div>
            </div>
            <div class="hero-inline">
                <h2 id="heroTitle">Welcome to Number Gear!</h2>
                <p id="heroSub">Choose a level and tap any card to play! 👇</p>
            </div>
        </header>
    </div>

    <!-- ── SCROLLABLE CONTENT ── -->
    <div class="scroll-area">


        <section class="accessibility-panel" aria-label="Reading preferences">
            <div class="accessibility-title">🔤 Reading Preferences</div>
            <div class="accessibility-controls">
                <label class="accessibility-field" for="fontPicker">Font
                    <select id="fontPicker" class="accessibility-select" onchange="setDashboardFont(this.value)">
                        <option value="'Nunito', 'Segoe UI', system-ui, sans-serif">Nunito</option>
                        <option value="'Segoe UI', Arial, sans-serif">Segoe UI</option>
                        <option value="Arial, sans-serif">Arial</option>
                        <option value="Verdana, Geneva, sans-serif">Verdana</option>
                        <option value="'Trebuchet MS', Arial, sans-serif">Trebuchet MS</option>
                        <option value="'Comic Sans MS', 'Comic Sans', cursive">Comic Sans</option>
                    </select>
                </label>
                <label class="accessibility-field" for="fontSizePicker">Size
                    <select id="fontSizePicker" class="accessibility-select" onchange="setDashboardFontSize(this.value)">
                        <option value="0.9">Small</option>
                        <option value="1">Normal</option>
                        <option value="1.12">Large</option>
                        <option value="1.25">Extra Large</option>
                    </select>
                </label>
                <button type="button" class="accessibility-reset" onclick="resetReadingPrefs()">Reset</button>
            </div>
        </section>

        <div class="level-grid">

            <!-- Level 1 -->
            <a href="modules/level1/index.php" class="level-card level-1">
                <div class="level-icon">🔢</div>
                <div class="level-badge" data-i18n="level1Badge">Level 1</div>
                <div class="level-name"  data-i18n="level1Name">Number Recognition</div>
                <div class="level-desc"  data-i18n="level1Desc">Tap numbers 1–100 to hear them, then practise identifying them in fun lessons!</div>
                <div class="level-progress-wrap"><div class="level-progress-bar" id="prog-1"></div></div>
                <div class="progress-label" id="prog-label-1">Not started yet</div>
                <div class="level-play" data-i18n="play">Play →</div>
            </a>

            <!-- Level 2 -->
            <a href="modules/level2/index.php" class="level-card level-2">
                <div class="level-icon">🍎</div>
                <div class="level-badge" data-i18n="level2Badge">Level 2</div>
                <div class="level-name"  data-i18n="level2Name">Counting Objects</div>
                <div class="level-desc"  data-i18n="level2Desc">Add and subtract using colourful fruits and toys with animated counting!</div>
                <div class="level-progress-wrap"><div class="level-progress-bar" id="prog-2"></div></div>
                <div class="progress-label" id="prog-label-2">Not started yet</div>
                <div class="level-play" data-i18n="play">Play →</div>
            </a>

            <!-- Level 3 -->
            <a href="modules/level3/index.php" class="level-card level-3">
                <div class="level-icon">⚙️</div>
                <div class="level-badge" data-i18n="level3Badge">Level 3</div>
                <div class="level-name"  data-i18n="level3Name">Number Gear</div>
                <div class="level-desc"  data-i18n="level3Desc">Spin the circular number gear and discover amazing number patterns!</div>
                <div class="level-progress-wrap"><div class="level-progress-bar" id="prog-3"></div></div>
                <div class="progress-label" id="prog-label-3">Not started yet</div>
                <div class="level-play" data-i18n="play">Play →</div>
            </a>

            <!-- Level 4 -->
            <a href="modules/level4/index.php" class="level-card level-4">
                <div class="level-icon">✖️</div>
                <div class="level-badge" data-i18n="level4Badge">Level 4</div>
                <div class="level-name"  data-i18n="level4Name">Multiply &amp; Divide</div>
                <div class="level-desc"  data-i18n="level4Desc">Learn grouping, sharing, multiplication and division using the number gear.</div>
                <div class="level-progress-wrap"><div class="level-progress-bar" id="prog-4"></div></div>
                <div class="progress-label" id="prog-label-4">Not started yet</div>
                <div class="level-play" data-i18n="play">Play →</div>
            </a>

            <!-- Level 5 -->
            <a href="modules/level5/index.php" class="level-card level-5">
                <div class="level-icon">🔢</div>
                <div class="level-badge" data-i18n="level5Badge">Level 5</div>
                <div class="level-name"  data-i18n="level5Name">Even &amp; Odd Numbers</div>
                <div class="level-desc"  data-i18n="level5Desc">Learn numbers that make equal pairs and numbers that have one left over.</div>
                <div class="level-progress-wrap"><div class="level-progress-bar" id="prog-5"></div></div>
                <div class="progress-label" id="prog-label-5">Not started yet</div>
                <div class="level-play" data-i18n="play">Play →</div>
            </a>

            <!-- Level 6 -->
            <a href="modules/level6/index.php" class="level-card level-6">
                <div class="level-icon">🔵</div>
                <div class="level-badge" data-i18n="level6Badge">Level 6</div>
                <div class="level-name"  data-i18n="level6Name">Prime Numbers</div>
                <div class="level-desc"  data-i18n="level6Desc">Explore prime numbers on a spinning gear and learn what makes primes special.</div>
                <div class="level-progress-wrap"><div class="level-progress-bar" id="prog-6"></div></div>
                <div class="progress-label" id="prog-label-6">Not started yet</div>
                <div class="level-play" data-i18n="play">Play →</div>
            </a>

            <!-- Level 7 -->
            <a href="modules/level7/index.php" class="level-card level-7">
                <div class="level-icon">🥇</div>
                <div class="level-badge" data-i18n="level7Badge">Level 7</div>
                <div class="level-name"  data-i18n="level7Name">Ordinal Numbers</div>
                <div class="level-desc"  data-i18n="level7Desc">Learn positions like 1st, 2nd, 3rd and match numbers to ordinal words.</div>
                <div class="level-progress-wrap"><div class="level-progress-bar" id="prog-7"></div></div>
                <div class="progress-label" id="prog-label-7">Not started yet</div>
                <div class="level-play" data-i18n="play">Play →</div>
            </a>

        </div>

        <div class="reset-row">
            <button class="reset-link" onclick="resetProgress()" data-i18n="reset">Reset all progress</button>
        </div>

    </div><!-- /scroll-area -->

    <!-- ── FIXED FOOTER ── -->
    <footer class="ng-footer">
        © <?= date('Y') ?> Number Gear &nbsp;·&nbsp; Developed by <strong>Purretech Solutions</strong>
    </footer>

</div><!-- /page-shell -->

<canvas id="gearBg" style="position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:0;"></canvas>

<script>
    window.NG_USER_ID  = <?= json_encode($user['id']) ?>;
    window.NG_API_BASE = 'api/';
</script>
<script src="assets/js/storage.js"></script>
<script>
/* ================================================================
   TRANSLATIONS
================================================================ */
const I18N = {
    en: {
        langLabel:'🌐 Language:', headerSub:'Pre-Primary Mathematics',
        heroTitle:'Welcome to Number Gear!', heroSub:'Choose a level and tap any card to play! 👇',
        level1Badge:'Level 1', level1Name:'Number Recognition',
        level1Desc:'Tap numbers 1–100 to hear them, then practise identifying them in fun lessons!',
        level2Badge:'Level 2', level2Name:'Counting Objects',
        level2Desc:'Add and subtract using colourful fruits and toys with animated counting!',
        level3Badge:'Level 3', level3Name:'Number Gear',
        level3Desc:'Spin the circular number gear and discover amazing number patterns!',
        level4Badge:'Level 4', level4Name:'Multiply & Divide',
        level4Desc:'Learn grouping, sharing, multiplication and division using the number gear.',
        level5Badge:'Level 5', level5Name:'Even & Odd Numbers',
        level5Desc:'Learn numbers that make equal pairs and numbers that have one left over.',
        level6Badge:'Level 6', level6Name:'Prime Numbers',
        level6Desc:'Explore prime numbers on a spinning gear and learn what makes primes special.',
        level7Badge:'Level 7', level7Name:'Ordinal Numbers',
        level7Desc:'Learn positions like 1st, 2nd and 3rd, then match numbers to ordinal words.',
        play:'Play →', notStarted:'Not started yet', complete:'% complete',
        reset:'Reset all progress', dir:'ltr'
    },
    de: {
        langLabel:'🌐 Sprache:', headerSub:'Vorschulmathematik',
        heroTitle:'Willkommen bei Number Gear!', heroSub:'Wähle eine Stufe und tippe auf eine Karte! 👇',
        level1Badge:'Stufe 1', level1Name:'Zahlenerkennung',
        level1Desc:'Tippe auf Zahlen von 1–100 und übe das Erkennen!',
        level2Badge:'Stufe 2', level2Name:'Objekte zählen',
        level2Desc:'Addiere und subtrahiere mit bunten Früchten und Spielzeugen!',
        level3Badge:'Stufe 3', level3Name:'Zahlenrad',
        level3Desc:'Drehe das Zahlenrad und entdecke Zahlenmuster!',
        level4Badge:'Stufe 4', level4Name:'Multiplizieren & Dividieren',
        level4Desc:'Lerne Gruppen, Teilen, Multiplikation und Division.',
        level5Badge:'Stufe 5', level5Name:'Gerade & ungerade Zahlen',
        level5Desc:'Lerne Zahlen mit Paaren und Zahlen mit einem Rest.',
        level6Badge:'Stufe 6', level6Name:'Primzahlen',
        level6Desc:'Entdecke Primzahlen auf dem Zahlenrad.',
        level7Badge:'Stufe 7', level7Name:'Ordinalzahlen',
        level7Desc:'Lerne Positionen wie erste, zweite und dritte.',
        play:'Spielen →', notStarted:'Noch nicht begonnen', complete:'% abgeschlossen',
        reset:'Fortschritte zurücksetzen', dir:'ltr'
    },
    fr: {
        langLabel:'🌐 Langue :', headerSub:'Mathématiques préscolaires',
        heroTitle:'Bienvenue dans Number Gear !', heroSub:'Choisis un niveau et touche une carte ! 👇',
        level1Badge:'Niveau 1', level1Name:'Reconnaissance des chiffres',
        level1Desc:'Touche les chiffres de 1 à 100 pour les entendre!',
        level2Badge:'Niveau 2', level2Name:'Compter les objets',
        level2Desc:'Additionne et soustrais avec des fruits colorés!',
        level3Badge:'Niveau 3', level3Name:'Engrenage numérique',
        level3Desc:'Fais tourner l\'engrenage et découvre des motifs numériques!',
        level4Badge:'Niveau 4', level4Name:'Multiplier et diviser',
        level4Desc:'Apprends les groupes, le partage, la multiplication et la division.',
        level5Badge:'Niveau 5', level5Name:'Nombres pairs et impairs',
        level5Desc:'Apprends les nombres qui font des paires et ceux qui ont un reste.',
        level6Badge:'Niveau 6', level6Name:'Nombres premiers',
        level6Desc:'Explore les nombres premiers sur l\'engrenage.',
        level7Badge:'Niveau 7', level7Name:'Nombres ordinaux',
        level7Desc:'Apprends les positions comme 1er, 2e et 3e.',
        play:'Jouer →', notStarted:'Pas encore commencé', complete:'% terminé',
        reset:'Réinitialiser la progression', dir:'ltr'
    },
    ar: {
        langLabel:'🌐 اللغة:', headerSub:'رياضيات ما قبل المدرسة',
        heroTitle:'مرحباً بك في Number Gear!', heroSub:'اختر مستوى واضغط على أي بطاقة للعب! 👇',
        level1Badge:'المستوى ١', level1Name:'التعرف على الأرقام',
        level1Desc:'اضغط على الأرقام من ١ إلى ١٠٠ لسماعها!',
        level2Badge:'المستوى ٢', level2Name:'عدّ الأشياء',
        level2Desc:'أجمع واطرح باستخدام الفواكه والألعاب الملونة!',
        level3Badge:'المستوى ٣', level3Name:'تروس الأرقام',
        level3Desc:'أدر التروس واكتشف أنماط الأرقام!',
        level4Badge:'المستوى ٤', level4Name:'الضرب والقسمة',
        level4Desc:'تعلم المجموعات والمشاركة والضرب والقسمة.',
        level5Badge:'المستوى ٥', level5Name:'الأعداد الزوجية والفردية',
        level5Desc:'تعلم الأعداد التي تكوّن أزواجاً والأعداد التي يتبقى منها واحد.',
        level6Badge:'المستوى ٦', level6Name:'الأعداد الأولية',
        level6Desc:'استكشف الأعداد الأولية على التروس.',
        level7Badge:'المستوى ٧', level7Name:'الأعداد الترتيبية',
        level7Desc:'تعلم المراكز مثل الأول والثاني والثالث.',
        play:'العب ←', notStarted:'لم يبدأ بعد', complete:'٪ مكتمل',
        reset:'إعادة تعيين التقدم', dir:'rtl'
    },
    zh: {
        langLabel:'🌐 语言：', headerSub:'学前数学',
        heroTitle:'欢迎来到 Number Gear！', heroSub:'选择一个关卡，点击卡片开始游戏！👇',
        level1Badge:'第1关', level1Name:'数字认知',
        level1Desc:'点击1到100的数字来听发音，然后练习辨认！',
        level2Badge:'第2关', level2Name:'数数物体',
        level2Desc:'用彩色水果和玩具进行加减法！',
        level3Badge:'第3关', level3Name:'数字齿轮',
        level3Desc:'转动圆形数字齿轮，发现神奇的数字规律！',
        level4Badge:'第4关', level4Name:'乘法与除法',
        level4Desc:'学习分组、分享、乘法和除法。',
        level5Badge:'第5关', level5Name:'偶数和奇数',
        level5Desc:'学习能配对的数字和会剩下一个的数字。',
        level6Badge:'第6关', level6Name:'质数',
        level6Desc:'在数字齿轮上探索质数。',
        level7Badge:'第7关', level7Name:'序数词',
        level7Desc:'学习第一、第二、第三等位置。',
        play:'开始 →', notStarted:'尚未开始', complete:'% 已完成',
        reset:'重置所有进度', dir:'ltr'
    }
};

let currentLang = localStorage.getItem('ng_lang') || 'en';

function setLang(lang) {
    currentLang = lang;
    localStorage.setItem('ng_lang', lang);
    const t = I18N[lang];
    document.getElementById('htmlRoot').setAttribute('lang', lang);
    document.getElementById('htmlRoot').setAttribute('dir', t.dir || 'ltr');
    document.getElementById('langLabel').textContent = t.langLabel;
    document.querySelectorAll('.lang-btn').forEach(b => b.classList.toggle('active', b.id === 'lang-' + lang));
    document.getElementById('headerSub').textContent = t.headerSub;
    document.getElementById('heroTitle').textContent  = t.heroTitle;
    document.getElementById('heroSub').textContent    = t.heroSub;
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (t[key] !== undefined) el.textContent = t[key];
    });
    updateProgressLabels(t);
}

function updateProgressLabels(t) {
    [
        { id:'prog-label-1', val: Math.min(100, NG_Storage.getLearnedNums().length) },
        { id:'prog-label-2', val: NG_Storage.getLvl2Score() },
        { id:'prog-label-3', val: NG_Storage.getLvl3Score() },
        { id:'prog-label-4', val: NG_Storage.getLvl4Score() },
        { id:'prog-label-5', val: NG_Storage.getLvl5Score() },
        { id:'prog-label-6', val: NG_Storage.getLvl6Score() },
        { id:'prog-label-7', val: NG_Storage.getLvl7Score() },
    ].forEach(s => {
        const el = document.getElementById(s.id);
        if (el) el.textContent = s.val > 0 ? s.val + t.complete : t.notStarted;
    });
}


function setDashboardFont(font) {
    if (window.NG_Accessibility) window.NG_Accessibility.setFont(font);
}

function setDashboardFontSize(scale) {
    if (window.NG_Accessibility) window.NG_Accessibility.setSize(scale);
}

function resetReadingPrefs() {
    if (window.NG_Accessibility) window.NG_Accessibility.reset();
    const fontPicker = document.getElementById('fontPicker');
    const fontSizePicker = document.getElementById('fontSizePicker');
    if (fontPicker) fontPicker.value = window.NG_Accessibility.getFont();
    if (fontSizePicker) fontSizePicker.value = window.NG_Accessibility.getSize();
}

function hydrateReadingPrefs() {
    if (!window.NG_Accessibility) return;
    const fontPicker = document.getElementById('fontPicker');
    const fontSizePicker = document.getElementById('fontSizePicker');
    if (fontPicker) fontPicker.value = window.NG_Accessibility.getFont();
    if (fontSizePicker) fontSizePicker.value = window.NG_Accessibility.getSize();
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('prog-1').style.width = Math.min(100, NG_Storage.getLearnedNums().length) + '%';
    document.getElementById('prog-2').style.width = NG_Storage.getLvl2Score() + '%';
    document.getElementById('prog-3').style.width = NG_Storage.getLvl3Score() + '%';
    document.getElementById('prog-4').style.width = NG_Storage.getLvl4Score() + '%';
    document.getElementById('prog-5').style.width = NG_Storage.getLvl5Score() + '%';
    document.getElementById('prog-6').style.width = NG_Storage.getLvl6Score() + '%';
    document.getElementById('prog-7').style.width = NG_Storage.getLvl7Score() + '%';
    setLang(currentLang);
    hydrateReadingPrefs();
});

function resetProgress() {
    if (confirm(I18N[currentLang].reset + '?')) { NG_Storage.resetAll(); location.reload(); }
}
</script>
<script>
/* Animated gear background */
(function() {
    const canvas = document.getElementById('gearBg');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const GEARS = [
        {x:0.08,y:0.15,r:70,teeth:10,speed:0.0004,dir:1},
        {x:0.30,y:0.07,r:44,teeth:8,speed:0.0007,dir:-1},
        {x:0.57,y:0.10,r:86,teeth:12,speed:0.00025,dir:1},
        {x:0.84,y:0.09,r:52,teeth:9,speed:0.0006,dir:-1},
        {x:0.94,y:0.35,r:62,teeth:10,speed:0.0005,dir:1},
        {x:0.91,y:0.65,r:74,teeth:11,speed:0.0003,dir:-1},
        {x:0.70,y:0.90,r:56,teeth:9,speed:0.00055,dir:1},
        {x:0.44,y:0.95,r:78,teeth:12,speed:0.00028,dir:-1},
        {x:0.16,y:0.91,r:48,teeth:8,speed:0.00065,dir:1},
        {x:0.03,y:0.66,r:64,teeth:10,speed:0.00038,dir:-1},
        {x:0.05,y:0.40,r:42,teeth:7,speed:0.0008,dir:1},
        {x:0.36,y:0.52,r:94,teeth:14,speed:0.0002,dir:-1},
        {x:0.64,y:0.48,r:52,teeth:9,speed:0.00058,dir:1},
    ];
    const angles = GEARS.map(() => Math.random() * Math.PI * 2);

    function drawGear(cx,cy,outerR,innerR,teeth,angle) {
        const arc=Math.PI*2/teeth, tw=arc*0.38;
        ctx.beginPath();
        for(let i=0;i<teeth;i++){
            const a1=angle+i*arc-tw/2,a2=angle+i*arc+tw/2,
                  a3=angle+(i+.5)*arc-tw*.6,a4=angle+(i+.5)*arc+tw*.6;
            ctx.lineTo(Math.cos(a1)*innerR+cx,Math.sin(a1)*innerR+cy);
            ctx.lineTo(Math.cos(a1)*outerR+cx,Math.sin(a1)*outerR+cy);
            ctx.lineTo(Math.cos(a2)*outerR+cx,Math.sin(a2)*outerR+cy);
            ctx.lineTo(Math.cos(a2)*innerR+cx,Math.sin(a2)*innerR+cy);
            ctx.lineTo(Math.cos(a3)*innerR+cx,Math.sin(a3)*innerR+cy);
            ctx.lineTo(Math.cos(a4)*innerR+cx,Math.sin(a4)*innerR+cy);
        }
        ctx.closePath(); ctx.fill(); ctx.stroke();
        ctx.beginPath(); ctx.arc(cx,cy,innerR*.28,0,Math.PI*2); ctx.fill(); ctx.stroke();
        ctx.lineWidth=innerR*.10;
        for(let s=0;s<4;s++){
            const sa=angle+(s/4)*Math.PI*2;
            ctx.beginPath();
            ctx.moveTo(cx+Math.cos(sa)*innerR*.3,cy+Math.sin(sa)*innerR*.3);
            ctx.lineTo(cx+Math.cos(sa)*innerR*.82,cy+Math.sin(sa)*innerR*.82);
            ctx.stroke();
        }
        ctx.lineWidth=1;
    }

    function resize(){ canvas.width=window.innerWidth; canvas.height=window.innerHeight; }

    function draw(){
        ctx.clearRect(0,0,canvas.width,canvas.height);
        GEARS.forEach((g,i)=>{
            angles[i]+=g.speed*g.dir;
            ctx.fillStyle='rgba(90,90,160,0.055)';
            ctx.strokeStyle='rgba(90,90,160,0.09)';
            ctx.lineWidth=1.5;
            drawGear(g.x*canvas.width,g.y*canvas.height,g.r,g.r*.68,g.teeth,angles[i]);
        });
        requestAnimationFrame(draw);
    }
    window.addEventListener('resize',resize);
    resize(); requestAnimationFrame(draw);
})();
</script>
</body>
</html>
