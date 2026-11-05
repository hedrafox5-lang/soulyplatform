<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Service;
use App\Models\User;
use App\Services\SoulyNotificationService;
use App\Traits\NormalizesEgyptianPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminLeadController extends Controller
{
    use NormalizesEgyptianPhone;

    public function index(Request $request)
    {
        $status = $request->query('status');
        $sector = $request->query('sector');

        $leads = Lead::with('service')
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($sector, fn($q) => $q->where('sector', $sector))
            ->latest()
            ->paginate(15);

        return view('admin.leads.index', compact('leads'));
    }

    public function show(Lead $lead)
    {
        $lead->load(['service', 'activities.user']);
        return view('admin.leads.show', compact('lead'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'phone'       => ['required', 'string'],
            'email'       => ['nullable', 'email', 'max:150'],
            'company'     => ['nullable', 'string', 'max:150'],
            'service_id'  => ['nullable', 'exists:services,id'],
            'sector'      => ['nullable', 'string', 'max:50'],
            'source'      => ['nullable', 'string', 'max:50'],
            'notes'       => ['nullable', 'string'],
        ]);

        $validated['phone'] = $this->normalizeEgyptianPhone($validated['phone']) ?? $validated['phone'];
        $validated['status'] = 'new';

        $lead = Lead::create($validated);

        LeadActivity::create([
            'lead_id'     => $lead->id,
            'user_id'     => auth()->id(),
            'type'        => 'note',
            'description' => 'تم إنشاء العميل المحتمل يدوياً من لوحة التحكم.',
        ]);

        return back()->with('citadel_toast', 'تم تسجيل العميل المحتمل بنجاح.');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,contacted,qualified,converted,lost'],
        ]);

        $lead->update(['status' => $validated['status']]);

        LeadActivity::create([
            'lead_id'     => $lead->id,
            'user_id'     => auth()->id(),
            'type'        => 'status_change',
            'description' => "تم تغيير حالة العميل إلى: {$validated['status']}",
        ]);

        return back()->with('citadel_toast', 'تم تحديث حالة العميل المحتمل بنجاح.');
    }

    public function toggleCallConfirmed(Lead $lead)
    {
        $lead->update([
            'call_confirmed' => !$lead->call_confirmed,
        ]);

        return back()->with('citadel_toast', 'تم تحديث حالة الاتصال الهاتفي للعميل المحتمل.');
    }

    public function storeActivity(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'type'        => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        LeadActivity::create([
            'lead_id'     => $lead->id,
            'user_id'     => auth()->id(),
            'type'        => $validated['type'],
            'description' => $validated['description'],
        ]);

        return back()->with('citadel_toast', 'تمت إضافة النشاط لسجل العميل المحتمل.');
    }

    public function convertToUser(Request $request, Lead $lead, SoulyNotificationService $notifier)
    {
        $normalizedPhone = $this->normalizeEgyptianPhone($lead->phone) ?? $lead->phone;

        $user = User::firstOrCreate(
            ['phone' => $normalizedPhone],
            [
                'name'     => $lead->name,
                'email'    => $lead->email ?: ($normalizedPhone . '@soulyeg.online'),
                'company'  => $lead->company,
                'password' => Hash::make(Str::random(12)),
                'role'     => 'client',
            ]
        );

        $lead->update([
            'status'   => 'converted',
            'user_id'  => $user->id,
        ]);

        LeadActivity::create([
            'lead_id'     => $lead->id,
            'user_id'     => auth()->id(),
            'type'        => 'conversion',
            'description' => "تم تحويل العميل المحتمل إلى حساب عميل نشط برقم #{$user->id}",
        ]);

        $notifier->notify(
            $user,
            'مرحباً بك في منصة سولي للتكنولوجيا',
            'تم تفعيل حسابك بنجاح في بوابة عملاء سولي الرقمية.',
            route('portal.index')
        );

        return back()->with('citadel_toast', "تم تحويل العميل المحتمل إلى مستخدم نشط بنجاح (المستخدم #{$user->id}).");
    }

    public function convertToClient(Request $request, Lead $lead, SoulyNotificationService $notifier)
    {
        return $this->convertToUser($request, $lead, $notifier);
    }
}