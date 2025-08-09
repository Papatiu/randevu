@extends('layouts.app')

@section('styles')
    <style>
        .status-onaylandi {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-iptal_edildi {
            background-color: #f8d7da;
            color: #842029;
        }

        .status-onay_bekliyor {
            background-color: #fff3cd;
            color: #664d03;
        }

        .status-tamamlandi {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-gelmedi {
            background-color: #e2e3e5;
            color: #41464b;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-size: 0.8rem;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Yönetim Paneli - Tüm Randevular</h1>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Ad Soyad</th>
                                        <th>TC Kimlik</th>
                                        <th>Spor Dalı</th>
                                        <th>Tarih ve Saat</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($appointments as $appointment)
                                        <tr>
                                            <td>{{ $appointment->id }}</td>
                                            <td>{{ $appointment->ad }} {{ $appointment->soyad }}</td>
                                            <td>{{ $appointment->tc_kimlik }}</td>
                                            <td>{{ $appointment->slot->sport->ad }}</td>
                                            <td>{{ \Carbon\Carbon::parse($appointment->slot->tarih)->format('d/m/Y') }} <br>
                                                {{ $appointment->slot->saat }}</td>
                                            <td>
                                                <span class="badge status-{{ $appointment->durum }}">
                                                    {{ ucfirst(str_replace('_', ' ', $appointment->durum)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary"
                                                    onclick="showAppointmentDetails({{ $appointment->id }})">
                                                    Detay
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Henüz randevu bulunmamaktadır.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Sayfalama Linkleri --}}
                        <div class="d-flex justify-content-center">
                            {{ $appointments->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="appointmentDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Randevu Detayları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Detaylar AJAX ile buraya yüklenecek -->
                    <div class="text-center">
                        <div class="spinner-border"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div>
                        <!-- YENİ BUTON -->
                        <button type="button" class="btn btn-warning" id="noShowButton">Gelmedi Olarak İşaretle</button>
                        <button type="button" class="btn btn-danger" id="deleteButton">Randevuyu Sil</button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-primary" id="updateButton">Değişiklikleri Kaydet</button>
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
            modalBody.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>'; // Yükleniyor efekti
            detailModal.show();

            fetch(`/admin/appointments/${id}`)
                .then(res => res.json())
                .then(data => {
                    const appointmentDate = new Date(data.slot.tarih).toLocaleDateString('tr-TR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });

                    modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Katılımcı Bilgileri</h5>
                            <p><strong>Ad Soyad:</strong> ${data.ad} ${data.soyad}</p>
                            <p><strong>TC Kimlik:</strong> ${data.tc_kimlik}</p>
                            <p><strong>Telefon:</strong> ${data.telefon}</p>
                            <p><strong>Doğum Yılı:</strong> ${data.dogum_yili}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Randevu Bilgileri</h5>
                            <p><strong>Tarih / Saat:</strong> ${appointmentDate} - ${data.slot.saat}</p>
                            <div class="mb-3">
                                <label for="sport_id_select" class="form-label"><strong>Spor Dalı:</strong></label>
                                <select class="form-select" id="sport_id_select">
                                    @foreach ($sports as $sport)
                                        <option value="{{ $sport->id }}" ${data.slot.sport_id == {{ $sport->id }} ? 'selected' : ''}>
                                            {{ $sport->ad }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p><strong>Mevcut Durum:</strong> ${data.durum}</p>
                            <p><strong>İptal Kodu:</strong> ${data.iptal_kodu}</p>
                        </div>
                    </div>
                `;
                });
        }

        // Modal içindeki Sil butonuna tıklandığında...
        document.getElementById('deleteButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Randevu kalıcı olarak silinecek!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, Sil!',
                cancelButtonText: 'Vazgeç'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/appointments/${currentAppointmentId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => window.location.reload());
                }
            });
        });

        // Modal içindeki Kaydet butonuna tıklandığında...
        document.getElementById('updateButton').addEventListener('click', function() {
            const newSportId = document.getElementById('sport_id_select').value;

            fetch(`/admin/appointments/${currentAppointmentId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        new_sport_id: newSportId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Başarılı!', data.message, 'success').then(() => window.location.reload());
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                }).catch(() => Swal.fire('Hata!', 'İşlem sırasında bir hata oluştu.', 'error'));
        });
        document.getElementById('noShowButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu randevu 'gelmedi' olarak işaretlenecek ve bu kişi 14 gün boyunca yeni randevu alamayacak!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f0ad4e',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, İşaretle!',
                cancelButtonText: 'Vazgeç'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/appointments/${currentAppointmentId}/mark-as-no-show`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('İşlem Başarılı!', data.message, 'success').then(() => window
                                    .location.reload());
                            } else {
                                Swal.fire('Hata!', data.message, 'error');
                            }
                        });
                }
            });
        });
    </script>
@endsection
