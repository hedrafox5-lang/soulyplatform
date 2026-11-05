@extends('admin.layout')

@section('title', 'مركز إدارة الهوست والصيانة السحابية | سولي Citadel')

@section('admin-content')
<div class="space-y-8" x-data="hostOpsManager()">
    <!-- Header Strip -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 p-6 rounded-3xl backdrop-blur-xl">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-2xl text-cyan-400 shadow-[0_0_20px_rgba(0,245,212,0.15)]">
                ⚡
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2">
                    <span>مركز إدارة الهوست والصيانة السحابية</span>
                    <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-cyan-950 border border-cyan-800/60 text-cyan-400">HOST-OPS v2026</span>
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    أدوات التحكم الشاملة بالسيرفر وقاعدة البيانات وأوامر Artisan وسجلات النظام بدون الحاجة لاتصال SSH أو تيرمنال خارجي.
                </p>
            </div>
        </div>

        <!-- 1-Click Server Deploy Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.deploy') }}" target="_blank"
               class="px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:brightness-110 text-slate-950 font-black text-xs flex items-center gap-2 shadow-[0_0_25px_rgba(16,185,129,0.25)] transition-all active:scale-95">
                <span>🚀 تشغيل النشر السحابي الفوري (1-Click Deploy)</span>
            </a>
        </div>
    </div>

    <!-- Server Telemetry Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
            <span class="text-[11px] text-slate-500 block mb-1">إصدار PHP</span>
            <span class="text-lg font-black font-mono text-cyan-400">{{ $serverInfo['php_version'] }}</span>
        </div>
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
            <span class="text-[11px] text-slate-500 block mb-1">إصدار Laravel</span>
            <span class="text-lg font-black font-mono text-purple-400">v{{ $serverInfo['laravel_version'] }}</span>
        </div>
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
            <span class="text-[11px] text-slate-500 block mb-1">بيئة التشغيل (Env)</span>
            <span class="text-lg font-black font-mono text-amber-400 uppercase">{{ $serverInfo['app_env'] }}</span>
        </div>
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
            <span class="text-[11px] text-slate-500 block mb-1">حجم ملف السجلات</span>
            <span class="text-lg font-black font-mono text-rose-400">{{ $serverInfo['log_size_mb'] }} MB</span>
        </div>
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
            <span class="text-[11px] text-slate-500 block mb-1">محرك قاعدة البيانات</span>
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
                        <span>🛠️ أوامر الصيانة المعتمدة (Artisan)</span>
                    </h2>
                    <span class="text-[10px] text-slate-500 font-mono">SAFE EXECUTION</span>
                </div>

                <div class="space-y-2.5">
                    <button type="button" @click="runCommand('optimize:clear')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-cyan-950/30 border border-slate-800/80 hover:border-cyan-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-cyan-300">تنظيف شامل للكاش (optimize:clear)</span>
                            <span class="text-[11px] text-slate-500">حذف كاش الإعدادات والمسارات والقوالب لظهور التعديلات فوراً</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-cyan-400 font-mono text-base">▶</span>
                    </button>

                    <button type="button" @click="runCommand('config:cache')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-cyan-950/30 border border-slate-800/80 hover:border-cyan-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-cyan-300">تجميد كاش الإعدادات (config:cache)</span>
                            <span class="text-[11px] text-slate-500">تحسين سرعة قراءة ملفات .env والإعدادات</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-cyan-400 font-mono text-base">▶</span>
                    </button>

                    <button type="button" @click="runCommand('route:cache')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-cyan-950/30 border border-slate-800/80 hover:border-cyan-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-cyan-300">تجميد كاش المسارات (route:cache)</span>
                            <span class="text-[11px] text-slate-500">تسريع توجيه الطلبات وتخفيف استهلاك المعالج</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-cyan-400 font-mono text-base">▶</span>
                    </button>

                    <button type="button" @click="runCommand('view:cache')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-cyan-950/30 border border-slate-800/80 hover:border-cyan-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-cyan-300">تجميع قوالب Blade (view:cache)</span>
                            <span class="text-[11px] text-slate-500">تجهيز صفحات العرض مسبقاً للزوار</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-cyan-400 font-mono text-base">▶</span>
                    </button>

                    <button type="button" @click="runCommand('migrate')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-amber-950/30 border border-slate-800/80 hover:border-amber-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-amber-300">تنفيذ الميجريشنز (migrate)</span>
                            <span class="text-[11px] text-slate-500">إنشاء وتحديث أي جداول معلقة في قاعدة البيانات</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-amber-400 font-mono text-base">▶</span>
                    </button>

                    <button type="button" @click="runCommand('storage:link')" :disabled="isLoading"
                            class="w-full text-right p-3.5 rounded-2xl bg-slate-950/80 hover:bg-purple-950/30 border border-slate-800/80 hover:border-purple-500/40 text-xs transition-all flex items-center justify-between group">
                        <div>
                            <span class="font-bold text-white block group-hover:text-purple-300">ربط ملفات التخزين (storage:link)</span>
                            <span class="text-[11px] text-slate-500">إنشاء الرابط الرمزي لعرض صور الأجهزة والمنتجات</span>
                        </div>
                        <span class="text-slate-600 group-hover:text-purple-400 font-mono text-base">▶</span>
                    </button>
                </div>
            </div>

            <!-- Database Backup Box -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <div class="border-b border-slate-800/80 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>💾 النسخ الاحتياطي لقاعدة البيانات</span>
                    </h2>
                    <form method="POST" action="{{ route('admin.host_ops.backup') }}">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition-all shadow-md">
                            + نسخة فورية
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
                            تحميل ↓
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-4 text-xs text-slate-500">
                        لا توجد نسخ احتياطية مسجلة حتى الآن.
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
                    <span x-show="isLoading" class="text-xs font-mono text-cyan-400 animate-pulse">جاري التنفيذ...</span>
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
                        <span class="text-base">📋</span>
                        <h3 class="text-sm font-bold text-white">سجل أخطاء النظام (Laravel Error Logs)</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="fetchLogs()" :disabled="isLoading"
                                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all">
                            تحديث السجل ↺
                        </button>
                        <form method="POST" action="{{ route('admin.host_ops.clear_logs') }}" onsubmit="return confirm('هل أنت متأكد من تفريغ ملف السجلات؟')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-950/80 hover:bg-rose-900 border border-rose-800/60 text-rose-300 font-bold text-xs transition-all">
                                تفريغ السجل ✕
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
        terminalOutput: 'جاهز لتنفيذ الأوامر. اضغط على أي أمر من القائمة على اليمين.',
        logsContent: 'اضغط على زر (تحديث السجل) لاستعراض آخر 200 سطر من سجلات الخادم.',

        async runCommand(cmd) {
            this.isLoading = true;
            this.terminalOutput = `> php artisan ${cmd} --verbose\nجاري إرسال الأمر للسيرفر...`;
            
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
                this.terminalOutput = data.output || (data.success ? 'تم تنفيذ الأمر بنجاح بدون مخرجات نصية.' : 'فشل تنفيذ الأمر.');
            } catch (err) {
                this.terminalOutput = `خطأ أثناء الاتصال: ${err.message}`;
            } finally {
                this.isLoading = false;
            }
        },

        async fetchLogs() {
            this.isLoading = true;
            try {
                const res = await fetch("{{ route('admin.host_ops.view_logs') }}");
                const data = await res.json();
                this.logsContent = data.logs || 'لا توجد سجلات مسجلة.';
            } catch (err) {
                this.logsContent = `تعذر جلب السجلات: ${err.message}`;
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endsection
