<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::orderBy('description')->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validatedSettings = $request->validated()['settings'];

        DB::beginTransaction();
        try {
            foreach ($validatedSettings as $key => $value) {
                Setting::where('key', $key)->update(['value' => $value ?? '']);
            }
            DB::commit();

            return redirect()->route('admin.settings.index')
                ->with('success', 'Pengaturan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.settings.index')
                ->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
        }
    }
}
