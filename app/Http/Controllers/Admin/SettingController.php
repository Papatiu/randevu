<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Sport;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // ... index ve updateGeneralSettings fonksiyonları aynı kalacak ...
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        // ÖNCE: Sport::all();
        // SONRA: Aktif olanlar üstte, sonra pasif olanlar gelecek şekilde sırala
        $sports = Sport::orderBy('is_active', 'desc')->orderBy('ad', 'asc')->get();

        return view('admin.settings.index', compact('settings', 'sports'));
    }

    public function updateGeneralSettings(Request $request)
    {
        $data = $request->validate([
            'cancel_deadline_hours' => 'required|integer|min:0',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['success' => true, 'message' => 'Genel Ayarlar başarıyla güncellendi.']);
    }


    /**
     * Yeni bir spor tesisi oluşturur.
     */
    public function storeSport(Request $request)
    {
        // ÖNCEKİ HALİ: 'is_active' => 'boolean' -> BU SATIR SORUN ÇIKARIYOR.
        // DOĞRUSU: Gelen veriyi validation'da zorunlu boolean yapmayalım, biz kendimiz kontrol edelim.
        $validatedData = $request->validate([
            'ad' => 'required|string|max:255|unique:sports,ad',
            'status_reason' => 'nullable|string|max:255' // Bunu da ekleyelim ki boş gelebilsin.
        ]);

        // Veritabanına gidecek olan son veriyi hazırlayalım.
        $dataToCreate = $validatedData;

        // == DÜZELTME BURADA ==
        // Gelen istekte 'is_active' alanı 'true' olarak mı geldi?
        // JSON'dan gelen 'true' string'ini PHP'nin boolean true'suna çeviriyoruz.
        $dataToCreate['is_active'] = $request->input('is_active') === true;

        $sport = Sport::create($dataToCreate);

        $html = view('admin.settings.partials.sport-row', compact('sport'))->render();

        return response()->json(['success' => true, 'message' => 'Tesis başarıyla eklendi.', 'html' => $html]);
    }

    /**
     * Belirli bir spor tesisini günceller.
     */
    public function updateSport(Request $request, Sport $sport)
    {
        // ÖNCEKİ HALİ: 'is_active' => 'boolean' -> BU SATIR SORUN ÇIKARIYOR.
        // DOĞRUSU:
        $validatedData = $request->validate([
            'ad' => 'required|string|max:255|unique:sports,ad,' . $sport->id,
            'status_reason' => 'nullable|string|max:255'
        ]);

        $dataToUpdate = $validatedData;

        // == DÜZELTME BURADA ==
        // Gelen istekte 'is_active' alanı 'true' olarak mı geldi?
        $dataToUpdate['is_active'] = $request->input('is_active') === true;

        $sport->update($dataToUpdate);

        $html = view('admin.settings.partials.sport-row', compact('sport'))->render();

        return response()->json(['success' => true, 'message' => 'Tesis bilgileri güncellendi.', 'html' => $html]);
    }
}