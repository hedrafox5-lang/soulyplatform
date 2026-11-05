<!-- Admin Speed-Dial Floating Action Button (Solo-Founder Mobile Superpower) -->
<div x-data="{ open: false }" 
     class="md:hidden fixed bottom-20 left-4 z-40"
     @keydown.escape.window="open = false"
     @click.away="open = false">
    
    <!-- Expanded Tactical Action Pills -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-6 scale-90"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-90"
         class="space-y-2 mb-3"
         style="display: none;">
        
        <!-- Action 1: Sovereign AI Copilot Sheet -->
        <button type="button" 
                @click="open = false; $dispatch('open-admin-ai-sheet')"
                class="w-full flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-2xl bg-gradient-to-r from-cyber-gold to-amber-600 text-obsidian-base font-black text-xs font-cairo shadow-neon-gold">
            <span class="flex items-center gap-2">
                <span>🤖</span>
                <span>وكيل القيادة AI</span>
            </span>
            <span class="text-[10px] font-mono opacity-80">SOVEREIGN</span>
        </button>

        <!-- Action 2: Paste Client Chat to Dossier -->
        <button type="button" 
                @click="open = false; $dispatch('open-paste-chat-modal')"
                class="w-full flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-2xl bg-obsidian-surface text-cyber-cyan border border-cyber-cyan/40 font-bold text-xs font-cairo shadow-lg">
            <span class="flex items-center gap-2">
                <span>📋</span>
                <span>لصق شات وبناء ملف عميل</span>
            </span>
            <span class="text-[10px] font-mono opacity-70">1-TAP</span>
        </button>

        <!-- Action 3: Urgent Call Queue -->
        <a href="{{ route('admin.bookings.index', ['urgent_calls' => 1]) }}"
           @click="open = false"
           class="w-full flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-2xl bg-obsidian-surface text-rose-300 border border-rose-500/40 font-bold text-xs font-cairo shadow-lg">
            <span class="flex items-center gap-2">
                <span>📞</span>
                <span>مكالمات تأكيد اليوم</span>
            </span>
            <span class="text-[10px] font-mono opacity-70">CALLS</span>
        </a>

        <!-- Action 4: 1-Tap JSON Backup -->
        <a href="{{ route('admin.system.backup_snapshot') }}"
           @click="open = false"
           class="w-full flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-2xl bg-obsidian-surface text-slate-200 border border-obsidian-border font-bold text-xs font-cairo shadow-lg">
            <span class="flex items-center gap-2">
                <span>💾</span>
                <span>تحميل نسخة احتياطية</span>
            </span>
            <span class="text-[10px] font-mono opacity-70">BACKUP</span>
        </a>

        <!-- Action 5: AI Models & Keys Vault -->
        <a href="{{ route('admin.ai_copilot.index') }}"
           @click="open = false"
           class="w-full flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-2xl bg-obsidian-surface text-purple-300 border border-purple-500/40 font-bold text-xs font-cairo shadow-lg">
            <span class="flex items-center gap-2">
                <span>🔑</span>
                <span>خزينة المفاتيح والموديلات</span>
            </span>
            <span class="text-[10px] font-mono opacity-70">MODELS</span>
        </a>
    </div>

    <!-- Main Floating FAB Button -->
    <button type="button" 
            @click="open = !open"
            :class="open ? 'rotate-45 bg-rose-500 text-white' : 'bg-gradient-to-tr from-cyber-gold to-amber-500 text-obsidian-base shadow-neon-gold'"
            class="w-13 h-13 w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-2xl transition-all duration-300 transform active:scale-95 shadow-xl border border-cyber-gold/50"
            title="الإجراءات التكتيكية السريعة">
        <span x-text="open ? '×' : '⚡'"></span>
    </button>
</div>
