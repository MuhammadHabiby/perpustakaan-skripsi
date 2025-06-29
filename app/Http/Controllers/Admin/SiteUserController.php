<?php

namespace App\Http\Controllers\Admin;

use App\Enum\BorrowingStatus;
use App\Events\UserAccountActivated;
use App\Http\Controllers\Controller;
use App\Models\SiteUser;
use App\Http\Requests\Admin\StoreSiteUserRequest;
use App\Http\Requests\Admin\UpdateSiteUserRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SiteUserController extends Controller
{
    public function index(): View
    {
        $siteUsers = SiteUser::latest()->get();
        return view('admin.site-users.index', compact('siteUsers'));
    }

    public function create(): View
    {
        return view('admin.site-users.create');
    }

    public function show(SiteUser $siteUser): View
    {
        $siteUser->load(['borrowings' => function ($query) {
            $query->with(['bookCopy' => function ($qCopy) {
                $qCopy->with('book:id,title');
            }])->latest('borrow_date');
        }]);

        return view('admin.site-users.show', compact('siteUser'));
    }

    public function store(StoreSiteUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        DB::beginTransaction();
        try {
            SiteUser::create($validated);

            DB::commit();

            return redirect()->route('admin.site-users.index')
                ->with('success', 'Data siswa baru berhasil ditambahkan dan diaktifkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan siswa: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(SiteUser $siteUser): View
    {
        return view('admin.site-users.edit', compact('siteUser'));
    }

    public function update(UpdateSiteUserRequest $request, SiteUser $siteUser): RedirectResponse
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $siteUser->update($validated);
        $siteUser->save();

        return redirect()->route('admin.site-users.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(SiteUser $siteUser): RedirectResponse
    {
        $activeBorrowings = $siteUser->borrowings()
            ->whereIn('status', [
                BorrowingStatus::Borrowed,
                BorrowingStatus::Overdue
            ])
            ->exists();

        if ($activeBorrowings) {
            return redirect()->route('admin.site-users.index')
                ->with('error', 'Gagal menghapus! Siswa ' . $siteUser->name . ' masih memiliki pinjaman aktif atau lewat tempo.');
        }

        try {
            $userName = $siteUser->name;
            $siteUser->delete();
            return redirect()->route('admin.site-users.index')
                ->with('success', 'Data siswa ' . $userName . ' berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.site-users.index')
                ->with('error', 'Gagal menghapus data siswa: ' . $e->getMessage());
        }
    }
}
