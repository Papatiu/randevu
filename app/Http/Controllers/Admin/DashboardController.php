<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Sport;
use App\Models\Slot;
use Illuminate\Support\Facades\DB;
use App\Models\BannedTc;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Temel sorguyu oluştur. İlişkili verileri (`slot` ve `slot.sport`) en başta çek.
        $query = Appointment::with(['slot.sport']);

        // 2. Filtreleri uygula
        // Arama (Ad, Soyad, TC ile)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('ad', 'like', "%{$search}%")
                  ->orWhere('soyad', 'like', "%{$search}%")
                  ->orWhere('tc_kimlik', 'like', "%{$search}%");
            });
        }
        // Spor Dalı
        if ($request->filled('sport_id')) {
            $sportId = $request->input('sport_id');
            $query->whereHas('slot', function($q) use ($sportId) {
                $q->where('sport_id', $sportId);
            });
        }
        // Durum
        if ($request->filled('status')) {
            $query->where('durum', $request->input('status'));
        }
        // Tarih Aralığı
        if ($request->filled('start_date')) {
            $startDate = $request->input('start_date');
            $query->whereHas('slot', function($q) use ($startDate) {
                $q->where('tarih', '>=', $startDate);
            });
        }
        if ($request->filled('end_date')) {
            $endDate = $request->input('end_date');
            $query->whereHas('slot', function($q) use ($endDate) {
                $q->where('tarih', '<=', $endDate);
            });
        }

        // 3. Sırala ve Sayfala
        $appointments = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $sports = Sport::orderBy('ad')->get();

        return view('admin.dashboard', compact('appointments', 'sports'));
    }

    public function show($id)
    {
        // with ile takım bilgilerini de çekebiliriz (eager loading)
        $appointment = Appointment::with('slot.sport')->findOrFail($id);
        return response()->json($appointment);
    }
    
    // YENİ METOT: Randevuya katılımı onayla
    public function confirmAttendance($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Sadece "onaylandi" durumundaki bir randevu güncellenebilir.
        if ($appointment->durum !== 'onaylandi') {
            return response()->json(['success' => false, 'message' => 'Sadece "Onaylandı" durumundaki randevular için katılım onayı verilebilir.'], 422);
        }

        $appointment->durum = 'katilim_onaylandi'; // Yeni durumumuz
        $appointment->aciklama = 'Personel tarafından katılımı onaylandı.';
        $appointment->save();

        return response()->json(['success' => true, 'message' => 'Katılımcının randevuya geldiği başarıyla onaylandı.']);
    }
    
    // ... Diğer metodlar (destroy, update, markAsNoShow) aynı kalıyor ...
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        DB::transaction(function () use ($appointment) {
            $slot = Slot::find($appointment->slot_id);
            if ($slot) {
                $slot->decrement('rezervasyon_sayisi');
            }
            $appointment->delete();
        });

        return response()->json(['success' => true, 'message' => 'Randevu başarıyla silindi.']);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'new_sport_id' => 'required|exists:sports,id'
        ]);
        $appointment = Appointment::findOrFail($id);
        $oldSlot = $appointment->slot;

        $newSlot = Slot::where('sport_id', $data['new_sport_id'])
            ->where('tarih', $oldSlot->tarih)
            ->where('saat', $oldSlot->saat)
            ->where('rezervasyon_sayisi', '<', DB::raw('kapasite'))
            ->first();

        if (!$newSlot) {
            return response()->json([
                'success' => false,
                'message' => 'Seçilen yeni spor dalında bu tarih ve saatte uygun yer bulunmamaktadır.'
            ], 422);
        }

        DB::transaction(function () use ($appointment, $oldSlot, $newSlot) {
            $oldSlot->decrement('rezervasyon_sayisi');
            $newSlot->increment('rezervasyon_sayisi');
            $appointment->slot_id = $newSlot->id;
            $appointment->save();
        });

        return response()->json(['success' => true, 'message' => 'Randevu yeni spor dalına başarıyla taşındı.']);
    }

    public function markAsNoShow(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->durum !== 'onaylandi') {
            return response()->json(['success' => false, 'message' => 'Sadece onaylanmış randevular "gelmedi" olarak işaretlenebilir.'], 422);
        }

        $appointment->durum = 'gelmedi';
        $appointment->aciklama = 'Admin tarafından "gelmedi" olarak işaretlendi.';
        $appointment->save();

        $banSuresiGun = 14;
        $banBitisTarihi = Carbon::now()->addDays($banSuresiGun);

        BannedTc::updateOrCreate(
            ['tc_kimlik' => $appointment->tc_kimlik],
            [
                'ban_bitis_tarihi' => $banBitisTarihi,
                'sebep' => "Randevu #{$appointment->id}'e gelinmediği için {$banSuresiGun} gün yasaklandı."
            ]
        );
        return response()->json(['success' => true, 'message' => 'Randevu "gelmedi" olarak işaretlendi ve kullanıcı 14 gün süreyle randevu almaktan yasaklandı.']);
    }
}