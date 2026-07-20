<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    /**
     * Laravel's auth middleware redirects guests to route('login').
     * We don't have a dedicated login page; open the shared auth modal instead.
     */
    public function redirectToLogin(): RedirectResponse
    {
        return redirect()->route('home', ['login' => 1]);
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], ['login' => 'شماره موبایل یا ایمیل']);

        $login = $data['login'];
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL) !== false;

        if ($isEmail) {
            $field = 'email';
            $identifier = $login;
        } else {
            $normalizedMobile = $this->normalizeMobile($login);

            if (preg_match('/^09[0-9]{9}$/', $normalizedMobile)) {
                $field = 'mobile';
                $identifier = $normalizedMobile;
            } else {
                $field = 'email';
                $identifier = $login;
            }
        }

        if (! Auth::attempt([$field => $identifier, 'password' => $data['password']], true)) {
            throw ValidationException::withMessages([
                'login' => 'شماره موبایل/ایمیل یا رمز عبور اشتباه است.',
            ]);
        }

        $request->session()->regenerate();
        $this->cartService->mergeGuestCartIntoUser(Auth::user());

        return redirect()->intended(route('home'));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/', 'unique:users,mobile'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [], [
            'name' => 'نام و نام خانوادگی',
            'mobile' => 'شماره موبایل',
            'password' => 'رمز عبور',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'mobile_verified_at' => now(),
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();
        $this->cartService->mergeGuestCartIntoUser($user);

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function normalizeMobile(string $mobile): string
    {
        $mobile = preg_replace('/\D/', '', $mobile);

        if (str_starts_with($mobile, '0098')) {
            $mobile = '0'.substr($mobile, 4);
        } elseif (str_starts_with($mobile, '98')) {
            $mobile = '0'.substr($mobile, 2);
        } elseif (str_starts_with($mobile, '9') && strlen($mobile) === 10) {
            $mobile = '0'.$mobile;
        }

        return $mobile;
    }
}
