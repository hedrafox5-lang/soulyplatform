<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErpCatalogItem;
use App\Models\ErpOrder;
use App\Models\ErpProposal;
use App\Services\SoulyNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminErpStoreController extends Controller
{
    public function index()
    {
        $catalog = ErpCatalogItem::latest()->get();
        $orders = ErpOrder::with('user')->latest()->paginate(15);
        $proposals = ErpProposal::latest()->paginate(10);

        return view('admin.erp.index', compact('catalog', 'orders', 'proposals'));
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
            'license_key' => $order->license_key ?? ('SOULY-MANUAL-' . strtoupper(Str::random(12))),
            'expires_at'  => $order->expires_at ?? now()->addYear(),
        ]);

        if ($order->user && in_array($validated['status'], ['paid', 'active'])) {
            $notifier->notify(
                $order->user,
                'تم تفعيل ترخيص ERP الخاص بك',
                "تم تفعيل ترخيصك بنجاح. مفتاح الترخيص: {$order->license_key}",
                route('portal.erp_store')
            );
        }

        return back()->with('citadel_toast', 'تم تحديث حالة طلب وترخيص الـ ERP.');
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
