<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceIcon;
use App\Models\ServicePackage;
use App\Models\Setting;
use App\Models\User;
use App\Services\SoulyAiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AdminSettingController extends Controller
{
    /**
     * Ensure only Admin (Hedra / ROOT) can manage system settings.
     */
    protected function authorizeAdmin(): void
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'staff'])) {
            abort(403, 'غير مصرح لغير الإدارة العليا (ROOT) أو طاقم العمل بالوصول إلى إعدادات النظام ومزامنة المعرفة.');
        }
    }

    /**
     * Display the System Settings CMS & AI Vector Sync Dashboard.
     */
    public function index(): View
    {
        $this->authorizeAdmin();

        $groupedSettings = Setting::getAllGrouped();
        $lastSyncedAt = Setting::get('pinecone_last_synced_at');

        // Calculate knowledge assets count
        $servicesCount = Service::where('is_active', true)->count();
        $packagesCount = ServicePackage::where('is_active', true)->count();
        $iconsCount = ServiceIcon::count();
        $totalKnowledgeCount = $servicesCount + $packagesCount + $iconsCount;

        $clientsList = User::where('is_active', true)->select('id', 'name', 'phone', 'email', 'role')->orderBy('name')->get();

        return view('admin.settings.index', compact(
            'groupedSettings',
            'lastSyncedAt',
            'servicesCount',
            'packagesCount',
            'iconsCount',
            'totalKnowledgeCount',
            'clientsList'
        ));
    }

    /**
     * Update dynamic settings from the browser without modifying .env files.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $inputs = $request->input('settings', []);

        foreach ($inputs as $key => $value) {
            $existing = Setting::where('key', $key)->first();
            $group = $existing ? $existing->group : 'general';
            $type = $existing ? $existing->type : 'string';

            Setting::set($key, $value, $group, $type);
        }

        // Also handle checkbox toggles that may be absent from POST
        $checkboxKeys = ['enable_cinematic_loader', 'enable_cinematic_audio'];
        foreach ($checkboxKeys as $chkKey) {
            if (isset($inputs[$chkKey])) {
                Setting::set($chkKey, '1', 'cinematic', 'boolean');
            } elseif ($request->has('__submitted_cinematic')) {
                Setting::set($chkKey, '0', 'cinematic', 'boolean');
            }
        }

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => 'تم حفظ وتحديث إعدادات النظام وقاعدة البيانات بنجاح ⚡'
        ]);
    }

    /**
     * Push all 6 services, packages, and interactive icons into Pinecone Vector Store.
     */
    public function syncPinecone(Request $request, SoulyAiService $aiService): JsonResponse|RedirectResponse
    {
        $this->authorizeAdmin();

        $services = Service::with(['packages' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();

        $icons = ServiceIcon::all();

        $documents = [];

        // 1. Ingest Services
        foreach ($services as $srv) {
            $documents[] = [
                'id' => "srv_{$srv->slug}",
                'type' => 'service',
                'title' => $srv->name,
                'slug' => $srv->slug,
                'text' => "خدمة سولي: {$srv->name}. المعرف: {$srv->slug}. الوصف الموجز: {$srv->short_description}. النظرة الشاملة والسيادة التقنية: {$srv->full_overview}",
                'metadata' => [
                    'category' => 'services',
                    'slug' => $srv->slug,
                    'url' => url("/services/{$srv->slug}"),
                ],
            ];

            // 2. Ingest Packages
            foreach ($srv->packages as $pkg) {
                $featuresText = is_array($pkg->features_json) ? implode('، ', $pkg->features_json) : '';
                $documents[] = [
                    'id' => "pkg_{$srv->slug}_{$pkg->slug}",
                    'type' => 'package',
                    'title' => "باقة {$pkg->name} - {$srv->name}",
                    'slug' => $pkg->slug,
                    'price' => (float) $pkg->price,
                    'text' => "باقة ({$pkg->name}) تابعة لخدمة ({$srv->name}). السعر: {$pkg->price} ج.م / {$pkg->unit_label}. الضمان الهندسي: {$pkg->warranty_days} يوماً. الميزات التقنية: {$featuresText}",
                    'metadata' => [
                        'category' => 'packages',
                        'service_slug' => $srv->slug,
                        'package_slug' => $pkg->slug,
                        'price' => (float) $pkg->price,
                        'url' => url("/services/{$srv->slug}/{$pkg->slug}"),
                    ],
                ];
            }
        }

        // 3. Ingest Interactive Icons & Industries
        foreach ($icons as $icon) {
            $bullets = is_array($icon->popup_bullets) ? implode('، ', $icon->popup_bullets) : '';
            $documents[] = [
                'id' => "icon_{$icon->id}",
                'type' => 'icon',
                'title' => $icon->title,
                'category' => $icon->category,
                'text' => "أيقونة سولي التفاعلية: {$icon->title} ({$icon->subtitle}). التصنيف: {$icon->category}. الملخص: {$icon->popup_summary}. التفاصيل والنقاط: {$bullets}",
                'metadata' => [
                    'category' => 'icons',
                    'icon_id' => $icon->id,
                    'badge' => $icon->category,
                ],
            ];
        }

        $totalSynced = count($documents);

        // Store local vector knowledge store
        Cache::forever('souly_vector_knowledge_store', $documents);
        $syncTimestamp = now()->toDateTimeString();
        Setting::set('pinecone_last_synced_at', $syncTimestamp, 'ai');

        // Check if Pinecone API Key & Host are provided for remote upsert
        $pineconeKey = Setting::get('pinecone_api_key') ?: config('services.pinecone.api_key');
        $pineconeHost = Setting::get('pinecone_host') ?: (Setting::get('pinecone_index_host') ?: config('services.pinecone.host'));
        $pineconeIndex = Setting::get('pinecone_index') ?: config('services.pinecone.index', 'souly-knowledge-v1');

        $remotePushed = false;
        if (!empty($pineconeKey) && !empty($pineconeHost)) {
            try {
                $cleanHost = rtrim(preg_replace('#^https?://#', '', $pineconeHost), '/');
                $sampleDocs = array_slice($documents, 0, 50);

                // 1. Try vector upsert with authentic 768-dim embeddings
                $texts = array_column($sampleDocs, 'text');
                $embeddings = $aiService->batchGenerateEmbeddings($texts);

                $vectorsPayload = [];
                foreach ($sampleDocs as $idx => $doc) {
                    $vectorsPayload[] = [
                        'id' => (string) $doc['id'],
                        'values' => $embeddings[$idx] ?? $aiService->generateDeterministicEmbedding($doc['text'], 768),
                        'metadata' => [
                            'text' => $doc['text'],
                            'title' => $doc['title'] ?? '',
                            'type' => $doc['type'] ?? 'service',
                        ],
                    ];
                }

                $res = Http::withHeaders([
                    'Api-Key' => $pineconeKey,
                    'Content-Type' => 'application/json',
                ])->timeout(app()->environment('testing') ? 2 : 8)->post("https://{$cleanHost}/vectors/upsert", [
                    'vectors' => $vectorsPayload,
                    'namespace' => $pineconeIndex,
                ]);

                if ($res->successful()) {
                    $remotePushed = true;
                } else {
                    // 2. If index uses Pinecone Document Schema (2026), push via documents API
                    $docsPayload = array_map(function ($doc) {
                        return [
                            '_id' => (string) $doc['id'],
                            'chunk_text' => $doc['text'],
                        ];
                    }, $sampleDocs);

                    $docsRes = Http::withHeaders([
                        'Api-Key' => $pineconeKey,
                        'Content-Type' => 'application/json',
                        'X-Pinecone-API-Version' => '2026-07',
                    ])->timeout(app()->environment('testing') ? 2 : 12)->post("https://{$cleanHost}/namespaces/{$pineconeIndex}/documents/upsert", [
                        'documents' => $docsPayload,
                    ]);

                    if ($docsRes->successful()) {
                        $remotePushed = true;
                    }
                }
            } catch (\Throwable $e) {
                // Gracefully fallback to local hybrid index on network/timeout error
                \Illuminate\Support\Facades\Log::info("[PineconeSync] Remote upsert bypassed: " . $e->getMessage());
            }
        }

        $msg = "تمت مزامنة قاعدة معرفة سولي بنجاح! تم تحديث وتجهيز {$totalSynced} وثيقة ومعلم معرفي في المستودع الفكتوري" . ($remotePushed ? ' وقاعدة Pinecone السحابية.' : '.');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'synced_count' => $totalSynced,
                'remote_pinecone_synced' => $remotePushed,
                'timestamp' => $syncTimestamp,
                'message' => $msg,
            ]);
        }

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => $msg,
        ]);
    }

    /**
     * Generate 1-Tap JSON Database Backup Snapshot.
     */
    public function backupSnapshot(): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorizeAdmin();

        $snapshot = [
            'app' => 'Souly Citadel Platform',
            'version' => '2026.4',
            'exported_at' => now()->toIso8601String(),
            'author' => auth()->user()->name,
            'tables' => [
                'users' => \App\Models\User::all(),
                'services' => \App\Models\Service::all(),
                'service_packages' => \App\Models\ServicePackage::all(),
                'bookings' => \App\Models\Booking::all(),
                'leads' => \App\Models\Lead::all(),
                'wallets' => \App\Models\Wallet::all(),
                'hardware_products' => \App\Models\HardwareProduct::all(),
                'hardware_orders' => \App\Models\HardwareOrder::all(),
                'erp_store_catalog' => \App\Models\ErpStoreCatalog::all(),
                'erp_orders' => \App\Models\ErpOrder::all(),
                'tickets' => \App\Models\Ticket::all(),
                'settings' => \App\Models\Setting::all(),
                'ai_api_keys' => \App\Models\AiApiKey::all(),
            ],
        ];

        $filename = 'souly-backup-snapshot-' . date('Y-m-d-His') . '.json';
        $json = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Update WhatsApp Message Template from gear modal.
     */
    public function updateWhatsAppTemplate(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'template_key'     => ['required', 'string', 'max:100'],
            'template_content' => ['required', 'string', 'max:2000'],
        ]);

        Setting::set($validated['template_key'], $validated['template_content'], 'whatsapp', 'قوالب رسائل الواتساب الدائمة');

        return back()->with('citadel_toast', 'تم حفظ وتحديث قالب رسالة الواتساب بنجاح.');
    }
}
