@extends('layouts.app')

@section('styles')
<style>
    .auth-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .auth-card-header {
        background-color: #0d6efd;
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
        <div class="col-md-6">
            <div class="card auth-card">
                <div class="card-header auth-card-header">
                    <img src="https://www.eyyubiye.bel.tr/images/logo.png" alt="logo" class="auth-logo">
                    <h4>{{ __('Sisteme Giriş Yap') }}</h4>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('E-posta Adresi') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Şifre') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="row mb-3">
                             <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">{{ __('Beni Hatırla') }}</label>
                                </div>
                            </div>
                             <div class="col-md-6 text-end">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Şifremi Unuttum') }}
                                    </a>
                                @endif
                             </div>
                        </div>


                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">{{ __('Giriş Yap') }}</button>
                        </div>

                        <p class="text-center mt-3">
                            Hesabın yok mu? <a href="{{ route('register') }}">Hemen Kayıt Ol</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection