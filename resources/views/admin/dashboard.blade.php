@extends('layouts.app')

@section('styles')
    <style>
        /* Yeni eklenen durum için stil */
        .status-katilim_onaylandi {
            background-color: #b6d4fe; /* Açık mavi */
            color: #084298;
        }
        .status-onaylandi { background-color: #d1e7dd; color: #0f5132; }
        .status-iptal_edildi { background-color: #f8d7da; color: #842029; }
        .status-onay_bekliyor { background-color: #fff3cd; color: #664d03; }
        .status-tamamlandi { background-color: #cce5ff; color: #004085; }
        .status-gelmedi { background-color: #e2e3e5; color: #41464b; }
        .badge { padding: 0.5em 0.75em; font-size: 0.8rem; text-transform: capitalize; }
        .filter-card { background-color: #f8f9fa; }
    </style>
@endsection

@section('content')
<div class="container"> {{-- TEKRAR 'container' YAPILDI --}}
    <h1 class="mb-4">Yönetim Paneli - Tüm Randevular</h1>

    {{-- FİLTRELEME FORMU --}}
    <div class="card filter-card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.dashboard') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Arama Yap (Ad, Soyad, TC)</label>
                        <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Arama metni...">
                    </div>
                    <div class="col-md-2">
                        <label for="sport_id" class="form-label">Spor Dalı</label>
                        <select name="sport_id" id="sport_id" class="form-select">
                            <option value="">Tümü</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}" @if(request('sport_id') == $sport->id) selected @endif>{{ $sport->ad }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Durum</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tümü</option>
                            <option value="onaylandi" @if(request('status') == 'onaylandi') selected @endif>Onaylandı</option>
                            <option value="katilim_onaylandi" @if(request('status') == 'katilim_onaylandi') selected @endif>Katılım Onaylandı</option>
                            <option value="iptal_edildi" @if(request('status') == 'iptal_edildi') selected @endif>İptal Edildi</option>
                            <option value="gelmedi" @if(request('status') == 'gelmedi') selected @endif>Gelmedi</option>
                            <option value="tamamlandi" @if(request('status') == 'tamamlandi') selected @endif>Tamamlandı</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label">Bitiş Tarihi</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- RANDEVU TABLOSU --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Ad Soyad</th><th>TC Kimlik</th><th>Spor Dalı</th>
                            <th>Tarih ve Saat</th><th>Durum</th><th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->id }}</td>
                                <td>{{ $appointment->ad }} {{ $appointment->soyad }}</td>
                                <td>{{ $appointment->tc_kimlik }}</td>
                                <td>{{ $appointment->slot->sport->ad }}</td>
                                <td>{{ \Carbon\Carbon::parse($appointment->slot->tarih)->format('d/m/Y') }} <br> {{ $appointment->slot->saat }}</td>
                                <td><span class="badge status-{{ $appointment->durum }}">{{ str_replace('_', ' ', $appointment->durum) }}</span></td>
                                <td><button class="btn btn-sm btn-info" onclick="showAppointmentDetails({{ $appointment->id }})"><i class="fas fa-eye me-1"></i> Detay</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">Arama kriterlerine uygun randevu bulunamadı.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">{{ $appointments->links() }}</div>
        </div>
    </div>
</div>

{{-- RANDEVU DETAY MODALI --}}
{{-- 1. DEĞİŞİKLİK: modal-lg CLASS'INI SİLİYORUZ --}}
<div class="modal fade" id="appointmentDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Randevu Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody"><div class="text-center"><div class="spinner-border"></div></div></div>
            <div class="modal-footer justify-content-between">
                <div>
                    <button type="button" class="btn btn-success" id="confirmAttendanceButton"><i class="fas fa-check-circle me-1"></i> Katılımı Onayla</button>
                    <button type="button" class="btn btn-warning" id="noShowButton"><i class="fas fa-user-clock me-1"></i> Gelmedi</button>
                    <button type="button" class="btn btn-danger" id="deleteButton"><i class="fas fa-trash me-1"></i> Sil</button>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="updateButton"><i class="fas fa-sync-alt me-1"></i> Spor Dalını Değiştir</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const detailModal = new bootstrap.Modal(document.getElementById('appointmentDetailModal'));
    let currentAppointmentId = null;

    function showAppointmentDetails(id) {
        currentAppointmentId = id;
        const modalBody = document.getElementById('modalBody');
        const confirmBtn = document.getElementById('confirmAttendanceButton');
        const noShowBtn = document.getElementById('noShowButton');
        modalBody.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';
        detailModal.show();

        fetch(`/admin/appointments/${id}`)
            .then(res => res.json())
            .then(data => {
                const appointmentDate = new Date(data.slot.tarih).toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                
                // 2. DEĞİŞİKLİK: Takım bilgileri varsa göster, yoksa boş string bas.
                // Ve bunu Katılımcı Bilgileri altına alarak daha düzenli hale getiriyoruz.
                const teamInfoHtml = data.team_name ? `
                    <p><strong>Takım Adı:</strong> ${data.team_name}</p>
                    <p><strong>Kişi Sayısı:</strong> ${data.participant_count || '<i>Belirtilmemiş</i>'}</p>
                ` : '';

                modalBody.innerHTML = `
                <h5><i class="fas fa-user-circle text-primary me-2"></i>Katılımcı Bilgileri</h5>
                <p><strong>Ad Soyad:</strong> ${data.ad} ${data.soyad}</p>
                <p><strong>TC Kimlik:</strong> ${data.tc_kimlik}</p>
                <p><strong>Telefon:</strong> ${data.telefon}</p>
                <p><strong>Doğum Yılı:</strong> ${data.dogum_yili}</p>
                ${teamInfoHtml}
                
                <hr>

                <h5><i class="fas fa-info-circle text-info me-2"></i>Randevu Bilgileri</h5>
                <p><strong>Tarih / Saat:</strong> ${appointmentDate} - ${data.slot.saat}</p>
                <p><strong>Mevcut Durum:</strong> <span class="badge status-${data.durum}">${data.durum.replace('_', ' ')}</span></p>
                <p><strong>İptal Kodu:</strong> ${data.iptal_kodu || 'Yok'}</p>
                <div class="mb-3">
                    <label for="sport_id_select" class="form-label"><strong>Spor Dalı Değiştir:</strong></label>
                    <select class="form-select form-select-sm" id="sport_id_select">
                        @foreach ($sports as $sport)
                            <option value="{{ $sport->id }}" ${data.slot.sport_id == {{ $sport->id }} ? 'selected' : ''}>{{ $sport->ad }}</option>
                        @endforeach
                    </select>
                </div>
                `;
                
                // Butonların görünürlüğünü duruma göre ayarla
                if (data.durum === 'onaylandi') {
                    confirmBtn.style.display = 'inline-block';
                    noShowBtn.style.display = 'inline-block';
                } else {
                    confirmBtn.style.display = 'none';
                    noShowBtn.style.display = 'none';
                }
            });
    }

    // YENİ BUTON İÇİN EVENT LISTENER
    document.getElementById('confirmAttendanceButton').addEventListener('click', function() {
        if (!currentAppointmentId) return;
        
        Swal.fire({
            title: 'Katılım Onayı',
            text: "Bu randevuya katılımı onaylamak istediğinize emin misiniz?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Evet, Onayla!',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/appointments/${currentAppointmentId}/confirm-attendance`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(response => {
                    if (response.status === 200 && response.body.success) {
                        Swal.fire('Onaylandı!', response.body.message, 'success').then(() => window.location.reload());
                    } else {
                        Swal.fire('Hata!', response.body.message || 'Bir sorun oluştu.', 'error');
                    }
                });
            }
        });
    });

    // Varolan butonların event listener'ları aynı kalıyor
    document.getElementById('deleteButton').addEventListener('click', function() {
        if (!currentAppointmentId) return;
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Randevu kalıcı olarak silinecek!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Vazgeç',
            confirmButtonText: 'Evet, Sil!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/appointments/${currentAppointmentId}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                }).then(() => window.location.reload());
            }
        });
    });
    document.getElementById('updateButton').addEventListener('click', function() {
        if (!currentAppointmentId) return;
        const newSportId = document.getElementById('sport_id_select').value;
        fetch(`/admin/appointments/${currentAppointmentId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({ new_sport_id: newSportId })
        })
        .then(res => res.json().then(data => ({status: res.status, body: data})))
        .then(response => {
            if (response.status === 200 && response.body.success) {
                Swal.fire('Başarılı!', response.body.message, 'success').then(() => window.location.reload());
            } else {
                Swal.fire('Hata!', response.body.message, 'error');
            }
        });
    });
    document.getElementById('noShowButton').addEventListener('click', function() {
        if (!currentAppointmentId) return;
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu randevu 'gelmedi' olarak işaretlenecek ve kullanıcı 14 gün yasaklanacak!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            confirmButtonText: 'Evet, İşaretle!',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/appointments/${currentAppointmentId}/mark-as-no-show`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                })
                .then(res => res.json().then(data => ({status: res.status, body: data})))
                .then(response => {
                     if (response.status === 200 && response.body.success) {
                        Swal.fire('İşlem Başarılı!', response.body.message, 'success').then(() => window.location.reload());
                    } else {
                        Swal.fire('Hata!', response.body.message, 'error');
                    }
                });
            }
        });
    });
</script>
@endsection