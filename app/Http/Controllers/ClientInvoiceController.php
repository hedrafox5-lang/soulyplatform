<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Warranty;
use App\Models\WarrantyClaim;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientInvoiceController extends Controller
{
    /**
     * Display client's invoices and warranties dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $invoices = Invoice::where('user_id', $user->id)
            ->with(['items', 'warranties'])
            ->latest('id')
            ->paginate(15);

        $warranties = Warranty::where('user_id', $user->id)
            ->with(['invoice', 'invoiceItem'])
            ->latest('id')
            ->get();

        return view('portal.invoices', compact('invoices', 'warranties'));
    }

    /**
     * Show client specific invoice details.
     */
    public function show(Invoice $invoice)
    {
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بعرض هذه الفاتورة.');
        }

        $invoice->load(['items', 'warranties']);
        $companySettings = Setting::whereIn('key', [
            'company_name_ar', 'company_name_en', 'tax_id', 'commercial_record',
            'company_address', 'support_phone', 'support_email'
        ])->pluck('value', 'key');

        return view('portal.invoice-details', compact('invoice', 'companySettings'));
    }

    /**
     * Submit a warranty claim for free maintenance during coverage period.
     */
    public function submitWarrantyClaim(Request $request, Warranty $warranty)
    {
        if ($warranty->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتقديم مطالبة لهذا الضمان.');
        }

        if ($warranty->status !== 'active' || $warranty->expires_at->isPast()) {
            return back()->with('error', 'عفواً، انتهت فترة سريان هذا الضمان أو تم إلغاؤه.');
        }

        $validated = $request->validate([
            'issue_description' => ['required', 'string', 'min:10', 'max:2000'],
            'attachments'       => ['nullable', 'array', 'max:3'],
            'attachments.*'     => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        try {
            $claim = DB::transaction(function () use ($warranty, $validated, $request) {
                $uploadedFiles = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $uploadedFiles[] = $file->store('warranty-claims/' . Auth::id(), 'public');
                    }
                }

                $claimNumber = 'CLM-' . date('ymd') . '-' . strtoupper(Str::random(4));

                $claimRecord = WarrantyClaim::create([
                    'claim_number'      => $claimNumber,
                    'warranty_id'       => $warranty->id,
                    'user_id'           => Auth::id(),
                    'issue_description' => strip_tags(trim($validated['issue_description'])),
                    'attachments'       => $uploadedFiles,
                    'claim_status'      => 'pending',
                ]);

                // Auto-create zero-cost warranty booking for engineering dispatch
                $booking = Booking::create([
                    'booking_number' => 'BK-WAR-' . strtoupper(Str::random(5)),
                    'user_id'        => Auth::id(),
                    'service_id'     => 1, // Fallback engineering maintenance
                    'name'           => Auth::user()->name,
                    'phone'          => Auth::user()->phone,
                    'status'         => 'new',
                    'payment_status' => 'paid',
                    'payment_method' => 'cash',
                    'total_amount'   => 0.00,
                    'notes'          => "طلب صيانة بالضمان لـ: {$warranty->product_or_service_name} (مطالبة: {$claimNumber})",
                    'is_field_visit' => true,
                ]);

                $claimRecord->update(['booking_id' => $booking->id]);

                return $claimRecord;
            });

            return back()->with('citadel_toast', "تم تسجيل طلب الضمان رقم {$claim->claim_number} بنجاح، جاري المراجعة.");

        } catch (\Throwable $e) {
            Log::error('Warranty Claim Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'حدث خطأ أثناء إرسال طلب الضمان.')->withInput();
        }
    }

    /**
     * Public verification endpoint accessible via QR code scan.
     */
    public function verifyPublic(string $hashId)
    {
        $invoice = Invoice::where('hash_id', $hashId)
            ->with(['items', 'warranties'])
            ->firstOrFail();

        $companySettings = Setting::whereIn('key', [
            'company_name_ar', 'company_name_en', 'tax_id', 'commercial_record',
            'company_address', 'support_phone', 'support_email'
        ])->pluck('value', 'key');

        return view('pages.invoice-verify', compact('invoice', 'companySettings'));
    }
}
