<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\User\Auth\ResetPasswordRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null): View
    {
        return view('user.auth.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker('site_users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Password Anda telah berhasil direset! Silakan login dengan password baru Anda.');
        } else {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $this->getResetPasswordFailureMessage($status)]);
        }
    }

    protected function getResetPasswordFailureMessage(string $status): string
    {
        switch ($status) {
            case Password::INVALID_TOKEN:
                return 'Token reset password ini tidak valid atau sudah kedaluwarsa.';
            case Password::INVALID_USER:
                return 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.';
            default:
                return 'Gagal mereset password. Silakan coba lagi.';
        }
    }
}
