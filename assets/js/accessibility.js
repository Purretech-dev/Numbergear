(function () {
    const FONT_KEY = 'ng_font_family';
    const SIZE_KEY = 'ng_font_scale';
    const DEFAULT_FONT = "'Nunito', 'Segoe UI', system-ui, sans-serif";
    const DEFAULT_SCALE = '1';

    function applyAccessibilityPrefs() {
        const font = localStorage.getItem(FONT_KEY) || DEFAULT_FONT;
        const scale = localStorage.getItem(SIZE_KEY) || DEFAULT_SCALE;
        document.documentElement.style.setProperty('--ng-user-font-family', font);
        document.documentElement.style.setProperty('--ng-user-font-scale', scale);
    }

    window.NG_Accessibility = {
        apply: applyAccessibilityPrefs,
        setFont: function (font) {
            localStorage.setItem(FONT_KEY, font || DEFAULT_FONT);
            applyAccessibilityPrefs();
        },
        setSize: function (scale) {
            localStorage.setItem(SIZE_KEY, scale || DEFAULT_SCALE);
            applyAccessibilityPrefs();
        },
        getFont: function () { return localStorage.getItem(FONT_KEY) || DEFAULT_FONT; },
        getSize: function () { return localStorage.getItem(SIZE_KEY) || DEFAULT_SCALE; },
        reset: function () {
            localStorage.removeItem(FONT_KEY);
            localStorage.removeItem(SIZE_KEY);
            applyAccessibilityPrefs();
        }
    };

    applyAccessibilityPrefs();
    document.addEventListener('DOMContentLoaded', applyAccessibilityPrefs);
})();
