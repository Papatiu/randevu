@extends('layouts.app')

@section('styles')
<style>
    .auth-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .auth-card-header {
        background-color: #198754;
        color: white;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        padding: 1.5rem;
        text-align: center;
    }
    .auth-logo {
        display: block;
        margin: 0 auto 1rem;
        height: 60px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card auth-card">
                 <div class="card-header auth-card-header">
                    <img src="https://www.eyyubiye.bel.tr/images/logo.png" alt="logo" class="auth-logo">
                    <h4>{{ __('Yeni Hesap Oluştur') }}</h4>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        {{-- Laravel'in standart register controller'ı yerine kendi AuthController'ımızdaki register metodunu kullandığımız için User modeline göre alanları dolduruyoruz. --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ad" class="form-label">{{ __('Ad') }}</label>
                                <input id="ad" type="text" class="form-control @error('ad') is-invalid @enderror" name="ad" value="{{ old('ad') }}" required autofocus>
                                @error('ad')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="soyad" class="form-label">{{ __('Soyad') }}</label>
                                <input id="soyad" type="text" class="form-control @error('soyad') is-invalid @enderror" name="soyad" value="{{ old('soyad') }}" required>
                                @error('soyad')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tc_kimlik" class="form-label">{{ __('TC Kimlik Numarası') }}</label>
                            <input id="tc_kimlik" type="text" class="form-control @error('tc_kimlik') is-invalid @enderror" name="tc_kimlik" value="{{ old('tc_kimlik') }}" required>
                            @error('tc_kimlik')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-3">
                             <label for="telefon" class="form-label">{{ __('Telefon') }}</label>
                             <input id="telefon" type="text" class="form-control @error('telefon') is-invalid @enderror" name="telefon" value="{{ old('telefon') }}" required>
                             @error('telefon')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        
                        <div class="mb-3">
                             <label for="dogum_tarihi" class="form-label">{{ __('Doğum Tarihi') }}</label>
                             <input id="dogum_tarihi" type="date" class="form-control @error('dogum_tarihi') is-invalid @enderror" name="dogum_tarihi" value="{{ old('dogum_tarihi') }}" required>
                             @error('dogum_tarihi')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-3">
                             <label for="adres" class="form-label">{{ __('Adres') }}</label>
                             <textarea id="adres" class="form-control @error('adres') is-invalid @enderror" name="adres" required>{{ old('adres') }}</textarea>
                             @error('adres')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('E-posta Adresi') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                             @error('email')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">{{ __('Şifre') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                 @error('password')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password-confirm" class="form-label">{{ __('Şifre Tekrar') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">{{ __('Kayıt Ol') }}</button>
                        </div>
                        <p class="text-center mt-3">
                            Zaten bir hesabın var mı? <a href="{{ route('login') }}">Giriş Yap</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection