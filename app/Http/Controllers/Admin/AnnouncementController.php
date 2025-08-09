<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Duyuruların yönetileceği ana sayfayı gösterir.
     * Tüm duyuruları bu sayfaya JSON olarak da gönderebiliriz ama sayfa ilk yüklendiğinde gelmesi daha iyi.
     */
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Yeni bir duyuru oluşturur ve JSON olarak döner.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'show_until' => 'nullable|date',
        ]);
        
        $data['is_active'] = $request->has('is_active');

        $announcement = Announcement::create($data);

        return response()->json([
            'success' => true, 
            'message' => 'Duyuru başarıyla oluşturuldu!',
            'announcement' => $announcement
        ]);
    }

    /**
     * ID'si verilen duyurunun bilgilerini düzenleme için JSON olarak döner.
     * Edit view'ı yerine doğrudan veri döndürüyoruz.
     */
    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);
        return response()->json($announcement);
    }

    /**
     * Mevcut bir duyuruyu günceller.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'show_until' => 'nullable|date',
        ]);

        $announcement = Announcement::findOrFail($id);
        
        $data['is_active'] = $request->has('is_active');
        
        $announcement->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Duyuru başarıyla güncellendi!',
            'announcement' => $announcement
        ]);
    }

    /**
     * Bir duyuruyu siler.
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json(['success' => true, 'message' => 'Duyuru başarıyla silindi.']);
    }
}