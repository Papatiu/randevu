<tr id="note-row-{{ $note->id }}">
    <td>{!! Str::limit(strip_tags($note->content), 80) !!}</td>
    <td class="text-center">
        @if($note->is_active)
            <span class="badge bg-success">Aktif</span>
        @else
            <span class="badge bg-secondary">Pasif</span>
        @endif
    </td>
    <td class="text-center">
        <button class="btn btn-sm btn-info edit-btn" data-id="{{ $note->id }}">
            <i class="fas fa-edit"></i> Düzenle
        </button>
        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $note->id }}">
            <i class="fas fa-trash"></i> Sil
        </button>
    </td>
</tr>