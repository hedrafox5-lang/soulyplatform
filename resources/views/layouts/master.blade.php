<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#05050A">

    <title>@yield('title', config('app.name', 'Souly Digital Citadel'))</title>

    <!-- Google Fonts: Cairo & Readex Pro for Arabic Digital Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Readex+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
                        sans: ['Readex Pro', 'Cairo', 'sans-serif'],
                        cairo: ['Cairo', 'sans-serif'],
                        readex: ['Readex Pro', 'sans-serif'],
                        mono: ['JetBrains Mono', 'Fira Code', 'monospace']
                    },
                    boxShadow: {
                        'neon-cyan': '0 0 20px rgba(0, 245, 212, 0.35)',
                        'neon-gold': '0 0 20px rgba(255, 184, 0, 0.35)',
                        'neon-purple': '0 0 25px rgba(123, 44, 191, 0.45)',
                        'cyber-card': '0 8px 32px 0 rgba(0, 0, 0, 0.65)'
                    }
                }
            }
        }
    </script>

    <!-- Color Variables & Cosmic Obsidian Engine Styling -->
    <style>
        @view-transition { navigation: auto; }

        ::view-transition-old(root) {
            animation: 180ms ease-out both citadel-fade-out;
        }
        ::view-transition-new(root) {
            animation: 250ms ease-in both citadel-fade-in;
        }

        @keyframes citadel-fade-out {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.99); }
        }
        @keyframes citadel-fade-in {
            from { opacity: 0; transform: scale(1.01); }
            to { opacity: 1; transform: scale(1); }
        }

        :root {
            --color-obsidian-base: #05050A;
            --color-obsidian-surface: #0B0C16;
            --color-cyber-cyan: #00F5D4;
            --color-cyber-gold: #FFB800;
            --color-cyber-purple: #7B2CBF;
            --font-arabic: 'Cairo', 'Readex Pro', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--color-obsidian-base);
            color: #E2E8F0;
            font-family: 'Readex Pro', 'Cairo', sans-serif;
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }

        /* Scanline Overlay for Cosmic Obsidian Aesthetic */
        .cyber-grid-backdrop {
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(123, 44, 191, 0.15) 0%, transparent 60%),
                radial-gradient(circle at 100% 100%, rgba(0, 245, 212, 0.08) 0%, transparent 50%),
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 48px 48px, 48px 48px;
        }

        /* Custom Scrollbar for Citadel HUD */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #05050A;
        }
        ::-webkit-scrollbar-thumb {
            background: #1C1F36;
            border-radius: 9999px;
            border: 1px solid rgba(0, 245, 212, 0.2);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #7B2CBF;
        }

        .neon-border-cyan {
            border-color: rgba(0, 245, 212, 0.4);
            box-shadow: 0 0 15px rgba(0, 245, 212, 0.2);
        }
        .neon-border-gold {
            border-color: rgba(255, 184, 0, 0.4);
            box-shadow: 0 0 15px rgba(255, 184, 0, 0.2);
        }
        .neon-border-purple {
            border-color: rgba(123, 44, 191, 0.5);
            box-shadow: 0 0 20px rgba(123, 44, 191, 0.25);
        }

        /* Glassmorphism Surface */
        .glass-obsidian {
            background: rgba(11, 12, 22, 0.82);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* Safe area support for iOS/Android notches & navigation bars */
        .safe-top {
            padding-top: env(safe-area-inset-top, 0px);
        }
        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
    </style>

    <!-- GSAP 3.12 CDN + ScrollTrigger -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/ScrollTrigger.min.js"></script>

    <!-- Core Storage, Loader & UI Architecture Scripts -->
    <script src="{{ asset('js/souly-storage.js') }}"></script>
    <script src="{{ asset('js/souly-loader.js') }}"></script>
    <script src="{{ asset('js/souly-ui.js') }}"></script>

    <!-- Alpine.js 3.14 CDN (Deferred) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <script>
        // Initialize Citadel Reactive Core Store
        document.addEventListener('alpine:init', () => {
            Alpine.store('citadel', {
                isMobile: window.innerWidth < 1024,
                activeTab: 'overview',
                telemetryPulse: true,
                storageEngine: 'initializing...',
                nodePing: '12ms',
                cairoTime: '',
                sidebarCollapsed: false,
                mobileNavOpen: false,
                aiSheetOpen: false,
                walletBalance: '{{ $citadelWalletBalance ?? "3500.00" }}',

                init() {
                    const updateViewport = () => {
                        this.isMobile = window.innerWidth < 1024;
                    };
                    window.addEventListener('resize', updateViewport);

                    // Update Cairo Local Time (Africa/Cairo)
                    const tickClock = () => {
                        const now = new Date();
                        this.cairoTime = now.toLocaleTimeString('ar-EG', {
                            timeZone: 'Africa/Cairo',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });
                    };
                    tickClock();
                    setInterval(tickClock, 1000);

                    // Query Storage Engine
                    if (window.SoulyDB) {
                        window.SoulyDB.getDriver().then(() => {
                            this.storageEngine = window.SoulyDB.getEngine();
                        });
                    }

                    // Listen for AI Chat Sheet Trigger
                    window.addEventListener('open-ai-chat-sheet', () => {
                        this.aiSheetOpen = true;
                    });
                }
            });
        });
    </script>

    @stack('styles')
</head>
<body class="min-h-screen bg-obsidian-base text-slate-100 antialiased selection:bg-cyber-cyan selection:text-obsidian-base cyber-grid-backdrop relative"
      x-data>

    <!-- 25-Second Cinematic Story Loader with Ambient Audio -->
    @include('partials.cinematic-loader')

    <!-- Pre-cache payload for 0ms Icon Popups -->
    <script id="souly-precache-data" type="application/json">{!! $citadelPrecacheJson ?? '{}' !!}</script>

    <!-- Unified Adaptive Shell Container -->
    <div id="citadel-root" class="min-h-screen flex flex-col justify-between">
        @yield('layout-body')
    </div>

    <!-- Universal Interactive Icon Popup & Mobile Bottom-Sheet Engine -->
    @include('partials.icon-popup')

    <!-- Souly AI Agent Interactive Widget (Desktop Floating & Mobile Dock) -->
    @include('partials.souly-ai-widget')

    <!-- Telemetry Notification Beacon Slot -->
    <div id="citadel-toasts" class="fixed top-4 left-4 z-50 flex flex-col gap-2 pointer-events-none"></div>

    @stack('scripts')

    <script>
        // Citadel GSAP Ambient Bioluminescence Initialization
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof gsap !== 'undefined') {
                gsap.to('.cyber-pulse-glow', {
                    opacity: 0.85,
                    duration: 2.2,
                    repeat: -1,
                    yoyo: true,
                    ease: "sine.inOut"
                });
            }
        });
    </script>
</body>
</html>
