<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        return view('user.profile.edit', compact('user'));
    }

    public function update(UpdateUserProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'] ?? null;


        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        try {
            $user->save();

            return redirect()->route('user.profile.edit')->with('success', 'Profil Anda berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error("Error updating user profile for User ID: {$user->id} - " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui profil. Terjadi kesalahan sistem.')
                ->withInput();
        }
    }
}
