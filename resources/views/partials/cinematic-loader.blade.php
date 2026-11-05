<!-- Souly Digital Citadel: 11-Second Executive Story Loader -->
<style>
    html.souly-loader-hidden #souly-cinematic-loader {
        display: none !important;
        pointer-events: none !important;
    }
</style>
<script>
    (function() {
        try {
            const hasSeen = localStorage.getItem('souly_story_seen');
            const isHome = window.location.pathname === '/' || window.location.pathname === '';
            const params = new URLSearchParams(window.location.search);
            const forceReplay = params.get('intro') === '1' || params.get('loader') === '1';

            if ((hasSeen || !isHome) && !forceReplay) {
                document.documentElement.classList.add('souly-loader-hidden');
            }
        } catch(e) {
            document.documentElement.classList.add('souly-loader-hidden');
        }
    })();
</script>

<div id="souly-cinematic-loader" 
     class="fixed inset-0 z-[100] flex flex-col justify-between p-4 sm:p-8 lg:p-10 bg-obsidian-base/95 backdrop-blur-2xl text-slate-100 select-none transition-all duration-500 font-cairo safe-top safe-bottom"
     dir="rtl">
    
    <!-- Cyber Geometric Background Elements -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[380px] sm:w-[680px] h-[380px] sm:h-[680px] rounded-full bg-cyber-purple/10 blur-[110px] animate-pulse"></div>
        <div class="absolute top-1/3 right-1/4 w-[300px] sm:w-[480px] h-[300px] sm:h-[480px] rounded-full bg-cyber-cyan/10 blur-[100px]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#1c1f36_1px,transparent_1px)] [background-size:24px_24px] opacity-25"></div>
    </div>

    <!-- Top Status Ribbon & Audio Synthesizer Controls -->
    <header class="relative z-10 flex items-center justify-between gap-2 sm:gap-4">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 shrink-0">
            <x-souly-logo size="xs" :showText="true" />
        </a>

        <!-- Ambient Sci-Fi Audio Synthesizer Button -->
        <button id="loader-sound-toggle" 
                type="button"
                class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl bg-obsidian-surface/90 border border-obsidian-border text-[11px] sm:text-xs font-mono font-medium text-slate-300 hover:text-cyber-cyan hover:border-cyber-cyan/40 transition-all duration-300 shadow-lg active:scale-95 cursor-pointer">
            <span class="sound-btn-icon flex h-2 w-2 relative shrink-0">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyber-cyan opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-cyber-cyan"></span>
            </span>
            <span class="sound-btn-text font-cairo font-bold">تشغيل الموسيقى التكنولوجية 🔊</span>
        </button>
    </header>

    <!-- Center Cinematic Stage: Chapters & Dynamic Story Narration -->
    <main class="relative z-10 max-w-4xl mx-auto w-full my-auto text-center space-y-4 sm:space-y-6 px-2">
        
        <!-- Glowing Cyber-Falcon Hologram Centerpiece -->
        <div class="relative mx-auto flex items-center justify-center py-1 sm:py-2 scale-75 sm:scale-100">
            <x-souly-logo size="hero" :showText="false" />
        </div>

        <!-- Chapter Category Badge -->
        <div class="inline-block">
            <span id="loader-chapter-badge" 
                  class="px-3.5 py-1.5 rounded-full text-[11px] sm:text-xs font-mono font-bold tracking-wider bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan">
                قلعة سولي الرقمية · CITADEL CORE
            </span>
        </div>

        <!-- Chapter Main Title -->
        <h2 id="loader-chapter-title" 
            class="text-xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold font-cairo text-white leading-snug sm:leading-tight min-h-[64px] sm:min-h-[90px] flex items-center justify-center">
            مرحباً بك في قلعة سولي الرقمية
        </h2>

        <!-- Chapter Narrative Subtitle -->
        <p id="loader-chapter-subtitle" 
           class="text-xs sm:text-base lg:text-lg text-slate-300 font-cairo max-w-2xl mx-auto leading-relaxed min-h-[44px] sm:min-h-[56px]">
            منظومة سيادية متكاملة تجمع بين أقوى برمجيات الأعمال وكوادر الذكاء الاصطناعي الفائقة والتدريع السيبراني المتقدم
        </p>

        <!-- Chapter Dynamic HUD Metric -->
        <div class="pt-1">
            <span id="loader-chapter-metric" 
                  class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-obsidian-surface/90 border border-cyber-cyan/30 text-[11px] sm:text-xs font-mono font-bold text-cyber-cyan shadow-sm">
                جاهزية 100% · سيادة رقمية · معايير عالمية
            </span>
        </div>
    </main>

    <!-- Bottom Controls HUD: Progress Bar, 4 Chapter Segments & Instant Skip -->
    <footer class="relative z-10 max-w-4xl mx-auto w-full space-y-3 sm:space-y-4">
        
        <!-- 4 Segmented Chapter Indicators -->
        <div class="flex items-center gap-2 w-full">
            <div class="loader-chapter-pill flex-1 h-1.5 rounded-full bg-cyber-cyan transition-all duration-300"></div>
            <div class="loader-chapter-pill flex-1 h-1.5 rounded-full bg-obsidian-border transition-all duration-300"></div>
            <div class="loader-chapter-pill flex-1 h-1.5 rounded-full bg-obsidian-border transition-all duration-300"></div>
            <div class="loader-chapter-pill flex-1 h-1.5 rounded-full bg-obsidian-border transition-all duration-300"></div>
        </div>

        <!-- Progress Track -->
        <div class="relative w-full h-2 rounded-full bg-obsidian-surface border border-obsidian-border overflow-hidden">
            <div id="loader-progress-bar" 
                 class="h-full bg-gradient-to-r from-cyber-cyan to-teal-400 transition-all duration-100 shadow-sm" 
                 style="width: 0%;"></div>
        </div>

        <!-- Progress Metadata & Skip Button -->
        <div class="flex items-center justify-between gap-2 text-xs font-cairo text-slate-400">
            <div class="flex items-center gap-2 sm:gap-3">
                <span class="text-cyber-cyan font-bold font-mono" id="loader-percent-text">0%</span>
                <span class="text-obsidian-border">|</span>
                <span>المتبقي: <strong class="text-cyber-gold font-mono" id="loader-seconds-text">11s</strong></span>
                <span class="text-obsidian-border hidden sm:inline">|</span>
                <span class="text-slate-400 hidden sm:inline">4 محطات حاسمة</span>
            </div>

            <!-- Skip Button -->
            <button id="loader-skip-btn" 
                    type="button" 
                    class="group flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl bg-cyber-cyan/15 hover:bg-cyber-cyan text-cyber-cyan hover:text-obsidian-base border border-cyber-cyan/50 font-cairo font-black text-xs transition-all duration-200 active:scale-95 shadow-neon-cyan cursor-pointer">
                <span>تخطي العرض والبدء فوراً ⏭️</span>
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>
    </footer>

</div>