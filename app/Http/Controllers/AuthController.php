<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Display the login page.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle standard user authentication.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            // Ensure wallet exists
            $user->walletOrCreate();

            return redirect()->intended(route('portal.index'))
                ->with('citadel_toast', [
                    'type' => 'success',
                    'message' => "مرحباً بعودتك إلى قلعة سولي، {$user->name}!"
                ]);
        }

        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ])->onlyInput('email');
    }

    /**
     * Instant 1-Click Demo Login for testing and evaluations.
     */
    public function quickLogin(string $role): RedirectResponse
    {
        $roleMap = [
            'client' => 'client@soulyeg.online',
            'staff' => 'staff@soulyeg.online',
            'admin' => 'admin@soulyeg.online',
        ];

        if (!array_key_exists($role, $roleMap)) {
            abort(404, 'دور غير صالح');
        }

        $email = $roleMap[$role];
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Fallback: create the demo user if missing
            $user = User::create([
                'name' => ucfirst($role) . ' Demo',
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => $role,
                'is_active' => true,
            ]);
        }

        $user->walletOrCreate();
        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->route('portal.index')->with('citadel_toast', [
            'type' => 'success',
            'message' => "تم تسجيل الدخول التجريبي الفوري بصلاحية: {$user->role} ({$user->name})"
        ]);
    }

    /**
     * Display the client registration page.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle client registration with automatic wallet initialization.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()->min(8)],
        ], [
            'name.required' => 'يرجى إدخال الاسم بالكامل.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.unique' => 'البريد الإلكتروني مسجل لدينا بالفعل.',
            'phone.required' => 'يرجى إدخال رقم الهاتف / الواتساب.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company_name' => $validated['company_name'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'client',
            'is_active' => true,
        ]);

        // Initialize user wallet with 0.00 EGP
        $user->walletOrCreate();

        // Capture a CRM Lead registration entry
        Lead::create([
            'name' => $user->name,
            'phone' => $user->phone ?? '01000000000',
            'email' => $user->email,
            'company_name' => $user->company_name,
            'source' => 'website_booking',
            'status' => 'new',
            'notes' => 'عميل مسجل حديثاً في البوابة السيادية لقلعة سولي.',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('portal.index')->with('citadel_toast', [
            'type' => 'success',
            'message' => "أهلاً بك في قلعة سولي الرقمية! تم تفعيل حسابك ومحفظتك الرقمية بنجاح."
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('citadel_toast', [
            'type' => 'info',
            'message' => 'تم تسجيل الخروج بنجاح. نتمنى لك يوماً سعيداً!'
        ]);
    }
}
