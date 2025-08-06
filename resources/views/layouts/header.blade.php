<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="https://www.eyyubiye.bel.tr/images/logo.png" height="50" alt="Logo">
        </a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Giriş Yap</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Kayıt Ol</a></li>
                @else
                    <li class="nav-item"><span class="nav-link">{{ Auth::user()->name }}</span></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-link nav-link" type="submit">Çıkış Yap</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
