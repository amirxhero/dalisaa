<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function edit()
    {
        return view('admin.account.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/', 'unique:users,mobile,'.$user->id],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ], [], [
            'name' => 'نام و نام خانوادگی',
            'email' => 'ایمیل',
            'mobile' => 'شماره موبایل',
            'password' => 'رمز عبور',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'] ?? null;
        $user->mobile = $data['mobile'];

        if (! empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();

        return back()->with('success', 'اطلاعات حساب کاربری با موفقیت به‌روزرسانی شد.');
    }
}
