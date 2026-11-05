## 2026-11-05T14:26:03Z
You are worker_m1, an implementation worker subagent.
Your working directory is: c:\xampp\htdocs\soulyplatform\.agents\teamwork\worker_m1\

Read the original request at: c:\xampp\htdocs\soulyplatform\.agents\teamwork\ORIGINAL_REQUEST.md
Read the project specification at: c:\xampp\htdocs\soulyplatform\.agents\teamwork\orchestrator\PROJECT.md
Read the detailed survey report at: c:\xampp\htdocs\soulyplatform\.agents\teamwork\explorer_survey_1\handoff.md

## Write Ownership
You own EXCLUSIVELY:
`app/Http/Controllers/HardwareStoreController.php`
Do NOT edit any other files.

## Mandatory Integrity Warning
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

## Objective
Implement the fix for Requirement R1 (Hardware Store Checkout 422 error and order placement) in `app/Http/Controllers/HardwareStoreController.php`:
1. In `checkout(Request $request)`:
   - At the beginning of the method, merge/alias `customer_name` and `name`, and `customer_phone` and `phone`:
     ```php
     $user = Auth::user();
     $customerName = $request->input('customer_name') ?? $request->input('name') ?? ($user?->name);
     $customerPhone = $request->input('customer_phone') ?? $request->input('phone') ?? ($user?->phone);
     $request->merge([
         'customer_name'  => $customerName,
         'customer_phone' => $customerPhone,
         'name'           => $customerName,
         'phone'          => $customerPhone,
     ]);
     ```
   - Update the validation rules:
     ```php
     $isAuth = Auth::check();
     $validated = $request->validate([
         'items'            => ['required', 'array', 'min:1'],
         'items.*.id'       => ['required', 'integer', 'exists:hardware_products,id'],
         'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
         'customer_name'    => [\Illuminate\Validation\Rule::requiredIf(!$isAuth), 'nullable', 'string', 'max:150'],
         'customer_phone'   => [\Illuminate\Validation\Rule::requiredIf(!$isAuth), 'nullable', 'string'],
         'name'             => ['nullable', 'string', 'max:150'],
         'phone'            => ['nullable', 'string'],
         'email'            => ['nullable', 'email', 'max:190'],
         'company'          => ['nullable', 'string', 'max:150'],
         'shipping_address' => ['required', 'string', 'max:255'],
         'city'             => ['nullable', 'string', 'max:100'],
         'promo_code'       => ['nullable', 'string', 'max:50'],
         'payment_method'   => ['required', 'in:cash_on_delivery,wallet,card,bank_transfer,transfer,cod_deposit'],
         'notes'            => ['nullable', 'string', 'max:500'],
     ]);
     ```
   - Align status:
     - If payment_method === 'wallet': `$orderStatus = 'confirmed_preparing'`, `$paymentStatus = 'paid'`
     - Else: `$orderStatus = 'pending_review'`, `$paymentStatus = 'pending'`
   - Return standard JSON response:
     ```php
     return response()->json([
         'success'      => true,
         'message'      => 'تم تسجيل طلب الشراء بنجاح وجارٍ تجهيز الشحنة.',
         'order_number' => $order->order_number,
         'status'       => $order->status,
         'redirect'     => route('portal.index'),
     ]);
     ```
   - Fix `HardwareOrderItem::create`: Populate `order_id` (NOT `hardware_order_id`), `product_sku`, `product_name`, `unit_price`, `quantity`, `subtotal`.
   - Fix `Wallet::create`: Set `balance => 0.00` (instead of `cash_balance`).
   - Fix `WalletTransaction::create`: Set `type => 'purchase'`, `reference_code => 'TXN-' . strtoupper(\Illuminate\Support\Str::random(12))`, `payment_method => 'wallet_balance'`, `status => 'approved'`.

2. Run test verification using:
   `c:\xampp\php\php.exe artisan test --filter=HardwareStoreAndTicketScopeFullSuiteTest`
   `c:\xampp\php\php.exe artisan test --filter=Task1StoresAndUxAuditTest`
   Document test output verbatim in your report.

3. Update progress in `c:\xampp\htdocs\soulyplatform\.agents\teamwork\worker_m1\progress.md`.
Write full handoff report to `c:\xampp\htdocs\soulyplatform\.agents\teamwork\worker_m1\handoff.md`.
Send a completion message via `send_message` to orchestrator (8633b54e-287a-486c-b797-1bd6c95f97a8).
