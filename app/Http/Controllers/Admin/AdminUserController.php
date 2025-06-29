<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $adminUsers = AdminUser::latest()->get();
        return view('admin.admin-users.index', compact('adminUsers'));
    }

    public function create(): View
    {
        return view('admin.admin-users.create');
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        AdminUser::create($validated);

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Admin baru berhasil ditambahkan.');
    }

    public function edit(AdminUser $adminUser): View
    {
        return view('admin.admin-users.edit', compact('adminUser'));
    }

    public function update(UpdateAdminUserRequest $request, AdminUser $adminUser): RedirectResponse
    {
        $validated = $request->validated();

        if ($adminUser->id === Auth::guard('admin')->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.')
                ->withInput();
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $adminUser->update($validated);
        $adminUser->save();

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroy(AdminUser $adminUser): RedirectResponse
    {
        if ($adminUser->id === Auth::guard('admin')->id()) {
            return redirect()->route('admin.admin-users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        try {
            $userName = $adminUser->name;
            $adminUser->delete();
            return redirect()->route('admin.admin-users.index')
                ->with('success', 'Data admin ' . $userName . ' berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.admin-users.index')
                ->with('error', 'Gagal menghapus data admin: ' . $e->getMessage());
        }
    }
}
