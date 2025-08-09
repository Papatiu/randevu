@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Site Ayarları</h1>
    <p>Sistemin genel çalışma kurallarını buradan yönetebilirsiniz.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.settings.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="cancel_deadline_hours" class="form-label">
                        <strong>Randevu İptali İçin Son Saat</strong>
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="cancel_deadline_hours" 
                           name="cancel_deadline_hours" 
                           value="{{ $settings['cancel_deadline_hours'] ?? 24 }}">
                    <div class="form-text">
                        Kullanıcıların randevularına kaç saat kalana kadar iptal işlemi yapabileceklerini belirtin. (Örn: 24)
                    </div>
                </div>

                {{-- Gelecekte buraya yeni ayar alanları eklenebilir --}}

                <button type="submit" class="btn btn-primary">Ayarları Kaydet</button>
            </form>
        </div>
    </div>
</div>
@endsection