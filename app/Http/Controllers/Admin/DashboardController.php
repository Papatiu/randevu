<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Sport;
use App\Models\Slot;
use Illuminate\Support\Facades\DB; // DB işlemleri için ekliyoruz
use App\Models\BannedTc; // Bunu ekle
use Carbon\Carbon; // Bunu ekle


class DashboardController extends Controller
{
    public function index()
    {
        // ... (Bu fonksiyon aynı kalıyor)
        $appointments = Appointment::with('slot.sport')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $sports = Sport::all(); // Güncelleme modalı için tüm sporları view'e gönderelim

        return view('admin.dashboard', compact('appointments', 'sports'));
    }

    /**
     * Tek bir randevunun detaylarını JSON olarak döndürür.
     */
    public function show($id)
    {
        $appointment = Appointment::with('slot.sport')->findOrFail($id);
        return response()->json($appointment);
    }

    /**
     * Randevuyu siler.
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        DB::transaction(function () use ($appointment) {
            // Önce slot'un rezervasyon sayısını azalt
            $slot = Slot::find($appointment->slot_id);
            if ($slot) {
                $slot->decrement('rezervasyon_sayisi');
            }
            // Sonra randevuyu sil
            $appointment->delete();
        });

        return response()->json(['success' => true, 'message' => 'Randevu başarıyla silindi.']);
    }

    /**
     * Randevunun spor dalını günceller. (Bu işlem biraz karmaşık)
     * Not: Sadece spor dalını değil, aynı tarih ve saatteki yeni spor dalına ait boş bir slot bulup
     * randevuyu oraya taşımamız gerekiyor.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'new_sport_id' => 'required|exists:sports,id'
        ]);

        $appointment = Appointment::findOrFail($id);
        $oldSlot = $appointment->slot;

        // 1. Yeni spor dalında, aynı tarih ve saatte boş bir slot var mı?
        $newSlot = Slot::where('sport_id', $data['new_sport_id'])
            ->where('tarih', $oldSlot->tarih)
            ->where('saat', $oldSlot->saat)
            ->where('rezervasyon_sayisi', '<', DB::raw('kapasite')) // Dolu olmayanları bul
            ->first();

        if (!$newSlot) {
            return response()->json([
                'success' => false,
                'message' => 'Seçilen yeni spor dalında bu tarih ve saatte uygun yer bulunmamaktadır.'
            ], 422); // 422: Unprocessable Entity
        }

        DB::transaction(function () use ($appointment, $oldSlot, $newSlot) {
            // 2. Eski slotun doluluğunu azalt
            $oldSlot->decrement('rezervasyon_sayisi');

            // 3. Yeni slotun doluluğunu arttır
            $newSlot->increment('rezervasyon_sayisi');

            // 4. Randevuyu yeni slota bağla
            $appointment->slot_id = $newSlot->id;
            $appointment->save();
        });

        return response()->json(['success' => true, 'message' => 'Randevu yeni spor dalına başarıyla taşındı.']);
    }

    public function markAsNoShow(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Sadece onaylanmış randevular "gelmedi" olarak işaretlenebilir.
        if ($appointment->durum !== 'onaylandi') {
            return response()->json(['success' => false, 'message' => 'Sadece onaylanmış randevular "gelmedi" olarak işaretlenebilir.'], 422);
        }

        // 1. Randevunun durumunu güncelle
        $appointment->durum = 'gelmedi';
        $appointment->aciklama = 'Admin tarafından "gelmedi" olarak işaretlendi.';
        $appointment->save();

        // 2. Kullanıcıyı banla (örneğin 14 gün)
        $banSuresiGun = 14;
        $banBitisTarihi = Carbon::now()->addDays($banSuresiGun);

        // BannedTc modelini kullanarak TC'yi banla veya ban süresini güncelle
        BannedTc::updateOrCreate(
            ['tc_kimlik' => $appointment->tc_kimlik], // Bu TC'yi bul
            [
                'ban_bitis_tarihi' => $banBitisTarihi, // Ban bitiş tarihini güncelle/oluştur
                'sebep' => "Randevu #{$appointment->id}'e gelinmediği için {$banSuresiGun} gün yasaklandı."
            ]
        );

        return response()->json(['success' => true, 'message' => 'Randevu "gelmedi" olarak işaretlendi ve kullanıcı 14 gün süreyle randevu almaktan yasaklandı.']);
    }
}