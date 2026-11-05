@extends('admin.layout')

@section('title', 'Ù…Ø±ÙƒØ² Ø¥Ø¯Ø§Ø±Ø© Ø§Ù„Ù‡ÙˆØ³Øª ÙˆØ§Ù„ØµÙŠØ§Ù†Ø© Ø§Ù„Ø³Ø­Ø§Ø¨ÙŠØ© | Ø³ÙˆÙ„ÙŠ Citadel')

@section('admin-content')
<div class="space-y-8" x-data="hostOpsManager()">
    <!-- Header Strip -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 p-6 rounded-3xl backdrop-blur-xl">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-2xl text-cyan-400 shadow-[0_0_20px_rgba(0,245,212,0.15)]">
                âš¡
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2">
                    <span>Ù…Ø±ÙƒØ² Ø¥Ø¯Ø§Ø±Ø© Ø§Ù„Ù‡ÙˆØ³Øª ÙˆØ§Ù„ØµÙŠØ§Ù†Ø© Ø§Ù„Ø³Ø­Ø§Ø¨ÙŠØ©</span>
                    <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-cyan-950 border border-cyan-800/60 text-cyan-400">HOST-OPS v2026</span>
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    Ø£Ø¯ÙˆØ§Øª Ø§Ù„ØªØ­ÙƒÙ… Ø§Ù„Ø´Ø§Ù…Ù„Ø© Ø¨Ø§Ù„Ø³ÙŠØ±ÙØ± ÙˆÙ‚Ø§Ø¹Ø¯Ø© Ø§Ù„Ø¨ÙŠØ§Ù†Ø§Øª ÙˆØ£ÙˆØ§Ù…Ø± Artisan ÙˆØ³Ø¬Ù„Ø§Øª Ø§Ù„Ù†Ø¸Ø§Ù… Ø¨Ø¯ÙˆÙ† Ø§Ù„Ø­Ø§Ø¬Ø© Ù„Ø§ØªØµØ§Ù„ SSH Ø£Ùˆ ØªÙŠØ±Ù…Ù†Ø§Ù„ Ø®Ø§Ø±Ø¬ÙŠ.
                </p>
            </div>
        </div>

        <!-- 1-Click Server Deploy Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.deploy') }}" target="_blank"
               class="px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:brightness-110 text-slate-950 font-black text-xs flex items-center gap-2 shadow-[0_0_25px_rgba(16,185,129,0.25)] transition-all active:scale-95">
                <span>ðŸš€ ØªØ´ØºÙŠÙ„ Ø§Ù„Ù†Ø´Ø± Ø§Ù„Ø³Ø­Ø§Ø¨ÙŠ Ø§Ù„ÙÙˆØ±ÙŠ (1-Click Deploy)</span>
            </a>
        </div>
    </div>

    <!-- Server Telemetry Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
            <span class="text-[11px] text-slate-500 block mb-1">Ø¥ØµØ¯Ø§Ø± PHP</span>
            <span class="text-lg font-black font-mono text-cyan-400">{{ $serverInfo['php_version'] }}</span>
        </div>
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
            <span class="text-[11px] text-slate-500 block mb-1">Ø¥ØµØ¯Ø§Ø± Laravel</span>
            <span class="text-lg font-black font-mono text-purple-400">v{{ $serverInfo['laravel_version'] }}</span>
        </div>
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
            <span class="text-[11px] text-slate-500 block mb-1">Ø¨ÙŠØ¦Ø© Ø§Ù„ØªØ´ØºÙŠÙ„ (Env)</span>
            <span class="text-lg font-black font-mono text-amber-400 uppercase">{{ $serverInfo['app_env'] }}</span>
        </div>
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
            <span class="text-[11px] text-slate-500 block mb-1">Ø­Ø¬Ù… Ù…Ù„Ù Ø§Ù„Ø³Ø¬Ù„Ø§Øª</span>
            <span class="text-lg font-black font-mono text-rose-400">{{ $serverInfo['log_size_mb'] }} MB</span>
        </div>
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
            <span class="text-[11px] text-slate-500 block mb-1">Ù…Ø­Ø±Ùƒ Ù‚Ø§Ø¹Ø¯Ø© Ø§Ù„Ø¨ÙŠØ§Ù†Ø§Øª</span>
            <span class="text-lg font-black font-mono text-emerald-400 uppercase">{{ $serverInfo['db_driver'] }}</span>
        </div>
    </div>

    <!-- Main Grid: Commands & Terminal -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Quick Command Buttons Column -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <div class="border-b border-slate-800/80 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>ðŸ› ï¸ Ø£ÙˆØ§Ù…Ø± Ø§Ù„ØµÙŠØ§Ù†Ø© Ø§Ù„Ù…Ø¹ØªÙ…Ø¯Ø© (Artisan)</span>
                    </h2>
                    <span class="text-[10px] text-slate-500 font-mono">SAFE EXECUTION</span>
                </div>

                <div class="space-y-2.5">
                    <button type="button" @click="runCommand('optimize:clear')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-cyan-950/30 border border-slate-800/80 hover:border-cyan-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-cyan-300">ØªÙ†Ø¸ÙŠÙ Ø´Ø§Ù…Ù„ Ù„Ù„ÙƒØ§Ø´ (optimize:clear)</span>
                            <span class="text-[11px] text-slate-500">Ø­Ø°Ù ÙƒØ§Ø´ Ø§Ù„Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª ÙˆØ§Ù„Ù…Ø³Ø§Ø±Ø§Øª ÙˆØ§Ù„Ù‚ÙˆØ§Ù„Ø¨ Ù„Ø¸Ù‡ÙˆØ± Ø§Ù„ØªØ¹Ø¯ÙŠÙ„Ø§Øª ÙÙˆØ±Ø§Ù‹</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-cyan-400 font-mono text-base">â–¶</span>
                    </button>

                    <button type="button" @click="runCommand('config:cache')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-cyan-950/30 border border-slate-800/80 hover:border-cyan-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-cyan-300">ØªØ¬Ù…ÙŠØ¯ ÙƒØ§Ø´ Ø§Ù„Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª (config:cache)</span>
                            <span class="text-[11px] text-slate-500">ØªØ­Ø³ÙŠÙ† Ø³Ø±Ø¹Ø© Ù‚Ø±Ø§Ø¡Ø© Ù…Ù„ÙØ§Øª .env ÙˆØ§Ù„Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-cyan-400 font-mono text-base">â–¶</span>
                    </button>

                    <button type="button" @click="runCommand('route:cache')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-cyan-950/30 border border-slate-800/80 hover:border-cyan-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-cyan-300">ØªØ¬Ù…ÙŠØ¯ ÙƒØ§Ø´ Ø§Ù„Ù…Ø³Ø§Ø±Ø§Øª (route:cache)</span>
                            <span class="text-[11px] text-slate-500">ØªØ³Ø±ÙŠØ¹ ØªÙˆØ¬ÙŠÙ‡ Ø§Ù„Ø·Ù„Ø¨Ø§Øª ÙˆØªØ®ÙÙŠÙ Ø§Ø³ØªÙ‡Ù„Ø§Ùƒ Ø§Ù„Ù…Ø¹Ø§Ù„Ø¬</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-cyan-400 font-mono text-base">â–¶</span>
                    </button>

                    <button type="button" @click="runCommand('view:cache')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-cyan-950/30 border border-slate-800/80 hover:border-cyan-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-cyan-300">ØªØ¬Ù…ÙŠØ¹ Ù‚ÙˆØ§Ù„Ø¨ Blade (view:cache)</span>
                            <span class="text-[11px] text-slate-500">ØªØ¬Ù‡ÙŠØ² ØµÙØ­Ø§Øª Ø§Ù„Ø¹Ø±Ø¶ Ù…Ø³Ø¨Ù‚Ø§Ù‹ Ù„Ù„Ø²ÙˆØ§Ø±</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-cyan-400 font-mono text-base">â–¶</span>
                    </button>

                    <button type="button" @click="runCommand('migrate')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-amber-950/30 border border-slate-800/80 hover:border-amber-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-amber-300">ØªÙ†ÙÙŠØ° Ø§Ù„Ù…ÙŠØ¬Ø±ÙŠØ´Ù†Ø² (migrate)</span>
                            <span class="text-[11px] text-slate-500">Ø¥Ù†Ø´Ø§Ø¡ ÙˆØªØ­Ø¯ÙŠØ« Ø£ÙŠ Ø¬Ø¯Ø§ÙˆÙ„ Ù…Ø¹Ù„Ù‚Ø© ÙÙŠ Ù‚Ø§Ø¹Ø¯Ø© Ø§Ù„Ø¨ÙŠØ§Ù†Ø§Øª</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-amber-400 font-mono text-base">â–¶</span>
                    </button>

                    <button type="button" @click="runCommand('storage:link')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-purple-950/30 border border-slate-800/80 hover:border-purple-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-purple-300">Ø±Ø¨Ø· Ù…Ù„ÙØ§Øª Ø§Ù„ØªØ®Ø²ÙŠÙ† (storage:link)</span>
                            <span class="text-[11px] text-slate-500">Ø¥Ù†Ø´Ø§Ø¡ Ø§Ù„Ø±Ø§Ø¨Ø· Ø§Ù„Ø±Ù…Ø²ÙŠ Ù„Ø¹Ø±Ø¶ ØµÙˆØ± Ø§Ù„Ø£Ø¬Ù‡Ø²Ø© ÙˆØ§Ù„Ù…Ù†ØªØ¬Ø§Øª</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-purple-400 font-mono text-base">â–¶</span>
                    </button>
                </div>
            </div>

            <!-- Database Backup Box -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <div class="border-b border-slate-800/80 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>ðŸ’¾ Ø§Ù„Ù†Ø³Ø® Ø§Ù„Ø§Ø­ØªÙŠØ§Ø·ÙŠ Ù„Ù‚Ø§Ø¹Ø¯Ø© Ø§Ù„Ø¨ÙŠØ§Ù†Ø§Øª</span>
                    </h2>
                    <form method="POST" action="{{ route('admin.host_ops.backup') }}">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition-all shadow-md">
                            + Ù†Ø³Ø®Ø© ÙÙˆØ±ÙŠØ©
                        </button>
                    </form>
                </div>

                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @forelse($backups as $backup)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800/80 text-xs font-mono">
                        <div>
                            <span class="text-white block font-bold">{{ $backup['name'] }}</span>
                            <span class="text-[10px] text-slate-500">{{ $backup['date'] }} | {{ $backup['size'] }} MB</span>
                        </div>
                        <a href="{{ route('admin.host_ops.download_backup', $backup['name']) }}"
                           class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-cyan-400 text-[11px] font-bold">
                            ØªØ­Ù…ÙŠÙ„ â†“
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-4 text-xs text-slate-500">
                        Ù„Ø§ ØªÙˆØ¬Ø¯ Ù†Ø³Ø® Ø§Ø­ØªÙŠØ§Ø·ÙŠØ© Ù…Ø³Ø¬Ù„Ø© Ø­ØªÙ‰ Ø§Ù„Ø¢Ù†.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Terminal & Logs Column -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Command Output Terminal -->
            <div class="bg-slate-950 border border-slate-800 rounded-3xl p-5 shadow-2xl flex flex-col h-[320px]">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-mono font-bold text-slate-400 ml-2">OUTPUT CONSOLE</span>
                    </div>
                    <span x-show="isLoading" class="text-xs font-mono text-cyan-400 animate-pulse">Ø¬Ø§Ø±ÙŠ Ø§Ù„ØªÙ†ÙÙŠØ°...</span>
                </div>

                <div class="flex-1 bg-black/60 rounded-2xl p-4 overflow-y-auto font-mono text-xs text-emerald-400 leading-relaxed whitespace-pre-wrap select-all border border-slate-900"
                     x-ref="outputBox">
                    <span x-text="terminalOutput"></span>
                </div>
            </div>

            <!-- Error Log Viewer Section -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-base">ðŸ“‹</span>
                        <h3 class="text-sm font-bold text-white">Ø³Ø¬Ù„ Ø£Ø®Ø·Ø§Ø¡ Ø§Ù„Ù†Ø¸Ø§Ù… (Laravel Error Logs)</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="fetchLogs()" :disabled="isLoading"
                                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all">
                            ØªØ­Ø¯ÙŠØ« Ø§Ù„Ø³Ø¬Ù„ âŸ³
                        </button>
                        <form method="POST" action="{{ route('admin.host_ops.clear_logs') }}" onsubmit="return confirm('Ù‡Ù„ Ø£Ù†Øª Ù…ØªØ£ÙƒØ¯ Ù…Ù† ØªÙØ±ÙŠØº Ù…Ù„Ù Ø§Ù„Ø³Ø¬Ù„Ø§ØªØŸ')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-950/80 hover:bg-rose-900 border border-rose-800/60 text-rose-300 font-bold text-xs transition-all">
                                ØªÙØ±ÙŠØº Ø§Ù„Ø³Ø¬Ù„ âœ•
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-4 h-[240px] overflow-y-auto font-mono text-[11px] text-slate-300 whitespace-pre-wrap leading-relaxed select-all">
                    <span x-text="logsContent"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function hostOpsManager() {
    return {
        isLoading: false,
        terminalOutput: 'Ø¬Ø§Ù‡Ø² Ù„ØªÙ†ÙÙŠØ° Ø§Ù„Ø£ÙˆØ§Ù…Ø±. Ø§Ø¶ØºØ· Ø¹Ù„Ù‰ Ø£ÙŠ Ø£Ù…Ø± Ù…Ù† Ø§Ù„Ù‚Ø§Ø¦Ù…Ø© Ø¹Ù„Ù‰ Ø§Ù„ÙŠÙ…ÙŠÙ†.',
        logsContent: 'Ø§Ø¶ØºØ· Ø¹Ù„Ù‰ Ø²Ø± (ØªØ­Ø¯ÙŠØ« Ø§Ù„Ø³Ø¬Ù„) Ù„Ø§Ø³ØªØ¹Ø±Ø§Ø¶ Ø¢Ø®Ø± 200 Ø³Ø·Ø± Ù…Ù† Ø³Ø¬Ù„Ø§Øª Ø§Ù„Ø®Ø§Ø¯Ù….',

        async runCommand(cmd) {
            this.isLoading = true;
            this.terminalOutput = `> php artisan ${cmd} --verbose\nØ¬Ø§Ø±ÙŠ Ø¥Ø±Ø³Ø§Ù„ Ø§Ù„Ø£Ù…Ø± Ù„Ù„Ø³ÙŠØ±ÙØ±...`;
            
            try {
                const res = await fetch("{{ route('admin.host_ops.run') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ command: cmd })
                });

                const data = await res.json();
                this.terminalOutput = data.output || (data.success ? 'ØªÙ… ØªÙ†ÙÙŠØ° Ø§Ù„Ø£Ù…Ø± Ø¨Ù†Ø¬Ø§Ø­ Ø¨Ø¯ÙˆÙ† Ù…Ø®Ø±Ø¬Ø§Øª Ù†ØµÙŠØ©.' : 'ÙØ´Ù„ ØªÙ†ÙÙŠØ° Ø§Ù„Ø£Ù…Ø±.');
            } catch (err) {
                this.terminalOutput = `Ø®Ø·Ø£ Ø£Ø«Ù†Ø§Ø¡ Ø§Ù„Ø§ØªØµØ§Ù„: ${err.message}`;
            } finally {
                this.isLoading = false;
            }
        },

        async fetchLogs() {
            this.isLoading = true;
            try {
                const res = await fetch("{{ route('admin.host_ops.view_logs') }}");
                const data = await res.json();
                this.logsContent = data.logs || 'Ù„Ø§ ØªÙˆØ¬Ø¯ Ø³Ø¬Ù„Ø§Øª Ù…Ø³Ø¬Ù„Ø©.';
            } catch (err) {
                this.logsContent = `ØªØ¹Ø°Ø± Ø¬Ù„Ø¨ Ø§Ù„Ø³Ø¬Ù„Ø§Øª: ${err.message}`;
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endsection