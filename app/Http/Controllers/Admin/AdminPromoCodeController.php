<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class AdminPromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::withCount('usages')
            ->latest()
            ->paginate(15);

        return view('admin.promo-codes.index', compact('promoCodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'              => ['required', 'string', 'max:30', 'unique:promo_codes,code'],
            'type'              => ['required', 'in:fixed,percentage,bonus_balance'],
            'value'             => ['required', 'numeric', 'min:1'],
            'min_order_amount'  => ['nullable', 'numeric', 'min:0'],
            'max_discount_cap'  => ['nullable', 'numeric', 'min:1'],
            'max_uses_total'    => ['nullable', 'integer', 'min:1'],
            'max_uses_per_user' => ['required', 'integer', 'min:1'],
            'starts_at'         => ['nullable', 'date'],
            'expires_at'        => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = true;

        PromoCode::create($validated);

        return back()->with('citadel_toast', 'تم إنشاء كود الخصم بنجاح.');
    }

    public function toggle(PromoCode $promoCode)
    {
        $promoCode->update(['is_active' => !$promoCode->is_active]);
        return back()->with('citadel_toast', 'تم تحديث حالة كود الخصم.');
    }

    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();
        return back()->with('citadel_toast', 'تم حذف كود الخصم.');
    }
}
