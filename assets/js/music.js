/* ============================================================
   NUMBER GEAR — CALM BACKGROUND MUSIC
   A small, self-contained ambient music player built on the
   Web Audio API. No audio files needed (works offline), and
   nothing copyrighted — the notes are generated on the fly.

   Usage (already wired into the levels that use it):
       <script src="../../assets/js/music.js"></script>
       <script>NG_Music.init();</script>

   It adds a floating 🎵 / 🔇 button (bottom-right) that lets the
   child (or teacher) start or mute the music, and remembers the
   choice in localStorage so it carries across levels.
============================================================ */

const NG_Music = (function () {

    const PREF_KEY = 'ng_music_on';

    let ctx        = null;   // AudioContext (created on first start)
    let masterGain = null;   // overall volume
    let filter     = null;   // low-pass for warmth
    let timer      = null;   // schedules the next chord
    let playing    = false;
    let btn        = null;

    // A gentle, soothing chord progression (frequencies in Hz).
    // Soft major-7 / add9 voicings — calm and child-friendly.
    const CHORDS = [
        [196.00, 246.94, 293.66, 392.00], // G  major-ish
        [174.61, 220.00, 261.63, 349.23], // F  major-ish
        [220.00, 261.63, 329.63, 440.00], // A  minor-ish
        [164.81, 196.00, 246.94, 329.63], // E  minor-ish
    ];
    let chordIndex = 0;

    /* ---- preference ---- */
    function prefersOn() {
        try { return localStorage.getItem(PREF_KEY) === '1'; } catch (e) { return false; }
    }
    function savePref(on) {
        try { localStorage.setItem(PREF_KEY, on ? '1' : '0'); } catch (e) {}
    }

    /* ---- audio engine ---- */
    function ensureContext() {
        if (ctx) return;
        const AC = window.AudioContext || window.webkitAudioContext;
        if (!AC) return; // very old browser — silently no-op
        ctx = new AC();

        masterGain = ctx.createGain();
        masterGain.gain.value = 0.0;          // fade in later

        filter = ctx.createBiquadFilter();
        filter.type = 'lowpass';
        filter.frequency.value = 900;          // soft, mellow tone
        filter.Q.value = 0.6;

        filter.connect(masterGain);
        masterGain.connect(ctx.destination);
    }

    // Play one soft, slowly-swelling chord.
    function playChord(freqs) {
        if (!ctx) return;
        const now = ctx.currentTime;
        const dur = 7.5;                       // each chord lingers

        freqs.forEach((f, i) => {
            const osc  = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.value = f;

            // Gentle detune between notes for a warm, organic feel
            osc.detune.value = (i - 1.5) * 4;

            // Slow swell in and out so nothing is jarring
            const peak = 0.16 / freqs.length;
            gain.gain.setValueAtTime(0.0001, now);
            gain.gain.exponentialRampToValueAtTime(peak, now + 2.5);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + dur);

            osc.connect(gain);
            gain.connect(filter);
            osc.start(now);
            osc.stop(now + dur + 0.1);
        });
    }

    function scheduleNext() {
        playChord(CHORDS[chordIndex]);
        chordIndex = (chordIndex + 1) % CHORDS.length;
        timer = setTimeout(scheduleNext, 6000); // overlap chords slightly
    }

    /* ---- controls ---- */
    function start() {
        ensureContext();
        if (!ctx) return;
        if (ctx.state === 'suspended') ctx.resume();

        playing = true;
        savePref(true);
        updateButton();

        // Fade master volume up smoothly
        const now = ctx.currentTime;
        masterGain.gain.cancelScheduledValues(now);
        masterGain.gain.setValueAtTime(masterGain.gain.value, now);
        masterGain.gain.linearRampToValueAtTime(0.9, now + 2.0);

        chordIndex = 0;
        scheduleNext();
    }

    function stop() {
        playing = false;
        savePref(false);
        updateButton();

        if (timer) { clearTimeout(timer); timer = null; }
        if (ctx && masterGain) {
            const now = ctx.currentTime;
            masterGain.gain.cancelScheduledValues(now);
            masterGain.gain.setValueAtTime(masterGain.gain.value, now);
            masterGain.gain.linearRampToValueAtTime(0.0, now + 1.0);
        }
    }

    function toggle() {
        if (playing) stop(); else start();
    }

    /* ---- floating button ---- */
    function updateButton() {
        if (!btn) return;
        if (playing) {
            btn.innerHTML = '<span class="ng-music-icon">🎵</span><span class="ng-music-label">Music on</span>';
            btn.classList.add('on');
            btn.setAttribute('aria-label', 'Mute background music');
        } else {
            btn.innerHTML = '<span class="ng-music-icon">🔇</span><span class="ng-music-label">Play music</span>';
            btn.classList.remove('on');
            btn.setAttribute('aria-label', 'Play calm background music');
        }
    }

    function buildButton() {
        if (document.getElementById('ngMusicBtn')) return;

        const style = document.createElement('style');
        style.textContent = `
            #ngMusicBtn {
                position: fixed; bottom: 18px; right: 18px; z-index: 1000;
                display: inline-flex; align-items: center; gap: 8px;
                padding: 10px 16px; border: none; border-radius: 30px;
                background: var(--surface, #fff); color: var(--text, #2d3748);
                box-shadow: 0 4px 16px rgba(0,0,0,0.18);
                font-family: 'Nunito', system-ui, sans-serif;
                font-size: 14px; font-weight: 800; cursor: pointer;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }
            #ngMusicBtn:hover  { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(0,0,0,0.22); }
            #ngMusicBtn:active { transform: translateY(0); }
            #ngMusicBtn.on {
                background: var(--mint-light, #d4f5ec);
                color: var(--mint-dark, #389e80);
            }
            #ngMusicBtn .ng-music-icon  { font-size: 18px; line-height: 1; }
            #ngMusicBtn .ng-music-label { white-space: nowrap; }
            @media (max-width: 480px) {
                #ngMusicBtn .ng-music-label { display: none; }
                #ngMusicBtn { padding: 12px; }
            }
        `;
        document.head.appendChild(style);

        btn = document.createElement('button');
        btn.id = 'ngMusicBtn';
        btn.type = 'button';
        btn.addEventListener('click', toggle);
        document.body.appendChild(btn);
        updateButton();
    }

    /* ---- init ---- */
    function init() {
        if (document.body) {
            buildButton();
        } else {
            document.addEventListener('DOMContentLoaded', buildButton);
        }

        // Browsers block audio until the user interacts with the page.
        // If the child had music ON last time, start it on their very
        // first tap/click anywhere — which counts as that interaction.
        if (prefersOn()) {
            const kick = function () {
                start();
                window.removeEventListener('pointerdown', kick);
                window.removeEventListener('keydown', kick);
            };
            window.addEventListener('pointerdown', kick);
            window.addEventListener('keydown', kick);
        }
    }

    return { init, start, stop, toggle };

})();
