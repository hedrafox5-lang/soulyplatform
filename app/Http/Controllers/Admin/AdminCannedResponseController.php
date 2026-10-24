<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CannedResponse;
use App\Models\CannedResponseCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminCannedResponseController extends Controller
{
    /**
     * Display the Smart Canned Responses Vault.
     */
    public function index(Request $request): View
    {
        $categories = CannedResponseCategory::with(['responses' => function ($q) {
            $q->where('is_active', true)->orderBy('usage_count', 'desc');
        }])->orderBy('display_order')->get();

        $allResponses = CannedResponse::with('category')
            ->where('is_active', true)
            ->orderBy('usage_count', 'desc')
            ->get();

        return view('admin.canned-responses.index', compact('categories', 'allResponses'));
    }

    /**
     * Increment usage counter on copy via AJAX.
     */
    public function incrementUsage(CannedResponse $response): JsonResponse
    {
        $response->increment('usage_count');

        return response()->json([
            'status' => 'success',
            'id' => $response->id,
            'usage_count' => $response->usage_count,
        ]);
    }

    /**
     * Store new Canned Response.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:canned_response_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'shortcut_code' => ['required', 'string', 'max:50', 'unique:canned_responses,shortcut_code'],
            'content' => ['required', 'string', 'max:10000'],
            'tags' => ['nullable', 'string'],
        ]);

        $tagsArray = [];
        if (!empty($validated['tags'])) {
            $tagsArray = array_map('trim', explode(',', $validated['tags']));
        }

        CannedResponse::create([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'shortcut_code' => $validated['shortcut_code'],
            'content' => $validated['content'],
            'tags_json' => $tagsArray,
            'usage_count' => 0,
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => 'تم حفظ الرد الجاهز الجديد في الخزينة بنجاح!'
        ]);
    }

    /**
     * Update existing Canned Response.
     */
    public function update(Request $request, CannedResponse $response): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:canned_response_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'shortcut_code' => ['required', 'string', 'max:50', 'unique:canned_responses,shortcut_code,' . $response->id],
            'content' => ['required', 'string', 'max:10000'],
            'tags' => ['nullable', 'string'],
        ]);

        $tagsArray = [];
        if (!empty($validated['tags'])) {
            $tagsArray = array_map('trim', explode(',', $validated['tags']));
        }

        $response->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'shortcut_code' => $validated['shortcut_code'],
            'content' => $validated['content'],
            'tags_json' => $tagsArray,
        ]);

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => 'تم تحديث الرد الجاهز بنجاح.'
        ]);
    }

    /**
     * Delete Canned Response.
     */
    public function destroy(CannedResponse $response): RedirectResponse
    {
        $response->delete();

        return back()->with('citadel_toast', [
            'type' => 'info',
            'message' => 'تم حذف الرد الجاهز من الخزينة.'
        ]);
    }
}
