@forelse($announcements as $announcement)
<tr id="announcement-row-{{ $announcement->id }}">
    <td>{{ $announcement->title }}</td>
    <td>
        @if($announcement->is_active)
            <span class="badge bg-success">Aktif</span>
        @else
            <span class="badge bg-secondary">Pasif</span>
        @endif
    </td>
    <td>{{ $announcement->show_until ? \Carbon\Carbon::parse($announcement->show_until)->format('d/m/Y') : '-' }}</td>
    <td>
        <button class="btn btn-sm btn-info edit-btn" data-id="{{ $announcement->id }}">Düzenle</button>
        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $announcement->id }}">Sil</button>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center">Henüz oluşturulmuş bir duyuru yok.</td>
</tr>
@endforelse