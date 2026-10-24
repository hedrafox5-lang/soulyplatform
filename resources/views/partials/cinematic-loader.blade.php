<!-- Souly Digital Citadel: 25-Second Cinematic Story Loader -->
<div id="souly-cinematic-loader" 
     class="fixed inset-0 z-[100] flex flex-col justify-between p-6 sm:p-10 bg-obsidian-base/95 backdrop-blur-2xl text-slate-100 select-none transition-all duration-700 font-sans"
     dir="rtl">
    
    <!-- Cyber Geometric Background Elements -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] rounded-full bg-cyber-purple/10 blur-[130px] animate-pulse"></div>
        <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] rounded-full bg-cyber-cyan/10 blur-[110px]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#1c1f36_1px,transparent_1px)] [background-size:24px_24px] opacity-25"></div>
    </div>

    <!-- Top Status Ribbon & Audio Synthesizer Controls -->
    <header class="relative z-10 flex items-center justify-between gap-4">
        <!-- Citadel Shield Brand -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyber-purple via-obsidian-surface to-cyber-cyan p-[1.5px] shadow-neon-cyan">
                <div class="w-full h-full bg-obsidian-surface rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
            <div>
                <span class="font-extrabold text-base tracking-wide font-cairo text-white block">سولي ديدجيتال سيتادل</span>
                <span class="text-[10px] font-mono text-cyber-cyan tracking-wider">SOULYEG.ONLINE // INITIALIZING CORE</span>
            </div>
        </div>

        <!-- Ambient Sci-Fi Audio Synthesizer Button -->
        <button id="loader-sound-toggle" 
                type="button"
                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-obsidian-surface/90 border border-obsidian-border text-xs font-mono font-medium text-slate-400 hover:text-cyber-cyan hover:border-cyber-cyan/40 transition-all duration-300 shadow-lg">
            <span class="sound-btn-icon flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyber-cyan opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-cyber-cyan"></span>
            </span>
            <span class="sound-btn-text font-cairo font-semibold">تشغيل الموسيقى التكنولوجية 🔊</span>
        </button>
    </header>

    <!-- Center Cinematic Stage: Chapters & Dynamic Story Narration -->
    <main class="relative z-10 max-w-4xl mx-auto w-full my-auto text-center space-y-6">
        
        <!-- Glowing Core Orb / Hologram -->
        <div class="relative mx-auto w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center">
            <!-- Pulsing outer ring -->
            <div class="absolute inset-0 rounded-full border border-cyber-cyan/30 animate-spin" style="animation-duration: 12s;"></div>
            <div class="absolute inset-2 rounded-full border border-cyber-purple/40 animate-spin" style="animation-duration: 8s; animation-direction: reverse;"></div>
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-cyber-purple/30 via-obsidian-surface to-cyber-cyan/20 border border-cyber-cyan/50 backdrop-blur-md flex items-center justify-center shadow-neon-cyan">
                <svg class="w-8 h-8 text-cyber-cyan animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
        </div>

        <!-- Chapter Category Badge -->
        <div class="inline-block">
            <span id="loader-chapter-badge" 
                  class="px-4 py-1.5 rounded-full text-xs font-mono font-semibold tracking-wider bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan">
                قلعة سولي الرقمية · CITADEL CORE
            </span>
        </div>

        <!-- Chapter Main Title -->
        <h2 id="loader-chapter-title" 
            class="text-2xl sm:text-4xl md:text-5xl font-extrabold font-cairo text-white leading-tight min-h-[70px] sm:min-h-[90px] flex items-center justify-center">
            مرحباً بك في قلعة سولي الرقمية
        </h2>

        <!-- Chapter Narrative Subtitle -->
        <p id="loader-chapter-subtitle" 
           class="text-sm sm:text-lg text-slate-300 font-readex max-w-2xl mx-auto leading-relaxed min-h-[50px] sm:min-h-[60px]">
            حيث ينتهي المستحيل وتبدأ السيادة التكنولوجية المتكاملة لأعمالك
        </p>
    </main>

    <!-- Bottom Controls HUD: Progress Bar, 5 Chapter Segments & Instant Skip -->
    <footer class="relative z-10 max-w-4xl mx-auto w-full space-y-4">
        
        <!-- 5 Segmented Chapter Indicators -->
        <div class="flex items-center gap-2 w-full">
            <div class="loader-chapter-pill flex-1 h-1.5 rounded-full bg-cyber-cyan shadow-neon-cyan transition-all duration-300"></div>
            <div class="loader-chapter-pill flex-1 h-1.5 rounded-full bg-obsidian-border transition-all duration-300"></div>
            <div class="loader-chapter-pill flex-1 h-1.5 rounded-full bg-obsidian-border transition-all duration-300"></div>
            <div class="loader-chapter-pill flex-1 h-1.5 rounded-full bg-obsidian-border transition-all duration-300"></div>
            <div class="loader-chapter-pill flex-1 h-1.5 rounded-full bg-obsidian-border transition-all duration-300"></div>
        </div>

        <!-- Progress Track -->
        <div class="relative w-full h-2 rounded-full bg-obsidian-surface border border-obsidian-border overflow-hidden">
            <div id="loader-progress-bar" 
                 class="h-full bg-gradient-to-r from-cyber-purple via-cyber-gold to-cyber-cyan transition-all duration-100 shadow-neon-cyan" 
                 style="width: 0%;"></div>
        </div>

        <!-- Progress Metadata & Skip Button -->
        <div class="flex items-center justify-between text-xs font-mono text-slate-400">
            <div class="flex items-center gap-3">
                <span class="text-cyber-cyan font-bold" id="loader-percent-text">0%</span>
                <span class="text-obsidian-border">|</span>
                <span>المتبقي: <strong class="text-cyber-gold" id="loader-seconds-text">25s</strong></span>
                <span class="text-obsidian-border hidden sm:inline">|</span>
                <span class="text-slate-400 hidden sm:inline">5 محطات تكنولوجية</span>
            </div>

            <!-- Skip Button -->
            <button id="loader-skip-btn" 
                    type="button" 
                    class="group flex items-center gap-2 px-5 py-2 rounded-xl bg-obsidian-surface/90 border border-obsidian-border hover:border-cyber-cyan/50 text-slate-200 hover:text-cyber-cyan font-cairo font-bold text-xs transition-all duration-200 active:scale-95">
                <span>تخطي العرض والدخول فوراً</span>
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>
    </footer>

</div>
