@extends('layouts.app')

@section('styles')
<style>
    /* Daha iyi bir görünüm için formun üstte sabitlenmesi */
    .form-card {
        position: sticky;
        top: 20px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Notlar ve Kurallar Yönetimi</h1>
    </div>

    <div class="row">
        <!-- Not Ekleme / Düzenleme Formu -->
        <div class="col-lg-5">
            <div class="card shadow-sm form-card">
                <div class="card-header bg-success text-white">
                    <h5 id="form-title" class="mb-0">Yeni Not Ekle</h5>
                </div>
                <div class="card-body">
                    <form id="note-form">
                        <input type="hidden" id="note_id">
                        <div class="mb-3">
                            <label for="content" class="form-label">İçerik</label>
                            <textarea class="form-control" id="content" name="content" rows="10"></textarea>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Ana Sayfada Aktif Olarak Gösterilsin</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Kaydet</button>
                            <button type="button" class="btn btn-secondary" id="reset-form-btn" style="display: none;"><i class="fas fa-plus-circle me-2"></i>Yeni Not Ekle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mevcut Notlar Listesi -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                 <div class="card-header">
                    <h5 class="mb-0">Mevcut Notlar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>İçerik (Önizleme)</th>
                                    <th class="text-center">Durum</th>
                                    <th class="text-center" style="width: 200px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="notes-table-body">
                                @forelse($notes as $note)
                                    @include('admin.notes.partials.note-row', ['note' => $note])
                                @empty
                                    <tr id="no-notes-row"><td colspan="3" class="text-center text-muted">Henüz oluşturulmuş bir not yok.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let editor;

    // Önce CKEditor'ü başlat. Eğer başarısız olursa konsola yaz.
    ClassicEditor
        .create(document.querySelector('#content'), { /* CKEditor config... */ })
        .then(newEditor => { editor = newEditor; })
        .catch(error => { console.error('CKEditor başlatma hatası:', error); });

    // Sayfa tamamen yüklendikten sonra JavaScript kodunu çalıştır.
    document.addEventListener('DOMContentLoaded', function () {
        
        const form = document.getElementById('note-form');
        const formTitle = document.getElementById('form-title');
        const noteIdInput = document.getElementById('note_id');
        const resetBtn = document.getElementById('reset-form-btn');
        const tableBody = document.getElementById('notes-table-body');

        // Formu temizle ve "Yeni Ekle" moduna geçir.
        function resetForm() {
            form.reset();
            noteIdInput.value = '';
            if (editor) editor.setData('');
            document.getElementById('is_active').checked = true;
            formTitle.innerText = 'Yeni Not Ekle';
            resetBtn.style.display = 'none';
        }
        
        resetBtn.addEventListener('click', resetForm);

        // KAYDET butonuna basıldığında (Ekleme veya Güncelleme)
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const id = noteIdInput.value;
            const url = id ? `/admin/notes/${id}` : '/admin/notes';
            const method = id ? 'PUT' : 'POST';
            const data = {
                content: editor.getData(),
                is_active: document.getElementById('is_active').checked
            };

            fetch(url, {
                method: method,
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Başarılı!', text: result.message, timer: 1500, showConfirmButton: false });
                    const noNotesRow = document.getElementById('no-notes-row');
                    if(noNotesRow) noNotesRow.remove();

                    if (id) {
                        document.getElementById(`note-row-${id}`).outerHTML = result.html;
                    } else {
                        tableBody.insertAdjacentHTML('afterbegin', result.html);
                    }
                    resetForm();
                } else {
                    Swal.fire({ icon: 'error', title: 'Hata!', text: 'İşlem sırasında bir sorun oluştu.' });
                }
            });
        });

        // Tablodaki butonlara tıklandığında (DÜZENLE veya SİL)
        tableBody.addEventListener('click', function(event) {
            const button = event.target.closest('button');
            if (!button) return;

            const id = button.dataset.id;
            
            if (button.classList.contains('edit-btn')) {
                fetch(`/admin/notes/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        formTitle.innerText = `Notu Düzenle (ID: ${data.id})`;
                        noteIdInput.value = data.id;
                        editor.setData(data.content);
                        document.getElementById('is_active').checked = data.is_active;
                        resetBtn.style.display = 'block';
                        form.scrollIntoView({ behavior: 'smooth' });
                    });
            }

            if (button.classList.contains('delete-btn')) {
                Swal.fire({
                    title: 'Emin misiniz?', text: "Bu not kalıcı olarak silinecektir!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Vazgeç', confirmButtonText: 'Evet, Sil!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/notes/${id}`, {
                            method: 'DELETE',
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                        })
                        .then(res => res.json())
                        .then(result => {
                            if(result.success) {
                                document.getElementById(`note-row-${id}`).remove();
                                Swal.fire('Silindi!', result.message, 'success');
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endsection