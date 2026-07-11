<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses;

        return view('panel.addresses.index', ['addresses' => $addresses]);
    }

    public function create()
    {
        return view('panel.addresses.form', ['address' => new Address()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $user = Auth::user();

        if (!empty($data['is_default']) || !$user->addresses()->exists()) {
            $user->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $user->addresses()->create($data);

        return redirect()->route('panel.addresses.index')->with('success', 'آدرس جدید با موفقیت ثبت شد.');
    }

    public function edit(Address $address)
    {
        $this->authorizeAddress($address);

        return view('panel.addresses.form', ['address' => $address]);
    }

    public function update(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeAddress($address);

        $data = $this->validated($request);

        if (!empty($data['is_default'])) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $data['is_default'] = true;
        } else {
            $data['is_default'] = $address->is_default;
        }

        $address->update($data);

        return redirect()->route('panel.addresses.index')->with('success', 'آدرس با موفقیت ویرایش شد.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        $this->authorizeAddress($address);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            Auth::user()->addresses()->first()?->update(['is_default' => true]);
        }

        return redirect()->route('panel.addresses.index')->with('success', 'آدرس حذف شد.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
            'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address_line' => ['required', 'string', 'max:1000'],
            'postal_code' => ['required', 'string', 'max:10'],
            'is_default' => ['nullable', 'boolean'],
        ], [], [
            'title' => 'عنوان آدرس',
            'receiver_name' => 'نام گیرنده',
            'receiver_mobile' => 'موبایل گیرنده',
            'province' => 'استان',
            'city' => 'شهر',
            'address_line' => 'آدرس',
            'postal_code' => 'کد پستی',
        ]);
    }

    private function authorizeAddress(Address $address): void
    {
        abort_unless($address->user_id === Auth::id(), 403);
    }
}
