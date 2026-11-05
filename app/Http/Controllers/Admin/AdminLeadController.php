<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use App\Services\SoulyNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminLeadController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $leads = Lead::with('service')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        $counts = [
            'new'       => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'qualified' => Lead::where('status', 'qualified')->count(),
            'converted' => Lead::where('status', 'converted')->count(),
        ];

        return view('admin.leads.index', compact('leads', 'counts'));
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,contacted,qualified,converted,lost'],
            'notes'  => ['nullable', 'string', 'max:500'],
        ]);

        $lead->update([
            'status'          => $validated['status'],
            'notes'           => $validated['notes'] ?? $lead->notes,
            'last_contact_at' => now(),
        ]);

        return back()->with('citadel_toast', 'تم تحديث حالة العميل المحتمل بنجاح.');
    }

    public function convertToClient(Request $request, Lead $lead, SoulyNotificationService $notifier)
    {
        if ($lead->status === 'converted' && $lead->user_id) {
            return back()->with('error', 'تم تحويل هذا العميل مسبقاً.');
        }

        $existingUser = User::where('phone', $lead->phone)->first();

        if ($existingUser) {
            $user = $existingUser;
        } else {
            $user = User::create([
                'name'      => $lead->name,
                'phone'     => $lead->phone,
                'email'     => $lead->email,
                'password'  => Hash::make(Str::random(16)),
                'role'      => 'client',
                'is_active' => true,
            ]);
        }

        $lead->update([
            'status'  => 'converted',
            'user_id' => $user->id,
        ]);

        $notifier->notify(
            $user,
            'مرحباً بك في منصة سولي',
            'تم تسجيل حسابك كعميل رسمي لدى سولي بنجاح. يمكنك الآن الدخول ومتابعة خدماتك.',
            route('portal.index')
        );

        return back()->with('citadel_toast', 'تم تحويل العميل المحتمل إلى حساب عميل رسمي بنجاح.');
    }
}
