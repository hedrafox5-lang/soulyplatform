<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Warranty;
use App\Models\Setting;
use App\Traits\NormalizesEgyptianPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminInvoiceController extends Controller
{
    use NormalizesEgyptianPhone;

    /**
     * Display a listing of all invoices with advanced filtering.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['user', 'items', 'warranties'])
            ->latest('id');

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->where('invoice_number', 'like', "%{$term}%")
                  ->orWhere('client_name', 'like', "%{$term}%")
                  ->orWhere('client_phone', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source_type', $request->source);
        }

        $invoices = $query->paginate(20)->withQueryString();

        $stats = [
            'total_revenue'    => Invoice::where('payment_status', 'paid')->sum('total_amount'),
            'unpaid_count'     => Invoice::where('payment_status', 'unpaid')->count(),
            'warranties_count' => Warranty::where('status', 'active')->count(),
        ];

        return view('admin.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Store and generate a new custom or operational invoice with atomic line items and warranties.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'                 => ['required', 'exists:users,id'],
            'client_name'             => ['required', 'string', 'max:150'],
            'client_phone'            => ['required', 'string'],
            'client_email'            => ['nullable', 'email'],
            'client_company'          => ['nullable', 'string', 'max:150'],
            'client_address'          => ['nullable', 'string'],
            'payment_method'          => ['required', 'in:cash,wallet,card,bank_transfer,vodafone_cash'],
            'payment_status'          => ['required', 'in:paid,unpaid,partially_paid'],
            'source_type'             => ['required', 'in:field_service,hardware_order,erp_license,custom'],
            'source_id'               => ['nullable', 'integer'],
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.name'            => ['required', 'string', 'max:255'],
            'items.*.code'            => ['nullable', 'string', 'max:100'],
            'items.*.qty'             => ['required', 'integer', 'min:1'],
            'items.*.price'           => ['required', 'numeric', 'min:0'],
            'items.*.discount'        => ['nullable', 'numeric', 'min:0'],
            'items.*.warranty_days'   => ['nullable', 'integer', 'min:0'],
            'items.*.warranty_policy' => ['nullable', 'in:replacement,repair,software_maintenance'],
        ]);

        $normalizedPhone = $this->normalizeEgyptianPhone($validated['client_phone']);

        try {
            $invoice = DB::transaction(function () use ($validated, $normalizedPhone) {
                $subtotal = 0.00;
                $totalDiscount = 0.00;

                foreach ($validated['items'] as $item) {
                    $subtotal += ((float)$item['price'] * (int)$item['qty']);
                    $totalDiscount += (float)($item['discount'] ?? 0);
                }

                $totalAmount = max(0, $subtotal - $totalDiscount);

                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . date('ymd') . '-' . strtoupper(Str::random(5)),
                    'hash_id'        => hash('sha256', Str::uuid() . time()),
                    'user_id'        => $validated['user_id'],
                    'client_name'    => strip_tags(trim($validated['client_name'])),
                    'client_phone'   => $normalizedPhone,
                    'client_email'   => !empty($validated['client_email']) ? strtolower(trim($validated['client_email'])) : null,
                    'client_company' => !empty($validated['client_company']) ? strip_tags(trim($validated['client_company'])) : null,
                    'client_address' => !empty($validated['client_address']) ? strip_tags(trim($validated['client_address'])) : null,
                    'subtotal'       => $subtotal,
                    'discount_amount'=> $totalDiscount,
                    'total_amount'   => $totalAmount,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $validated['payment_status'],
                    'paid_at'        => $validated['payment_status'] === 'paid' ? now() : null,
                    'source_type'    => $validated['source_type'],
                    'source_id'      => $validated['source_id'] ?? null,
                ]);

                foreach ($validated['items'] as $item) {
                    $itemDiscount = (float)($item['discount'] ?? 0);
                    $itemTotalPrice = ((float)$item['price'] * (int)$item['qty']) - $itemDiscount;
                    $warrantyDays = (int)($item['warranty_days'] ?? 0);

                    $lineItem = InvoiceItem::create([
                        'invoice_id'      => $invoice->id,
                        'item_name'       => strip_tags(trim($item['name'])),
                        'item_code'       => !empty($item['code']) ? strip_tags(trim($item['code'])) : null,
                        'quantity'        => (int)$item['qty'],
                        'unit_price'      => (float)$item['price'],
                        'discount'        => $itemDiscount,
                        'total_price'     => $itemTotalPrice,
                        'warranty_days'   => $warrantyDays,
                        'warranty_policy' => $item['warranty_policy'] ?? null,
                    ]);

                    if ($warrantyDays > 0) {
                        Warranty::create([
                            'certificate_number'      => 'WAR-' . date('ym') . '-' . strtoupper(Str::random(6)),
                            'invoice_id'              => $invoice->id,
                            'invoice_item_id'         => $lineItem->id,
                            'user_id'                 => $validated['user_id'],
                            'product_or_service_name' => $lineItem->item_name,
                            'serial_number'           => $lineItem->item_code,
                            'coverage_type'           => $item['warranty_policy'] ?? 'repair',
                            'starts_at'               => now(),
                            'expires_at'              => now()->addDays($warrantyDays),
                            'status'                  => 'active',
                            'terms_and_conditions'    => 'ضمان رسمي معتمد من مؤسسة سولي للتكنولوجيا وفق سياسة الصيانة والاستبدال.',
                        ]);
                    }
                }

                return $invoice;
            });

            return redirect()->route('admin.invoices.show', $invoice->id)
                             ->with('citadel_toast', "تم إصدار الفاتورة رقم {$invoice->invoice_number} بنجاح.");

        } catch (\Throwable $e) {
            Log::error('Admin Invoice Creation Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'حدث خطأ أثناء إصدار الفاتورة.')->withInput();
        }
    }

    /**
     * Show detailed invoice view with print & warranty controls.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'items', 'warranties']);
        $companySettings = Setting::whereIn('key', [
            'company_name_ar', 'company_name_en', 'tax_id', 'commercial_record',
            'company_address', 'support_phone', 'support_email', 'whatsapp_tpl_invoice'
        ])->pluck('value', 'key');

        return view('admin.invoices.show', compact('invoice', 'companySettings'));
    }

    /**
     * Update payment status of an invoice.
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'in:paid,unpaid,partially_paid,refunded'],
        ]);

        $invoice->update([
            'payment_status' => $validated['payment_status'],
            'paid_at'        => $validated['payment_status'] === 'paid' ? ($invoice->paid_at ?? now()) : null,
        ]);

        return back()->with('citadel_toast', 'تم تحديث حالة الفاتورة بنجاح.');
    }

    /**
     * Generate direct messaging payload for customer notification without external APIs.
     */
    public function getCustomerMessageData(Invoice $invoice)
    {
        $invoice->load('warranties');
        $formattedPhone = $this->formatForWhatsApp($invoice->client_phone);

        if (!$formattedPhone) {
            return response()->json(['success' => false, 'message' => 'رقم هاتف العميل غير صالح.'], 422);
        }

        $template = Setting::where('key', 'whatsapp_tpl_invoice')->value('value') 
            ?? "مرحباً أستاذ {name}، شكراً لتعاملك مع مؤسسة سولي للتكنولوجيا. تم إصدار فاتورتك المعتمدة رقم #{invoice_number} بقيمة {total_amount} ج.م.\nرابط الفاتورة والضمان: {invoice_url}";

        $invoiceUrl = route('invoice.verify', $invoice->hash_id);

        $activeWarranties = $invoice->warranties->where('status', 'active');
        $warrantySummary = $activeWarranties->isNotEmpty()
            ? $activeWarranties->map(fn($w) => "{$w->product_or_service_name} (حتى " . $w->expires_at->format('Y-m-d') . ")")->join('، ')
            : 'بدون ضمان';

        $replacements = [
            '{name}'            => $invoice->client_name,
            '{invoice_number}'  => $invoice->invoice_number,
            '{total_amount}'    => number_format($invoice->total_amount, 2),
            '{invoice_url}'     => $invoiceUrl,
            '{warranty_period}' => $warrantySummary,
        ];

        $message = str_replace(array_keys($replacements), array_values($replacements), $template);

        return response()->json([
            'success' => true,
            'phone'   => $formattedPhone,
            'message' => $message,
        ]);
    }
}
