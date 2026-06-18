/* ============================================================
   NUMBER GEAR — SPEECH SYNTHESIS UTILITY
   Localised: English (Kenya) — Swahili praise words, Kenyan
   English phrasing, child-friendly slow rate
============================================================ */

const NG_Speech = (function () {

    let _voices = [];
    let _voice  = null;
    let _ready  = false;

    function _loadVoices() {
        _voices = window.speechSynthesis ? window.speechSynthesis.getVoices() : [];

        _voice =
            // 1. English Kenya
            _voices.find(v => v.lang === 'en-KE') ||
            // 2. English Tanzania / Uganda / Nigeria (East/West African English)
            _voices.find(v => /en-TZ|en-UG|en-NG|en-ZA/i.test(v.lang)) ||
            // 3. English UK female
            _voices.find(v => v.lang === 'en-GB' && /hazel|susan|kate|female/i.test(v.name)) ||
            // 4. Any English female name
            _voices.find(v => v.lang.startsWith('en') && /female|woman|girl|zira|hazel|susan|kate|victoria|karen|samantha|moira|veena/i.test(v.name)) ||
            // 5. Any English
            _voices.find(v => v.lang.startsWith('en')) ||
            _voices[0] || null;

        _ready = true;
    }

    if (window.speechSynthesis) {
        window.speechSynthesis.onvoiceschanged = _loadVoices;
        _loadVoices();
    }

    /* ---- core speak ---- */
    function speak(text, onEnd) {
        if (!window.speechSynthesis) return;
        window.speechSynthesis.cancel();

        const utt    = new SpeechSynthesisUtterance(text);
        utt.rate     = 0.62;   // slow — young learners can follow clearly
        utt.pitch    = 1.15;   // warm, engaging tone
        utt.volume   = 1;
        if (_voice) utt.voice = _voice;
        if (onEnd)  utt.onend = onEnd;

        setTimeout(() => window.speechSynthesis.speak(utt), 80);
    }

    function sayNumber(n) { speak(String(n)); }
    function sayWord(word) { speak(word); }

    /* Vizuri = Good/Well done  |  Hongera = Congratulations
       Hodari = Clever/Brave    |  Sawa sawa = Alright/Okay
       Poa = Cool/Great         |  Umefanya vizuri = You have done well */
    function sayCorrect(n) {
        speak(`Very good! The answer is ${n}.`);
    }

    function sayWrong(correct) {
        if (correct !== undefined) {
            speak(`Sorry, try again. The answer is ${correct}.`);
        } else {
            speak('Sorry, try again.');
        }
    }

    function sayInstruction(text) { speak(text); }

    function sayCompletion() {
        speak('Very good! You have completed the lesson.');
    }

    function sayAddition(a, b, result) {
        speak(`Very good! ${a} and ${b} all together makes ${result}.`);
    }

    function saySubtraction(a, b, result) {
        speak(`Very good! ${a} take away ${b} leaves ${result}.`);
    }

    function sayMultiplication(a, b, result) {
        speak(`Very good! ${a} times ${b} equals ${result}.`);
    }

    function sayDivision(a, b, result) {
        speak(`Very good! ${a} divided by ${b} equals ${result}.`);
    }

    function stop() {
        if (window.speechSynthesis) window.speechSynthesis.cancel();
    }

    return {
        speak, sayNumber, sayWord, sayCorrect, sayWrong,
        sayInstruction, sayCompletion, sayAddition,
        saySubtraction, sayMultiplication, sayDivision, stop
    };

})();
