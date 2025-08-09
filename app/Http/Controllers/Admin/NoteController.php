<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::orderBy('order', 'asc')->get();
        return view('admin.notes.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->has('is_active');
        $note = Note::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Not başarıyla eklendi!',
            'html' => view('admin.notes.partials.note-row', ['note' => $note])->render()
        ]);
    }
    
    public function show(Note $note)
    {
        return response()->json($note);
    }

    public function update(Request $request, Note $note)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->has('is_active');
        $note->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Not başarıyla güncellendi!',
            'html' => view('admin.notes.partials.note-row', ['note' => $note])->render()
        ]);
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(['success' => true, 'message' => 'Not başarıyla silindi.']);
    }
}