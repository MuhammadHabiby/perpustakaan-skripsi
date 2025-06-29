<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\Admin\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $adminUser = $request->user('admin');
        return view('admin.profile.edit', compact('adminUser'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $adminUser = $request->user('admin');

        $validated = $request->validated();

        $adminUser->name = $validated['name'];
        $adminUser->email = $validated['email'];

        if (!empty($validated['password'])) {
            $adminUser->password = Hash::make($validated['password']);
        }

        $adminUser->save();

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
