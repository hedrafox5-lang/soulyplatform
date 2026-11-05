/**
 * Souly Digital Citadel - Core UI Engine (SoulyUI)
 * Version: 2.5.0 (Mobile-First & Zero-Jump Edition)
 * Features:
 * - Universal Bioluminescent Top Loading Bar (SoulyLoadingBar)
 * - Smart Scroll-Lock Guardian (Zero Page Freeze Guarantee)
 * - Horizontal-Only RTL-Safe Mobile Carousel (Zero Vertical Page Jump)
 * - 0ms Batch IndexedDB Pre-caching for Universal Icon Matrix
 * - Touch-Aware Micro-Interactions & Spring Physics
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
            this.head = null;
            this.createBar();
        }

        createBar() {
            const existing = document.getElementById('souly-top-loader') || document.getElementById('souly-top-progress');
            if (existing) {
                this.element = existing;
                this.head = this.element.querySelector('.souly-loader-head');
                return;
            }

            const bar = document.createElement('div');
            bar.id = 'souly-top-loader';
            bar.className = 'souly-cyber-loader';
            bar.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                height: 3px;
                width: 0%;
                background: linear-gradient(90deg, #7B2CBF 0%, #00F5D4 65%, #FFFFFF 100%);
                box-shadow: 0 0 16px rgba(0, 245, 212, 0.9), 0 0 32px rgba(0, 245, 212, 0.5);
                z-index: 9999999;
                pointer-events: none;
                transition: width 0.18s cubic-bezier(0.1, 0.8, 0.2, 1), opacity 0.2s ease;
                opacity: 0;
            `;

            const head = document.createElement('div');
            head.className = 'souly-loader-head';
            head.style.cssText = `
                position: absolute;
                top: -2px;
                right: 0;
                width: 90px;
                height: 7px;
                background: radial-gradient(circle, #FFFFFF 0%, #00F5D4 60%, transparent 100%);
                box-shadow: 0 0 18px #00F5D4, 0 0 32px #00F5D4;
                border-radius: 9999px;
                pointer-events: none;
                opacity: 0.95;
            `;
            bar.appendChild(head);

            if (document.body) {
                document.body.appendChild(bar);
            } else {
                document.addEventListener('DOMContentLoaded', () => {
                    if (document.body && !document.getElementById('souly-top-loader')) {
                        document.body.appendChild(bar);
                    }
                });
            }
            this.element = bar;
            this.head = head;
        }

        start() {
            if (!this.element) this.createBar();
            if (!this.element) return;

            if (document.body && !document.body.contains(this.element)) {
                document.body.appendChild(this.element);
            }
            if (this.timer) clearInterval(this.timer);

            this.progress = 24;
            this.element.style.opacity = '1';
            this.element.style.width = '24%';

            this.timer = setInterval(() => {
                if (this.progress < 90) {
                    const diff = Math.max(0.8, (90 - this.progress) * 0.16);
                    this.progress += diff;
                    this.element.style.width = `${this.progress}%`;
                }
            }, 80);
        }

        set(pct) {
            if (!this.element) this.createBar();
            if (!this.element) return;
            this.progress = Math.min(100, Math.max(0, pct));
            this.element.style.opacity = '1';
            this.element.style.width = `${this.progress}%`;
        }

        done() {
            if (!this.element) return;
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }

            this.progress = 100;
            this.element.style.width = '100%';

            setTimeout(() => {
                if (!this.element) return;
                this.element.style.opacity = '0';
                setTimeout(() => {
                    if (!this.element) return;
                    this.element.style.width = '0%';
                    this.progress = 0;
                }, 200);
            }, 100);
        }

        pulse(durationMs = 240) {
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
     * 2. SOULY UI MANAGER (SCROLL GUARDIAN, TRANSITIONS, PRE-CACHE)
     * =========================================================================
     */
    class SoulyUIManager {
        constructor() {
            this.initialized = false;
            this.activeModals = new Set();
            this.originalPaddingRight = '';
            this.isTouchDevice = false;
        }

        lockScroll(source = 'modal') {
            if (!document.body) return;
            if (this.activeModals.size === 0) {
                const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                this.originalPaddingRight = document.body.style.paddingRight || '';
                if (scrollbarWidth > 0 && window.innerWidth >= 1024) {
                    document.body.style.paddingRight = `${scrollbarWidth}px`;
                }
                document.body.classList.add('overflow-hidden');
            }
            this.activeModals.add(source);
        }

        unlockScroll(source = 'modal') {
            if (!document.body) return;
            if (source) {
                this.activeModals.delete(source);
            } else {
                this.activeModals.clear();
            }

            if (this.activeModals.size === 0) {
                this.forceUnlockScroll();
            }
        }

        forceUnlockScroll() {
            this.activeModals.clear();
            if (!document.body) return;
            // Only keep overflow-hidden if cinematic loader is actively visible
            const loader = document.getElementById('souly-cinematic-loader');
            const isLoaderVisible = loader && loader.style.display !== 'none' && getComputedStyle(loader).display !== 'none';
            if (!isLoaderVisible) {
                document.body.style.paddingRight = this.originalPaddingRight || '';
                document.body.style.overflow = '';
                document.body.classList.remove('overflow-hidden');
            }
        }

        init() {
            if (this.initialized) return;
            this.initialized = true;
            this.isTouchDevice = ('ontouchstart' in window) || navigator.maxTouchPoints > 0 || window.matchMedia('(pointer: coarse)').matches;

            this.setupViewTransitions();
            this.setupIconAutoListeners();
            this.setupPrecachePayload();
            this.setupMobileNavSprings();
            this.setupFetchInterceptor();
            this.setupGlobalLoadingHooks();
            this.setupMicroAnimations();
            this.setupOneClickCopy();
            this.setupCursorGlow();
        }

        setupFetchInterceptor() {
            if (typeof window.fetch !== 'function') return;

            const originalFetch = window.fetch;
            window.fetch = async (...args) => {
                const url = typeof args[0] === 'string' ? args[0] : (args[0]?.url || '');
                const isSilent = url.includes('/tasks') || url.includes('/log') || url.includes('/notifications') || url.includes('/api/catalog/icons');

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

        setupGlobalLoadingHooks() {
            const modalPairs = [
                ['open-icon-popup', 'close-icon-popup', 'icon-popup'],
                ['open-express-booking', 'close-express-booking', 'express-booking'],
                ['open-sector-popup', 'close-sector-popup', 'sector-popup'],
                ['branderp-store-open', 'branderp-store-close', 'branderp-store'],
                ['branderp-roi-open', 'branderp-roi-close', 'branderp-roi']
            ];

            modalPairs.forEach(([openEvt, closeEvt, key]) => {
                window.addEventListener(openEvt, () => {
                    this.lockScroll(key);
                    LoadingBarInstance.pulse(200);
                });
                window.addEventListener(closeEvt, () => {
                    this.unlockScroll(key);
                });
            });

            // Escape key universal unlock safeguard
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    setTimeout(() => this.forceUnlockScroll(), 60);
                }
            });

            document.addEventListener('click', (e) => {
                const trigger = e.target.closest('[data-modal-trigger], [data-close-modal], [data-close], [data-bs-dismiss], [data-drawer-toggle]');
                if (trigger) {
                    LoadingBarInstance.pulse(180);
                }
            });
        }

        setupViewTransitions() {
            this.triggerPageEntry();

            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                const href = link.getAttribute('href');
                const target = link.getAttribute('target');

                if (!href ||
                    href.startsWith('#') ||
                    href.startsWith('javascript:') ||
                    href.startsWith('mailto:') ||
                    href.startsWith('tel:') ||
                    target === '_blank' ||
                    e.ctrlKey || e.metaKey || e.shiftKey) {
                    return;
                }

                let url;
                try {
                    url = new URL(href, window.location.origin);
                } catch (err) {
                    return;
                }

                if (url.origin !== window.location.origin) return;

                // Same-page hash link: smooth scroll without firing page reload bar
                if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) {
                    return;
                }

                // Auto-close any open panels before navigating
                if (window.Alpine && window.Alpine.store('citadel')) {
                    const store = window.Alpine.store('citadel');
                    store.mobileNavOpen = false;
                    store.contextMenuOpen = false;
                    store.notificationCenterOpen = false;
                    store.commandPaletteOpen = false;
                    store.aiSheetOpen = false;
                }
                this.forceUnlockScroll();

                LoadingBarInstance.start();
            });

            window.addEventListener('pageshow', () => {
                LoadingBarInstance.done();
                this.forceUnlockScroll();
            });

            window.addEventListener('load', () => {
                LoadingBarInstance.done();
            });
        }

                triggerPageEntry() {
            const root = document.getElementById('citadel-root');
            if (root && typeof gsap !== 'undefined') {
                gsap.fromTo(root,
                    { opacity: 0, y: 8 },
                    { opacity: 1, y: 0, duration: 0.28, ease: 'power2.out', clearProps: 'transform,opacity' }
                );

                // Scope staggered entry ONLY to main page static cards (NEVER touch popups or modals)
                const staticCards = document.querySelectorAll('#citadel-root main section .glass-obsidian:not([x-show]):not([x-cloak])');
                if (staticCards.length > 0) {
                    gsap.from(staticCards, {
                        opacity: 0,
                        y: 14,
                        duration: 0.35,
                        stagger: 0.03,
                        ease: 'power2.out',
                        clearProps: 'transform,opacity'
                    });
                }
            }
        }



        async setupPrecachePayload() {
            const dataEl = document.getElementById('souly-precache-data');
            if (dataEl && window.SoulyDB) {
                try {
                    const data = JSON.parse(dataEl.textContent || '{}');
                    if (data.icons && Array.isArray(data.icons) && data.icons.length > 0) {
                        const entries = data.icons.map(icon => [`icon_${icon.id}`, icon]);
                        await window.SoulyDB.setMany(entries);
                    }
                } catch (err) {
                    console.warn('[SoulyUI] Pre-caching error:', err);
                }
            }

            if (window.fetch && window.SoulyDB) {
                const runFetch = () => {
                    fetch('/api/catalog/icons')
                        .then(res => res.ok ? res.json() : null)
                        .then(async data => {
                            if (data && data.icons && Array.isArray(data.icons)) {
                                const entries = data.icons.map(icon => [`icon_${icon.id}`, icon]);
                                await window.SoulyDB.setMany(entries);
                            }
                        })
                        .catch(() => {});
                };

                if ('requestIdleCallback' in window) {
                    window.requestIdleCallback(runFetch, { timeout: 2500 });
                } else {
                    setTimeout(runFetch, 600);
                }
            }
        }

        setupIconAutoListeners() {
            document.addEventListener('click', async (e) => {
                const trigger = e.target.closest('[data-icon-popup]');
                if (!trigger) return;

                // Prevent double execution if button already has Alpine @click="$dispatch('open-icon-popup'...)"
                if (trigger.hasAttribute('@click') || trigger.hasAttribute('x-on:click')) {
                    return;
                }

                e.preventDefault();
                LoadingBarInstance.pulse(180);

                const iconId = trigger.dataset.iconId;
                const iconTitle = trigger.dataset.iconTitle;
                const iconBadge = trigger.dataset.iconBadge;
                const iconSummary = trigger.dataset.iconSummary;
                let iconBullets = null;
                try {
                    iconBullets = trigger.dataset.iconBullets ? JSON.parse(trigger.dataset.iconBullets) : null;
                } catch (err) {
                    iconBullets = [];
                }
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

        setupMobileNavSprings() {
            document.querySelectorAll('.mobile-dock-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    LoadingBarInstance.pulse(150);
                    if (typeof gsap !== 'undefined') {
                        gsap.timeline()
                            .to(tab, { scale: 0.88, duration: 0.08, ease: 'power2.in' })
                            .to(tab, { scale: 1.05, duration: 0.16, ease: 'back.out(2)' })
                            .to(tab, { scale: 1, duration: 0.12, ease: 'power1.out' });
                    }
                });
            });

            // Spring animation ONLY on AI Orb (State toggling is handled cleanly by Alpine in mobile.blade.php)
            document.querySelectorAll('.mobile-souly-ai-orb, #mobile-souly-ai-orb').forEach(aiOrb => {
                aiOrb.addEventListener('click', () => {
                    LoadingBarInstance.pulse(180);
                    if (typeof gsap !== 'undefined') {
                        gsap.timeline()
                            .to(aiOrb, { scale: 0.85, duration: 0.1 })
                            .to(aiOrb, { scale: 1.1, duration: 0.2, ease: 'back.out(2)' })
                            .to(aiOrb, { scale: 1, duration: 0.15 });
                    }
                });
            });
        }

        setupMicroAnimations() {
            if (this.isTouchDevice) return;

            document.querySelectorAll('.btn-magnetic').forEach(btn => {
                btn.addEventListener('mousemove', (e) => {
                    const rect = btn.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    if (typeof gsap !== 'undefined') {
                        gsap.to(btn, { x: x * 0.22, y: y * 0.22, duration: 0.2, ease: 'power1.out' });
                    }
                });
                btn.addEventListener('mouseleave', () => {
                    if (typeof gsap !== 'undefined') {
                        gsap.to(btn, { x: 0, y: 0, duration: 0.35, ease: 'elastic.out(1, 0.5)' });
                    }
                });
            });
        }

        setupOneClickCopy() {
            document.addEventListener('click', (e) => {
                const target = e.target.closest('[data-copy], .copyable, [data-clipboard-text]');
                if (!target) return;

                const text = target.getAttribute('data-copy') ||
                             target.getAttribute('data-clipboard-text') ||
                             target.innerText.trim();
                if (!text) return;

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(() => {
                        const preview = text.length > 26 ? text.slice(0, 26) + '...' : text;
                        if (window.Alpine && window.Alpine.store('citadel')) {
                            window.Alpine.store('citadel').showToast(`تم النسخ بنجاح ✓ (${preview})`);
                        }
                    }).catch(() => {});
                }
            });
        }

        setupCursorGlow() {
            // Skip radial mouse tracking on mobile touch screens to preserve 60fps scrolling
            if (this.isTouchDevice) return;

            document.addEventListener('mousemove', (e) => {
                const card = e.target.closest('.cursor-radial-glow, .group\\/card, section[id^="service-"], .glass-obsidian');
                if (!card) return;

                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            }, { passive: true });
        }
    }

    /**
     * =========================================================================
     * 3. VISITOR PERSISTENT MULTI-LAYER IDENTITY (COOKIE + LOCALSTORAGE + INDEXEDDB)
     * =========================================================================
     */
    function getVisitorCookie(name) {
        const matches = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'));
        return matches ? decodeURIComponent(matches[1]) : null;
    }

    function setVisitorCookie(name, value, days = 365) {
        const d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${encodeURIComponent(value)};expires=${d.toUTCString()};path=/;SameSite=Lax`;
    }

    function generateUuidV4() {
        if (typeof crypto !== 'undefined' && crypto.randomUUID) {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            const r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    class SoulyVisitorIdentityManager {
        constructor() {
            this.visitorUuid = null;
            this.visitorName = null;
            this.init();
        }

        init() {
            let id = getVisitorCookie('souly_visitor_uuid');

            if (!id) {
                try {
                    id = localStorage.getItem('souly_visitor_uuid');
                } catch (e) {}
            }

            if (!id) {
                id = generateUuidV4();
            }

            this.visitorUuid = id;

            try {
                setVisitorCookie('souly_visitor_uuid', id, 365);
                localStorage.setItem('souly_visitor_uuid', id);
            } catch (e) {}

            if (window.SoulyDB) {
                window.SoulyDB.set('souly_visitor_uuid', id).catch(() => {});
            }

            try {
                this.visitorName = localStorage.getItem('souly_visitor_name') || getVisitorCookie('souly_visitor_name') || null;
            } catch (e) {}
        }

        getUuid() {
            if (!this.visitorUuid) this.init();
            return this.visitorUuid;
        }

        getName() {
            return this.visitorName;
        }

        setName(name) {
            if (!name) return;
            this.visitorName = name;
            try {
                localStorage.setItem('souly_visitor_name', name);
                setVisitorCookie('souly_visitor_name', name, 365);
            } catch (e) {}
            if (window.SoulyDB) {
                window.SoulyDB.set('souly_visitor_name', name).catch(() => {});
            }
        }
    }

    const IdentityInstance = new SoulyVisitorIdentityManager();
    global.SoulyIdentity = IdentityInstance;

    const instance = new SoulyUIManager();
    global.SoulyUI = instance;

    /**
     * =========================================================================
     * 4. GLOBAL BRAND-ERP ROI SIMULATOR ENGINE (Alpine.js)
     * =========================================================================
     */
    global.erpRoiSimulatorEngine = function erpRoiSimulatorEngine() {
        return {
            activity: 'retail',
            branches: 1,
            staff: 2,
            orders: 1500,

            get calculatedHoursSaved() {
                return Math.round(25 + (this.branches * 14) + (this.staff * 6) + (this.orders / 300));
            },

            get calculatedAnnualSavings() {
                const base = this.branches * 18000;
                const volumeFactor = this.orders * 4.5;
                const manufacturingBonus = this.activity === 'manufacturing' ? 35000 : 0;
                return Math.round(base + volumeFactor + manufacturingBonus);
            },

            get recommendedPackage() {
                if (this.activity === 'manufacturing' || this.branches >= 6 || this.staff >= 15) {
                    return {
                        name: 'باقة المصانع والمؤسسات (Enterprise)',
                        slug: 'enterprise',
                        price: 26500,
                        unit: 'سنوياً',
                        reason: 'تتضمن وحدة التصنيع وهدر المواد (BOM) والمخازن غير المحدودة والربط المحاسبي الكامل.',
                        modules: ['كاشير POS السريع', 'الفاتورة الإلكترونية ETA', 'وحدة التصنيع وBOM', 'المخازن المركزية', 'شجرة الحسابات', 'الربط البرمجي API']
                    };
                }
                if (this.branches >= 2 || this.staff >= 4 || this.activity === 'multi_branch') {
                    return {
                        name: 'باقة النمو للشركات (Growth Pro)',
                        slug: 'growth',
                        price: 1850,
                        unit: 'شهرياً',
                        reason: 'مثالية للربط بين حتى 5 فروع، شجرة حسابات متقدمة، موديول الأقساط وشؤون الموظفين HR.',
                        modules: ['كاشير POS السريع', 'الفاتورة والإيصال ETA', 'ربط حتى 5 فروع', 'الأقساط والمديونيات', 'شؤون الموظفين HR', 'تطبيق الإدارة']
                    };
                }
                return {
                    name: 'باقة الانطلاق السحابي (Starter)',
                    slug: 'starter',
                    price: 850,
                    unit: 'شهرياً',
                    reason: 'الحل الأمثل للأنشطة الناشئة والمتاجر: كاشير ذكي، جرد الباركود، والربط الضريبي المعتمد.',
                    modules: ['كاشير POS أوفلاين/أونلاين', 'الفاتورة والإيصال ETA', 'إدارة المخزن والباركود', 'تقارير الأرباح والخزينة', 'نسخ احتياطي يومي']
                };
            }
        };
    };

    /**
     * =========================================================================
     * 5. SOULY NATIVE MOBILE CAROUSEL CONTROLLER (Horizontal-Only, Zero Vertical Jump)
     * =========================================================================
     */
    global.soulyMobileCarousel = function soulyMobileCarousel(options = {}) {
        return {
            active: 0,
            currentIndex: 0,
            totalItems: options.total || 0,
            // Autoplay disabled by default unless explicitly requested to prevent unwanted jumping
            autoPlayInterval: options.autoPlay === true ? (options.autoPlayInterval || 5500) : 0,
            autoPlayTimer: null,
            isPaused: false,
            isVisible: false,
            observer: null,

            init() {
                this.$nextTick(() => {
                    const track = this.$refs.track;
                    if (track) {
                        const children = track.children;
                        if (children && children.length > 0) {
                            this.totalItems = children.length;
                        }

                        // Observe visibility so off-screen carousels never run timers
                        if ('IntersectionObserver' in window) {
                            this.observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    this.isVisible = entry.isIntersecting;
                                });
                            }, { threshold: 0.4 });
                            this.observer.observe(track);
                        } else {
                            this.isVisible = true;
                        }
                    }

                    if (this.autoPlayInterval > 0) {
                        this.startAutoPlay();
                    }
                });
            },

            syncIndex(idx) {
                this.active = idx;
                this.currentIndex = idx;
            },

            startAutoPlay() {
                if (this.autoPlayTimer) {
                    clearInterval(this.autoPlayTimer);
                }
                this.autoPlayTimer = setInterval(() => {
                    if (!this.isPaused && this.isVisible) {
                        this.next();
                    }
                }, this.autoPlayInterval);
            },

            stopAutoPlay() {
                if (this.autoPlayTimer) {
                    clearInterval(this.autoPlayTimer);
                    this.autoPlayTimer = null;
                }
            },

            pause() {
                this.isPaused = true;
            },

            resume() {
                setTimeout(() => {
                    this.isPaused = false;
                }, 2500);
            },

            next() {
                if (this.totalItems <= 1) return;
                const nextIdx = (this.active + 1) % this.totalItems;
                this.syncIndex(nextIdx);
                this.scrollToActive();
            },

            prev() {
                if (this.totalItems <= 1) return;
                const prevIdx = (this.active - 1 + this.totalItems) % this.totalItems;
                this.syncIndex(prevIdx);
                this.scrollToActive();
            },

            goTo(index) {
                if (index < 0 || index >= this.totalItems) return;
                this.syncIndex(index);
                this.scrollToActive();
            },

            /**
             * Strictly Horizontal Scroll using BoundingRect Delta (100% RTL-Safe & Zero Vertical Jump)
             */
            scrollToActive() {
                const track = this.$refs.track;
                if (!track) return;
                const activeCard = track.children[this.active];
                if (!activeCard) return;

                const trackRect = track.getBoundingClientRect();
                const cardRect = activeCard.getBoundingClientRect();
                const deltaX = (cardRect.left + cardRect.width / 2) - (trackRect.left + trackRect.width / 2);

                track.scrollBy({
                    left: deltaX,
                    top: 0,
                    behavior: 'smooth'
                });
            },

            /**
             * Updates active indicator dot when user swipes manually
             */
            onScroll() {
                const track = this.$refs.track;
                if (!track || !track.children.length) return;

                const trackRect = track.getBoundingClientRect();
                const trackCenter = trackRect.left + trackRect.width / 2;

                let closestIndex = this.active;
                let minDistance = Infinity;

                for (let i = 0; i < track.children.length; i++) {
                    const cardRect = track.children[i].getBoundingClientRect();
                    const cardCenter = cardRect.left + cardRect.width / 2;
                    const dist = Math.abs(cardCenter - trackCenter);
                    if (dist < minDistance) {
                        minDistance = dist;
                        closestIndex = i;
                    }
                }

                if (closestIndex !== this.active) {
                    this.syncIndex(closestIndex);
                }
            }
        };
    };

    document.addEventListener('DOMContentLoaded', () => {
        instance.init();
    });

})(typeof window !== 'undefined' ? window : globalThis);