<?php
namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Note;
use App\Models\Sport;

class HomeController extends Controller
{
    public function index()
    {
        $sports = Sport::all();
        
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

        return view('home', compact('sports', 'notes', 'announcements'));
    }
}
