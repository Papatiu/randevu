@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <!-- DUYURU EKLEME / DÜZENLEME FORMU -->
            <div class="card">
                <div class="card-header">
                    <h5 id="form-title">Yeni Duyuru Ekle</h5>
                </div>
                <div class="card-body">
                    <form id="announcement-form">
                        <!-- ID'yi saklamak için gizli input -->
                        <input type="hidden" id="announcement_id" name="id">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Başlık</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">İçerik</label>
                            <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="show_until" class="form-label">Bitiş Tarihi (Opsiyonel)</label>
                            <input type="date" class="form-control" id="show_until" name="show_until">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Aktif Mi?
                            </label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                            <button type="button" class="btn btn-secondary" id="reset-form-btn" style="display: none;">Formu Temizle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- MEVCUT DUYURULAR LİSTESİ -->
            <div class="card">
                 <div class="card-header">
                    <h5>Mevcut Duyurular</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Başlık</th>
                                <th>Durum</th>
                                <th>Bitiş Tarihi</th>
                                <th style="width: 150px;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody id="announcements-table-body">
                            @include('admin.announcements.partials.announcement-rows', ['announcements' => $announcements])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('announcement-form');
    const formTitle = document.getElementById('form-title');
    const announcementIdInput = document.getElementById('announcement_id');
    const resetBtn = document.getElementById('reset-form-btn');

    // FORMU GÖNDERME (HEM EKLEME HEM GÜNCELLEME İÇİN)
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const id = announcementIdInput.value;
        const url = id ? `/admin/announcements/${id}` : '/admin/announcements';
        const method = id ? 'PUT' : 'POST';

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Checkbox değeri FormData'da düzgün gelmeyebilir, manuel kontrol
        data.is_active = document.getElementById('is_active').checked;

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if(result.success) {
                Swal.fire('Başarılı!', result.message, 'success');
                // Tabloyu yeniden yükle
                fetchAnnouncements();
                resetForm();
            } else {
                // Hata yönetimi (daha sonra geliştirilebilir)
                Swal.fire('Hata!', 'Bir sorun oluştu.', 'error');
            }
        });
    });

    // FORMU TEMİZLEME BUTONU
    resetBtn.addEventListener('click', resetForm);
    
    function resetForm() {
        form.reset();
        announcementIdInput.value = '';
        formTitle.innerText = 'Yeni Duyuru Ekle';
        resetBtn.style.display = 'none';
    }
    
    // TABLOYU AJAX İLE YENİDEN YÜKLEME
    function fetchAnnouncements() {
        // Normalde tabloyu yeniden doldurmak için bir GET isteği yapılır.
        // Ama biz sayfa yenilemenin basitliğini kullanabiliriz.
        // Veya daha iyisi, partial view render edip HTML'i basabiliriz.
        // Şimdilik en basit yöntem: Sayfayı yenilemek.
        // İstenirse bu daha da geliştirilebilir.
        window.location.reload(); 
    }

    // DÜZENLEME VE SİLME BUTONLARI İÇİN EVENT LISTENER (EVENT DELEGATION)
    const tableBody = document.getElementById('announcements-table-body');
    tableBody.addEventListener('click', function(e) {
        const target = e.target;
        
        // DÜZENLE BUTONU
        if(target.classList.contains('edit-btn')) {
            const id = target.dataset.id;
            
            fetch(`/admin/announcements/${id}`)
                .then(res => res.json())
                .then(data => {
                    formTitle.innerText = 'Duyuruyu Düzenle';
                    announcementIdInput.value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('content').value = data.content;
                    document.getElementById('is_active').checked = data.is_active;
                    // Tarih formatını YYYY-MM-DD olarak ayarlamamız gerekiyor
                    document.getElementById('show_until').value = data.show_until ? data.show_until.split('T')[0] : '';
                    resetBtn.style.display = 'block';
                    window.scrollTo(0, 0); // Sayfanın en üstüne git
                });
        }
        
        // SİL BUTONU
        if(target.classList.contains('delete-btn')) {
            const id = target.dataset.id;
            
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu duyuru kalıcı olarak silinecektir!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Evet, Sil!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/announcements/${id}`, {
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                    }).then(() => fetchAnnouncements());
                }
            });
        }
    });
});
</script>
@endsection