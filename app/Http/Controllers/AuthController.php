<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lead;
use App\Models\Wallet;
use App\Traits\NormalizesEgyptianPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use NormalizesEgyptianPhone;

    /**
     * Render the authentication login view.
     */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return in_array(Auth::user()->role, ['admin', 'staff'], true)
                ? (Route::has('admin.index') ? redirect()->route('admin.index') : redirect('/admin'))
                : (Route::has('portal.index') ? redirect()->route('portal.index') : redirect('/portal'));
        }

        if (view()->exists('auth.login')) {
            return view('auth.login');
        }

        return view('pages.login');
    }

    /**
     * Render the registration view.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return Route::has('portal.index') ? redirect()->route('portal.index') : redirect('/portal');
        }

        if (view()->exists('auth.register')) {
            return view('auth.register');
        }

        return view('pages.register');
    }

    /**
     * Handle user login.
     * Supports both 'phone' and 'login' inputs (accepting either mobile number or email).
     */
    public function login(Request $request)
    {
        $throttleKey = 'login-attempt:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "محاولات كثيرة خاطئة. يرجى الانتظار {$seconds} ثانية.")
                         ->withInput($request->except('password'));
        }

        // Determine input field ('login' or 'phone')
        $loginInput = trim($request->input('login', $request->input('phone', '')));
        $password = $request->input('password', '');

        if (empty($loginInput) || empty($password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->with('error', 'يرجى إدخال رقم الموبايل أو البريد الإلكتروني وكلمة المرور.')
                         ->withInput($request->except('password'));
        }

        $user = null;

        // Check if login input is email
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', strtolower($loginInput))->first();
        } else {
            // Otherwise treat as Egyptian phone number
            $normalizedPhone = method_exists($this, 'normalizeEgyptianPhone')
                ? $this->normalizeEgyptianPhone($loginInput)
                : preg_replace('/[^0-9]/', '', $loginInput);

            $user = User::where('phone', $normalizedPhone)->first();
        }

        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->with('error', 'بيانات الدخول غير صحيحة، يرجى التأكد من رقم الموبايل / البريد وكلمة المرور.')
                         ->withInput($request->except('password'));
        }

        if (isset($user->is_active) && !$user->is_active) {
            return back()->with('error', 'تم تعطيل هذا الحساب، يرجى التواصل مع الدعم الفني.');
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, (bool)$request->filled('remember'));
        $request->session()->regenerate();

        if (in_array($user->role, ['admin', 'staff'], true)) {
            return Route::has('admin.index') 
                ? redirect()->intended(route('admin.index'))->with('citadel_toast', 'مرحباً بك مجدداً في مركز القيادة.')
                : redirect()->intended('/admin');
        }

        return Route::has('portal.index')
            ? redirect()->intended(route('portal.index'))->with('citadel_toast', 'أهلاً بك في بوابتك الرقمية.')
            : redirect()->intended('/portal');
    }

    /**
     * Register a new client account, provision wallet, and sync with CRM leads.
     */
    public function register(Request $request)
    {
        $throttleKey = 'register-attempt:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "محاولات تسجيل متكررة. يرجى الانتظار {$seconds} ثانية.")
                         ->withInput($request->except('password', 'password_confirmation'));
        }

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:150'],
            'phone'                 => ['required', 'string'],
            'company_name'          => ['nullable', 'string', 'max:150'],
            'email'                 => ['nullable', 'email', 'max:190', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required'         => 'الاسم الكامل مطلوب.',
            'phone.required'        => 'رقم الموبايل مطلوب.',
            'password.required'     => 'كلمة المرور مطلوبة.',
            'password.min'          => 'يجب ألا تقل كلمة المرور عن 6 خانات.',
            'password.confirmed'    => 'كلمة المرور غير متطابقة مع تأكيد كلمة المرور.',
            'email.unique'          => 'هذا البريد الإلكتروني مسجل بالفعل مسبقاً.',
        ]);

        $normalizedPhone = method_exists($this, 'normalizeEgyptianPhone') 
            ? $this->normalizeEgyptianPhone($validated['phone'])
            : preg_replace('/[^0-9]/', '', $validated['phone']);

        if (method_exists($this, 'isValidEgyptianPhone') && !$this->isValidEgyptianPhone($normalizedPhone)) {
            return back()->with('error', 'رقم الموبايل غير صالح. يجب أن يكون رقماً مصرياً صالحاً (010, 011, 012, 015).')
                         ->withInput($request->except('password', 'password_confirmation'));
        }

        if (User::where('phone', $normalizedPhone)->exists()) {
            return back()->with('error', 'هذا الهاتف مسجل بالفعل مسبقاً، يمكنك تسجيل الدخول مباشرة.')
                         ->withInput($request->except('password', 'password_confirmation'));
        }

        RateLimiter::hit($throttleKey, 120);

        try {
            $user = DB::transaction(function () use ($validated, $normalizedPhone) {
                $createdUser = User::create([
                    'name'         => strip_tags(trim($validated['name'])),
                    'phone'        => $normalizedPhone,
                    'company_name' => !empty($validated['company_name']) ? strip_tags(trim($validated['company_name'])) : null,
                    'email'        => !empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
                    'password'     => Hash::make($validated['password']),
                    'role'         => 'client',
                    'is_active'    => true,
                ]);

                Wallet::firstOrCreate(
                    ['user_id' => $createdUser->id],
                    [
                        'cash_balance'  => 0.00,
                        'bonus_balance' => 0.00,
                        'currency'      => 'EGP',
                        'is_locked'     => false,
                    ]
                );

                Lead::where('phone', $normalizedPhone)->update([
                    'status'  => 'converted',
                    'user_id' => $createdUser->id,
                ]);

                return $createdUser;
            });

            RateLimiter::clear($throttleKey);
            Auth::login($user);
            $request->session()->regenerate();

            return Route::has('portal.index')
                ? redirect()->route('portal.index')->with('citadel_toast', 'تم إنشاء حسابك وتفعيل محفظتك بنجاح 🚀')
                : redirect('/portal');

        } catch (\Throwable $e) {
            Log::error('Registration Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'حدث خطأ غير متوقع أثناء إنشاء الحساب، يرجى المحاولة لاحقاً.')
                         ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Send OTP for login/recovery with rate limiting.
     */
    public function sendOtp(Request $request)
    {
        $phoneInput = $request->input('phone', $request->input('login', ''));
        if (empty($phoneInput)) {
            return response()->json(['success' => false, 'message' => 'رقم الهاتف مطلوب.'], 422);
        }
        
        $normalizedPhone = method_exists($this, 'normalizeEgyptianPhone') 
            ? $this->normalizeEgyptianPhone($phoneInput)
            : preg_replace('/[^0-9]/', '', $phoneInput);

        $throttleKey = 'send-otp:' . $normalizedPhone;
        if (RateLimiter::tooManyAttempts($throttleKey, 2)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json(['success' => false, 'message' => "يرجى الانتظار {$seconds} ثانية لطلب رمز جديد."], 429);
        }

        RateLimiter::hit($throttleKey, 60);

        $otp = app()->isLocal() ? '1234' : (string)random_int(1000, 9999);
        cache()->put('auth_otp_' . $normalizedPhone, $otp, now()->addMinutes(5));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز التحقق بنجاح.',
            'debug'   => app()->isLocal() ? $otp : null,
        ]);
    }

    /**
     * Verify OTP and authenticate user.
     */
    public function verifyOtp(Request $request)
    {
        $phoneInput = $request->input('phone', $request->input('login', ''));
        $request->validate([
            'otp' => ['required', 'string', 'size:4'],
        ]);

        $normalizedPhone = method_exists($this, 'normalizeEgyptianPhone') 
            ? $this->normalizeEgyptianPhone($phoneInput)
            : preg_replace('/[^0-9]/', '', $phoneInput);

        $cachedOtp = cache()->get('auth_otp_' . $normalizedPhone);

        if (!$cachedOtp || $cachedOtp !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'رمز التحقق غير صحيح أو انتهت صلاحيته.'], 422);
        }

        cache()->forget('auth_otp_' . $normalizedPhone);

        $user = User::firstOrCreate(
            ['phone' => $normalizedPhone],
            [
                'name'      => 'عميل سولي ' . substr($normalizedPhone, -4),
                'password'  => Hash::make(Str::random(16)),
                'role'      => 'client',
                'is_active' => true,
            ]
        );

        if (!$user->wallet) {
            Wallet::create([
                'user_id'       => $user->id,
                'cash_balance'  => 0.00,
                'bonus_balance' => 0.00,
                'currency'      => 'EGP',
                'is_locked'     => false,
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'success'  => true,
            'message'  => 'تم تسجيل الدخول بنجاح.',
            'redirect' => Route::has('portal.index') ? route('portal.index') : url('/portal'),
        ]);
    }

    /**
     * Quick dev login strictly restricted to local development environments.
     * Supports both ($role) string from routes or ($request) object.
     */
    public function quickLogin($role = 'client', ?Request $request = null)
    {
        if (!app()->environment('local') || config('app.debug') !== true) {
            abort(403, 'الدخول التجريبي السريع معطل تماماً في بيئة التشغيل لسلامة النظام.');
        }

        if ($role instanceof Request) {
            $request = $role;
            $role = $request->route('role') ?? $request->input('role') ?? 'client';
        }

        $request = $request ?: request();

        $validRoles = ['admin', 'staff', 'client'];
        $targetRole = in_array($role, $validRoles, true) ? $role : 'client';

        $phoneMap = [
            'admin'  => '01000000001',
            'staff'  => '01000000002',
            'client' => '01000000003',
        ];

        $targetPhone = $phoneMap[$targetRole] ?? '01000000003';

        // Find or provision demo account safely for testing
        $user = User::where('role', $targetRole)->first();

        if (!$user) {
            $user = User::create([
                'name'      => 'تجريبي ' . ucfirst($targetRole),
                'phone'     => $targetPhone,
                'email'     => "{$targetRole}@soulyeg.online",
                'password'  => Hash::make('password123'),
                'role'      => $targetRole,
                'is_active' => true,
            ]);
        } else {
            // Ensure phone and password match default testing credentials
            $user->update([
                'phone'     => $targetPhone,
                'password'  => Hash::make('password123'),
                'is_active' => true,
            ]);
        }

        Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'cash_balance'  => 5000.00,
                'bonus_balance' => 1000.00,
                'currency'      => 'EGP',
                'is_locked'     => false,
            ]
        );

        Auth::login($user);
        $request->session()->regenerate();

        $redirectUrl = in_array($user->role, ['admin', 'staff'], true)
            ? (Route::has('admin.index') ? route('admin.index') : url('/admin'))
            : (Route::has('portal.index') ? route('portal.index') : url('/portal'));

        return redirect($redirectUrl)->with('citadel_toast', [
            'type'    => 'success',
            'message' => "تم الدخول السريع كـ {$user->name} (بيئة تجريبية) ⚡",
        ]);
    }

    /**
     * Log out current user and invalidate session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('citadel_toast', 'تم تسجيل الخروج بنجاح.');
    }
}
