<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Tüm ayarları çekip 'key' bazlı bir array'e dönüştürelim
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cancel_deadline_hours' => 'required|integer|min:0',
            // Gelecekte eklenecek diğer ayarlar buraya gelebilir
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Ayarlar başarıyla güncellendi!');
    }
}