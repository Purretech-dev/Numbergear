/* ============================================================
   NUMBER GEAR — LOCAL STORAGE UTILITY
============================================================ */

const NG_Storage = (function () {

    const K = {
        learnedNums:   'ng_learned_nums',
        identifiedNums:'ng_identified_nums',
        lvl2Score:     'ng_lvl2_score',
        lvl3Score:     'ng_lvl3_score',
        lvl4Score:     'ng_lvl4_score',
        lvl2Activity:  'ng_lvl2_activity',
    };

    /* ---- helpers ---- */
    function _get(key, def) {
        try {
            const v = localStorage.getItem(key);
            return v !== null ? JSON.parse(v) : def;
        } catch(e) { return def; }
    }

    function _set(key, value) {
        try { localStorage.setItem(key, JSON.stringify(value)); } catch(e) {}
    }

    /* ---- Level 1: number map ---- */
    function getLearnedNums()      { return _get(K.learnedNums, []); }
    function isLearned(n)          { return getLearnedNums().includes(n); }
    function markLearned(n) {
        const arr = getLearnedNums();
        if (!arr.includes(n)) { arr.push(n); _set(K.learnedNums, arr); }
    }

    /* ---- Level 1: quiz ---- */
    function getIdentifiedNums()   { return _get(K.identifiedNums, []); }
    function markIdentified(n) {
        const arr = getIdentifiedNums();
        if (!arr.includes(n)) { arr.push(n); _set(K.identifiedNums, arr); }
    }

    /* ---- Level 2 ---- */
    function getLvl2Score()        { return _get(K.lvl2Score, 0); }
    function setLvl2Score(v)       { _set(K.lvl2Score, Math.min(100, Math.max(0, v))); }
    function getLvl2Activity()     { return _get(K.lvl2Activity, 'addition'); }
    function setLvl2Activity(v)    { _set(K.lvl2Activity, v); }

    /* ---- Level 3 ---- */
    function getLvl3Score()        { return _get(K.lvl3Score, 0); }
    function setLvl3Score(v)       { _set(K.lvl3Score, Math.min(100, Math.max(0, v))); }

    /* ---- Level 4 ---- */
    function getLvl4Score()        { return _get(K.lvl4Score, 0); }
    function setLvl4Score(v)       { _set(K.lvl4Score, Math.min(100, Math.max(0, v))); }
    function getLvl5Score()        { return _get(K.lvl5Score, 0); }
    function setLvl5Score(v)       { _set(K.lvl5Score, Math.min(100, Math.max(0, v))); }
    function getLvl6Score()        { return _get('ng_lvl6_score', 0); }
    function setLvl6Score(v)       { _set('ng_lvl6_score', Math.min(100, Math.max(0, v))); }

    /* ---- reset ---- */
    function resetAll() {
        Object.values(K).forEach(k => { try { localStorage.removeItem(k); } catch(e){} });
    }

    return {
        getLearnedNums, isLearned, markLearned,
        getIdentifiedNums, markIdentified,
        getLvl2Score, setLvl2Score, getLvl2Activity, setLvl2Activity,
        getLvl3Score, setLvl3Score,
        getLvl4Score, setLvl4Score,
        getLvl5Score, setLvl5Score,
        getLvl6Score, setLvl6Score,
        resetAll
    };

})();
