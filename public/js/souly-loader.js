/**
 * Souly Digital Citadel - 11-Second Executive Story Loader with Native Cyber Synthesizer
 * Features:
 * - 4 Executive Chapters (11s total fast-paced showcase)
 * - Native Web Audio API Ambient Cyber Synthesizer (Zero MP3 Dependencies)
 * - First-Visit Persistence + Watchdog Scroll Unlock Guarantee
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
                this.ctx.resume().catch(() => {});
            }
        }

        startSynthesizer() {
            this.initContext();
            if (!this.ctx || this.isPlaying) return;

            try {
                const now = this.ctx.currentTime;

                this.masterGain = this.ctx.createGain();
                this.masterGain.gain.setValueAtTime(0.0001, now);
                this.masterGain.connect(this.ctx.destination);

                this.filter = this.ctx.createBiquadFilter();
                this.filter.type = 'lowpass';
                this.filter.frequency.setValueAtTime(450, now);
                this.filter.Q.setValueAtTime(4, now);
                this.filter.connect(this.masterGain);

                this.lfo = this.ctx.createOscillator();
                const lfoGain = this.ctx.createGain();
                this.lfo.frequency.setValueAtTime(0.2, now);
                lfoGain.gain.setValueAtTime(250, now);
                this.lfo.connect(lfoGain);
                lfoGain.connect(this.filter.frequency);
                this.lfo.start();

                const osc1 = this.ctx.createOscillator();
                const gain1 = this.ctx.createGain();
                osc1.type = 'sawtooth';
                osc1.frequency.setValueAtTime(55, now);
                gain1.gain.setValueAtTime(0.35, now);
                osc1.connect(gain1);
                gain1.connect(this.filter);
                osc1.start();
                this.oscillators.push(osc1);

                const osc2 = this.ctx.createOscillator();
                const gain2 = this.ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(82.41, now);
                gain2.gain.setValueAtTime(0.4, now);
                osc2.connect(gain2);
                gain2.connect(this.filter);
                osc2.start();
                this.oscillators.push(osc2);

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
                    this.masterGain.gain.linearRampToValueAtTime(0.18, now + 1.2);
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
                this.masterGain.gain.linearRampToValueAtTime(0.0001, now + 0.4);
            } else {
                this.masterGain.gain.linearRampToValueAtTime(0.18, now + 0.6);
            }
            return !this.isMuted;
        }

        stop() {
            if (!this.ctx || !this.isPlaying) return;
            try {
                const now = this.ctx.currentTime;
                if (this.masterGain) {
                    this.masterGain.gain.linearRampToValueAtTime(0.0001, now + 0.5);
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
                }, 550);
            } catch (e) {}
        }
    }

    class SoulyCinematicLoaderManager {
        constructor() {
            this.audio = new SoulyCyberAudioEngine();
            this.duration = 11000; // 11 seconds Executive Cyber Showcase
            this.chapters = [
                {
                    id: 1,
                    range: [0, 25],
                    badge: 'القلعة الرقمية الأولى · SOULY CITADEL',
                    title: 'سولي.. القلعة الرقمية الأولى لإدارة وتشغيل الأعمال بالذكاء الاصطناعي في مصر',
                    subtitle: 'منظومة سيادية متكاملة تجمع بين أقوى برمجيات الأعمال وكوادر الذكاء الاصطناعي الفائقة والتدريع السيبراني المتقدم',
                    metric: 'جاهزية 100% · سيادة رقمية · معايير عالمية',
                    color: '#00F5D4'
                },
                {
                    id: 2,
                    range: [25, 50],
                    badge: 'الأنظمة السحابية والمحلية · BRAND-ERP',
                    title: 'نظام BrandERP الثوري: ابدأ بنظامك الأساسي بـ 2,000 ج.م فقط',
                    subtitle: 'وشكّل منظومتك من بين +300 ميزة ذكية حتى باقات المؤسسات الكاملة',
                    metric: 'نواة 2,000 EGP · 12 موديول · +300 ميزة دقيقة',
                    color: '#00F5D4'
                },
                {
                    id: 3,
                    range: [50, 75],
                    badge: 'الكوادر الرقمية والتدريع الهندسي · AI & CYBER',
                    title: 'موظفو ذكاء اصطناعي يعملون 24/7 بدون أخطاء',
                    subtitle: 'أمن سيبراني وحماية عائلية ومؤسسية.. وصيانة هندسية بمعايير عالمية',
                    metric: 'رد في 1.8 ثانية · تشفير 256-bit · صيانة بمقرك',
                    color: '#9D4EDD'
                },
                {
                    id: 4,
                    range: [75, 100],
                    badge: 'انطلاق المنظومة · SYSTEM ACTIVATION',
                    title: 'مرحباً بك في المستقبل.. كل شيء تحت سيطرتك بضغطة زر',
                    subtitle: 'محفظتك الرقمية ولوحة تحكمك جاهزتان فوراً لإطلاق خدماتك وحجز أنظمتك السيادية',
                    metric: '0ms استجابة فورية · تحكم مركزي كامل',
                    color: '#10B981'
                }
            ];

            this.currentChapter = 0;
            this.watchdogTimer = null;
            this.startTime = null;
            this.isSkipped = false;
            this.dom = {};
        }

        async shouldShowLoader() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('intro') === '1' || params.get('loader') === '1') {
                return true;
            }

            try {
                if (localStorage.getItem('souly_story_seen') === 'true') {
                    return false;
                }
            } catch (e) {}

            const path = window.location.pathname;
            if (path !== '/' && path !== '') {
                try { localStorage.setItem('souly_story_seen', 'true'); } catch (e) {}
                return false;
            }

            try {
                if (window.SoulyDB) {
                    const record = await Promise.race([
                        window.SoulyDB.get('souly_loader_watched'),
                        new Promise(resolve => setTimeout(() => resolve(null), 150))
                    ]);
                    if (record && record.timestamp) {
                        try { localStorage.setItem('souly_story_seen', 'true'); } catch (e) {}
                        return false;
                    }
                }
            } catch (e) {}

            return true;
        }

        async recordWatched() {
            try {
                localStorage.setItem('souly_story_seen', 'true');
                if (window.SoulyDB) {
                    await window.SoulyDB.set('souly_loader_watched', {
                        timestamp: Date.now(),
                        version: '2.5'
                    });
                }
            } catch (e) {}
        }

        hideAndUnlock() {
            document.documentElement.classList.add('souly-loader-hidden');
            if (this.dom.wrapper) {
                this.dom.wrapper.style.display = 'none';
            }
            document.body.classList.remove('overflow-hidden');
            if (window.SoulyUI && typeof window.SoulyUI.forceUnlockScroll === 'function') {
                window.SoulyUI.forceUnlockScroll();
            }
        }

        async init() {
            this.dom.wrapper = document.getElementById('souly-cinematic-loader');
            if (!this.dom.wrapper) return;

            this.dom.skipBtn = document.getElementById('loader-skip-btn');
            this.dom.soundBtn = document.getElementById('loader-sound-toggle');
            this.dom.progressBar = document.getElementById('loader-progress-bar');
            this.dom.percentText = document.getElementById('loader-percent-text');
            this.dom.secondsText = document.getElementById('loader-seconds-text');
            this.dom.badge = document.getElementById('loader-chapter-badge');
            this.dom.title = document.getElementById('loader-chapter-title');
            this.dom.subtitle = document.getElementById('loader-chapter-subtitle');
            this.dom.metric = document.getElementById('loader-chapter-metric');
            this.dom.indicators = document.querySelectorAll('.loader-chapter-pill');

            // 1. Bind events synchronously and immediately so skip works instantly
            this.bindEvents();

            // 2. Check if loader should display
            const shouldShow = await this.shouldShowLoader();
            if (!shouldShow) {
                this.hideAndUnlock();
                return;
            }

            document.documentElement.classList.remove('souly-loader-hidden');
            this.dom.wrapper.style.display = 'flex';
            document.body.classList.add('overflow-hidden');

            this.startLoader();
        }

        bindEvents() {
            if (this.eventsBound) return;
            this.eventsBound = true;

            const handleSkip = (e) => {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                this.skip();
            };

            if (this.dom.skipBtn) {
                this.dom.skipBtn.addEventListener('click', handleSkip);
                this.dom.skipBtn.addEventListener('touchend', handleSkip, { passive: false });
            }

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !this.isSkipped) {
                    this.skip();
                }
            });

            if (this.dom.soundBtn) {
                this.dom.soundBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const active = this.audio.toggleMute();
                    this.updateSoundBtn(active);
                });
            }

            this.dom.wrapper.addEventListener('click', () => {
                if (!this.audio.isPlaying && !this.audio.isMuted) {
                    this.audio.startSynthesizer();
                }
            }, { once: true });
        }

        updateSoundBtn(isActive) {
            if (!this.dom.soundBtn) return;
            const textEl = this.dom.soundBtn.querySelector('.sound-btn-text');
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

            // Watchdog timer ensures loader always closes even if tab is backgrounded
            if (this.watchdogTimer) clearTimeout(this.watchdogTimer);
            this.watchdogTimer = setTimeout(() => {
                if (!this.isSkipped) this.complete();
            }, this.duration + 1200);

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

                const chapterIdx = Math.min(3, Math.floor(progress / 25));
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

            const animEls = [this.dom.badge, this.dom.title, this.dom.subtitle, this.dom.metric].filter(Boolean);
            if (typeof gsap !== 'undefined') {
                gsap.timeline()
                    .to(animEls, {
                        opacity: 0,
                        y: -8,
                        duration: 0.18,
                        onComplete: () => {
                            if (this.dom.badge) {
                                this.dom.badge.textContent = chap.badge;
                                this.dom.badge.style.color = chap.color;
                                this.dom.badge.style.borderColor = `${chap.color}50`;
                            }
                            if (this.dom.title) this.dom.title.textContent = chap.title;
                            if (this.dom.subtitle) this.dom.subtitle.textContent = chap.subtitle;
                            if (this.dom.metric) {
                                this.dom.metric.textContent = chap.metric;
                                this.dom.metric.style.color = chap.color;
                                this.dom.metric.style.borderColor = `${chap.color}40`;
                            }
                        }
                    })
                    .to(animEls, {
                        opacity: 1,
                        y: 0,
                        duration: 0.32,
                        stagger: 0.05,
                        ease: 'power2.out'
                    });
            } else {
                if (this.dom.badge) this.dom.badge.textContent = chap.badge;
                if (this.dom.title) this.dom.title.textContent = chap.title;
                if (this.dom.subtitle) this.dom.subtitle.textContent = chap.subtitle;
                if (this.dom.metric) this.dom.metric.textContent = chap.metric;
            }
        }

        skip() {
            if (this.isSkipped) return;
            this.complete();
        }

        complete() {
            if (this.isSkipped) return;
            this.isSkipped = true;
            if (this.watchdogTimer) clearTimeout(this.watchdogTimer);

            this.audio.stop();
            this.recordWatched();

            if (typeof gsap !== 'undefined' && this.dom.wrapper) {
                gsap.to(this.dom.wrapper, {
                    opacity: 0,
                    scale: 1.03,
                    duration: 0.45,
                    ease: 'power3.inOut',
                    onComplete: () => {
                        this.hideAndUnlock();
                        if (window.SoulyUI && typeof window.SoulyUI.triggerPageEntry === 'function') {
                            window.SoulyUI.triggerPageEntry();
                        }
                    }
                });
            } else {
                this.hideAndUnlock();
            }
        }

        async replay() {
            try {
                localStorage.removeItem('souly_story_seen');
                if (window.SoulyDB) {
                    await window.SoulyDB.delete('souly_loader_watched');
                }
            } catch (e) {}
            window.location.href = window.location.pathname + '?intro=1';
        }
    }

    const loaderInstance = new SoulyCinematicLoaderManager();
    global.SoulyLoader = loaderInstance;

    function bootLoader() {
        try {
            loaderInstance.init();
        } catch(e) {
            console.error('[SoulyLoader] Boot error:', e);
            loaderInstance.hideAndUnlock();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootLoader);
    } else {
        bootLoader();
    }

})(typeof window !== 'undefined' ? window : globalThis);