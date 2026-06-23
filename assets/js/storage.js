/* ============================================================
   NUMBER GEAR — STORAGE UTILITY
   Keeps a fast local copy in localStorage (works instantly,
   even offline) AND syncs every score change up to the server
   (api/save_progress.php) so instructors can see progress on
   the dashboard.

   Pages that include this file should set, BEFORE this script,
   two globals so syncing knows where to send data:
       window.NG_USER_ID  = <the logged-in user's id, or omit if none>
       window.NG_API_BASE = '<relative path to the /api folder>'
   e.g. on index.php:               NG_API_BASE = 'api/'
        on modules/levelX/index.php: NG_API_BASE = '../../api/'
============================================================ */

const NG_Storage = (function () {

    const K = {
        learnedNums:   'ng_learned_nums',
        identifiedNums:'ng_identified_nums',
        lvl2Score:     'ng_lvl2_score',
        lvl3Score:     'ng_lvl3_score',
        lvl4Score:     'ng_lvl4_score',
        lvl5Score:     'ng_lvl5_score',
        lvl6Score:     'ng_lvl6_score',
        lvl7Score:     'ng_lvl7_score',
        lvl2Activity:  'ng_lvl2_activity',
    };

    /* ---- local storage helpers ---- */
    function _get(key, def) {
        try {
            const v = localStorage.getItem(key);
            return v !== null ? JSON.parse(v) : def;
        } catch(e) { return def; }
    }

    function _set(key, value) {
        try { localStorage.setItem(key, JSON.stringify(value)); } catch(e) {}
    }

    /* ---- server sync ---- */
    function _syncProgress(level, score, details) {
        if (!window.NG_USER_ID) return; // page didn't set a logged-in user, skip
        try {
            fetch((window.NG_API_BASE || '') + 'save_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ level: level, score: score, details: details || null })
            }).catch(function () { /* offline / server unreachable — local copy still saved */ });
        } catch (e) { /* ignore */ }
    }

    /* ---- Level 1: number map ---- */
    function getLearnedNums()      { return _get(K.learnedNums, []); }
    function isLearned(n)          { return getLearnedNums().includes(n); }
    function markLearned(n) {
        const arr = getLearnedNums();
        if (!arr.includes(n)) {
            arr.push(n);
            _set(K.learnedNums, arr);
            _syncProgress(1, Math.min(100, arr.length), { learned: arr, identified: getIdentifiedNums() });
        }
    }

    /* ---- Level 1: quiz ---- */
    function getIdentifiedNums()   { return _get(K.identifiedNums, []); }
    function markIdentified(n) {
        const arr = getIdentifiedNums();
        if (!arr.includes(n)) {
            arr.push(n);
            _set(K.identifiedNums, arr);
            _syncProgress(1, Math.min(100, getLearnedNums().length), { learned: getLearnedNums(), identified: arr });
        }
    }

    /* ---- Level 2 ---- */
    function getLvl2Score()        { return _get(K.lvl2Score, 0); }
    function setLvl2Score(v) {
        v = Math.min(100, Math.max(0, v));
        _set(K.lvl2Score, v);
        _syncProgress(2, v, { activity: getLvl2Activity() });
    }
    function getLvl2Activity()     { return _get(K.lvl2Activity, 'addition'); }
    function setLvl2Activity(v)    { _set(K.lvl2Activity, v); }

    /* ---- Level 3 ---- */
    function getLvl3Score()        { return _get(K.lvl3Score, 0); }
    function setLvl3Score(v) {
        v = Math.min(100, Math.max(0, v));
        _set(K.lvl3Score, v);
        _syncProgress(3, v);
    }

    /* ---- Level 4 ---- */
    function getLvl4Score()        { return _get(K.lvl4Score, 0); }
    function setLvl4Score(v) {
        v = Math.min(100, Math.max(0, v));
        _set(K.lvl4Score, v);
        _syncProgress(4, v);
    }

    /* ---- Level 5 ---- */
    function getLvl5Score()        { return _get(K.lvl5Score, 0); }
    function setLvl5Score(v) {
        v = Math.min(100, Math.max(0, v));
        _set(K.lvl5Score, v);
        _syncProgress(5, v);
    }

    /* ---- Level 6 ---- */
    function getLvl6Score()        { return _get(K.lvl6Score, 0); }
    function setLvl6Score(v) {
        v = Math.min(100, Math.max(0, v));
        _set(K.lvl6Score, v);
        _syncProgress(6, v);
    }

    /* ---- Level 7 ---- */
    function getLvl7Score()        { return _get(K.lvl7Score, 0); }
    function setLvl7Score(v) {
        v = Math.min(100, Math.max(0, v));
        _set(K.lvl7Score, v);
        _syncProgress(7, v);
    }

    /* ---- reset ---- */
    function resetAll() {
        Object.values(K).forEach(k => { try { localStorage.removeItem(k); } catch(e){} });
        for (let lvl = 1; lvl <= 7; lvl++) _syncProgress(lvl, 0);
    }

    return {
        getLearnedNums, isLearned, markLearned,
        getIdentifiedNums, markIdentified,
        getLvl2Score, setLvl2Score, getLvl2Activity, setLvl2Activity,
        getLvl3Score, setLvl3Score,
        getLvl4Score, setLvl4Score,
        getLvl5Score, setLvl5Score,
        getLvl6Score, setLvl6Score,
        getLvl7Score, setLvl7Score,
        resetAll
    };

})();
