@php
    $pageTitle = trim($__env->yieldContent('title')) ?: 'سولي | أنظمة الأعمال والحلول التكنولوجية المتطورة';
    $metaDescription = trim($__env->yieldContent('meta_description')) ?: 'منظومة سولي - أنظمة BrandERP السحابية المعتمدة، موظفو الذكاء الاصطناعي للأعمال، الصيانة الهندسية الميدانية، والتدريع السيبراني المتقدم.';
    $ogTitle = trim($__env->yieldContent('og_title')) ?: $pageTitle;
    $ogDescription = trim($__env->yieldContent('og_description')) ?: $metaDescription;
    $ogImage = trim($__env->yieldContent('og_image')) ?: asset('icons/icon-512.png');
    $ogType = trim($__env->yieldContent('og_type')) ?: 'website';
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>

    <!-- Arabic SEO & Search Engine Indexing -->
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="سولي, Souly, BrandERP, ذكاء اصطناعي للأعمال, أنظمة ERP سحابية, صيانة لابتوب وماك, تدريع سيبراني, أمن معلومات, مصر, القاهرة">
    <meta name="author" content="Souly Technologies">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- PWA & Mobile Sovereign App Meta -->
    <meta name="theme-color" content="#05050A">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="سولي | Souly">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icons/icon-512.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-512.png') }}">

    <!-- OpenGraph Campaign Social Proof (Facebook Ads, WhatsApp, Telegram) -->
    <meta property="og:locale" content="ar_EG">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="سولي | Souly">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- Structured Data (JSON-LD Organization Schema) -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'سولي',
        'alternateName' => 'Souly',
        'url' => url('/'),
        'logo' => asset('icons/icon-512.png'),
        'description' => 'منظومة هندسية وتقنية متقدمة تقدم أنظمة BrandERP السحابية، موظفي الذكاء الاصطناعي، الصيانة الميدانية، والتدريع السيبراني في مصر.',
        'telephone' => '+201090623326',
        'email' => 'support@soulyeg.online',
        'address' => [
            '@type' => 'PostalAddress',
            'addressCountry' => 'EG',
            'addressLocality' => 'Cairo',
        ],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => '+201090623326',
            'contactType' => 'customer service',
            'areaServed' => 'EG',
            'availableLanguage' => ['Arabic', 'English'],
        ],
        'sameAs' => [
            'https://facebook.com/soulyeg',
            'https://soulyeg.online',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Google Fonts: Cairo & JetBrains Mono for Arabic Digital Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        obsidian: {
                            base: '#05050A',
                            surface: '#0B0C16',
                            card: '#121422',
                            border: '#1C1F36',
                            muted: '#2A2E4D'
                        },
                        cyber: {
                            cyan: '#00F5D4',
                            gold: '#FFB800',
                            purple: '#7B2CBF',
                            emerald: '#10B981',
                            crimson: '#FF2A55'
                        }
                    },
                    fontFamily: {
                        sans: ['Cairo', 'Tahoma', 'sans-serif'],
                        cairo: ['Cairo', 'Tahoma', 'sans-serif'],
                        mono: ['JetBrains Mono', 'Fira Code', 'monospace']
                    },
                    boxShadow: {
                        'neon-cyan': '0 4px 20px -2px rgba(0, 245, 212, 0.25), 0 0 12px rgba(0, 245, 212, 0.12)',
                        'neon-gold': '0 4px 20px -2px rgba(255, 184, 0, 0.25), 0 0 12px rgba(255, 184, 0, 0.12)',
                        'neon-purple': '0 4px 22px -2px rgba(123, 44, 191, 0.3), 0 0 14px rgba(123, 44, 191, 0.15)',
                        'cyber-card': '0 12px 40px -4px rgba(0, 0, 0, 0.75), inset 0 1px 0 0 rgba(255, 255, 255, 0.07)'
                    }
                }
            }
        }
    </script>

    <!-- Global Mobile Performance CSS Engine -->
    <link rel="stylesheet" href="{{ asset('css/souly-mobile-perf.css') }}?v={{ time() }}">
    <!-- Global Obsidian Cyber Glassmorphism & Standardized Buttons -->
    <style>
        :root {
            --obsidian-base: #05050A;
            --obsidian-surface: #0B0C16;
            --obsidian-card: #121422;
            --obsidian-border: #1C1F36;
            --cyber-cyan: #00F5D4;
            --cyber-gold: #FFB800;
            --cyber-purple: #7B2CBF;
            --cyber-emerald: #10B981;
            --cyber-crimson: #FF2A55;
        }

        [x-cloak] {
            display: none !important;
        }

        * {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--obsidian-base);
            color: #F8FAFC;
            font-family: 'Cairo', 'Tahoma', sans-serif;
            overflow-x: hidden;
        }

        ::selection {
            background: rgba(0, 245, 212, 0.25);
            color: #00F5D4;
        }

        /* Sleek Cyber Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #05050A;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(0, 245, 212, 0.35), rgba(123, 44, 191, 0.45));
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #00F5D4, #7B2CBF);
        }

        /* Button Framework */
        .btn-citadel-primary,
        .btn-souly-primary {
            background: linear-gradient(135deg, #00F5D4 0%, #00BB9B 100%);
            color: #05050A !important;
            font-family: 'Cairo', sans-serif;
            font-weight: 800;
            border: 1px solid rgba(0, 245, 212, 0.6);
            box-shadow: 0 4px 20px -2px rgba(0, 245, 212, 0.35), inset 0 1px 0 0 rgba(255, 255, 255, 0.35);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-citadel-primary:hover,
        .btn-souly-primary:hover {
            filter: brightness(1.08);
            box-shadow: 0 6px 26px rgba(0, 245, 212, 0.5), inset 0 1px 0 0 rgba(255, 255, 255, 0.5);
            transform: translateY(-1px);
        }

        .btn-citadel-secondary,
        .btn-souly-outline {
            background: rgba(18, 20, 34, 0.85);
            color: #E2E8F0;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            border: 1px solid rgba(0, 245, 212, 0.3);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
            transition: all 0.25s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-citadel-secondary:hover,
        .btn-souly-outline:hover {
            background: rgba(0, 245, 212, 0.12);
            color: #00F5D4;
            border-color: rgba(0, 245, 212, 0.6);
        }

        .btn-citadel-gold {
            background: linear-gradient(135deg, #FFB800 0%, #E09F00 100%);
            color: #05050A !important;
            font-family: 'Cairo', sans-serif;
            font-weight: 800;
            border: 1px solid rgba(255, 184, 0, 0.6);
            box-shadow: 0 4px 20px -2px rgba(255, 184, 0, 0.35);
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .btn-citadel-gold:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .btn-souly-compact {
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
        }

        .portal-welcome-banner {
            background: linear-gradient(135deg, rgba(0, 245, 212, 0.12) 0%, rgba(18, 20, 34, 0.92) 50%, rgba(123, 44, 191, 0.16) 100%);
            border: 1px solid rgba(0, 245, 212, 0.35);
        }

        /* Glassmorphism Classes */
        .glass-obsidian {
            background: linear-gradient(135deg, rgba(18, 20, 34, 0.78) 0%, rgba(11, 12, 22, 0.88) 100%);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 12px 36px 0 rgba(0, 0, 0, 0.65), inset 0 1px 0 0 rgba(255, 255, 255, 0.1);
        }

        .glass-obsidian-heavy {
            background: linear-gradient(160deg, rgba(11, 12, 22, 0.92) 0%, rgba(5, 5, 10, 0.98) 100%);
            backdrop-filter: blur(28px) saturate(200%);
            -webkit-backdrop-filter: blur(28px) saturate(200%);
            border: 1px solid rgba(0, 245, 212, 0.25);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.85), inset 0 1px 0 0 rgba(255, 255, 255, 0.12);
        }

        .cursor-radial-glow {
            position: relative;
        }
        .cursor-radial-glow::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(500px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(0, 245, 212, 0.06), transparent 40%);
            pointer-events: none;
            z-index: 1;
        }

        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .safe-top {
            padding-top: env(safe-area-inset-top, 0px);
        }
        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }

        .cyber-grid-bg {
            background-size: 42px 42px;
            background-image:
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            mask-image: radial-gradient(ellipse 80% 70% at 50% 25%, #000 50%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 80% 70% at 50% 25%, #000 50%, transparent 100%);
        }
    </style>

    <!-- GSAP 3.12 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js"></script>

    <!-- Souly Core Architecture Scripts (Storage, Cinematic Loader & UI Engine v2.5) -->
    <script src="{{ asset('js/souly-storage.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/souly-loader.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/souly-ui.js') }}?v={{ time() }}"></script>

    <!-- Alpine.js 3.14 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" defer></script>

    <script>
        document.addEventListener('alpine:init', () => {
            if (window.soulyAiWidgetController) {
                Alpine.data('soulyAiWidgetController', window.soulyAiWidgetController);
            }
            Alpine.store('citadel', {
                isMobile: window.innerWidth < 1024,
                isOnline: navigator.onLine,
                mobileNavOpen: false,
                aiSheetOpen: false, // Strict Single Source of Truth
                contextMenuOpen: false,
                contextMenuX: 0,
                contextMenuY: 0,
                commandPaletteOpen: false,
                notificationCenterOpen: false,
                unreadNotificationsCount: {{ Auth::check() ? \App\Services\SoulyNotificationService::getUnreadCount(Auth::id()) : 0 }},
                notificationsList: [],
                notificationsLoaded: false,
                notificationsLoading: false,
                toastMessage: '',
                toastVisible: false,
                toastTimeout: null,
                walletBalance: '{{ $citadelWalletBalance ?? "3500.00" }}',

                // PWA
                deferredPrompt: null,
                canInstallApp: false,
                isIos: false,
                showIosInstallModal: false,
                appInstalled: false,

                showToast(msg, duration = 3000) {
                    this.toastMessage = msg;
                    this.toastVisible = true;
                    if (this.toastTimeout) clearTimeout(this.toastTimeout);
                    this.toastTimeout = setTimeout(() => {
                        this.toastVisible = false;
                    }, duration);
                },

                copyCurrentUrl() {
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        this.showToast('تم نسخ رابط الصفحة السيادية بنجاح ✓');
                    }).catch(() => {
                        this.showToast('تم نسخ الرابط');
                    });
                },

                openSoulyAi() {
                    this.aiSheetOpen = true;
                    if (window.SoulyUI) window.SoulyUI.lockScroll('ai-widget');
                },

                closeSoulyAi() {
                    this.aiSheetOpen = false;
                    if (window.SoulyUI) window.SoulyUI.unlockScroll('ai-widget');
                },

                toggleSoulyAi(force = null) {
                    if (force !== null) {
                        this.aiSheetOpen = Boolean(force);
                    } else {
                        this.aiSheetOpen = !this.aiSheetOpen;
                    }
                    if (this.aiSheetOpen) {
                        if (window.SoulyUI) window.SoulyUI.lockScroll('ai-widget');
                    } else {
                        if (window.SoulyUI) window.SoulyUI.unlockScroll('ai-widget');
                    }
                },

                async openNotificationCenter() {
                    this.notificationCenterOpen = true;
                    await this.fetchNotifications();
                },

                async fetchNotifications() {
                    @auth
                    this.notificationsLoading = true;
                    try {
                        const res = await fetch('{{ route("notifications.list") }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.notificationsList = data.notifications || [];
                            this.unreadNotificationsCount = data.unread_count || 0;
                            this.notificationsLoaded = true;
                        }
                    } catch (e) {
                    } finally {
                        this.notificationsLoading = false;
                    }
                    @endauth
                },

                async markAllNotificationsRead() {
                    @auth
                    try {
                        const res = await fetch('{{ route("notifications.mark_all_read") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (res.ok) {
                            this.unreadNotificationsCount = 0;
                            this.notificationsList.forEach(n => n.is_read = true);
                            this.showToast('تم تحديد كافة الإشعارات كمقروءة ✓');
                        }
                    } catch (e) {}
                    @endauth
                },

                async clickNotification(notif) {
                    if (!notif.is_read) {
                        notif.is_read = true;
                        if (this.unreadNotificationsCount > 0) this.unreadNotificationsCount--;
                        fetch(`/notifications/${notif.id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                    }
                    if (notif.action_url) {
                        this.notificationCenterOpen = false;
                        window.location.href = notif.action_url;
                    }
                },

                async installApp() {
                    if (this.deferredPrompt) {
                        this.deferredPrompt.prompt();
                        const { outcome } = await this.deferredPrompt.userChoice;
                        if (outcome === 'accepted') {
                            this.canInstallApp = false;
                            this.appInstalled = true;
                            this.showToast('جاري تثبيت تطبيق قلعة سولي على جهازك ⚡');
                        }
                        this.deferredPrompt = null;
                    } else if (this.isIos) {
                        this.showIosInstallModal = true;
                    } else {
                        this.showToast('انقر على أيقونة التثبيت ⊕ في شريط المتصفح لتثبيت التطبيق');
                    }
                },

                init() {
                    const updateViewport = () => {
                        this.isMobile = window.innerWidth < 1024;
                    };
                    window.addEventListener('resize', updateViewport);
                    updateViewport();

                    window.addEventListener('online', () => {
                        this.isOnline = true;
                        this.showToast('تم استعادة الاتصال بالشبكة السيادية بنجاح ⚡');
                    });
                    window.addEventListener('offline', () => {
                        this.isOnline = false;
                        this.showToast('وضع عدم الاتصال نشط — تعمل الذاكرة المحلية المؤقتة 📡', 4000);
                    });

                    // Context Menu ONLY for desktop devices with mice (never interrupt mobile touch)
                    const isTouch = ('ontouchstart' in window) || navigator.maxTouchPoints > 0;
                    if (!isTouch) {
                        window.addEventListener('contextmenu', (e) => {
                            if (e.shiftKey) return;
                            e.preventDefault();

                            const menuWidth = 265;
                            const menuHeight = 390;
                            const x = Math.min(e.clientX, window.innerWidth - menuWidth - 10);
                            const y = Math.min(e.clientY, window.innerHeight - menuHeight - 10);

                            this.contextMenuX = Math.max(10, x);
                            this.contextMenuY = Math.max(10, y);
                            this.contextMenuOpen = true;
                        });
                    }

                    window.addEventListener('click', () => {
                        if (this.contextMenuOpen) this.contextMenuOpen = false;
                    });

                    window.addEventListener('keydown', (e) => {
                        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                            e.preventDefault();
                            this.commandPaletteOpen = !this.commandPaletteOpen;
                        }
                        if (e.key === 'Escape') {
                            this.contextMenuOpen = false;
                            this.commandPaletteOpen = false;
                            this.notificationCenterOpen = false;
                            this.mobileNavOpen = false;
                            this.aiSheetOpen = false;
                            this.showIosInstallModal = false;
                            if (window.SoulyUI) window.SoulyUI.forceUnlockScroll();
                        }
                    });

                    @auth
                    this.fetchNotifications();
                    @endauth

                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        this.deferredPrompt = e;
                        this.canInstallApp = true;
                    });

                    window.addEventListener('appinstalled', () => {
                        this.canInstallApp = false;
                        this.appInstalled = true;
                        this.deferredPrompt = null;
                    });

                    const isIosDevice = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
                    if (isIosDevice && !isStandalone) {
                        this.isIos = true;
                        this.canInstallApp = true;
                    }

                    if ('serviceWorker' in navigator) {
                        window.addEventListener('load', () => {
                            navigator.serviceWorker.register('/sw.js').catch(() => {});
                        });
                    }
                }
            });
        });
    </script>

    @stack('styles')
</head>
<body x-data class="bg-obsidian-base text-slate-100 font-cairo antialiased selection:bg-cyber-cyan/25 selection:text-cyber-cyan min-h-screen relative overflow-x-hidden">

    <!-- Top Cyber Progress Bar -->
    <div id="souly-top-progress" class="fixed top-0 right-0 h-[2.5px] w-0 opacity-0 z-[999999] bg-gradient-to-l from-cyber-cyan via-cyber-gold to-cyber-purple shadow-neon-cyan transition-all duration-200 pointer-events-none"></div>

    <!-- Cosmic Obsidian Ambient Glass Backdrop -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden" aria-hidden="true">
        <div class="absolute inset-0 cyber-grid-bg"></div>
        <div class="cyber-pulse-glow absolute -top-36 -right-36 w-[520px] h-[520px] rounded-full bg-cyber-cyan/[0.06] blur-[130px]"></div>
        <div class="cyber-pulse-glow absolute top-1/3 -left-44 w-[480px] h-[480px] rounded-full bg-cyber-purple/[0.08] blur-[140px]"></div>
        <div class="cyber-pulse-glow absolute -bottom-40 right-1/4 w-[420px] h-[420px] rounded-full bg-cyber-gold/[0.04] blur-[130px]"></div>
    </div>

    <!-- 11-Second Executive Story Loader -->
    @include('partials.cinematic-loader')

    <!-- Pre-cache payload for 0ms Icon Popups -->
    <script id="souly-precache-data" type="application/json">
        {!! $citadelPrecacheJson ?? '{}' !!}
    </script>

    <!-- Unified Adaptive Shell Container -->
    <div id="citadel-root" class="relative z-10 min-h-screen flex flex-col justify-between">
        @yield('layout-body')
    </div>

    <!-- Universal Interactive Icon Popup -->
    @include('partials.icon-popup')

    <!-- Universal 1-Tap Express Mobile Booking Modal -->
    @include('partials.express-booking-modal')

    <!-- Souly AI Agent Interactive Widget -->
    @include('partials.souly-ai-widget')

    <!-- Smart Role-Aware Cyber Context Menu -->
    @include('partials.context-menu')

    <!-- Omni-Search Command Palette [Ctrl + K] -->
    @include('partials.command-palette')

    <!-- Glassmorphic Notification Center Sheet -->
    @include('partials.notification-center')

    <!-- Floating Quick-Jump Sector Dock (Homepage Only) -->
    @if(request()->routeIs('home'))
        @include('partials.floating-quick-dock')
    @endif

    <!-- Global Reactive Cyber Toast Pill -->
    <div x-cloak
         x-show="$store.citadel && $store.citadel.toastVisible"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[999999] px-5 py-3 rounded-2xl glass-obsidian-heavy border border-cyber-cyan/50 text-white font-cairo text-xs font-bold shadow-neon-cyan flex items-center gap-2.5 pointer-events-none">
        <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-ping"></span>
        <span x-text="$store.citadel.toastMessage"></span>
    </div>

    <!-- iOS / PWA Installation Guidance Modal -->
    <div x-cloak
         x-show="$store.citadel && $store.citadel.showIosInstallModal"
         x-transition.opacity.duration.250ms
         @click.self="$store.citadel.showIosInstallModal = false"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        <div x-show="$store.citadel.showIosInstallModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-6 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="glass-obsidian-heavy rounded-3xl border border-cyber-cyan/40 p-6 max-w-sm w-full space-y-5 text-center shadow-neon-cyan">
            <div class="flex justify-center">
                <div class="w-14 h-14 rounded-2xl bg-cyber-cyan/10 border border-cyber-cyan/30 flex items-center justify-center shadow-neon-cyan">
                    <svg class="w-7 h-7 text-cyber-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-1.5">
                <h3 class="font-extrabold text-lg text-white font-cairo">تثبيت تطبيق قلعة سولي 📱</h3>
                <p class="text-xs text-slate-300 font-cairo leading-relaxed">تمتع بتجربة تطبيق سيادي كامل فائق السرعة وبدون شريط المتصفح:</p>
            </div>
            <div class="space-y-2.5 text-right text-xs text-slate-300 font-cairo bg-obsidian-surface/90 p-4 rounded-2xl border border-obsidian-border">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg bg-cyber-cyan/20 text-cyber-cyan flex items-center justify-center font-bold font-mono text-xs shrink-0">1</span>
                    <span>اضغط على زر المشاركة <strong>(Share / مشاركة)</strong> في شريط Safari بالأسفل</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg bg-cyber-gold/20 text-cyber-gold flex items-center justify-center font-bold font-mono text-xs shrink-0">2</span>
                    <span>اختر <strong>"إضافة إلى الصفحة الرئيسية (Add to Home Screen) ➕"</strong></span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg bg-cyber-purple/20 text-cyber-purple flex items-center justify-center font-bold font-mono text-xs shrink-0">3</span>
                    <span>اضغط على <strong>"إضافة (Add)"</strong> أعلى الشاشة وسيظهر تطبيق سولي فوراً!</span>
                </div>
            </div>
            <button @click="$store.citadel.showIosInstallModal = false"
                    class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-black shadow-neon-cyan active:scale-95 transition-all"
                    type="button">
                حسناً، فهمت ذلك ✓
            </button>
        </div>
    </div>

    <!-- Modern Modular Performance Scripts (Loaded at bottom with Cache Busting) -->
    <script src="{{ asset('js/souly-core.js') }}?v={{ time() }}" defer></script>
    <script type="module" src="{{ asset('js/souly-bundle.js') }}?v={{ time() }}"></script>

    @stack('scripts')
</body>
</html>