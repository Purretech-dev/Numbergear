/* ============================================================
   Number Gear — generic per-level translation engine
   Each level page defines its OWN small dictionary object
   (5 languages, same keys the dashboard already uses: en, de,
   fr, ar, zh) and calls NG_LevelI18n.applyStatic(dict) once on
   load, then NG_LevelI18n.t(dict, 'key', {placeholders}) for any
   text the page builds dynamically in JS (feedback, toasts,
   narrator messages, progress labels, etc).

   This only affects on-screen TEXT. Spoken narration (speech.js)
   intentionally keeps its English/Kenyan voice — see speech.js.
   ============================================================ */
const NG_LevelI18n = (function () {
    const LANG_KEY = 'ng_lang';

    function lang() {
        return localStorage.getItem(LANG_KEY) || 'en';
    }

    function row(dict) {
        return dict[lang()] || dict.en;
    }

    // t(dict, 'key', {name: value, ...}) — looks up `key` in the
    // current language's row (falling back to English, then to the
    // key itself), then substitutes {placeholders}.
    function t(dict, key, vars) {
        const r = row(dict);
        let s = (r && r[key] !== undefined) ? r[key]
              : (dict.en && dict.en[key] !== undefined) ? dict.en[key]
              : key;
        if (vars) {
            Object.keys(vars).forEach(function (k) {
                s = s.split('{' + k + '}').join(vars[k]);
            });
        }
        return s;
    }

    // Applies translations to every element tagged with
    // data-i18n="key" (sets textContent), data-i18n-html="key"
    // (sets innerHTML — for text with embedded <strong> etc.),
    // data-i18n-title="key" (tooltip), or data-i18n-placeholder="key".
    function applyStatic(dict) {
        document.querySelectorAll('[data-i18n]').forEach(function (el) {
            el.textContent = t(dict, el.getAttribute('data-i18n'));
        });
        document.querySelectorAll('[data-i18n-html]').forEach(function (el) {
            el.innerHTML = t(dict, el.getAttribute('data-i18n-html'));
        });
        document.querySelectorAll('[data-i18n-title]').forEach(function (el) {
            el.title = t(dict, el.getAttribute('data-i18n-title'));
        });
        document.querySelectorAll('[data-i18n-placeholder]').forEach(function (el) {
            el.placeholder = t(dict, el.getAttribute('data-i18n-placeholder'));
        });
    }

    return { lang, t, applyStatic };
})();
