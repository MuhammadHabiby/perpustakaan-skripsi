<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\User\Auth\ForgotPasswordRequest;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(): View
    {
        return view('user.auth.email');
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker('site_users')->sendResetLink(
            $request->validated()
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link reset password telah dikirim ke alamat email Anda. Silakan periksa kotak masuk (atau folder spam) Anda.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
