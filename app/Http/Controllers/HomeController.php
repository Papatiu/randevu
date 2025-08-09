<?php
namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Note;
use App\Models\Setting; // YENİ
use App\Models\Sport;

class HomeController extends Controller
{
    public function index()
    {
        // ÖNCEKİ HALİ: Sadece sport'ları alıyorduk.
        // $sports = Sport::all();
        
        // YENİ HALİ: Artık tüm sporları alıyoruz (aktif/pasif fark etmez)
        $sports = Sport::orderBy('ad', 'asc')->get();

        // Aktif notları al
        $notes = Note::where('is_active', true)->orderBy('order')->get();

        // Aktif ve süresi geçmemiş duyuruları al
        $announcements = Announcement::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('show_until')
                      ->orWhere('show_until', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // YENİ: İletişim numarasını ayarlardan çek
        $contactPhone = Setting::where('key', 'contact_phone')->value('value');

        return view('home', compact('sports', 'notes', 'announcements', 'contactPhone'));
    }
}