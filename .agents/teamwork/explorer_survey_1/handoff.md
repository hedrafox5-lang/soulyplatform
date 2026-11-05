# Handoff Report: Requirement R1 — Hardware Store Checkout Error Fix

## 1. Observation

### 1.1 Route Definition
In `routes/web.php` (lines 157–160):
```php
// Standalone Hardware & POS Equipment Marketplace Routes
Route::prefix('store/hardware')->name('store.hardware')->group(function () {
    Route::get('/', [HardwareStoreController::class, 'index']);
    Route::post('/checkout', [HardwareStoreController::class, 'checkout'])->name('.checkout');
});
```
- Endpoint: `POST /store/hardware/checkout`
- Route name: `store.hardware.checkout`
- Handled by: `App\Http\Controllers\HardwareStoreController::checkout`

### 1.2 Controller and Validation Rules
In `app/Http/Controllers/HardwareStoreController.php` (lines 90–105):
```php
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items'            => ['required', 'array', 'min:1'],
            'items.*.id'       => ['required', 'integer', 'exists:hardware_products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'name'             => ['required_without:user', 'nullable', 'string', 'max:150'],
            'phone'            => ['required_without:user', 'nullable', 'string'],
            'email'            => ['nullable', 'email', 'max:190'],
            'company'          => ['nullable', 'string', 'max:150'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'city'             => ['nullable', 'string', 'max:100'],
            'promo_code'       => ['nullable', 'string', 'max:50'],
            'payment_method'   => ['required', 'in:cash_on_delivery,wallet,card,bank_transfer'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);
```

### 1.3 Verbatim Error Observed During Test Execution
Command:
```powershell
c:\xampp\php\php.exe artisan test --filter=test_hardware_checkout_via_wallet_deducts_balance_and_activates_order
```
Output:
```
   FAILED  Tests\Feature\HardwareStoreAndTicketScopeFullSuiteTest > hardware checkout via wallet deducts balance and…   
  Expected response status code [200] but received 422.
Failed asserting that 422 is identical to 200.

The following errors occurred during the last request:

{
    "message": "The name field is required when user is not present. (and 1 more error)",
    "errors": {
        "name": [
            "The name field is required when user is not present."
        ],
        "phone": [
            "The phone field is required when user is not present."
        ]
    }
}
```

Command:
```powershell
c:\xampp\php\php.exe artisan test --filter=test_hardware_checkout_validation_and_direct_transfer
```
Output:
```
   FAILED  Tests\Feature\HardwareStoreAndTicketScopeFullSuiteTest > hardware checkout validation and direct transfer    
  Failed to find a validation error in the response for key: 'customer_name'

Response has the following JSON validation errors:

{
    "items": [
        "The items field is required."
    ],
    "name": [
        "The name field is required when user is not present."
    ],
    "phone": [
        "The phone field is required when user is not present."
    ],
    "shipping_address": [
        "The shipping address field is required."
    ],
    "payment_method": [
        "The payment method field is required."
    ]
}

Failed asserting that an array has the key 'customer_name'.
```

Command:
```powershell
c:\xampp\php\php.exe artisan test --filter=Task1StoresAndUxAuditTest
```
Output:
```
   FAILED  Tests\Feature\Task1StoresAndUxAuditTest > standalone stores checkout flow and lead capture                   
  Expected response status code [200] but received 422.
Failed asserting that 422 is identical to 200.

The following errors occurred during the last request:

{
    "message": "The name field is required when user is not present. (and 1 more error)",
    "errors": {
        "name": [
            "The name field is required when user is not present."
        ],
        "phone": [
            "The phone field is required when user is not present."
        ]
    }
}
```

### 1.4 Frontend Views and Payloads
Three views interact with `/store/hardware/checkout`:

1. `resources/views/pages/hardware-store.blade.php`:
   - Lines 298–310 wrap inputs in `@guest`:
     ```blade
     @guest
         <div>
             <label class="block text-slate-300 font-bold mb-1">الاسم بالكامل *</label>
             <input type="text" x-model="formData.name" required ...>
         </div>
         <div>
             <label class="block text-slate-300 font-bold mb-1">رقم الموبايل (واتساب) *</label>
             <input type="tel" x-model="formData.phone" required ...>
         </div>
     @endguest
     ```
   - When authenticated, `formData.name` and `formData.phone` remain empty strings `""`.
   - Payload submitted (lines 435–443):
     ```javascript
     {
         items: this.cartItems.map(i => ({ id: i.id, quantity: i.quantity })),
         name: this.formData.name,
         phone: this.formData.phone,
         city: this.formData.city,
         shipping_address: this.formData.shipping_address,
         payment_method: this.formData.payment_method,
         promo_code: this.formData.promo_code
     }
     ```

2. `resources/views/pages/store/hardware-store.blade.php`:
   - Lines 263–272 also wrap `checkoutForm.name` and `checkoutForm.phone` in `@guest ... @endguest`.
   - Payload submitted (lines 393–402): sends `name`, `phone`, `city`, `shipping_address`, `promo_code`, `payment_method`, `notes`.

3. `resources/views/pages/store/hardware.blade.php`:
   - Form fields use `customer_name` and `customer_phone` (lines 301–309):
     ```blade
     <input type="text" x-model="checkoutForm.customer_name" required ...>
     <input type="text" x-model="checkoutForm.customer_phone" required ...>
     ```
   - Payload submitted (lines 498–505):
     ```javascript
     {
         customer_name: this.checkoutForm.customer_name,
         customer_phone: this.checkoutForm.customer_phone,
         shipping_address: this.checkoutForm.shipping_address,
         payment_method: this.checkoutForm.payment_method,
         notes: this.checkoutForm.notes,
         items: this.cart.map(i => ({ id: i.id, quantity: i.quantity }))
     }
     ```

### 1.5 Database Schema & Related Discrepancies
- In migration `database/migrations/2026_10_27_000005_create_hardware_store_and_update_tickets_tables.php`:
  - `hardware_orders` columns include: `order_number`, `user_id`, `customer_name`, `customer_phone`, `shipping_address`, `items_json`, `total_amount`, `payment_method` (default `'transfer'`), `payment_status` (default `'pending'`), `status` (default `'pending_review'`).
  - `hardware_order_items` columns: `order_id` (foreignId), `product_id`, `product_sku`, `product_name`, `unit_price`, `quantity`, `subtotal`.
- In `app/Http/Controllers/HardwareStoreController.php`:
  - `payment_method` validator is `'in:cash_on_delivery,wallet,card,bank_transfer'`, missing `'transfer'` and `'cod_deposit'`. Tests pass `'payment_method' => 'transfer'`.
  - Line 247-254 creates `HardwareOrderItem` with `hardware_order_id`, `name_ar`, `item_name`, but misses `order_id`, `product_sku`, `product_name`. The thrown SQL exception is caught silently by `catch (\Throwable $itemEx) {}`, leaving `order->items` empty (failing `$this->assertCount(2, $order->items)`).
  - Lines 214-222: `WalletTransaction` creation sets `'type' => 'payment'`, `'reference' => $orderNumber`, missing `reference_code` and `payment_method`, and failing tests expecting `'type' => 'purchase'`.
  - Lines 134-140: Guest provisioning passes `cash_balance` and `bonus_balance` to `Wallet::create`, but `wallets` table only has `balance`.

---

## 2. Logic Chain

1. **Rule Semantics**:
   - In Laravel validation, `required_without:user` checks whether `$request->input('user')` is missing or empty in the HTTP request payload array.
   - It does **not** check Laravel session authentication (`Auth::check()`).
   - Observations 1.2 and 1.4 show that neither guest nor authenticated frontend payloads ever submit a key named `user`.
   - Therefore, the validator considers the parameter `user` absent in 100% of requests.

2. **Triggering the 422 Error**:
   - Because `user` is never present in `$request`, the rule `required_without:user` forces `name` and `phone` to be strictly required for every submission.
   - When an authenticated user submits via `pages/hardware-store.blade.php` or `pages/store/hardware-store.blade.php`, the fields are excluded from the form DOM by `@guest`, submitting `name: ""` and `phone: ""`.
   - When any client or test submits using `customer_name` and `customer_phone` (as in `pages/store/hardware.blade.php`, `HardwareStoreAndTicketScopeFullSuiteTest`, and `Task1StoresAndUxAuditTest`), the keys `name` and `phone` are completely omitted.
   - In all these cases, Laravel immediately aborts with HTTP status 422 and validation error:
     `"The name field is required when user is not present."` (and `"The phone field is required when user is not present."`).

3. **Field Naming Discrepancy**:
   - Observation 1.3 shows that `HardwareStoreAndTicketScopeFullSuiteTest::test_hardware_checkout_validation_and_direct_transfer` explicitly verifies:
     `$badResponse->assertJsonValidationErrors(['customer_name', 'customer_phone', 'shipping_address', 'payment_method', 'items']);`
   - Because the controller validates `name` and `phone` instead of `customer_name` and `customer_phone`, empty payload validation fails to find `customer_name` and `customer_phone`.

4. **Payment Method & Downstream Processing**:
   - The test payload in `Task1StoresAndUxAuditTest` and `HardwareStoreAndTicketScopeFullSuiteTest` passes `'payment_method' => 'transfer'`.
   - Because `HardwareStoreController::checkout()` restricts payment methods to `in:cash_on_delivery,wallet,card,bank_transfer`, `'transfer'` causes a secondary 422 validation failure: `"The selected payment method is invalid."`
   - In order for checkout to complete without errors, the allowed payment methods must include both `'transfer'` and `'cod_deposit'`.

---

## 3. Caveats

- **View Consistencies**: There are three hardware store Blade views in the codebase (`pages/hardware-store.blade.php`, `pages/store/hardware-store.blade.php`, and `pages/store/hardware.blade.php`). The controller should alias/normalize `name` and `customer_name`, and `phone` and `customer_phone` so that checkout succeeds regardless of which view or client invokes it.
- **Scope Limit**: As an explorer subagent under read-only mode, no production code has been modified.

---

## 4. Conclusion

The 422 checkout error `"The name field is required when user is not present"` is caused by:
1. Using the Laravel input validation rule `required_without:user`, which inspects `$request['user']` rather than `Auth::check()`.
2. Input field naming mismatch between `name`/`phone` and `customer_name`/`customer_phone`.

### Recommended Fix Plan:
In `app/Http/Controllers/HardwareStoreController.php`:

1. **Pre-validate Aliasing / Merging**:
   At the start of `checkout(Request $request)`:
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

2. **Correct Validation Rules**:
   ```php
   $isAuth = Auth::check();
   $validated = $request->validate([
       'items'            => ['required', 'array', 'min:1'],
       'items.*.id'       => ['required', 'integer', 'exists:hardware_products,id'],
       'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
       'customer_name'    => [Illuminate\Validation\Rule::requiredIf(!$isAuth), 'nullable', 'string', 'max:150'],
       'customer_phone'   => [Illuminate\Validation\Rule::requiredIf(!$isAuth), 'nullable', 'string'],
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

3. **Status and Response Alignment**:
   - Set `$orderStatus = ($validated['payment_method'] === 'wallet') ? 'confirmed_preparing' : 'pending_review';`
   - Set `$paymentStatus = ($validated['payment_method'] === 'wallet') ? 'paid' : 'pending';`
   - Return in JSON response:
     ```php
     return response()->json([
         'success'      => true,
         'message'      => 'تم تسجيل طلب الشراء بنجاح وجارٍ تجهيز الشحنة.',
         'order_number' => $order->order_number,
         'status'       => $order->status,
         'redirect'     => route('portal.index'),
     ]);
     ```

4. **Line Item & Wallet Synchronization**:
   - In `HardwareOrderItem::create`: populate `order_id` (not `hardware_order_id`), `product_sku`, `product_name`, `unit_price`, `quantity`, `subtotal`.
   - In `Wallet::create`: populate `balance => 0.00` (instead of `cash_balance`).
   - In `WalletTransaction::create`: populate `type => 'purchase'`, `reference_code => 'TXN-' . strtoupper(Str::random(12))`, `payment_method => 'wallet_balance'`, `status => 'approved'`.

---

## 5. Verification Method

To verify after implementation:
1. Run direct transfer validation and checkout test:
   ```powershell
   c:\xampp\php\php.exe artisan test --filter=test_hardware_checkout_validation_and_direct_transfer
   ```
   **Expected**: 0 failures, status 200 on valid guest checkout, status 422 with `customer_name`, `customer_phone`, `shipping_address`, `payment_method`, `items` on empty payload.
2. Run wallet checkout test:
   ```powershell
   c:\xampp\php\php.exe artisan test --filter=test_hardware_checkout_via_wallet_deducts_balance_and_activates_order
   ```
   **Expected**: 0 failures, status 200 on authenticated user checkout, wallet balance deducted.
3. Run store audit test:
   ```powershell
   c:\xampp\php\php.exe artisan test --filter=test_standalone_stores_checkout_flow_and_lead_capture
   ```
   **Expected**: Pass without 422 validation errors.
