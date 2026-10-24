<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\Lead;
use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SoulyAiService
{
    protected ?string $pineconeKey;
    protected ?string $pineconeHost;
    protected string $pineconeIndex;
    protected ?string $geminiKey;
    protected ?string $openaiKey;

    public function __construct()
    {
        $this->pineconeKey = config('services.pinecone.api_key');
        $this->pineconeHost = config('services.pinecone.host');
        $this->pineconeIndex = config('services.pinecone.index', 'souly-knowledge-v1');
        $this->geminiKey = config('services.gemini.api_key');
        $this->openaiKey = config('services.openai.api_key');
    }

    /**
     * Process message through the Smart Hybrid Retrieval & AI Synthesis Engine.
     */
    public function reply(string $message, ?string $sessionUuid = null, ?string $visitorName = null, ?string $visitorPhone = null): array
    {
        $sessionUuid = $sessionUuid ?: (string) Str::uuid();

        // 1. Retrieve or initialize conversation record
        $conversation = AiConversation::firstOrCreate(
            ['session_uuid' => $sessionUuid],
            [
                'user_id' => auth()->id(),
                'device_type' => request()->header('sec-ch-ua-mobile') === '?1' ? 'mobile' : 'desktop',
                'pinecone_namespace' => $this->pineconeIndex,
            ]
        );

        // 2. Log visitor message
        $visitorMsg = AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'sender' => 'visitor',
            'content' => $message,
            'tokens_used' => mb_strlen($message),
        ]);

        // 3. Lead Intelligence Extraction (Egyptian mobile & contact detection)
        $extractedPhone = $visitorPhone;
        if (!$extractedPhone && preg_match('/(01[0125][0-9]{8})|(\+201[0125][0-9]{8})/', $message, $matches)) {
            $extractedPhone = $matches[0];
        }

        $leadCaptured = false;
        if ($extractedPhone) {
            $lead = Lead::updateOrCreate(
                ['phone' => $extractedPhone],
                [
                    'name' => $visitorName ?: ($conversation->lead?->name ?: 'عميل شات الذكاء الاصطناعي'),
                    'source' => 'ai_agent',
                    'status' => 'new',
                    'ai_summary' => "تواصل العميل عبر شات سولي AI. آخر استفسار: {$message}",
                    'notes' => "سجل تلقائي من المحادثة رقم: {$conversation->session_uuid}",
                ]
            );

            $conversation->lead_id = $lead->id;
            $conversation->save();
            $leadCaptured = true;
        }

        // 4. Retrieve Hybrid Knowledge (Live MySQL Catalog + Pinecone if configured)
        $knowledgeContext = $this->buildKnowledgeContext();
        $pineconeContext = $this->queryPineconeIfAvailable($message);
        if ($pineconeContext) {
            $knowledgeContext .= "\n\n[PINECONE RETRIEVED VECTORS]:\n" . $pineconeContext;
        }

        // 5. Detect Relevant Services / Packages for Actionable Suggestion Cards
        $packageSuggestions = $this->extractPackageSuggestions($message);

        // 6. Generate Response (LLM API or Context-Aware Hybrid Synthesis Engine)
        $aiReplyText = $this->synthesizeResponse($message, $knowledgeContext, $extractedPhone);

        // 7. Log AI Message
        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'sender' => 'souly_ai',
            'content' => $aiReplyText,
            'suggested_links_json' => $packageSuggestions,
            'tokens_used' => mb_strlen($aiReplyText),
        ]);

        return [
            'reply' => $aiReplyText,
            'conversation_id' => $conversation->session_uuid,
            'lead_captured' => $leadCaptured,
            'package_suggestions' => $packageSuggestions,
        ];
    }

    /**
     * Query Pinecone Vector Database via REST API (No gRPC needed).
     */
    public function queryPineconeIfAvailable(string $queryText): ?string
    {
        if (empty($this->pineconeKey) || empty($this->pineconeHost)) {
            return null;
        }

        try {
            // Pinecone REST endpoint: https://{host}/query
            $response = Http::withHeaders([
                'Api-Key' => $this->pineconeKey,
                'Content-Type' => 'application/json',
            ])->timeout(4)->post("https://{$this->pineconeHost}/query", [
                'namespace' => $this->pineconeIndex,
                'topK' => 3,
                'includeMetadata' => true,
            ]);

            if ($response->successful()) {
                $matches = $response->json('matches', []);
                $snippets = [];
                foreach ($matches as $match) {
                    if (isset($match['metadata']['text'])) {
                        $snippets[] = $match['metadata']['text'];
                    }
                }
                return implode("\n---\n", $snippets);
            }
        } catch (\Throwable $e) {
            // Graceful fallback to MySQL catalog on network/DNS error
        }

        return null;
    }

    /**
     * Build live grounding knowledge context from MySQL.
     */
    protected function buildKnowledgeContext(): string
    {
        $services = Service::with(['packages' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();

        $lines = ["=== قاعدة معرفة قلعة سولي الرقمية (SOULY DIGITAL CITADEL) ==="];

        foreach ($services as $srv) {
            $lines[] = "\n[خدمة: {$srv->name} (Slug: {$srv->slug})]";
            $lines[] = "الوصف: {$srv->short_description}";
            $lines[] = "الباقات المتاحة:";
            foreach ($srv->packages as $pkg) {
                $lines[] = "- باقة ({$pkg->name}): السعر {$pkg->price} ج.م / {$pkg->unit_label}. الضمان: {$pkg->warranty_days} يوماً. الميزات: " . implode(', ', array_slice($pkg->features_json ?? [], 0, 4));
            }
        }

        $lines[] = "\n[عروض وتخفيضات استثنائية]:";
        $lines[] = "1. الصيانة الهندسية: خصم 50% على الزيارة الميدانية الأولى بمقر العميل (سعر الساعة 175 ج.م بدلاً من 350 ج.م).";
        $lines[] = "2. الاشتراكات السنوية لنظام BrandERP: خصم 20% عند السداد السنوي.";
        $lines[] = "3. طرق شحن المحفظة: فودافون كاش (01090623326) وإنستاباي (souly@instapay).";

        return implode("\n", $lines);
    }

    /**
     * Extract relevant package suggestions matching user intent.
     */
    protected function extractPackageSuggestions(string $message): array
    {
        $lower = mb_strtolower($message);
        $suggestions = [];

        if (str_contains($lower, 'erp') || str_contains($lower, 'كاشير') || str_contains($lower, 'مخازن') || str_contains($lower, 'فواتير')) {
            $starter = ServicePackage::where('slug', 'starter')->first();
            $growth = ServicePackage::where('slug', 'growth')->first();
            if ($growth) {
                $suggestions[] = [
                    'name' => $growth->name,
                    'price' => number_format($growth->price) . ' EGP / شهر',
                    'link' => url('/services/brand-erp/growth'),
                ];
            }
            if ($starter) {
                $suggestions[] = [
                    'name' => $starter->name,
                    'price' => number_format($starter->price) . ' EGP / شهر',
                    'link' => url('/services/brand-erp/starter'),
                ];
            }
        } elseif (str_contains($lower, 'صيانة') || str_contains($lower, 'زيارة') || str_contains($lower, 'شبكات') || str_contains($lower, 'اعطال')) {
            $promax = ServicePackage::where('slug', 'promax')->first();
            if ($promax) {
                $suggestions[] = [
                    'name' => 'زيارة صيانة هندسية (خصم 50% للزيارة الأولى)',
                    'price' => '175 EGP / ساعة',
                    'link' => url('/services/maintenance/promax'),
                ];
            }
        } elseif (str_contains($lower, 'ذكاء') || str_contains($lower, 'ai') || str_contains($lower, 'شات') || str_contains($lower, 'واتساب')) {
            $aiPkg = ServicePackage::where('slug', 'whatsapp-sales')->first();
            if ($aiPkg) {
                $suggestions[] = [
                    'name' => $aiPkg->name,
                    'price' => number_format($aiPkg->price) . ' EGP / شهر',
                    'link' => url('/services/ai-agents/whatsapp-sales'),
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Synthesize natural, persuasive, technical Arabic reply.
     */
    protected function synthesizeResponse(string $message, string $knowledgeContext, ?string $extractedPhone): string
    {
        // If Gemini or OpenAI is configured, invoke REST API
        if ($this->geminiKey) {
            $prompt = "أنت 'سولي AI' المستشار الهندسي والتقني الذكي لقلعة سولي الرقمية (Souly Digital Citadel). تحدث بأسلوب عربي فصيح، لبق، تقني ومقنع. استخدم المعلومات التالية للإجابة بدقة:\n\n{$knowledgeContext}\n\nسؤال العميل: {$message}";
            try {
                $res = Http::timeout(6)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$this->geminiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);
                if ($res->successful()) {
                    $text = $res->json('candidates.0.content.parts.0.text');
                    if ($text) return trim($text);
                }
            } catch (\Throwable $e) {}
        }

        // Smart Built-in Contextual Knowledge Synthesis
        $lower = mb_strtolower($message);

        if ($extractedPhone) {
            return "أهلاً بك يا فندم! 🌟 تم استلام رقم هاتفك ({$extractedPhone}) بنجاح وتسجيله في نظام خدمة العملاء. سيتواصل معك أحد مهندسينا الاستشاريين في أقرب وقت لترتيب التفاصيل وتخصيص أفضل باقة لنشاطك. هل لديك أي استفسار آخر ترغب في معرفته حالياً؟";
        }

        if (str_contains($lower, 'erp') || str_contains($lower, 'كاشير') || str_contains($lower, 'نقاط بيع') || str_contains($lower, 'مخازن')) {
            return "أهلاً بحضرتك! نظام BrandERP السحابي من قلعة سولي هو الجيل المتطور لإدارة مبيعاتك ومخازنك:\n" .
                   "• كاشير POS فائق السرعة يعمل حتى مع انقطاع الإنترنت ويزامن تلقائياً.\n" .
                   "• ربط معتمد 100% مع الفاتورة والإيصال الإلكتروني المصري (ETA).\n" .
                   "• تبدأ باقة الانطلاق (Starter) من 850 ج.م شهرياً، وباقة النمو (Growth) الأكثر طلباً بـ 1,850 ج.م لربط الفروع والأقساط.\n" .
                   "يمكنك حجز نسختك الآن مباشرة بالخصم من المحفظة أو ترك رقمك لترتيب ديمو تجريبي فوري!";
        }

        if (str_contains($lower, 'صيانة') || str_contains($lower, 'زيارة') || str_contains($lower, 'مهندس')) {
            return "يسعدنا خدمتك! نوفر في قطاع الصيانة الهندسية الميدانية زيارات مهندسين متخصصين لمقرك:\n" .
                   "🔥 عرض حصري: خصم 50% على الزيارة الهندسية الأولى (الساعة بـ 175 ج.م فقط بدلاً من 350 ج.م).\n" .
                   "• فحص شامل لشبكات الفروع، السيرفرات، أجهزة الكاشير، والطابعات مع ضمان هندسي معتمد 14 يوماً.\n" .
                   "يمكنك حجز الزيارة مباشرة من صفحة خدمات الصيانة أو كتابة رقمك للتنسيق فوراً!";
        }

        if (str_contains($lower, 'ذكاء') || str_contains($lower, 'ai') || str_contains($lower, 'موظف') || str_contains($lower, 'بوت')) {
            return "موظفو الذكاء الاصطناعي (AI Agents) من سولي هم عقول رقمية مدربة لخدمة عملائك وإغلاق المبيعات 24/7:\n" .
                   "• ربط متكامل مع واتساب بيزنس الرسمي، ماسنجر، وانستجرام.\n" .
                   "• الرد في 0 ثانية بأسلوب بشري جذاب وإقناع فائق، مع توفير أكثر من 70% من تكاليف الموظفين.\n" .
                   "تبدأ باقات وكلاء الـ AI من 2,400 ج.م شهرياً مع تدريب كامل على منتجاتك وسياساتك.";
        }

        if (str_contains($lower, 'امن') || str_contains($lower, 'سيبراني') || str_contains($lower, 'اختراق') || str_contains($lower, 'فحص')) {
            return "في قطاع التدريع والأمن السيبراني، نقدم اختبارات اختراق متقدمة (VAPT) وتدريعاً ضد هجمات حجب الخدمة (DDoS) وسد ثغرات الـ OWASP:\n" .
                   "• توقيع اتفاقية سرية وعدم إفصاح (NDA) رسمية لحماية بياناتك بالكامل.\n" .
                   "• تبدأ باقات الفحص من 1,200 ج.م بالساعة مع تقرير أمني رسمي وشهادة سلامة معتمدة.";
        }

        if (str_contains($lower, 'شحن') || str_contains($lower, 'محفظة') || str_contains($lower, 'دفع') || str_contains($lower, 'فلوس')) {
            return "يمكنك شحن محفظتك الرقمية في قلعة سولي بسهولة عبر:\n" .
                   "1️⃣ فودافون كاش: 01090623326\n" .
                   "2️⃣ إنستاباي: souly@instapay\n" .
                   "3️⃣ تحويل بنكي مباشر (NBE)\n" .
                   "بعد التحويل، سجل إشعار الشحن في صفحة محفظتي (/portal/wallet) ليتم اعتماده وإضافة الرصيد فوراً خلال دقائق!";
        }

        return "مرحباً بك في قلعة سولي الرقمية! 🛡️ أنا مستشارك الذكي، وأستطيع مساعدتك في التعرف على:\n" .
               "1. نظام BrandERP السحابي والربط الضريبي ETA.\n" .
               "2. موظفو الذكاء الاصطناعي (AI Agents) للمبيعات والدعم.\n" .
               "3. الصيانة الهندسية الميدانية (خصم 50% للزيارة الأولى).\n" .
               "4. التدريع والأمن السيبراني وكورسات الأكاديمية.\n" .
               "بماذا يمكنني خدمتك اليوم؟ أو يمكنك ترك رقم هاتفك ليتواصل معك مهندس حسابات مختص.";
    }
}
