<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Traits\NormalizesEgyptianPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExpressBookingController extends Controller
{
    use NormalizesEgyptianPhone;

    /**
     * Services strictly eligible for physical on-site field visits.
     */
    protected array $fieldEligibleSectors = [
        'engineering_maintenance',
        'cyber_shield',
    ];

    /**
     * Render the express booking view with active services and packages.
     */
    public function show(Request $request)
    {
        $services = Service::where('is_active', true)
            ->with(['packages' => fn($q) => $q->where('is_active', true)])
            ->get();

        $selectedService = null;
        if ($request->filled('service')) {
            $selectedService = $services->firstWhere('slug', $request->query('service'));
        }

        return view('pages.express-booking', compact('services', 'selectedService'));
    }

    /**
     * Process and store express booking with strict security and transaction isolation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id'     => ['required', 'exists:services,id'],
            'package_id'     => ['nullable', 'exists:packages,id'],
            'name'           => ['required', 'string', 'max:150'],
            'phone'          => ['required', 'string'],
            'email'          => ['nullable', 'email', 'max:190'],
            'company'        => ['nullable', 'string', 'max:150'],
            'address'        => ['nullable', 'string', 'max:255'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'preferred_time' => ['nullable', 'string', 'max:50'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cash,wallet,card,bank_transfer'],
        ]);

        $normalizedPhone = $this->normalizeEgyptianPhone($validated['phone']);

        if (!$this->isValidEgyptianPhone($normalizedPhone)) {
            return back()->with('error', 'رقم الهاتف غير صالح، يرجى كتابة رقم مصري صحيح.')
                         ->withInput();
        }

        $service = Service::findOrFail($validated['service_id']);
        $package = !empty($validated['package_id']) ? Package::findOrFail($validated['package_id']) : null;

        $isFieldVisit = in_array($service->sector, $this->fieldEligibleSectors, true);
        if (!$isFieldVisit && ($request->filled('preferred_date') || $request->filled('preferred_time'))) {
            return back()->with('error', 'المواعيد والزيارات الميدانية مقتصرة فقط على قطاعي الصيانة الهندسية والأمن السيبراني.')
                         ->withInput();
        }

        $calculatedTotal = $package ? (float)$package->price : (float)($service->base_price ?? 0.00);

        try {
            $booking = DB::transaction(function () use ($validated, $normalizedPhone, $service, $package, $calculatedTotal, $isFieldVisit) {
                $user = Auth::user();

                if (!$user) {
                    $existingUser = User::where('phone', $normalizedPhone)->first();

                    if ($existingUser) {
                        if ($validated['payment_method'] === 'wallet') {
                            throw new \DomainException('هذا الهاتف مرتبط بحساب مسجل، يرجى تسجيل الدخول أولاً للدفع من رصيد المحفظة.');
                        }
                        $userId = $existingUser->id;
                    } else {
                        $newUser = User::create([
                            'name'      => strip_tags(trim($validated['name'])),
                            'phone'     => $normalizedPhone,
                            'email'     => !empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
                            'password'  => Hash::make(Str::random(16)),
                            'role'      => 'client',
                            'is_active' => true,
                        ]);

                        Wallet::create([
                            'user_id'       => $newUser->id,
                            'cash_balance'  => 0.00,
                            'bonus_balance' => 0.00,
                            'currency'      => 'EGP',
                            'is_locked'     => false,
                        ]);

                        $userId = $newUser->id;
                    }
                } else {
                    $userId = $user->id;
                }

                $bookingNumber = 'BK-' . date('ymd') . '-' . strtoupper(Str::random(5));
                $paymentStatus = 'unpaid';

                if ($validated['payment_method'] === 'wallet') {
                    $wallet = Wallet::where('user_id', $userId)
                        ->lockForUpdate()
                        ->first();

                    if (!$wallet || $wallet->is_locked || (float)$wallet->cash_balance < $calculatedTotal) {
                        throw new \DomainException('رصيدك النقدي في المحفظة غير كافٍ لإتمام حجز هذه الخدمة.');
                    }

                    $wallet->decrement('cash_balance', $calculatedTotal);

                    WalletTransaction::create([
                        'wallet_id'   => $wallet->id,
                        'user_id'     => $userId,
                        'type'        => 'payment',
                        'amount'      => $calculatedTotal,
                        'currency'    => 'EGP',
                        'status'      => 'completed',
                        'reference'   => $bookingNumber,
                        'description' => "سداد قيمة حجز الخدمة ({$service->name_ar})",
                    ]);

                    $paymentStatus = 'paid';
                }

                $newBooking = Booking::create([
                    'booking_number' => $bookingNumber,
                    'user_id'        => $userId,
                    'service_id'     => $service->id,
                    'package_id'     => $package?->id,
                    'name'           => strip_tags(trim($validated['name'])),
                    'phone'          => $normalizedPhone,
                    'email'          => !empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
                    'company'        => !empty($validated['company']) ? strip_tags(trim($validated['company'])) : null,
                    'address'        => !empty($validated['address']) ? strip_tags(trim($validated['address'])) : null,
                    'preferred_date' => $isFieldVisit ? ($validated['preferred_date'] ?? null) : null,
                    'preferred_time' => $isFieldVisit ? ($validated['preferred_time'] ?? null) : null,
                    'is_field_visit' => $isFieldVisit,
                    'status'         => 'new',
                    'payment_status' => $paymentStatus,
                    'payment_method' => $validated['payment_method'],
                    'total_amount'   => $calculatedTotal,
                    'notes'          => !empty($validated['notes']) ? strip_tags(trim($validated['notes'])) : null,
                ]);

                Lead::updateOrCreate(
                    ['phone' => $normalizedPhone],
                    [
                        'name'            => strip_tags(trim($validated['name'])),
                        'email'           => !empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
                        'company'         => !empty($validated['company']) ? strip_tags(trim($validated['company'])) : null,
                        'service_id'      => $service->id,
                        'status'          => 'contacted',
                        'user_id'         => $userId,
                        'source'          => 'express_booking',
                        'last_contact_at' => now(),
                    ]
                );

                return $newBooking;
            });

            return redirect()->route('booking.success', ['booking' => $booking->booking_number])
                             ->with('citadel_toast', 'تم تسجيل طلب حجزك بنجاح، رقم الحجز: ' . $booking->booking_number);

        } catch (\DomainException $de) {
            return back()->with('error', $de->getMessage())->withInput();
        } catch (\Throwable $e) {
            Log::error('Express Booking Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'حدث خطأ أثناء معالجة الحجز، يرجى إعادة المحاولة.')->withInput();
        }
    }

    /**
     * Show booking success page.
     */
    public function success(string $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['service', 'package'])
            ->firstOrFail();

        return view('pages.booking-success', compact('booking'));
    }
}
