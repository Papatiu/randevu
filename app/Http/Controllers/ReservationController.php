<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\Slot;
use Illuminate\Support\Carbon;

class ReservationController extends Controller
{
    public function getDates($sport_id)
    {
        $dates = [];

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');

            // O güne ait tüm slotları çek
            $slots = Slot::where('sport_id', $sport_id)
                ->whereDate('tarih', $date)
                ->get();

            // Toplam slot ve dolu slot sayısını hesapla
            $total = $slots->count();
            $full = $slots->where('rezervasyon_sayisi', '>=', 1)->count(); // Kapasite 1 olduğu için

            // Toplam slot yoksa o günü gösterme (opsiyonel)
            if ($total == 0) {
                continue;
            }

            // Renk durumunu belirle
            if ($full == $total) {
                $status = 'red'; // Tümü dolu
            } elseif ($full > 0) {
                $status = 'yellow'; // Bazıları dolu (yarı dolu)
            } else {
                $status = 'green'; // Hepsi boş
            }

            $dates[] = [
                'date' => $date,
                'status' => $status,
            ];
        }

        return response()->json($dates);
    }
    public function getHours($sport_id, $tarih)
    {
        $slots = Slot::where('sport_id', $sport_id)
            ->whereDate('tarih', $tarih)
            ->get();

        $data = [];

        foreach ($slots as $slot) {
            $data[] = [
                'slot_id' => $slot->id, // ÖNEMLİ: slot_id'sini ekledik!
                'saat' => $slot->saat,
                'status' => $slot->rezervasyon_sayisi >= $slot->kapasite ? 'full' : 'empty',
            ];
        }

        return response()->json($data);
    }

    public function makeReservation(Request $request)
    {
        $validated = $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'tarih' => 'required|date',
            'saat' => 'required'
        ]);

        $slot = Slot::where('sport_id', $validated['sport_id'])
            ->where('tarih', $validated['tarih'])
            ->where('saat', $validated['saat'])
            ->first();

        if (!$slot || $slot->rezervasyon_sayisi >= $slot->kapasite) {
            // Hatalı veya dolu bir slota istek gelirse ana sayfaya hata mesajıyla yönlendir.
            return redirect()->route('home')->with('error', 'Bu saat rezerve edilemez veya dolu!');
        }

        $slot->increment('rezervasyon_sayisi');

        Reservation::create([
            'user_id' => auth()->id(),
            'slot_id' => $slot->id
        ]);

        // BAŞARILI OLURSA: Ana sayfaya başarı mesajıyla yönlendir.
        return redirect()->route('home')->with('success', 'Randevunuz başarıyla alınmıştır!');
    }
}
