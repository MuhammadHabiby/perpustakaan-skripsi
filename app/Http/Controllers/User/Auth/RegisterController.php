<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Models\SiteUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('user.auth.register');
    }
    public function register(RegisterRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
            SiteUser::create([
                'nis' => $validatedData['nis'],
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'class' => $validatedData['class'] ?? null,
                'major' => $validatedData['major'] ?? null,
            ]);

            DB::commit();


            return redirect()->route('login')
                ->with('status', 'Registrasi berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User Registration Error: ' . $e->getMessage());
            return redirect()->route('register')
                ->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi atau hubungi admin.')
                ->withInput();
        }
    }
}
