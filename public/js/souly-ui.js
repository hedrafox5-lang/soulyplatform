/**
 * Souly Digital Citadel - Core UI Engine (SoulyUI)
 * Version: 2.0.0
 * Features:
 * - Universal Bioluminescent Top Loading Bar (SoulyLoadingBar)
 * - View Transitions & Seamless Route Choreography
 * - Automatic Auto-Close for Modals, Drawers & Context Menus
 * - 0ms IndexedDB Pre-caching for Universal Icon Matrix
 * - Spring Physics & Micro-Interactions
 */
(function (global) {
    'use strict';

    /**
     * =========================================================================
     * 1. UNIVERSAL BIOLUMINESCENT TOP LOADING BAR
     * =========================================================================
     */
    class SoulyLoadingBarManager {
        constructor() {
            this.progress = 0;
            this.timer = null;
            this.element = null;
            this.createBar();
        }

        createBar() {
            if (document.getElementById('souly-top-loader')) {
                this.element = document.getElementById('souly-top-loader');
                return;
            }

            const bar = document.createElement('div');
            bar.id = 'souly-top-loader';
            bar.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                height: 3px;
                width: 0%;
                background: linear-gradient(90deg, #7B2CBF, #00F5D4 60%, #FFB800 100%);
                box-shadow: 0 0 12px rgba(0, 245, 212, 0.85), 0 0 24px rgba(0, 245, 212, 0.45);
                z-index: 999999;
                pointer-events: none;
                transition: width 0.22s cubic-bezier(0.1, 0.9, 0.2, 1), opacity 0.3s ease;
                opacity: 0;
            `;
            document.body.appendChild(bar);
            this.element = bar;
        }

        start() {
            if (!this.element) this.createBar();
            if (this.timer) clearInterval(this.timer);

            this.progress = 12;
            this.element.style.opacity = '1';
            this.element.style.width = '12%';

            this.timer = setInterval(() => {
                if (this.progress < 85) {
                    const diff = Math.max(1, (85 - this.progress) * 0.15);
                    this.progress += diff;
                    this.element.style.width = `${this.progress}%`;
                }
            }, 120);
        }

        set(pct) {
            if (!this.element) this.createBar();
            this.progress = Math.min(100, Math.max(0, pct));
            this.element.style.opacity = '1';
            this.element.style.width = `${this.progress}%`;
        }

        done() {
            if (!this.element) return;
            if (this.timer) clearInterval(this.timer);

            this.progress = 100;
            this.element.style.width = '100%';

            setTimeout(() => {
                this.element.style.opacity = '0';
                setTimeout(() => {
                    this.element.style.width = '0%';
                    this.progress = 0;
                }, 300);
            }, 180);
        }

        pulse(durationMs = 350) {
            this.start();
            setTimeout(() => {
                this.done();
            }, durationMs);
        }
    }

    const LoadingBarInstance = new SoulyLoadingBarManager();
    global.SoulyLoadingBar = LoadingBarInstance;

    /**
     * =========================================================================
     * 2. SOULY UI MANAGER (TRANSITIONS, PRE-CACHE, LISTENERS)
     * =========================================================================
     */
    class SoulyUIManager {
        constructor() {
            this.initialized = false;
        }

        init() {
            if (this.initialized) return;
            this.initialized = true;

            this.setupViewTransitions();
            this.setupIconAutoListeners();
            this.setupPrecachePayload();
            this.setupMobileNavSprings();
            this.setupFetchInterceptor();
            this.setupGlobalLoadingHooks();
            this.setupMicroAnimations();
        }

        /**
         * Global Interception for Fetch / AJAX to automatically run LoadingBar
         */
        setupFetchInterceptor() {
            if (typeof window.fetch !== 'function') return;

            const originalFetch = window.fetch;
            window.fetch = async (...args) => {
                const url = typeof args[0] === 'string' ? args[0] : (args[0]?.url || '');
                // Skip logging or background polling
                const isSilent = url.includes('/tasks') || url.includes('/log');
                
                if (!isSilent) {
                    LoadingBarInstance.start();
                }

                try {
                    const response = await originalFetch(...args);
                    if (!isSilent) LoadingBarInstance.done();
                    return response;
                } catch (error) {
                    if (!isSilent) LoadingBarInstance.done();
                    throw error;
                }
            };
        }

        /**
         * Trigger loading bar on modal/drawer state changes
         */
        setupGlobalLoadingHooks() {
            const pulseEvents = [
                'open-icon-popup',
                'close-icon-popup',
                'open-ai-chat-sheet',
                'close-ai-chat-sheet',
                'open-command-palette',
                'close-command-palette',
                'open-notification-center',
                'close-notification-center'
            ];

            pulseEvents.forEach(evtName => {
                window.addEventListener(evtName, () => {
                    LoadingBarInstance.pulse(280);
                });
            });
        }

        /**
         * Smooth SPA-like Page Transitions using CSS View Transitions API & GSAP Fallback
         */
        setupViewTransitions() {
            // Trigger Page Entry Animation
            this.triggerPageEntry();

            // Intercept internal page navigations for smooth app feel
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                const href = link.getAttribute('href');
                const target = link.getAttribute('target');

                // Skip non-navigational links
                if (!href || 
                    href.startsWith('#') || 
                    href.startsWith('javascript:') || 
                    href.startsWith('mailto:') || 
                    href.startsWith('tel:') || 
                    target === '_blank') {
                    return;
                }

                // Check origin
                let url;
                try {
                    url = new URL(href, window.location.origin);
                } catch (err) {
                    return;
                }

                if (url.origin !== window.location.origin) return;

                // Auto-close any open drawer or context menu
                if (window.Alpine && window.Alpine.store('citadel')) {
                    window.Alpine.store('citadel').mobileNavOpen = false;
                    window.Alpine.store('citadel').contextMenuOpen = false;
                    window.Alpine.store('citadel').notificationCenterOpen = false;
                    window.Alpine.store('citadel').commandPaletteOpen = false;
                }

                // Fire the Top Loading Bar!
                LoadingBarInstance.start();

                // Native View Transitions if supported
                if (document.startViewTransition) {
                    return; // Let browser perform native transition while bar animates
                }

                // GSAP exit choreography
                const root = document.getElementById('citadel-root');
                if (root && typeof gsap !== 'undefined') {
                    e.preventDefault();
                    gsap.to(root, {
                        opacity: 0,
                        y: -10,
                        filter: 'blur(3px)',
                        duration: 0.18,
                        ease: 'power2.in',
                        onComplete: () => {
                            window.location.href = href;
                        }
                    });
                }
            });

            // When navigating back/forward (pageshow)
            window.addEventListener('pageshow', () => {
                LoadingBarInstance.done();
                this.triggerPageEntry();
            });
        }

        triggerPageEntry() {
            const root = document.getElementById('citadel-root');
            if (root && typeof gsap !== 'undefined') {
                gsap.fromTo(root, 
                    { opacity: 0, y: 12 }, 
                    { opacity: 1, y: 0, duration: 0.35, ease: 'power2.out', clearProps: 'all' }
                );

                // Staggered entry for cards & metrics
                gsap.from('.glass-obsidian, .cyber-card-glow', {
                    opacity: 0,
                    y: 16,
                    duration: 0.45,
                    stagger: 0.04,
                    ease: 'power2.out',
                    clearProps: 'all'
                });
            }
        }

        /**
         * Pre-cache all icons and service payloads into SoulyDB for 0ms lookup
         */
        async setupPrecachePayload() {
            const dataEl = document.getElementById('souly-precache-data');
            if (!dataEl || !window.SoulyDB) return;

            try {
                const data = JSON.parse(dataEl.textContent || '{}');
                if (data.icons && Array.isArray(data.icons)) {
                    for (const icon of data.icons) {
                        await window.SoulyDB.set(`icon_${icon.id}`, icon);
                    }
                    console.info(`[SoulyUI] Pre-cached ${data.icons.length} service icons into SoulyDB (0ms ready).`);
                }
            } catch (err) {
                console.warn('[SoulyUI] Pre-caching error:', err);
            }
        }

        /**
         * Global Delegated Click Listener for [data-icon-popup]
         */
        setupIconAutoListeners() {
            document.addEventListener('click', async (e) => {
                const trigger = e.target.closest('[data-icon-popup]');
                if (!trigger) return;

                e.preventDefault();
                LoadingBarInstance.pulse(200);

                const iconId = trigger.dataset.iconId;
                const iconTitle = trigger.dataset.iconTitle;
                const iconBadge = trigger.dataset.iconBadge;
                const iconSummary = trigger.dataset.iconSummary;
                const iconBullets = trigger.dataset.iconBullets ? JSON.parse(trigger.dataset.iconBullets) : null;
                const serviceName = trigger.dataset.serviceName;
                const ctaText = trigger.dataset.ctaText;
                const ctaLink = trigger.dataset.ctaLink;

                let payload = {
                    id: iconId,
                    title: iconTitle,
                    badge_text: iconBadge,
                    popup_title: iconTitle,
                    popup_summary: iconSummary,
                    popup_bullets: iconBullets,
                    service_name: serviceName,
                    popup_cta_text: ctaText,
                    cta_link: ctaLink
                };

                // If cached in SoulyDB, load full rich data in 0ms
                if (iconId && window.SoulyDB) {
                    try {
                        const cached = await window.SoulyDB.get(`icon_${iconId}`);
                        if (cached) {
                            payload = { ...payload, ...cached };
                        }
                    } catch (err) {}
                }

                window.dispatchEvent(new CustomEvent('open-icon-popup', { detail: payload }));
            });
        }

        /**
         * Spring physics for Mobile Bottom Navigation
         */
        setupMobileNavSprings() {
            document.querySelectorAll('.mobile-dock-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    LoadingBarInstance.pulse(180);
                    if (typeof gsap !== 'undefined') {
                        gsap.timeline()
                            .to(tab, { scale: 0.85, duration: 0.1, ease: 'power2.in' })
                            .to(tab, { scale: 1.08, duration: 0.2, ease: 'back.out(2)' })
                            .to(tab, { scale: 1, duration: 0.15, ease: 'power1.out' });
                    }
                });
            });

            // Souly AI Orb Spring Effect
            const aiOrb = document.getElementById('mobile-souly-ai-orb');
            if (aiOrb) {
                aiOrb.addEventListener('click', () => {
                    LoadingBarInstance.pulse(220);
                    if (typeof gsap !== 'undefined') {
                        gsap.timeline()
                            .to(aiOrb, { scale: 0.82, rotate: -15, duration: 0.12 })
                            .to(aiOrb, { scale: 1.15, rotate: 10, duration: 0.25, ease: 'elastic.out(1, 0.4)' })
                            .to(aiOrb, { scale: 1, rotate: 0, duration: 0.18 });
                    }
                    window.dispatchEvent(new CustomEvent('open-ai-chat-sheet'));
                });
            }
        }

        /**
         * Subtle Artistic Micro-Interactions (Magnetic Hover & Cursor Glow)
         */
        setupMicroAnimations() {
            // Magnetic effect for primary buttons
            document.querySelectorAll('.btn-magnetic').forEach(btn => {
                btn.addEventListener('mousemove', (e) => {
                    const rect = btn.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    if (typeof gsap !== 'undefined') {
                        gsap.to(btn, { x: x * 0.25, y: y * 0.25, duration: 0.2, ease: 'power1.out' });
                    }
                });
                btn.addEventListener('mouseleave', () => {
                    if (typeof gsap !== 'undefined') {
                        gsap.to(btn, { x: 0, y: 0, duration: 0.35, ease: 'elastic.out(1, 0.5)' });
                    }
                });
            });
        }
    }

    const instance = new SoulyUIManager();
    global.SoulyUI = instance;

    document.addEventListener('DOMContentLoaded', () => {
        instance.init();
    });

})(typeof window !== 'undefined' ? window : globalThis);
