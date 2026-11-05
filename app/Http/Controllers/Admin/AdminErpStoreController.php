<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErpCatalogItem;
use App\Models\ErpCustomRequest;
use App\Models\ErpOrder;
use App\Models\ErpProposal;
use App\Models\User;
use App\Services\SoulyNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminErpStoreController extends Controller
{
    public function index()
    {
        $department = request()->query('department');
        $catalogQuery = ErpCatalogItem::latest();
        if ($department && $department !== 'all') {
            $catalogQuery->where('department', $department);
        }

        $catalogItems = $catalogQuery->paginate(15);
        $catalog = $catalogItems; // For backward compatibility
        $proposalCatalogItems = ErpCatalogItem::where('is_active', true)->get();
        $departments = ErpCatalogItem::select('department')->distinct()->pluck('department')->filter()->values();
        if ($departments->isEmpty()) {
            $departments = collect(['المبيعات والحسابات', 'المخازن والتوريدات', 'الموارد البشرية والمرتبات', 'الربط الضريبي والـ API', 'التصنيع وخطوط الإنتاج']);
        }

        $orders = ErpOrder::with('user')->latest()->paginate(15);
        $proposals = ErpProposal::latest()->paginate(10);
        $customRequests = ErpCustomRequest::latest()->paginate(10);
        $clients = User::where('is_active', true)->select('id', 'name', 'phone', 'email')->get();
        $activeTab = request()->query('tab', 'catalog');

        $counts = [
            'catalog_total'  => ErpCatalogItem::count(),
            'features_count' => ErpCatalogItem::where('is_active', true)->count(),
            'orders_pending' => ErpOrder::where('status', 'pending')->count(),
            'custom_pending' => ErpCustomRequest::where('status', 'pending')->count(),
            'proposals_sent' => ErpProposal::count(),
        ];

        return view('admin.erp-store.index', compact(
            'catalogItems',
            'catalog',
            'proposalCatalogItems',
            'departments',
            'orders',
            'proposals',
            'customRequests',
            'clients',
            'activeTab',
            'counts'
        ));
    }

    public function toggleCatalogItem(ErpCatalogItem $item)
    {
        $item->update(['is_active' => !$item->is_active]);
        return back()->with('citadel_toast', 'تم تحديث حالة ظهور الموديول في المتجر.');
    }

    public function updateCatalogItem(Request $request, ErpCatalogItem $item)
    {
        $validated = $request->validate([
            'name_ar'     => ['required', 'string', 'max:150'],
            'price'       => ['required', 'numeric', 'min:0'],
            'badge'       => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $item->update($validated);
        return back()->with('citadel_toast', 'تم تحديث بيانات الموديول بنجاح.');
    }

    public function updateOrderStatus(Request $request, ErpOrder $order, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,active,suspended,expired'],
        ]);

        $order->update([
            'status'      => $validated['status'],
            'license_key' => $order->license_key ?? ('SOULY-ERP-' . strtoupper(Str::random(12))),
            'expires_at'  => $order->expires_at ?? now()->addYear(),
        ]);

        if ($order->user && in_array($validated['status'], ['paid', 'active'])) {
            $notifier->notify(
                $order->user,
                'تم تفعيل ترخيص نظام ERP الخاص بك',
                "تم تفعيل ترخيصك بنجاح. مفتاح الترخيص: {$order->license_key}",
                route('portal.erp_store')
            );
        }

        return back()->with('citadel_toast', 'تم تحديث حالة طلب وترخيص الـ ERP بنجاح.');
    }

    public function issueManualLicense(Request $request, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'user_id'    => ['required', 'exists:users,id'],
            'tier'       => ['required', 'string'],
            'price'      => ['required', 'numeric', 'min:0'],
            'modules'    => ['nullable', 'array'],
            'valid_days' => ['nullable', 'integer', 'min:1'],
        ]);

        $licenseKey = 'SOULY-MANUAL-' . strtoupper(Str::random(12));
        $validDays = (int)($validated['valid_days'] ?? 365);

        $order = ErpOrder::create([
            'order_number'    => 'ERP-' . date('ymd') . '-' . strtoupper(Str::random(4)),
            'user_id'         => $validated['user_id'],
            'tier'            => $validated['tier'],
            'total_amount'    => $validated['price'],
            'payment_status'  => 'paid',
            'status'          => 'active',
            'license_key'     => $licenseKey,
            'modules_json'    => $validated['modules'] ?? [],
            'expires_at'      => now()->addDays($validDays),
        ]);

        $user = User::find($validated['user_id']);
        if ($user) {
            $notifier->notify(
                $user,
                'تم إصدار ترخيص ERP جديد لحسابك',
                "تم إصدار ترخيص نظام BrandERP بنجاح. مفتاح الترخيص: {$licenseKey}",
                route('portal.erp_store')
            );
        }

        return back()->with('citadel_toast', "تم إصدار الترخيص اليدوي بنجاح: {$licenseKey}");
    }

    public function replyCustomRequest(Request $request, ErpCustomRequest $customRequest, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'admin_response' => ['required', 'string', 'max:2000'],
            'status'         => ['required', 'in:pending,reviewed,accepted,rejected'],
            'quoted_price'   => ['nullable', 'numeric', 'min:0'],
        ]);

        $customRequest->update([
            'admin_response' => $validated['admin_response'],
            'status'         => $validated['status'],
            'quoted_price'   => $validated['quoted_price'] ?? $customRequest->quoted_price,
            'replied_at'     => now(),
        ]);

        if ($customRequest->user) {
            $notifier->notify(
                $customRequest->user,
                'رد جديد على طلب تخصيص ERP الخاص بك',
                "تمت مراجعة طلبك وإضافة رد من فريق الهندسة: " . Str::limit($validated['admin_response'], 60),
                route('portal.erp_store')
            );
        }

        return back()->with('citadel_toast', 'تم تسجيل الرد على طلب التخصيص وإشعار العميل.');
    }

    public function sendProposal(Request $request, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'client_name'    => ['required', 'string', 'max:150'],
            'client_phone'   => ['required', 'string'],
            'client_email'   => ['nullable', 'email'],
            'company'        => ['nullable', 'string', 'max:150'],
            'quoted_amount'  => ['required', 'numeric', 'min:0'],
            'proposal_scope' => ['required', 'string'],
        ]);

        $proposal = ErpProposal::create([
            'proposal_code' => 'PROP-' . date('ymd') . '-' . strtoupper(Str::random(4)),
            'client_name'   => $validated['client_name'],
            'client_phone'  => $validated['client_phone'],
            'client_email'  => $validated['client_email'] ?? null,
            'company'       => $validated['company'] ?? null,
            'quoted_amount' => $validated['quoted_amount'],
            'scope'         => $validated['proposal_scope'],
            'status'        => 'sent',
        ]);

        return back()->with('citadel_toast', "تم إصدار وحفظ العرض الفني رقم {$proposal->proposal_code}.");
    }
}