/* ============================================================
   Number Gear — shared i18n for level pages
   Reuses the exact same translated strings as the dashboard
   (index.php's I18N object) so a language picked on the
   dashboard stays applied once you're inside a level.
   ============================================================ */
(function () {
    const LANG_KEY = 'ng_lang';

    const DICT = {
        en: {
            backHome: '← Home', dir: 'ltr',
            level1Badge: 'Level 1', level1Name: 'Number Recognition',
            level2Badge: 'Level 2', level2Name: 'Counting Objects',
            level3Badge: 'Level 3', level3Name: 'Number Gear',
            level4Badge: 'Level 4', level4Name: 'Multiply & Divide',
            level5Badge: 'Level 5', level5Name: 'Even & Odd Numbers',
            level6Badge: 'Level 6', level6Name: 'Prime Numbers',
            level7Badge: 'Level 7', level7Name: 'Ordinal Numbers'
        },
        de: {
            backHome: '← Start', dir: 'ltr',
            level1Badge: 'Stufe 1', level1Name: 'Zahlenerkennung',
            level2Badge: 'Stufe 2', level2Name: 'Objekte zählen',
            level3Badge: 'Stufe 3', level3Name: 'Zahlenrad',
            level4Badge: 'Stufe 4', level4Name: 'Multiplizieren & Dividieren',
            level5Badge: 'Stufe 5', level5Name: 'Gerade & ungerade Zahlen',
            level6Badge: 'Stufe 6', level6Name: 'Primzahlen',
            level7Badge: 'Stufe 7', level7Name: 'Ordinalzahlen'
        },
        fr: {
            backHome: '← Accueil', dir: 'ltr',
            level1Badge: 'Niveau 1', level1Name: 'Reconnaissance des chiffres',
            level2Badge: 'Niveau 2', level2Name: 'Compter les objets',
            level3Badge: 'Niveau 3', level3Name: 'Engrenage numérique',
            level4Badge: 'Niveau 4', level4Name: 'Multiplier et diviser',
            level5Badge: 'Niveau 5', level5Name: 'Nombres pairs et impairs',
            level6Badge: 'Niveau 6', level6Name: 'Nombres premiers',
            level7Badge: 'Niveau 7', level7Name: 'Nombres ordinaux'
        },
        ar: {
            backHome: '← الرئيسية', dir: 'rtl',
            level1Badge: 'المستوى ١', level1Name: 'التعرف على الأرقام',
            level2Badge: 'المستوى ٢', level2Name: 'عدّ الأشياء',
            level3Badge: 'المستوى ٣', level3Name: 'تروس الأرقام',
            level4Badge: 'المستوى ٤', level4Name: 'الضرب والقسمة',
            level5Badge: 'المستوى ٥', level5Name: 'الأعداد الزوجية والفردية',
            level6Badge: 'المستوى ٦', level6Name: 'الأعداد الأولية',
            level7Badge: 'المستوى ٧', level7Name: 'الأعداد الترتيبية'
        },
        zh: {
            backHome: '← 首页', dir: 'ltr',
            level1Badge: '第1关', level1Name: '数字认知',
            level2Badge: '第2关', level2Name: '数数物体',
            level3Badge: '第3关', level3Name: '数字齿轮',
            level4Badge: '第4关', level4Name: '乘法与除法',
            level5Badge: '第5关', level5Name: '偶数和奇数',
            level6Badge: '第6关', level6Name: '质数',
            level7Badge: '第7关', level7Name: '序数词'
        }
    };

    // Applies the saved language to the elements every level page has
    // in common: the back-to-dashboard link, the level heading, and
    // the page's text direction/lang attribute (for Arabic RTL).
    function apply(levelNum) {
        const lang = localStorage.getItem(LANG_KEY) || 'en';
        const t = DICT[lang] || DICT.en;

        document.documentElement.setAttribute('lang', lang);
        document.documentElement.setAttribute('dir', t.dir);

        const back = document.getElementById('lvlBackLink');
        if (back) back.textContent = t.backHome;

        const heading = document.getElementById('lvlHeading');
        if (heading && levelNum) {
            const badge = t['level' + levelNum + 'Badge'];
            const name  = t['level' + levelNum + 'Name'];
            if (badge && name) heading.textContent = badge + ': ' + name;
        }
    }

    window.NG_I18nCommon = { apply: apply, dict: DICT, currentLang: function () {
        return localStorage.getItem(LANG_KEY) || 'en';
    } };
})();
