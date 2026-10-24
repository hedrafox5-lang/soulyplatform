<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminLeadController extends Controller
{
    /**
     * List all CRM Leads with pipeline status tabs.
     */
    public function index(Request $request): View
    {
        $query = Lead::with(['interestedService', 'interestedPackage', 'assignedStaff', 'activities']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('company_name', 'like', "%{$s}%");
            });
        }

        $leads = $query->latest()->paginate(15);
        $services = Service::where('is_active', true)->get();
        $staffMembers = User::whereIn('role', ['admin', 'staff'])->where('is_active', true)->get();

        $pipelineCounts = [
            'all' => Lead::count(),
            'new' => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'qualified' => Lead::where('status', 'qualified')->count(),
            'proposal_sent' => Lead::where('status', 'proposal_sent')->count(),
            'won' => Lead::where('status', 'won')->count(),
            'lost' => Lead::where('status', 'lost')->count(),
        ];

        return view('admin.leads.index', compact('leads', 'services', 'staffMembers', 'pipelineCounts'));
    }

    /**
     * Create a new Lead manually.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'business_activity' => ['nullable', 'string', 'max:255'],
            'interested_service_id' => ['nullable', 'exists:services,id'],
            'status' => ['required', 'string', 'in:new,contacted,qualified,proposal_sent,won,lost'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['source'] = 'manual';
        $lead = Lead::create($validated);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => 'note',
            'description' => 'تم إنشاء ملف العميل المحتمل يدوياً بواسطة ' . auth()->user()->name,
        ]);

        return redirect()->route('admin.leads.show', $lead)->with('citadel_toast', [
            'type' => 'success',
            'message' => "تم تسجيل العميل ({$lead->name}) في الـ CRM بنجاح!"
        ]);
    }

    /**
     * Display the full Lead Dossier and chronological activity timeline.
     */
    public function show(Lead $lead): View
    {
        $lead->load(['interestedService', 'interestedPackage', 'assignedStaff', 'activities.user']);
        $services = Service::where('is_active', true)->get();
        $staffMembers = User::whereIn('role', ['admin', 'staff'])->where('is_active', true)->get();

        return view('admin.leads.show', compact('lead', 'services', 'staffMembers'));
    }

    /**
     * Update Lead Pipeline status and log activity.
     */
    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,qualified,proposal_sent,won,lost'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldStatus = $lead->status;
        $lead->update($validated);

        if ($oldStatus !== $validated['status']) {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'type' => 'status_change',
                'description' => "تغيرت حالة العميل من [{$oldStatus}] إلى [{$validated['status']}] بواسطة " . auth()->user()->name,
            ]);
        }

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => 'تم تحديث حالة العميل وتوثيق العملية في سجل الأنشطة.'
        ]);
    }

    /**
     * Add a chronological interaction note (Call, WhatsApp, Meeting).
     */
    public function storeActivity(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:call,whatsapp,note,meeting,status_change'],
            'description' => ['required', 'string', 'max:3000'],
        ], [
            'description.required' => 'يرجى كتابة تفاصيل المحادثة أو الملاحظة.',
        ]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'description' => $validated['description'],
        ]);

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => 'تم إضافة الملاحظة التفاعلية إلى ملف العميل.'
        ]);
    }
}
