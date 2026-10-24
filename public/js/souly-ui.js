/**
 * Souly Digital Citadel - Core UI Engine (SoulyUI)
 * Features:
 * - SPA-Like View Transitions & GSAP Page Choreography
 * - 0ms IndexedDB Pre-caching for Universal Icon Engine
 * - Mobile Shell Spring Physics & Floating AI Orb Controls
 */
(function (global) {
    'use strict';

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
        }

        /**
         * Smooth SPA-like Page Transitions using CSS View Transitions API & GSAP Fallback
         */
        setupViewTransitions() {
            // Trigger GSAP Page Entry Animation
            this.triggerPageEntry();

            // Intercept internal page navigations for smooth app feel
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                const href = link.getAttribute('href');
                const target = link.getAttribute('target');

                // Skip non-navigational links
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:') || target === '_blank') {
                    return;
                }

                // Check if origin matches current host
                const url = new URL(href, window.location.origin);
                if (url.origin !== window.location.origin) return;

                // Support Native View Transitions if browser supports it
                if (document.startViewTransition) {
                    // Let native view transition handle or animate exit
                    return;
                }

                // GSAP exit choreography
                const root = document.getElementById('citadel-root');
                if (root && typeof gsap !== 'undefined') {
                    e.preventDefault();
                    gsap.to(root, {
                        opacity: 0,
                        y: -12,
                        filter: 'blur(4px)',
                        duration: 0.22,
                        ease: 'power2.in',
                        onComplete: () => {
                            window.location.href = href;
                        }
                    });
                }
            });
        }

        triggerPageEntry() {
            const root = document.getElementById('citadel-root');
            if (root && typeof gsap !== 'undefined') {
                gsap.fromTo(root, 
                    { opacity: 0, y: 15 }, 
                    { opacity: 1, y: 0, duration: 0.45, ease: 'power2.out', clearProps: 'all' }
                );
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
                    if (typeof gsap !== 'undefined') {
                        gsap.timeline()
                            .to(aiOrb, { scale: 0.82, rotate: -15, duration: 0.12 })
                            .to(aiOrb, { scale: 1.15, rotate: 10, duration: 0.25, ease: 'elastic.out(1, 0.4)' })
                            .to(aiOrb, { scale: 1, rotate: 0, duration: 0.18 });
                    }
                    // Trigger AI Conversation Sheet Event
                    window.dispatchEvent(new CustomEvent('open-ai-chat-sheet'));
                });
            }
        }
    }

    const instance = new SoulyUIManager();
    global.SoulyUI = instance;

    document.addEventListener('DOMContentLoaded', () => {
        instance.init();
    });

})(typeof window !== 'undefined' ? window : globalThis);
