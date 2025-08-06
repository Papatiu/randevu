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

            $slots = Slot::where('sport_id', $sport_id)
                ->whereDate('tarih', $date)
                ->get();

            $total = $slots->count();
            $full = $slots->where('rezervasyon_sayisi', '>=', 1)->count();

            if ($total == 0) {
                $status = 'green';
            } elseif ($full == $total) {
                $status = 'red';
            } elseif ($full > 0) {
                $status = 'yellow';
            } else {
                $status = 'green';
            }

            $dates[] = [
                'date' => $date, // JS tarafında date olarak kullanılıyor
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
                'saat' => $slot->saat,
                'status' => $slot->rezervasyon_sayisi > 0 ? 'full' : 'empty',
            ];
        }

        return response()->json($data);
    }

    public function makeReservation(Request $request)
    {
        $validated = $request->validate([
            'sport_id' => 'required|integer',
            'tarih' => 'required|date',
            'saat' => 'required'
        ]);

        $slot = Slot::where('sport_id', $validated['sport_id'])
            ->where('tarih', $validated['tarih'])
            ->where('saat', $validated['saat'])
            ->first();

        if (!$slot) {
            return response()->json(['message' => 'Uygun saat bulunamadı.'], 404);
        }

        if ($slot->rezervasyon_sayisi >= 1) {
            return response()->json(['message' => 'Bu saat dolu.'], 403);
        }

        $slot->rezervasyon_sayisi += 1;
        $slot->save();

        Reservation::create([
            'user_id' => auth()->id(),
            'slot_id' => $slot->id
        ]);

        return response()->json(['message' => 'Randevunuz başarıyla alındı.']);
    }
}
