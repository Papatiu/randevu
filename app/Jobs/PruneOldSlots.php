<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PruneOldSlots implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct() {}

    public function handle(): void
    {
        // Bugünden 3 gün öncesinin tarihini hesapla
        $limitDate = Carbon::now()->subDays(3)->toDateString();

        // Bu tarihten daha eski olan tüm slotları bul ve sil
        // Model'deki cascade on delete sayesinde bu slotlara bağlı randevular da otomatik olarak silinecektir.
        $deletedCount = Slot::where('tarih', '<', $limitDate)->delete();

        if ($deletedCount > 0) {
            Log::info("{$deletedCount} adet eski slot ve bağlı randevuları veritabanından silindi.");
        }
    }
}