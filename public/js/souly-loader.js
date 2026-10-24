/**
 * Souly Digital Citadel - 25-Second Cinematic Story Loader with Native Cyber Synthesizer
 * Features:
 * - 5 Educational Chapters (5s each, 25s total)
 * - Native Web Audio API Ambient Cyber Synthesizer (Zero MP3 Dependencies)
 * - IndexedDB 12-Hour Persistence (via SoulyDB)
 * - Interactive Skip & Replay Controls
 */
(function (global) {
    'use strict';

    class SoulyCyberAudioEngine {
        constructor() {
            this.ctx = null;
            this.isPlaying = false;
            this.masterGain = null;
            this.oscillators = [];
            this.filter = null;
            this.lfo = null;
            this.isMuted = true;
        }

        initContext() {
            if (!this.ctx) {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (AudioCtx) {
                    this.ctx = new AudioCtx();
                }
            }
            if (this.ctx && this.ctx.state === 'suspended') {
                this.ctx.resume();
            }
        }

        startSynthesizer() {
            this.initContext();
            if (!this.ctx || this.isPlaying) return;

            try {
                const now = this.ctx.currentTime;

                // Master Gain
                this.masterGain = this.ctx.createGain();
                this.masterGain.gain.setValueAtTime(0.0001, now);
                this.masterGain.connect(this.ctx.destination);

                // Lowpass Biquad Filter for Deep Sci-Fi Atmosphere
                this.filter = this.ctx.createBiquadFilter();
                this.filter.type = 'lowpass';
                this.filter.frequency.setValueAtTime(450, now);
                this.filter.Q.setValueAtTime(4, now);
                this.filter.connect(this.masterGain);

                // LFO for bioluminescent breathing filter sweep
                this.lfo = this.ctx.createOscillator();
                const lfoGain = this.ctx.createGain();
                this.lfo.frequency.setValueAtTime(0.2, now); // Slow 5-second breath
                lfoGain.gain.setValueAtTime(250, now);
                this.lfo.connect(lfoGain);
                lfoGain.connect(this.filter.frequency);
                this.lfo.start();

                // Layer 1: Sub Bass Drone (A1 - 55Hz)
                const osc1 = this.ctx.createOscillator();
                const gain1 = this.ctx.createGain();
                osc1.type = 'sawtooth';
                osc1.frequency.setValueAtTime(55, now);
                gain1.gain.setValueAtTime(0.35, now);
                osc1.connect(gain1);
                gain1.connect(this.filter);
                osc1.start();
                this.oscillators.push(osc1);

                // Layer 2: Harmonic Cyber Fifth (E2 - 82.4Hz)
                const osc2 = this.ctx.createOscillator();
                const gain2 = this.ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(82.41, now);
                gain2.gain.setValueAtTime(0.4, now);
                osc2.connect(gain2);
                gain2.connect(this.filter);
                osc2.start();
                this.oscillators.push(osc2);

                // Layer 3: High Bioluminescent Shimmer (A4 - 440Hz with detune)
                const osc3 = this.ctx.createOscillator();
                const gain3 = this.ctx.createGain();
                osc3.type = 'triangle';
                osc3.frequency.setValueAtTime(440, now);
                osc3.detune.setValueAtTime(8, now);
                gain3.gain.setValueAtTime(0.12, now);
                osc3.connect(gain3);
                gain3.connect(this.filter);
                osc3.start();
                this.oscillators.push(osc3);

                this.isPlaying = true;

                if (!this.isMuted) {
                    this.masterGain.gain.linearRampToValueAtTime(0.18, now + 1.5);
                }
            } catch (err) {
                console.warn('[SoulyAudio] Web Audio API init prevented:', err);
            }
        }

        playChime(freq = 880) {
            if (!this.ctx || this.isMuted) return;
            try {
                const now = this.ctx.currentTime;
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, now);
                osc.frequency.exponentialRampToValueAtTime(freq * 1.5, now + 0.3);

                gain.gain.setValueAtTime(0.1, now);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.6);

                osc.connect(gain);
                gain.connect(this.ctx.destination);

                osc.start(now);
                osc.stop(now + 0.6);
            } catch (e) {}
        }

        toggleMute() {
            this.initContext();
            if (!this.isPlaying) {
                this.isMuted = false;
                this.startSynthesizer();
                return true;
            }

            this.isMuted = !this.isMuted;
            const now = this.ctx.currentTime;
            if (this.isMuted) {
                this.masterGain.gain.linearRampToValueAtTime(0.0001, now + 0.5);
            } else {
                this.masterGain.gain.linearRampToValueAtTime(0.18, now + 0.8);
            }
            return !this.isMuted;
        }

        stop() {
            if (!this.ctx || !this.isPlaying) return;
            try {
                const now = this.ctx.currentTime;
                if (this.masterGain) {
                    this.masterGain.gain.linearRampToValueAtTime(0.0001, now + 0.8);
                }
                setTimeout(() => {
                    this.oscillators.forEach(o => {
                        try { o.stop(); o.disconnect(); } catch (e) {}
                    });
                    this.oscillators = [];
                    if (this.lfo) {
                        try { this.lfo.stop(); this.lfo.disconnect(); } catch (e) {}
                    }
                    this.isPlaying = false;
                }, 850);
            } catch (e) {}
        }
    }

    class SoulyCinematicLoaderManager {
        constructor() {
            this.audio = new SoulyCyberAudioEngine();
            this.duration = 25000; // 25 seconds
            this.chapters = [
                {
                    id: 1,
                    range: [0, 20],
                    badge: 'قلعة سولي الرقمية · CITADEL CORE',
                    title: 'مرحباً بك في قلعة سولي الرقمية',
                    subtitle: 'حيث ينتهي المستحيل وتبدأ السيادة التكنولوجية المتكاملة لأعمالك',
                    tag: 'السيادة والريادة',
                    color: '#00F5D4',
                    icon: 'shield'
                },
                {
                    id: 2,
                    range: [20, 40],
                    badge: 'الأتمتة والذكاء السيادي · ERP & AI AGENTS',
                    title: 'نظام BrandERP السحابي وموظفو الذكاء الاصطناعي',
                    subtitle: 'إدارة متكاملة ومؤتمتة للمخزون، الفواتير، وكوادر AI تعمل 24/7 لخدمة ومبيعات عملائك',
                    tag: 'أتمتة فائقة السرعة',
                    color: '#7B2CBF',
                    icon: 'cpu'
                },
                {
                    id: 3,
                    range: [40, 60],
                    badge: 'التدريع والجاهزية · CYBERSECURITY & HARDWARE',
                    title: 'تدريع سيبراني بتشفير عسكري 256-bit',
                    subtitle: 'حصانة سيبرانية ضد الهاكرز والفدية، وصيانة هندسية عاجلة لأجهزتك وسيرفراتك في مقرك',
                    tag: 'أمان غير قابل للاختراق',
                    color: '#FFB800',
                    icon: 'lock'
                },
                {
                    id: 4,
                    range: [60, 80],
                    badge: 'الاستشارات والأكاديمية · ADVISORY & ACADEMY',
                    title: 'استشارات بيزنس استراتيجية وأكاديمية احترافية',
                    subtitle: 'قيادة التحول الرقمي لمشروعك، وكورسات تطبيق عملي في هندسة الـ AI والهاكينج الأخلاقي',
                    tag: 'بناء المستقبل الرقمي',
                    color: '#00F5D4',
                    icon: 'academic'
                },
                {
                    id: 5,
                    range: [80, 100],
                    badge: 'لماذا سولي؟ · THE CITADEL GUARANTEE',
                    title: 'ضمان هندسي حقيقي، سرية مطلقة، ومحفظة ذكية',
                    subtitle: 'محفظتك الرقمية تتيح حجز وإدارة جميع خدمات المنصة بضغطة زر مع دعم تنفيذي فوري',
                    tag: 'جاهزون للانطلاق',
                    color: '#10B981',
                    icon: 'wallet'
                }
            ];

            this.currentChapter = 0;
            this.timer = null;
            this.startTime = null;
            this.isSkipped = false;
            this.dom = {};
            this.TTL_HOURS = 12;
        }

        async shouldShowLoader() {
            // Check if explicitly forced via query param
            const params = new URLSearchParams(window.location.search);
            if (params.get('intro') === '1' || params.get('loader') === '1') {
                return true;
            }

            // Check IndexedDB / LocalStorage persistence via SoulyDB
            try {
                if (window.SoulyDB) {
                    const record = await window.SoulyDB.get('souly_loader_watched');
                    if (record && record.timestamp) {
                        const ageMs = Date.now() - record.timestamp;
                        const maxAgeMs = this.TTL_HOURS * 60 * 60 * 1000;
                        if (ageMs < maxAgeMs) {
                            return false;
                        }
                    }
                }
            } catch (e) {
                console.warn('[SoulyLoader] Persistence read error:', e);
            }
            return true;
        }

        async recordWatched() {
            try {
                if (window.SoulyDB) {
                    await window.SoulyDB.set('souly_loader_watched', {
                        timestamp: Date.now(),
                        version: '1.0'
                    });
                }
            } catch (e) {
                console.warn('[SoulyLoader] Persistence write error:', e);
            }
        }

        async init() {
            this.dom.wrapper = document.getElementById('souly-cinematic-loader');
            if (!this.dom.wrapper) return;

            const shouldShow = await this.shouldShowLoader();
            if (!shouldShow) {
                this.dom.wrapper.style.display = 'none';
                document.body.classList.remove('overflow-hidden');
                return;
            }

            this.dom.progressBar = document.getElementById('loader-progress-bar');
            this.dom.percentText = document.getElementById('loader-percent-text');
            this.dom.secondsText = document.getElementById('loader-seconds-text');
            this.dom.badge = document.getElementById('loader-chapter-badge');
            this.dom.title = document.getElementById('loader-chapter-title');
            this.dom.subtitle = document.getElementById('loader-chapter-subtitle');
            this.dom.indicators = document.querySelectorAll('.loader-chapter-pill');
            this.dom.soundBtn = document.getElementById('loader-sound-toggle');
            this.dom.skipBtn = document.getElementById('loader-skip-btn');

            document.body.classList.add('overflow-hidden');

            this.bindEvents();
            this.startLoader();
        }

        bindEvents() {
            if (this.dom.skipBtn) {
                this.dom.skipBtn.addEventListener('click', () => this.skip());
            }

            if (this.dom.soundBtn) {
                this.dom.soundBtn.addEventListener('click', () => {
                    const active = this.audio.toggleMute();
                    this.updateSoundBtn(active);
                });
            }

            // Start sound on first user touch / click anywhere inside loader if browser blocked autoplay
            this.dom.wrapper.addEventListener('click', (e) => {
                if (!this.audio.isPlaying && !this.audio.isMuted) {
                    this.audio.startSynthesizer();
                }
            }, { once: true });
        }

        updateSoundBtn(isActive) {
            if (!this.dom.soundBtn) return;
            const textEl = this.dom.soundBtn.querySelector('.sound-btn-text');
            const iconEl = this.dom.soundBtn.querySelector('.sound-btn-icon');
            if (isActive) {
                if (textEl) textEl.textContent = 'كتم الموسيقى التكنولوجية 🔇';
                this.dom.soundBtn.classList.add('neon-border-cyan', 'text-cyber-cyan');
                this.dom.soundBtn.classList.remove('text-slate-400');
            } else {
                if (textEl) textEl.textContent = 'تشغيل الموسيقى التكنولوجية 🔊';
                this.dom.soundBtn.classList.remove('neon-border-cyan', 'text-cyber-cyan');
                this.dom.soundBtn.classList.add('text-slate-400');
            }
        }

        startLoader() {
            this.startTime = performance.now();
            this.setChapter(0);

            // Tick loop for 60fps smooth progress
            const tick = (now) => {
                if (this.isSkipped) return;

                const elapsed = now - this.startTime;
                const progress = Math.min(100, (elapsed / this.duration) * 100);
                const remainingSec = Math.max(0, Math.ceil((this.duration - elapsed) / 1000));

                if (this.dom.progressBar) {
                    this.dom.progressBar.style.width = `${progress}%`;
                }
                if (this.dom.percentText) {
                    this.dom.percentText.textContent = `${Math.floor(progress)}%`;
                }
                if (this.dom.secondsText) {
                    this.dom.secondsText.textContent = `${remainingSec}s`;
                }

                // Chapter evaluation (every 20%)
                const chapterIdx = Math.min(4, Math.floor(progress / 20));
                if (chapterIdx !== this.currentChapter) {
                    this.setChapter(chapterIdx);
                }

                if (elapsed < this.duration) {
                    requestAnimationFrame(tick);
                } else {
                    this.complete();
                }
            };

            requestAnimationFrame(tick);
        }

        setChapter(idx) {
            this.currentChapter = idx;
            const chap = this.chapters[idx];
            if (!chap) return;

            this.audio.playChime(660 + idx * 80);

            // Update Chapter Navigation Pills
            if (this.dom.indicators) {
                this.dom.indicators.forEach((pill, i) => {
                    if (i === idx) {
                        pill.className = 'loader-chapter-pill flex-1 h-1.5 rounded-full bg-cyber-cyan shadow-neon-cyan transition-all duration-300';
                    } else if (i < idx) {
                        pill.className = 'loader-chapter-pill flex-1 h-1.5 rounded-full bg-cyber-purple transition-all duration-300';
                    } else {
                        pill.className = 'loader-chapter-pill flex-1 h-1.5 rounded-full bg-obsidian-border transition-all duration-300';
                    }
                });
            }

            // Animate Text Transitions via GSAP if available
            if (typeof gsap !== 'undefined') {
                gsap.timeline()
                    .to([this.dom.badge, this.dom.title, this.dom.subtitle], {
                        opacity: 0,
                        y: -8,
                        duration: 0.25,
                        onComplete: () => {
                            if (this.dom.badge) this.dom.badge.textContent = chap.badge;
                            if (this.dom.title) this.dom.title.textContent = chap.title;
                            if (this.dom.subtitle) this.dom.subtitle.textContent = chap.subtitle;
                        }
                    })
                    .to([this.dom.badge, this.dom.title, this.dom.subtitle], {
                        opacity: 1,
                        y: 0,
                        duration: 0.45,
                        stagger: 0.08,
                        ease: 'power2.out'
                    });
            } else {
                if (this.dom.badge) this.dom.badge.textContent = chap.badge;
                if (this.dom.title) this.dom.title.textContent = chap.title;
                if (this.dom.subtitle) this.dom.subtitle.textContent = chap.subtitle;
            }
        }

        skip() {
            if (this.isSkipped) return;
            this.isSkipped = true;
            this.complete();
        }

        complete() {
            this.isSkipped = true;
            this.audio.stop();
            this.recordWatched();

            if (typeof gsap !== 'undefined' && this.dom.wrapper) {
                gsap.to(this.dom.wrapper, {
                    opacity: 0,
                    scale: 1.04,
                    filter: 'blur(10px)',
                    duration: 0.75,
                    ease: 'power3.inOut',
                    onComplete: () => {
                        this.dom.wrapper.style.display = 'none';
                        document.body.classList.remove('overflow-hidden');
                        // Trigger entry animations for main page content
                        if (window.SoulyUI && typeof window.SoulyUI.triggerPageEntry === 'function') {
                            window.SoulyUI.triggerPageEntry();
                        }
                    }
                });
            } else if (this.dom.wrapper) {
                this.dom.wrapper.style.display = 'none';
                document.body.classList.remove('overflow-hidden');
            }
        }

        async replay() {
            try {
                if (window.SoulyDB) {
                    await window.SoulyDB.delete('souly_loader_watched');
                }
            } catch (e) {}
            window.location.href = window.location.pathname + '?intro=1';
        }
    }

    const loaderInstance = new SoulyCinematicLoaderManager();
    global.SoulyLoader = loaderInstance;

    document.addEventListener('DOMContentLoaded', () => {
        loaderInstance.init();
    });

})(typeof window !== 'undefined' ? window : globalThis);
