@extends('layouts.app')

@section('styles')
    <style>
        .nav-tabs .nav-link {
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            font-weight: bold;
        }

        .form-text {
            font-size: 0.85rem;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <h1 class="mb-4">Yönetim Merkezi</h1>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-settings-tab" data-bs-toggle="tab"
                    data-bs-target="#general-settings" type="button" role="tab" aria-controls="general-settings"
                    aria-selected="true">
                    <i class="fas fa-cogs me-1"></i> Genel Ayarlar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sports-management-tab" data-bs-toggle="tab" data-bs-target="#sports-management"
                    type="button" role="tab" aria-controls="sports-management" aria-selected="false">
                    <i class="fas fa-futbol me-1"></i> Tesis Yönetimi
                </button>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="myTabContent">
            {{-- =================================== --}}
            {{-- ======== GENEL AYARLAR SEKMESİ ====== --}}
            {{-- =================================== --}}
            <div class="tab-pane fade show active" id="general-settings" role="tabpanel"
                aria-labelledby="general-settings-tab">
                <div class="card shadow-sm border-top-0 rounded-0 rounded-bottom">
                    <div class="card-body p-4">
                        <form id="general-settings-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="cancel_deadline_hours" class="form-label fw-bold">Randevu İptal Süresi
                                            (Saat)</label>
                                        <input type="number" class="form-control" id="cancel_deadline_hours"
                                            name="cancel_deadline_hours"
                                            value="{{ $settings['cancel_deadline_hours'] ?? 24 }}">
                                        <div class="form-text">Randevuya kaç saat kalana kadar iptal edilebileceğini
                                            belirtir.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="contact_phone" class="form-label fw-bold">İletişim Telefon
                                            Numarası</label>
                                        <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                                            value="{{ $settings['contact_phone'] ?? '' }}"
                                            placeholder="örn: 0555 123 45 67">
                                        <div class="form-text">İletişim için kullanılacak numara. (Gelecekte kullanılacak)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Genel
                                    Ayarları Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- =================================== --}}
            {{-- ====== TESİS YÖNETİMİ SEKMESİ ======= --}}
            {{-- =================================== --}}
            <div class="tab-pane fade" id="sports-management" role="tabpanel" aria-labelledby="sports-management-tab">
                <div class="card shadow-sm border-top-0 rounded-0 rounded-bottom">
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Tesis Ekleme/Düzenleme Formu -->
                            <div class="col-lg-5">
                                <h5 id="sport-form-title">Yeni Tesis Ekle</h5>
                                <hr>
                                <form id="sport-form">
                                    <input type="hidden" id="sport_id">
                                    <div class="mb-3">
                                        <label for="sport_ad" class="form-label">Tesis Adı</label>
                                        <input type="text" class="form-control" id="sport_ad" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status_reason" class="form-label">Durum Açıklaması (Pasif ise)</label>
                                        <input type="text" class="form-control" id="status_reason"
                                            placeholder="örn: Bakımda, Kapalı, Tadilatta vb.">
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="sport_is_active"
                                            checked>
                                        <label class="form-check-label" for="sport_is_active">Bu tesis randevuya açık
                                            mı?</label>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success"><i
                                                class="fas fa-save me-2"></i>Tesisi Kaydet</button>
                                        <button type="button" class="btn btn-secondary" id="reset-sport-form-btn"
                                            style="display: none;"><i class="fas fa-plus me-2"></i>Yeni Tesis
                                            Formu</button>
                                    </div>
                                </form>
                            </div>
                            <!-- Tesis Listesi -->
                            <div class="col-lg-7">
                                <h5>Mevcut Tesisler</h5>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Ad</th>
                                                <th class="text-center">Durum</th>
                                                <th class="text-center">İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sports-table-body">
                                            @forelse($sports as $sport)
                                                @include('admin.settings.partials.sport-row', [
                                                    'sport' => $sport,
                                                ])
                                            @empty
                                                <tr id="no-sports-row">
                                                    <td colspan="3" class="text-center">Henüz tesis eklenmemiş.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '{{ csrf_token() }}';

            // Helper function for SwalFire notifications
            const showSwal = (icon, title, text) => Swal.fire({
                icon,
                title,
                text,
                timer: 2000,
                showConfirmButton: false
            });

            // ===========================================
            // ========= GENEL AYARLAR İŞLEMLERİ =========
            // ===========================================
            const generalSettingsForm = document.getElementById('general-settings-form');
            generalSettingsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                fetch('{{ route('admin.settings.general.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            showSwal('success', 'Başarılı!', result.message);
                        } else {
                            showSwal('error', 'Hata!', 'Bir sorun oluştu.');
                        }
                    });
            });


            // ===========================================
            // ========= TESİS YÖNETİMİ İŞLEMLERİ ========
            // ===========================================
            const sportForm = document.getElementById('sport-form');
            const sportFormTitle = document.getElementById('sport-form-title');
            const sportIdInput = document.getElementById('sport_id');
            const sportAdInput = document.getElementById('sport_ad');
            const sportIsActiveInput = document.getElementById('sport_is_active');
            const statusReasonInput = document.getElementById('status_reason');
            const resetSportBtn = document.getElementById('reset-sport-form-btn');
            const sportsTableBody = document.getElementById('sports-table-body');

            function resetSportForm() {
                sportForm.reset();
                sportIdInput.value = '';
                sportFormTitle.innerText = 'Yeni Tesis Ekle';
                resetSportBtn.style.display = 'none';
            }

            resetSportBtn.addEventListener('click', resetSportForm);

            sportForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const id = sportIdInput.value;
                const url = id ? `/admin/sports/${id}` : '{{ route('admin.sports.store') }}';
                const method = id ? 'PUT' : 'POST';

                const data = {
                    ad: sportAdInput.value,
                    is_active: sportIsActiveInput.checked, // <-- BU SATIRI EKLİYORUZ

                    status_reason: statusReasonInput.value
                };

                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            showSwal('success', 'Başarılı!', result.message);
                            const noSportsRow = document.getElementById('no-sports-row');
                            if (noSportsRow) noSportsRow.remove();

                            const existingRow = document.getElementById(`sport-row-${id}`);
                            if (existingRow) {
                                existingRow.outerHTML = result.html;
                            } else {
                                sportsTableBody.insertAdjacentHTML('beforeend', result.html);
                            }
                            resetSportForm();
                        } else {
                            // Daha detaylı hata mesajı gösterebiliriz (örn: validation hatası)
                            showSwal('error', 'Hata!', result.message || 'Bir sorun oluştu.');
                        }
                    }).catch(() => showSwal('error', 'Hata!', 'Sunucuya bağlanılamadı.'));
            });

            // Düzenle butonu için event delegation
            sportsTableBody.addEventListener('click', function(e) {
                if (e.target.classList.contains('edit-sport-btn')) {
                    const button = e.target;
                    const sport = JSON.parse(button.dataset.sport);

                    sportFormTitle.innerText = 'Tesisi Düzenle';
                    sportIdInput.value = sport.id;
                    sportAdInput.value = sport.ad;
                    sportIsActiveInput.checked = sport.is_active;
                    statusReasonInput.value = sport.status_reason || '';
                    resetSportBtn.style.display = 'block';

                    // Formun olduğu yere scroll et
                    sportForm.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });

        });
    </script>
@endsection
