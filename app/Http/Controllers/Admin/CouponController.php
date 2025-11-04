<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::orderByDesc('created_at')->get();

        return view('admin.pages.coupons', compact('coupons'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCoupon($request);

        if ($validated['expires_at'] && Carbon::parse($validated['expires_at'])->isPast()) {
            return back()->withErrors(['expires_at' => 'Thời hạn phải lớn hơn thời gian hiện tại.'])->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['code'] = strtoupper($validated['code']);

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Đã tạo mã giảm giá mới thành công.');
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $this->validateCoupon($request, $coupon->id);

        if ($validated['expires_at'] && Carbon::parse($validated['expires_at'])->isPast()) {
            return back()->withErrors(['expires_at' => 'Thời hạn phải lớn hơn thời gian hiện tại.'])->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['code'] = strtoupper($validated['code']);

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Đã cập nhật mã giảm giá.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Đã xóa mã giảm giá.');
    }

    private function validateCoupon(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:coupons,code';

        if ($ignoreId) {
            $uniqueRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'code' => ['required', 'string', 'max:50', $uniqueRule],
            'discount_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'expires_at' => ['nullable', 'date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}