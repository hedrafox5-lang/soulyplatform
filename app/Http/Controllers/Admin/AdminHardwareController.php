<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HardwareOrder;
use App\Models\HardwareOrderItem;
use App\Models\HardwareProduct;
use App\Services\SoulyNotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminHardwareController extends Controller
{
    /**
     * Display Admin Hardware & Orders Management Dashboard.
     */
    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'products');

        // 1. Products Query
        $productsQuery = HardwareProduct::query();
        if ($request->filled('search')) {
            $search = $request->query('search');
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category') && $request->query('category') !== 'all') {
            $productsQuery->where('category', $request->query('category'));
        }
        $products = $productsQuery->orderByDesc('id')->paginate(15, ['*'], 'products_page')->withQueryString();

        // 2. Orders Query
        $ordersQuery = HardwareOrder::with(['user', 'items'])->latest();
        if ($request->filled('status') && $request->query('status') !== 'all') {
            $ordersQuery->where('status', $request->query('status'));
        }
        $orders = $ordersQuery->paginate(15, ['*'], 'orders_page')->withQueryString();

        // Stats
        $counts = [
            'products_total'     => HardwareProduct::count(),
            'products_active'    => HardwareProduct::where('is_active', true)->count(),
            'pos_count'          => HardwareProduct::where('category', 'pos_equipment')->count(),
            'orders_total'       => HardwareOrder::count(),
            'orders_pending'     => HardwareOrder::where('status', 'pending_review')->count(),
            'orders_preparing'   => HardwareOrder::where('status', 'confirmed_preparing')->count(),
            'orders_in_delivery' => HardwareOrder::where('status', 'out_for_delivery')->count(),
            'orders_delivered'   => HardwareOrder::where('status', 'delivered_warranty_active')->count(),
        ];

        return view('admin.hardware.index', compact('activeTab', 'products', 'orders', 'counts'));
    }

    /**
     * Store a Newly Created Hardware Product.
     */
    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar'                  => ['required', 'string', 'max:180'],
            'sku'                      => ['required', 'string', 'max:60', 'unique:hardware_products,sku'],
            'category'                 => ['required', 'in:pos_equipment,storage_ram,networking_security,workstations'],
            'brand'                    => ['required', 'string', 'max:100'],
            'price'                    => ['required', 'numeric', 'min:0'],
            'old_price'                => ['nullable', 'numeric', 'min:0'],
            'stock_qty'                => ['required', 'integer', 'min:0'],
            'warranty_months'          => ['required', 'integer', 'min:0'],
            'description_ar'           => ['nullable', 'string', 'max:2000'],
            'specs_raw'                => ['nullable', 'string', 'max:2000'],
            'compatible_with_branderp' => ['nullable'],
        ]);

        $slug = Str::slug($validated['sku']) . '-' . rand(100, 999);

        // Parse key-value lines for specs_json
        $specsJson = [];
        if (!empty($validated['specs_raw'])) {
            $lines = explode("\n", str_replace("\r", "", $validated['specs_raw']));
            foreach ($lines as $line) {
                if (str_contains($line, ':')) {
                    [$k, $v] = explode(':', $line, 2);
                    $specsJson[trim($k)] = trim($v);
                } elseif (!empty(trim($line))) {
                    $specsJson['المواصفات'] = trim($line);
                }
            }
        }

        HardwareProduct::create([
            'slug'                     => $slug,
            'sku'                      => strtoupper($validated['sku']),
            'name_ar'                  => $validated['name_ar'],
            'category'                 => $validated['category'],
            'brand'                    => $validated['brand'],
            'price'                    => $validated['price'],
            'old_price'                => $validated['old_price'] ?? null,
            'stock_qty'                => $validated['stock_qty'],
            'warranty_months'          => $validated['warranty_months'],
            'description_ar'           => $validated['description_ar'] ?? null,
            'specs_json'               => $specsJson,
            'compatible_with_branderp' => $request->has('compatible_with_branderp'),
            'is_active'                => true,
        ]);

        return redirect()->route('admin.hardware.index', ['tab' => 'products'])->with('citadel_toast', [
            'type'    => 'success',
            'message' => 'تم إضافة منتج الهاردوير الجديد بنجاح إلى المتجر.',
        ]);
    }

    /**
     * Update an Existing Hardware Product.
     */
    public function updateProduct(HardwareProduct $product, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar'                  => ['required', 'string', 'max:180'],
            'sku'                      => ['required', 'string', 'max:60', 'unique:hardware_products,sku,' . $product->id],
            'category'                 => ['required', 'in:pos_equipment,storage_ram,networking_security,workstations'],
            'brand'                    => ['required', 'string', 'max:100'],
            'price'                    => ['required', 'numeric', 'min:0'],
            'old_price'                => ['nullable', 'numeric', 'min:0'],
            'stock_qty'                => ['required', 'integer', 'min:0'],
            'warranty_months'          => ['required', 'integer', 'min:0'],
            'description_ar'           => ['nullable', 'string', 'max:2000'],
            'specs_raw'                => ['nullable', 'string', 'max:2000'],
            'compatible_with_branderp' => ['nullable'],
        ]);

        $specsJson = $product->specs_json ?? [];
        if (!empty($validated['specs_raw'])) {
            $specsJson = [];
            $lines = explode("\n", str_replace("\r", "", $validated['specs_raw']));
            foreach ($lines as $line) {
                if (str_contains($line, ':')) {
                    [$k, $v] = explode(':', $line, 2);
                    $specsJson[trim($k)] = trim($v);
                }
            }
        }

        $product->update([
            'sku'                      => strtoupper($validated['sku']),
            'name_ar'                  => $validated['name_ar'],
            'category'                 => $validated['category'],
            'brand'                    => $validated['brand'],
            'price'                    => $validated['price'],
            'old_price'                => $validated['old_price'] ?? null,
            'stock_qty'                => $validated['stock_qty'],
            'warranty_months'          => $validated['warranty_months'],
            'description_ar'           => $validated['description_ar'] ?? null,
            'specs_json'               => $specsJson,
            'compatible_with_branderp' => $request->has('compatible_with_branderp'),
        ]);

        return redirect()->route('admin.hardware.index', ['tab' => 'products'])->with('citadel_toast', [
            'type'    => 'success',
            'message' => 'تم تحديث بيانات المنتج ' . $product->sku . ' بنجاح.',
        ]);
    }

    /**
     * Toggle Product Active State.
     */
    public function toggleProduct(HardwareProduct $product, Request $request): JsonResponse|RedirectResponse
    {
        $product->is_active = !$product->is_active;
        $product->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'is_active' => $product->is_active,
                'message'   => 'تم تغيير حالة المنتج بنجاح.',
            ]);
        }

        return back()->with('citadel_toast', [
            'type'    => 'success',
            'message' => 'تم تحديث حالة المنتج بنجاح.',
        ]);
    }

    /**
     * Delete Hardware Product.
     */
    public function deleteProduct(HardwareProduct $product): RedirectResponse
    {
        $sku = $product->sku;
        $product->delete();

        return redirect()->route('admin.hardware.index', ['tab' => 'products'])->with('citadel_toast', [
            'type'    => 'success',
            'message' => 'تم حذف المنتج ' . $sku . ' من قاعدة البيانات.',
        ]);
    }

    /**
     * Update Hardware Order Status & Serial Numbers.
     */
    public function updateOrderStatus(HardwareOrder $order, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status'               => ['required', 'in:pending_review,confirmed_preparing,out_for_delivery,delivered_warranty_active,cancelled'],
            'serial_numbers_notes' => ['nullable', 'string', 'max:2000'],
            'admin_notes'          => ['nullable', 'string', 'max:1000'],
            'payment_status'       => ['nullable', 'in:pending,paid,refunded'],
        ]);

        $oldStatus = $order->status;
        $order->status = $validated['status'];
        if (!empty($validated['serial_numbers_notes'])) {
            $order->serial_numbers_notes = $validated['serial_numbers_notes'];
        }
        if (!empty($validated['admin_notes'])) {
            $order->admin_notes = $validated['admin_notes'];
        }
        if (!empty($validated['payment_status'])) {
            $order->payment_status = $validated['payment_status'];
        }
        $order->save();

        // Dispatch notification to client if status changed
        if ($oldStatus !== $order->status && $order->user_id) {
            SoulyNotificationService::send(
                $order->user_id,
                'system',
                'تحديث مرحلة شحن طلب الهاردوير #' . $order->order_number,
                "تم تحديث حالة طلبك رقم #{$order->order_number} إلى: " . $order->status_label_ar . ". يمكنك متابعة تفاصيل السيريال والضمان عبر بوابتك.",
                route('portal.hardware'),
                'truck'
            );
        }

        return redirect()->route('admin.hardware.index', ['tab' => 'orders'])->with('citadel_toast', [
            'type'    => 'success',
            'message' => 'تم تحديث حالة الطلب #' . $order->order_number . ' إلى (' . $order->status_label_ar . ') بنجاح.',
        ]);
    }

    /**
     * Mobile Inline AJAX Update for Price and Stock.
     */
    public function inlineUpdate(Request $request, HardwareProduct $product): JsonResponse
    {
        $validated = $request->validate([
            'price' => 'nullable|numeric|min:0',
            'stock_qty' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $product->update(array_filter($validated, fn($val) => $val !== null));

        return response()->json([
            'success' => true,
            'message' => "تم تحديث ({$product->name_ar}) بنجاح! السعر: {$product->price} ج.م | المخزون: {$product->stock_qty}",
            'product' => $product,
        ]);
    }
}
