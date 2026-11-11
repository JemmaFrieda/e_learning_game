document.addEventListener('DOMContentLoaded', () => {
    const loginModal = document.getElementById('loginRequiredModal');
    if (loginModal && window.bootstrap) new bootstrap.Modal(loginModal).show();

    document.querySelectorAll('form[data-disable-submit]').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('button[type="submit"]');
            if (button) button.disabled = true;
        });
    });

    const celebration = document.querySelector('.celebration-layer');
    if (celebration) {
        const colors = ['#f97316', '#ef4444', '#22c55e', '#3b82f6', '#a855f7', '#ec4899', '#facc15'];
        const container = celebration.querySelector('.confetti-container');
        for (let index = 0; index < 60; index += 1) {
            const piece = document.createElement('span');
            piece.className = 'confetti-piece';
            piece.style.left = `${Math.random() * 100}%`;
            piece.style.setProperty('--drift', `${(Math.random() - 0.5) * 180}px`);
            piece.style.backgroundColor = colors[index % colors.length];
            piece.style.animationDelay = `${Math.random() * 1.8}s`;
            container.appendChild(piece);
        }
        const playCelebrationSound = () => {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (!AudioContextClass) return;
            const audio = new AudioContextClass();
            if (audio.state === 'suspended') audio.resume();
            const master = audio.createGain();
            master.gain.value = 0.5;
            master.connect(audio.destination);
            [523.25, 659.25, 783.99, 1046.5].forEach((frequency, index) => {
                const oscillator = audio.createOscillator();
                const gain = audio.createGain();
                oscillator.type = 'sine';
                oscillator.frequency.value = frequency;
                gain.gain.setValueAtTime(0.0001, audio.currentTime + index * 0.12);
                gain.gain.exponentialRampToValueAtTime(0.12, audio.currentTime + index * 0.12 + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, audio.currentTime + index * 0.12 + 0.28);
                oscillator.connect(gain).connect(master);
                oscillator.start(audio.currentTime + index * 0.12);
                oscillator.stop(audio.currentTime + index * 0.12 + 0.3);
            });
            const applauseFilter = audio.createBiquadFilter();
            applauseFilter.type = 'bandpass';
            applauseFilter.frequency.value = 1800;
            applauseFilter.Q.value = 0.7;
            applauseFilter.connect(master);
            for (let index = 0; index < 14; index += 1) {
                const start = audio.currentTime + 0.18 + index * 0.13;
                const clap = audio.createBufferSource();
                const buffer = audio.createBuffer(1, audio.sampleRate * 0.09, audio.sampleRate);
                const data = buffer.getChannelData(0);
                for (let sample = 0; sample < data.length; sample += 1) data[sample] = (Math.random() * 2 - 1) * (1 - sample / data.length);
                clap.buffer = buffer;
                const clapGain = audio.createGain();
                clapGain.gain.setValueAtTime(0.0001, start);
                clapGain.gain.exponentialRampToValueAtTime(0.8, start + 0.005);
                clapGain.gain.exponentialRampToValueAtTime(0.0001, start + 0.09);
                clap.connect(clapGain).connect(applauseFilter);
                clap.start(start);
            }
        };
        try { playCelebrationSound(); } catch (error) { /* Browser autoplay may block automatic playback. */ }
    }
});
