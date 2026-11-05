<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من الفاتورة والضمان المعتمد | سولي Citadel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Cairo', Tahoma, sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="min-h-screen bg-[#070b14] text-slate-100 py-10 px-4">
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/15 border border-emerald-500/40 text-emerald-400 text-xs font-mono font-bold">
                <span>OFFICIAL VERIFIED INVOICE</span>
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white">منظومة التحقق من الفواتير والضمانات الرسمية</h1>
            <p class="text-xs text-slate-400">هذه الوثيقة صادرة ومعتمدة إلكترونياً من مؤسسة سولي للتكنولوجيا وتعتبر سنداً رسمياً للضمان.</p>
        </div>

        @include('partials.public-invoice-card', ['invoice' => $invoice])

        <div class="text-center pt-4">
            <a href="/" class="text-xs text-cyan-400 hover:underline">العودة للصفحة الرئيسية لمنصة سولي</a>
        </div>
    </div>
</body>
</html>