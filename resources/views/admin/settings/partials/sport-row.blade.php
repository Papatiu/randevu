<tr id="sport-row-{{ $sport->id }}">
    <td>
        {{-- Tesis Adı --}}
        {{ $sport->ad }}
        
        {{-- Eğer pasifse ve pasif olma nedeni belirtilmişse, adın altında gösterelim --}}
        @if(!$sport->is_active && $sport->status_reason)
            <br><small class="text-danger fst-italic">({{ $sport->status_reason }})</small>
        @endif
    </td>
    <td class="text-center">
        {{-- is_active durumuna göre dinamik olarak badge (etiket) gösterelim --}}
        @if($sport->is_active)
            <span class="badge bg-success">Aktif</span>
        @else
            <span class="badge bg-danger">Pasif</span>
        @endif
    </td>
    <td class="text-center">
        {{-- Düzenle butonunun data attribute'u zaten doğru çalışıyor --}}
        <button class="btn btn-sm btn-info edit-sport-btn" 
                data-sport="{{ $sport->toJson() }}">
            <i class="fas fa-edit"></i> Düzenle
        </button>
    </td>
</tr>