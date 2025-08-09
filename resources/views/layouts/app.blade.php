<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Eyyübiye Belediyesi Spor Randevu</title>
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <link rel="icon" href="{{ asset('storage/sports/favicon.ico') }}" type="image/x-icon">



    <!-- YENİ EKLENEN SWEETALERT2 KÜTÜPHANESİ -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* HTML ve BODY için temel ayarlar */
        html {
            height: 100%;
        }

        body {
            /* Flexbox'ı kullanarak dikey bir düzen oluşturuyoruz */
            display: flex;
            flex-direction: column;

            /* Sayfanın en az ekran yüksekliği kadar olmasını garantiliyoruz */
            min-height: 100vh;

            /* Genel Stil Ayarları */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f5f9;
            /* Senin istediğin renk */
            margin: 0;
            /* Tarayıcı varsayılan boşluklarını sıfırla */
        }

        /* Ana uygulama sarmalayıcısı (#app) */
        #app {
            /* Bu div'in de flexbox düzenini takip etmesini sağlıyoruz */
            display: flex;
            flex-direction: column;

            /* Ebeveyni olan body'deki kalan tüm boşluğu kaplamasını söylüyoruz */
            flex: 1;
        }

        /* Ana içerik alanı (main) */
        main.main-content {
            /* Bu alanın esneyerek header ve footer arasındaki tüm boşluğu doldurmasını sağlıyoruz */
            flex: 1;
        }

        /* Footer'ın kendisi */
        footer {
            /* Footer'ın içerik az olduğunda yukarı doğru büzülmesini engeller */
            flex-shrink: 0;
        }

        .announcement-bar {
            background-color: #0d6efd;
            /* Ana mavi renk */
            color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .announcement-item {
            display: inline-flex;
            align-items: center;
        }

        .announcement-item strong {
            margin-right: 5px;
        }
    </style>

    @yield('styles')
</head>

<body>
    <div id="app">
        <header class="bg-white shadow-sm">
            <nav class="navbar navbar-expand-lg navbar-light container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="https://www.eyyubiye.bel.tr/images/logo.png" alt="logo" height="50">
                    <span class="ms-2 fs-5 fw-bold d-none d-sm-inline">Eyyübiye Belediyesi</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto align-items-center">

                        <!-- Bu buton her zaman görünür -->
                        <li class="nav-item me-3">
                            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#cancelModal">
                                Randevu Sorgula / İptal Et
                            </button>
                        </li>

                        @guest
                            <!-- ======================================================= -->
                            <!--     MİSAFİR KULLANICILAR İÇİN GÖRÜNECEK ALAN         -->
                            <!-- ======================================================= -->
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Giriş Yap (Admin)') }}</a>
                                </li>
                            @endif
                        @else
                            <!-- ======================================================= -->
                            <!--      GİRİŞ YAPMIŞ KULLANICILAR İÇİN GÖRÜNECEK ALAN      -->
                            <!-- ======================================================= -->

                            <!-- Sadece admin ise bu linkleri göster -->
                            @if (Auth::user()->is_admin)
                                <li class="nav-item me-2">
                                    <a class="btn btn-primary btn-sm" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-calendar-alt me-1"></i> Randevular
                                    </a>
                                </li>
                                <li class="nav-item me-2">
                                    <a class="btn btn-secondary btn-sm" href="{{ route('admin.announcements.index') }}">
                                        <i class="fas fa-bullhorn me-1"></i> Duyurular
                                    </a>
                                </li>
                                <li class="nav-item me-2">
                                    <a class="btn btn-success btn-sm" href="{{ route('admin.notes.index') }}">
                                        <i class="fas fa-sticky-note me-1"></i> Notlar
                                    </a>
                                </li>
                                <li class="nav-item me-2">
                                    <a class="btn btn-warning btn-sm text-dark" href="{{ route('admin.settings.index') }}">
                                        <i class="fas fa-cog me-1"></i> Ayarlar
                                    </a>
                                </li>
                            @endif

                            <!-- Kullanıcı adı ve çıkış yap dropdown'ı -->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> {{ __('Çıkış Yap') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </nav>
        </header>

        <main class="py-4 main-content">
            @yield('content')
        </main>

        <footer class="text-center text-muted py-3 bg-white border-top mt-auto">
            © {{ date('Y') }} Eyyübiye Belediyesi | Tüm hakları saklıdır.
        </footer>
        <div class="modal fade" id="cancelModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Randevu Sorgulama ve İptal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Kod Giriş Formu -->
                        <div id="cancel-code-form">
                            <label for="cancel_code_input" class="form-label">Lütfen size verilen randevu iptal kodunu
                                giriniz:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="cancel_code_input"
                                    placeholder="Örn: AB12CD34">
                                <button class="btn btn-primary" type="button"
                                    onclick="findAppointment()">Sorgula</button>
                            </div>
                        </div>

                        <!-- Randevu Detayları (Başlangıçta gizli) -->
                        <div id="appointment-details-section" style="display: none;" class="mt-4">
                            <h5 class="text-center">Randevu Bilgileriniz</h5>
                            <div id="appointment-details-content" class="alert alert-light">
                                <!-- Detaylar buraya JS ile yüklenecek -->
                            </div>
                            <div class="text-danger" id="cancel-warning">
                                <strong>Uyarı:</strong> Bu işlem geri alınamaz. Randevunuzu iptal etmek istediğinizden
                                emin misiniz?
                            </div>
                        </div>

                        <div id="cancel-error" class="text-danger mt-2"></div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <!-- İptal Et butonu (Başlangıçta gizli) -->
                        <button type="button" class="btn btn-danger" id="confirm-cancel-btn" style="display: none;"
                            onclick="cancelAppointment()">Evet, Randevuyu İptal Et</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Dropdown için bu gerekli!) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')

    <!-- YENİ EKLENEN RANDEVU İPTAL JAVASCRIPT'İ -->
    <script>
        let foundAppointmentId = null;

        function findAppointment() {
            const code = document.getElementById('cancel_code_input').value;
            const errorDiv = document.getElementById('cancel-error');
            const detailsSection = document.getElementById('appointment-details-section');
            const detailsContent = document.getElementById('appointment-details-content');
            const confirmBtn = document.getElementById('confirm-cancel-btn');

            // Formu sıfırla
            errorDiv.textContent = '';
            detailsSection.style.display = 'none';
            confirmBtn.style.display = 'none';

            fetch('/randevu/sorgula', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        cancel_code: code,
                        _token: '{{ csrf_token() }}'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const app = data.appointment;
                        foundAppointmentId = app.id; // İptal işlemi için ID'yi sakla
                        detailsContent.innerHTML = `
                        <b>Spor Dalı:</b> ${app.sport_name}<br>
                        <b>Tarih:</b> ${new Date(app.date).toLocaleDateString('tr-TR')}<br>
                        <b>Saat:</b> ${app.time}<br>
                        <b>Ad Soyad:</b> ${app.full_name}
                    `;
                        detailsSection.style.display = 'block';
                        confirmBtn.style.display = 'block';
                    } else {
                        errorDiv.textContent = data.message;
                    }
                });
        }

        function cancelAppointment() {
            if (!foundAppointmentId) return;

            Swal.fire({
                title: 'Emin misiniz?',
                text: "Randevunuz kalıcı olarak iptal edilecektir!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, İptal Et!',
                cancelButtonText: 'Vazgeç'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/randevu/iptal-et', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                appointment_id: foundAppointmentId,
                                _token: '{{ csrf_token() }}'
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('İptal Edildi!', data.message, 'success')
                                    .then(() => window.location
                                        .reload()); // Sayfayı yenile ki slot durumu güncellensin
                            } else {
                                Swal.fire('Hata!', data.message, 'error');
                            }
                        });
                }
            });
        }
    </script>
</body>

</html>
