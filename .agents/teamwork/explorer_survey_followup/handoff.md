# Handoff Report: Investigation of `/portal/devices` 500 and `/portal/profile` 404

**Core Findings**:
1. `GET /portal/devices` throws HTTP 500 (`Undefined variable $wallet`) because `resources/views/portal/devices.blade.php` was erroneously committed as a duplicate copy of `resources/views/portal/wallet.blade.php`, which expects `$wallet` and `$transactions` rather than registered devices.
2. `POST /portal/profile` returns HTTP 404 (`NotFoundHttpException`) because the global profile/password modal form in `resources/views/portal/layout.blade.php` submits to `/portal/profile` (`portal.profile.update`), but no corresponding route or controller method exists in `routes/web.php` and `ClientPortalController.php`.

---

## 1. Observation

### Observation 1.1: Runtime Error on `GET /portal/devices`
From `storage/logs/laravel.log:110760`:
```text
[2026-11-05 16:07:47] local.ERROR: Undefined variable $wallet (View: C:\xampp\htdocs\soulyplatform\resources\views\portal\devices.blade.php) {"userId":10,"exception":"[object] (Illuminate\\View\\ViewException(code: 0): Undefined variable $wallet (View: C:\\xampp\\htdocs\\soulyplatform\\resources\\views\\portal\\devices.blade.php) at C:/xampp/htdocs/soulyplatform/storage/framework/views/e1b5584f010266c0be556e5a391c6f20.php:17)
```
Browser console log reported by user:
```text
16:07:50.137 devices:1 GET http://127.0.0.1:8000/portal/devices 500 (Internal Server Error)
```

Direct PHP reproduction command:
```powershell
c:\xampp\php\php.exe -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class); \$kernel->bootstrap(); \$client = App\Models\User::first(); Illuminate\Support\Facades\Auth::login(\$client); try { view('portal.devices', ['user' => \$client, 'devices' => collect()])->render(); } catch (Throwable \$e) { echo get_class(\$e) . ': ' . \$e->getMessage(); }"
```
Output:
```text
Illuminate\View\ViewException: Undefined variable $wallet (View: C:\xampp\htdocs\soulyplatform\resources\views\portal\devices.blade.php)
```

### Observation 1.2: Controller Contract for Devices
In `app/Http/Controllers/ClientDeviceController.php:22-30`:
```php
public function index(): View
{
    $user = Auth::user();
    $devices = ClientDevice::where('user_id', $user->id)
        ->latest()
        ->get();

    return view('portal.devices', compact('user', 'devices'));
}
```
`ClientDeviceController::index()` passes `$user` and `$devices`. It does not pass `$wallet` or `$transactions`.

### Observation 1.3: Content of `resources/views/portal/devices.blade.php`
In `resources/views/portal/devices.blade.php`:
- Line 3: `@section('title', 'المحفظة الرقمية وسجل العمليات | سولي')`
- Line 19: `currentBalance: {{ (float) $wallet->balance }},`
- Line 39: `<!-- 1. WALLET SUMMARY & BALANCE HEADER -->`
- Line 53: `{{ number_format($wallet->balance, 2) }}`
- Line 231: `@if($transactions->count() > 0)`
- Git inspection shows commit `96e287ea62d48ee85fd391e690251ab26fbef4c1` added `resources/views/portal/devices.blade.php` as a verbatim copy of `resources/views/portal/wallet.blade.php`. There are no device listings, no device registration forms, and no device maintenance modals.

### Observation 1.4: Database Schema for `client_devices`
Inspecting columns of table `client_devices`:
```text
Columns: id, user_id, model_number, status, details, images_json, created_at, updated_at
```
Missing columns:
- `last_maintenance_type` (called in `ClientDeviceController.php:171`: `$device->update(['last_maintenance_type' => $validated['maintenance_type']])`)
- `serial_number` (validated and referenced in `ClientDeviceController.php:61,86`)

### Observation 1.5: Runtime Error on `POST /portal/profile`
Browser console log reported by user:
```text
16:09:37.369 profile:1 POST http://127.0.0.1:8000/portal/profile 404 (Not Found)
```
Checking route dispatch for `POST /portal/profile`:
```powershell
c:\xampp\php\php.exe -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class); \$kernel->bootstrap(); try { \$route = app('router')->getRoutes()->match(Illuminate\Http\Request::create('/portal/profile', 'POST')); echo 'Matched: ' . \$route->getName(); } catch (Symfony\Component\HttpKernel\Exception\NotFoundHttpException \$e) { echo 'NotFoundHttpException (404)'; }"
```
Output:
```text
NotFoundHttpException (404)
```

### Observation 1.6: Profile Form in `resources/views/portal/layout.blade.php`
Lines 120–181 in `resources/views/portal/layout.blade.php`:
```blade
<!-- Line 137 -->
<form action="{{ Route::has('portal.profile.update') ? route('portal.profile.update') : url('/portal/profile') }}" method="POST" class="space-y-4 text-xs font-cairo text-right">
    @csrf
    <input type="text" name="name" value="{{ auth()->user()->name }}" required ...>
    <input type="text" name="phone" value="{{ auth()->user()->phone }}" required ...>
    <input type="email" name="email" value="{{ auth()->user()->email }}" ...>
    <input type="text" name="company_name" value="{{ auth()->user()->company_name }}" ...>
    <input type="password" name="password" ...>
    <input type="password" name="password_confirmation" ...>
    <button type="submit" ...>حفظ التعديلات ✓</button>
</form>
```
Because `Route::has('portal.profile.update')` evaluates to `false`, the form action falls back to `url('/portal/profile')`.

### Observation 1.7: Route Registration in `routes/web.php`
In `routes/web.php`:
Grep search for `profile` returned 0 matches in `routes/web.php`.
`php artisan route:list --path=portal` returned 22 routes, none of which contain `profile`.

### Observation 1.8: Controller Action in `ClientPortalController.php`
Grep search for `profile` in `app/Http/Controllers/ClientPortalController.php` returned 0 matches. No update profile action exists in the codebase.

---

## 2. Logic Chain

1. **Chain for Issue 1 (`GET /portal/devices 500`)**:
   - `GET /portal/devices` invokes `ClientDeviceController::index()` (Obs 1.1, 1.2).
   - `ClientDeviceController::index()` retrieves `$devices = ClientDevice::where('user_id', $user->id)->latest()->get()` and renders `view('portal.devices', compact('user', 'devices'))` (Obs 1.2).
   - When compiling `resources/views/portal/devices.blade.php`, Blade evaluates `{{ (float) $wallet->balance }}` on line 19 (Obs 1.3).
   - Because `$wallet` is not passed by `ClientDeviceController::index()`, PHP raises `ErrorException: Undefined variable $wallet` (Obs 1.1).
   - Investigation into file history revealed that in commit `96e287ea`, `resources/views/portal/devices.blade.php` was accidentally created with the contents of `wallet.blade.php` (Obs 1.3).
   - Furthermore, even if `$wallet` were passed into the view, the view would immediately crash on line 231 due to `Undefined variable $transactions`, and the client would see the digital wallet deposit page rather than their registered devices.
   - Secondary risk: Submitting a maintenance request invokes `ClientDeviceController::requestMaintenance()`, which writes to `last_maintenance_type` on `ClientDevice` (Obs 1.4), which is absent in `client_devices` table and would cause a subsequent SQL 1054 error.

2. **Chain for Issue 2 (`POST /portal/profile 404`)**:
   - The user opens the profile modal from the portal header button and submits the form (Obs 1.6).
   - In `resources/views/portal/layout.blade.php:137`, the form action conditionally checks `Route::has('portal.profile.update')` (Obs 1.6).
   - `routes/web.php` does not register any route named `portal.profile.update` or any route for `/portal/profile` (Obs 1.7).
   - `Route::has('portal.profile.update')` evaluates to `false`, causing the form to submit to `http://127.0.0.1:8000/portal/profile` with HTTP method `POST` (Obs 1.6).
   - Because Laravel's router finds no matching route for `POST /portal/profile`, it throws a `NotFoundHttpException` resulting in HTTP 404 (Obs 1.5).
   - `ClientPortalController` also lacks any profile update handling method (Obs 1.8).

---

## 3. Caveats

1. **Layout Dependencies**: `resources/views/portal/layout.blade.php` uses `auth()->user()->walletOrCreate()->balance` in its header, which is safe and self-contained; it does not depend on a controller-provided `$wallet` variable.
2. **Database Migration vs. Guarded Controller**: The `client_devices` table in the database lacks `serial_number` and `last_maintenance_type`. While `ClientDeviceController@store` silently ignores `serial_number` because it is omitted from `$fillable`, `ClientDeviceController@requestMaintenance` attempts to write `last_maintenance_type`. Running a database migration to add `serial_number` and `last_maintenance_type` to `client_devices` table guarantees clean operation across both the controller and view.
3. **Form Method Support**: While the Blade form sends `POST`, registering `Route::match(['post', 'put'], '/profile', ...)` accommodates both plain POST and RESTful PUT/PATCH without breaking existing behavior.

---

## 4. Conclusion & Proposed Fixes

### 4.1 Fix for `POST /portal/profile 404 (Not Found)`

#### A. Add Route in `routes/web.php`
Inside the authenticated `Route::prefix('portal')->name('portal.')->group(function () { ... })` block (around line 240):

```php
// User Profile & Password Update (Supports both POST from modal and PUT)
Route::match(['post', 'put'], '/profile', [ClientPortalController::class, 'updateProfile'])->name('profile.update');
Route::get('/profile', function () {
    return redirect()->route('portal.index');
});
```

#### B. Add `updateProfile` Method in `app/Http/Controllers/ClientPortalController.php`
Add the following method to `ClientPortalController`:

```php
/**
 * Update client profile details and password.
 */
public function updateProfile(Request $request): RedirectResponse
{
    $user = Auth::user();

    $validated = $request->validate([
        'name'         => ['required', 'string', 'max:150'],
        'phone'        => ['required', 'string', 'max:50'],
        'email'        => ['nullable', 'email', 'max:190', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
        'company_name' => ['nullable', 'string', 'max:150'],
        'password'     => ['nullable', 'string', 'min:6', 'confirmed'],
    ], [
        'name.required'      => 'يرجى إدخال الاسم بالكامل.',
        'phone.required'     => 'يرجى إدخال رقم الهاتف.',
        'email.unique'       => 'البريد الإلكتروني مسجل مسبقاً لمستخدم آخر.',
        'password.min'       => 'يجب ألا تقل كلمة المرور الجديدة عن 6 أحرف.',
        'password.confirmed' => 'تأكيد كلمة المرور غير متطابق مع كلمة المرور الجديدة.',
    ]);

    $user->name = strip_tags(trim($validated['name']));
    $user->phone = strip_tags(trim($validated['phone']));
    $user->email = !empty($validated['email']) ? strtolower(trim($validated['email'])) : null;
    $user->company_name = !empty($validated['company_name']) ? strip_tags(trim($validated['company_name'])) : null;

    if (!empty($validated['password'])) {
        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
    }

    $user->save();

    return back()->with('citadel_toast', 'تم تحديث بيانات الحساب وكلمة المرور بنجاح ✓');
}
```

---

### 4.2 Fix for `GET /portal/devices 500 (Internal Server Error)`

#### A. Replace `resources/views/portal/devices.blade.php` with Intended Devices View
Overwrite `resources/views/portal/devices.blade.php` with a clean Blade template that consumes `$devices` and provides:
1. Overview header with device counts (total, healthy, maintenance required).
2. Action button "+ تسجيل جهاز جديد" triggering an Alpine modal.
3. Responsive grid of client devices showing:
   - Model name (`model_number`)
   - Serial number (`serial_number`)
   - Status badge (`healthy`, `needs_maintenance`, `visit_requested`, `in_company_repair`, `warranty_active`)
   - Technical details (`details`)
   - Attached images/photos if present (`images_json`)
   - Actions: "طلب صيانة هندسية 🔧", "تعديل ✏️", "حذف 🗑️".
4. "تسجيل جهاز جديد" Modal (`POST route('portal.devices.store')`) supporting image uploads.
5. "طلب صيانة هندسية" Modal (`POST /portal/devices/{device}/maintenance`) supporting on-site visit and company repair scheduling.
6. "تعديل بيانات الجهاز" Modal (`PUT /portal/devices/{device}`).

#### B. Enhance `ClientDeviceController@index`
To prevent any edge-case view exception if child components query `$wallet`, pass `$wallet`:
```php
public function index(): View
{
    $user = Auth::user();
    $wallet = $user->walletOrCreate();
    $devices = ClientDevice::where('user_id', $user->id)
        ->latest()
        ->get();

    return view('portal.devices', compact('user', 'devices', 'wallet'));
}
```

#### C. Database Schema Migration for `client_devices`
Create and run a migration to add `serial_number` and `last_maintenance_type` to `client_devices`:
```php
Schema::table('client_devices', function (Blueprint $table) {
    if (!Schema::hasColumn('client_devices', 'serial_number')) {
        $table->string('serial_number')->nullable()->after('model_number');
    }
    if (!Schema::hasColumn('client_devices', 'last_maintenance_type')) {
        $table->string('last_maintenance_type')->nullable()->after('status');
    }
});
```
And add `'serial_number'` to `$fillable` in `app/Models/ClientDevice.php`.

---

## 5. Verification Method

### 5.1 Route & Controller Verification
Run:
```powershell
c:\xampp\php\php.exe artisan route:list --path=portal/profile
c:\xampp\php\php.exe artisan route:list --path=portal/devices
```
Expected output:
- `POST|PUT portal/profile` maps to `ClientPortalController@updateProfile` named `portal.profile.update`.
- `GET portal/devices` maps to `ClientDeviceController@index` named `portal.devices`.

### 5.2 View Compilation & Render Test
Run:
```powershell
c:\xampp\php\php.exe -r @"
require 'vendor/autoload.php';
`$app = require_once 'bootstrap/app.php';
`$kernel = `$app->make(Illuminate\Contracts\Console\Kernel::class);
`$kernel->bootstrap();
`$client = App\Models\User::first();
Illuminate\Support\Facades\Auth::login(`$client);
echo view('portal.devices', ['user' => `$client, 'devices' => collect(), 'wallet' => `$client->walletOrCreate()])->render();
"@
```
Expected output:
- Returns complete HTML string without any `ViewException` or `ErrorException`.

### 5.3 HTTP Test Simulation
Simulate profile update:
```powershell
c:\xampp\php\php.exe -r @"
require 'vendor/autoload.php';
`$app = require_once 'bootstrap/app.php';
`$kernel = `$app->make(Illuminate\Contracts\Http\Kernel::class);
`$kernel->bootstrap();
`$client = App\Models\User::first();
Illuminate\Support\Facades\Auth::login(`$client);
`$request = Illuminate\Http\Request::create('/portal/profile', 'POST', [
    'name' => `$client->name,
    'phone' => `$client->phone ?? '01012345678',
]);
`$response = `$kernel->handle(`$request);
echo 'Profile POST Status: ' . `$response->getStatusCode();
"@
```
Expected output:
- Status 302 redirect with success flash message `citadel_toast` (instead of 404).
