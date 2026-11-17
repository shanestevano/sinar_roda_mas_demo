<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->keyBy('key');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $inputs = $request->except(['_token', '_method']);

        foreach ($inputs as $key => $value) {
            Setting::set($key, (string) $value);
        }

        ActivityLog::record('UPDATE_SETTINGS', 'Memperbarui konfigurasi informasi toko & struk kasir.');

        return redirect()->route('settings.index')->with('success', 'Pengaturan toko berhasil disimpan!');
    }
}
