<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = DiscountCode::latest()->paginate(20);

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedDiscountData($request, [
            'code' => 'required|string|max:32|unique:discount_codes,code',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        DiscountCode::create($data);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'کد تخفیف با موفقیت ایجاد شد.');
    }

    public function edit(DiscountCode $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, DiscountCode $discount)
    {
        $data = $this->validatedDiscountData($request, [
            'code' => 'required|string|max:32|unique:discount_codes,code,'.$discount->id,
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $discount->update($data);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'کد تخفیف با موفقیت ویرایش شد.');
    }

    public function destroy(DiscountCode $discount)
    {
        $discount->delete();

        return back()->with('success', 'کد تخفیف حذف شد.');
    }

    public function toggle(DiscountCode $discount)
    {
        $discount->update(['is_active' => ! $discount->is_active]);

        return back()->with('success', 'وضعیت کد تخفیف تغییر کرد.');
    }

    private function validatedDiscountData(Request $request, array $extraRules = []): array
    {
        $data = $request->validate(array_merge([
            'type'      => 'required|in:percent,fixed',
            'value'     => 'required|numeric|min:0',
            'min_order' => 'nullable|integer|min:0',
            'max_uses'  => 'nullable|integer|min:1',
            'starts_at' => ['nullable', 'regex:/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/'],
            'expires_at'=> ['nullable', 'regex:/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/'],
        ], $extraRules), [
            'starts_at.regex'  => 'تاریخ شروع معتبر نیست.',
            'expires_at.regex' => 'تاریخ انقضا معتبر نیست.',
        ]);

        $startsAt = JalaliDate::toGregorian($data['starts_at'] ?? null);
        $expiresAt = JalaliDate::toGregorian($data['expires_at'] ?? null);

        if (($data['starts_at'] ?? null) && ! $startsAt) {
            throw ValidationException::withMessages([
                'starts_at' => 'تاریخ شروع معتبر نیست.',
            ]);
        }

        if (($data['expires_at'] ?? null) && ! $expiresAt) {
            throw ValidationException::withMessages([
                'expires_at' => 'تاریخ انقضا معتبر نیست.',
            ]);
        }

        if ($startsAt && $expiresAt && $expiresAt < $startsAt) {
            throw ValidationException::withMessages([
                'expires_at' => 'تاریخ انقضا باید بعد از تاریخ شروع باشد.',
            ]);
        }

        $data['code'] = strtoupper($data['code']);
        $data['starts_at'] = $startsAt;
        $data['expires_at'] = $expiresAt;

        return $data;
    }
}
