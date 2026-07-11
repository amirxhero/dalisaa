<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('orders')->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->input('role') === 'admin') {
            $query->where('is_admin', true);
        } elseif ($request->input('role') === 'customer') {
            $query->where('is_admin', false);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleAdmin(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'نمی‌توانید نقش خودتان را تغییر دهید.');
        }

        $user->update(['is_admin' => ! $user->is_admin]);

        return back()->with('success', 'نقش کاربر با موفقیت تغییر کرد.');
    }

    public function toggleBlock(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'نمی‌توانید حساب خودتان را مسدود کنید.');
        }

        $user->update([
            'blocked_at' => $user->isBlocked() ? null : now(),
        ]);

        $message = $user->isBlocked() ? 'کاربر مسدود شد.' : 'مسدودیت کاربر برداشته شد.';

        return back()->with('success', $message);
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'نمی‌توانید حساب خودتان را حذف کنید.');
        }

        $user->delete();

        return back()->with('success', 'کاربر با موفقیت حذف شد.');
    }
}
