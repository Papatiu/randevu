@extends('layouts.app')

@section('styles')
@section('styles')
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .login-card {
            max-width: 450px;
            width: 100%;
            background-color: #fff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(45deg, #0d6efd, #0d47a1);
            /* Daha şık bir gradient arkaplan */
            color: white;
            text-align: center;
            padding: 30px 20px;
        }

        .login-header .logo {
            max-width: 80px;
            margin-bottom: 15px;
            /* LOGO SORUNU ÇÖZÜMÜ: Saydamlığı korumak için filter'ı kaldırıyoruz.
               Bunun yerine logoya hafif bir gölge ekleyerek daha belirgin yapalım. */
            filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.3));
        }

        .login-header h2 {
            font-weight: 600;
            margin: 0;
        }

        .login-card .card-body {
            padding: 30px 40px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ced4da;
        }

        .form-control.is-invalid {
            /* Hata durumunda input'un görünümü */
            border-color: #dc3545;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
        }

        .bottom-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }
    </style>
@endsection
@endsection

@section('content')
<div class="login-container">
    <div class="card login-card">
        <div class="login-header">
            <img src="https://www.eyyubiye.bel.tr/images/logo.png" alt="logo" class="logo">
            <h2>Sisteme Giriş Yap</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('E-posta Adresi') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">{{ __('Şifre') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Giriş Yap') }}
                    </button>
                </div>

                <div class="bottom-links">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Beni Hatırla') }}
                        </label>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
